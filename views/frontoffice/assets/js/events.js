(() => {
  // Vérifier que PathUtils est disponible
  if (!window.PathUtils) {
    console.error('PathUtils not available! Make sure path-utils.js is loaded before events.js');
    return;
  }
  
  // Utiliser les utilitaires de chemin centralisés
  const API_URL = window.PathUtils.resolveApiUrl('event_actions.php');
  const BASE_PATH = window.PathUtils.getBasePath();
  

  const eventMap = new Map();
  let allEvents = [];

  function buildThumbnail(event) {
    const eventTheme = event.theme || 'Événement';
    const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f7b731', '#5f27cd', '#00d2d3', '#ff9ff3', '#54a0ff'];
    let hash = 0;
    for (let i = 0; i < eventTheme.length; i++) hash = eventTheme.charCodeAt(i) + ((hash << 5) - hash);
    const colorIndex = Math.abs(hash) % colors.length;
    const c1 = colors[colorIndex].replace('#', '%23');
    const c2 = colors[(colorIndex + 1) % colors.length].replace('#', '%23');

    const encodedTheme = encodeURIComponent(eventTheme);
    const fallback = `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='225'%3E%3Cdefs%3E%3ClinearGradient id='grad${event.id_evenement}' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' style='stop-color:${c1};stop-opacity:1' /%3E%3Cstop offset='100%25' style='stop-color:${c2};stop-opacity:1' /%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='400' height='225' fill='url(%23grad${event.id_evenement})'/%3E%3Ctext x='200' y='110' font-size='60' font-weight='bold' fill='white' text-anchor='middle' dominant-baseline='central' font-family='Arial'%3EGAME%3C/text%3E%3Ctext x='200' y='185' font-size='16' fill='rgba(255,255,255,0.9)' text-anchor='middle' font-family='Arial'%3E${encodedTheme}%3C/text%3E%3C/svg%3E`;
    
    let thumbUrl = null;
    // Utiliser banner_url directement (ignorer banner_full_url qui peut être incorrect)
    const sourceUrl = event.banner_url;
    
    if (sourceUrl && typeof sourceUrl === 'string') {
      const raw = sourceUrl.trim();
      if (raw) {
        if (raw.startsWith('http')) {
          // URL absolue
          thumbUrl = raw;
        } else {
          // Chemin relatif - construire l'URL correcte
          let cleanPath = raw;
          
          // Enlever les préfixes problématiques
          cleanPath = cleanPath.replace(/^\.\//, '').replace(/^\/+/, '');
          
          // Ajouter le slash initial
          cleanPath = '/' + cleanPath;
          
          // Construire l'URL complète avec le chemin de base
          const basePath = window.PathUtils.getBasePath();
          thumbUrl = basePath + cleanPath;
        }
      }
    }
    return { thumbUrl, fallback };
  }

  async function fetchEvents() {
    try {
      const res = await fetch(`${API_URL}?action=list`);
      const text = await res.text();
      let json;
      try {
        json = JSON.parse(text);
      } catch (parseErr) {
        console.error('JSON Parse Error. Full response:', text);
        throw new Error('Réponse invalide du serveur. Vérifiez la console.');
      }
      if (!json.success) throw new Error(json.message || 'Erreur de chargement');
      allEvents = json.data || [];
      eventMap.clear();
      allEvents.forEach(ev => {
        const key = Number(ev.id_evenement ?? ev.id);
        eventMap.set(key, ev);
      });
      populateFilters();
      renderEvents(getFilteredEvents());
    } catch (err) {
      console.error('Fetch error:', err);
      const empty = document.getElementById('events-empty');
      if (empty) {
        empty.style.display = 'block';
        empty.textContent = err.message;
      }
    }
  }

  function populateFilters() {
    const games = [...new Set(allEvents.map(e => e.theme).filter(Boolean))];
    const gameSelect = document.getElementById('filterGame');
    if (!gameSelect) return;
    
    games.forEach(game => {
      const option = document.createElement('option');
      option.value = game.toLowerCase();
      option.textContent = game;
      gameSelect.appendChild(option);
    });

    // Update event count
    const eventCount = document.getElementById('event-count');
    if (eventCount) eventCount.textContent = allEvents.length;
  }

  function getFilteredEvents() {
    const game = (document.getElementById('filterGame')?.value || '').toLowerCase();
    const status = document.getElementById('filterStatus')?.value || '';
    const sort = document.getElementById('filterSort')?.value || 'recent';

    let filtered = allEvents.filter(event => {
      if (game && (event.theme || '').toLowerCase() !== game) {
        return false;
      }
      if (status && event.statut !== status) {
        return false;
      }
      return true;
    });

    if (sort === 'participants') {
      filtered.sort((a, b) => (b.participants || 0) - (a.participants || 0));
    } else if (sort === 'date') {
      filtered.sort((a, b) => new Date(a.date_debut) - new Date(b.date_debut));
    } else {
      filtered.sort((a, b) => new Date(b.date_debut) - new Date(a.date_debut));
    }

    return filtered;
  }

  function renderEvents(events) {
    const grid = document.getElementById('events-grid');
    const empty = document.getElementById('events-empty');
    
    if (!grid) return;
    
    grid.innerHTML = '';
    if (!events.length) {
      if (empty) {
        empty.style.display = 'block';
        empty.textContent = 'Aucun événement ne correspond à vos critères.';
      }
      return;
    }
    if (empty) empty.style.display = 'none';

    events.forEach(event => {
      const col = document.createElement('div');
      col.className = 'col-lg-4 col-md-6 mb-4';

      const debut = new Date(event.date_debut);
      const fin = new Date(event.date_fin);
      const now = new Date();
      
      let statusLabel = '📅 À venir';
      let statusColor = '#0dcaf0';

      if (debut <= now && now <= fin) {
        statusLabel = '🔴 EN DIRECT';
        statusColor = '#22c55e';
      } else if (fin < now) {
        statusLabel = '✅ Terminé';
        statusColor = '#6c757d';
      }

      const { thumbUrl, fallback } = buildThumbnail(event);
      const imgSrc = thumbUrl || fallback;

      const card = document.createElement('div');
      card.className = 'event-card';

      const visual = document.createElement('div');
      visual.className = 'event-visual';

      const img = document.createElement('img');
      img.src = imgSrc;
      img.alt = event.titre;
      img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; display: block;';
      img.onerror = () => { img.onerror = null; console.warn('IMG fallback for event', event.id_evenement, imgSrc); img.src = fallback; };
      visual.appendChild(img);

      // Create status badge
      const status = document.createElement('div');
      status.className = 'event-status';
      status.textContent = statusLabel;
      status.style.background = statusColor;
      visual.appendChild(status);

      // Create theme badge
      const theme = document.createElement('div');
      theme.className = 'event-theme';
      theme.textContent = event.theme || 'Événement';
      visual.appendChild(theme);

      // Create body
      const body = document.createElement('div');
      body.className = 'event-body';
      body.innerHTML = `
        <h4 class="event-title">${event.titre}</h4>
        <div class="event-meta">
          <span><i class="fa fa-calendar"></i>${new Date(event.date_debut).toLocaleDateString('fr-FR')}</span>
          <span><i class="fa fa-map-marker"></i>En ligne</span>
        </div>
        <div class="event-pills">
          <div class="pill goal">🎯 ${event.objectif} DT</div>
          <div class="pill people">👥 ${event.participants || 0}</div>
        </div>
        <button class="event-btn" onclick="showEventDetails(${event.id_evenement})">Voir détails</button>
      `;

      card.appendChild(visual);
      card.appendChild(body);
      col.appendChild(card);
      grid.appendChild(col);
    });
  }

  // Filter event listeners
  document.addEventListener('DOMContentLoaded', function() {

    fetchEvents();

    const filterGame = document.getElementById('filterGame');
    const filterStatus = document.getElementById('filterStatus');
    const filterSort = document.getElementById('filterSort');
    const resetBtn = document.getElementById('resetFilters');

    if (filterGame) filterGame.addEventListener('change', () => renderEvents(getFilteredEvents()));
    if (filterStatus) filterStatus.addEventListener('change', () => renderEvents(getFilteredEvents()));
    if (filterSort) filterSort.addEventListener('change', () => renderEvents(getFilteredEvents()));
    
    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        if (filterGame) filterGame.value = '';
        if (filterStatus) filterStatus.value = '';
        if (filterSort) filterSort.value = 'recent';
        renderEvents(getFilteredEvents());
      });
    }
  });

  window.showEventDetails = function(id) {
    try {
      const keyNum = Number(id);
      console.log('showEventDetails called with id:', id, 'keyNum:', keyNum);
      const ev = eventMap.get(keyNum) || eventMap.get(String(id));
      console.log('Event data:', ev);
      if (!ev) {
        console.error('Event not found in map');
        return;
      }
      const modal = document.getElementById('eventDetailModal');
      console.log('Modal element:', modal);
      if (!modal) {
        console.error('Modal element not found');
        return;
      }

      const thumb = buildThumbnail(ev);
      const titleEl = document.getElementById('eventModalTitle');
      const dateEl = document.getElementById('eventModalDate');
      const themeEl = document.getElementById('eventModalTheme');
      const descEl = document.getElementById('eventModalDescription');
      const imgEl = document.getElementById('eventModalImg');

      console.log('Modal elements found:', { titleEl, dateEl, themeEl, descEl, imgEl });

      if (titleEl) titleEl.textContent = ev.titre || 'Événement';
      if (dateEl) dateEl.textContent = `${new Date(ev.date_debut).toLocaleDateString('fr-FR')} → ${new Date(ev.date_fin).toLocaleDateString('fr-FR')}`;
      if (themeEl) themeEl.textContent = ev.theme || 'Événement';
      if (descEl) descEl.textContent = ev.description || 'Aucune description fournie.';
      if (imgEl) imgEl.src = thumb.thumbUrl || thumb.fallback;

      console.log('Attempting to open modal...');
      
      // Try Bootstrap 5 first
      if (window.bootstrap && window.bootstrap.Modal) {
        console.log('Using Bootstrap 5 Modal');
        const bsModal = new window.bootstrap.Modal(modal, { backdrop: true });
        bsModal.show();
      } 
      // Fallback to jQuery Bootstrap 4
      else if (typeof jQuery !== 'undefined' && jQuery(modal).modal) {
        console.log('Using jQuery Bootstrap 4 Modal');
        jQuery(modal).modal('show');
      }
      // Last resort: add show class manually
      else {
        console.log('Using manual show class');
        modal.classList.add('show');
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
      }
    } catch (err) {
      console.error('Error in showEventDetails:', err, err.stack);
    }
  };
})();

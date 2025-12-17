(() => {
  // Utiliser les utilitaires de chemin centralisés
  const API_URL = window.PathUtils.resolveApiUrl('stream_actions.php');
  let allStreams = [];
  const userActions = {}; // Track user actions per stream: { streamId: { liked: bool, disliked: bool } }

  // Cache for found stream thumbnails
  const streamThumbCache = {};

  // Build streamer thumbnails: prefer pre-saved file per streamer, fallback to generated SVG
  function getStreamerThumbs(stream) {
    const pseudo = stream.streamer_name || stream.streamer_pseudo || 'Streamer';
    const streamerId = stream.id_streamer || stream.streamer_id || stream.id_user;

    const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f7b731', '#5f27cd', '#00d2d3', '#ff9ff3', '#54a0ff'];
    let hash = 0;
    for (let i = 0; i < pseudo.length; i++) hash = pseudo.charCodeAt(i) + ((hash << 5) - hash);
    const colorIndex = Math.abs(hash) % colors.length;
    const initial = pseudo.charAt(0).toUpperCase();
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="400" height="225">
        <defs>
          <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:${colors[colorIndex]};stop-opacity:1" />
            <stop offset="100%" style="stop-color:${colors[(colorIndex + 1) % colors.length]};stop-opacity:1" />
          </linearGradient>
        </defs>
        <rect width="400" height="225" fill="url(#grad)"/>
        <text x="200" y="110" font-size="80" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="central" font-family="Arial">${initial}</text>
        <text x="200" y="185" font-size="14" fill="rgba(255,255,255,0.9)" text-anchor="middle" font-family="Arial">${pseudo}</text>
      </svg>`;
    const fallback = `data:image/svg+xml;base64,${btoa(svg)}`;

    const primary = streamerId ? `assets/images/streamers/${streamerId}.jpg` : fallback;
    
    // Check for stream-specific thumbnail with multiple extensions (jpg, png, webp)
    let streamThumbnail = null;
    if (stream.id_stream) {
      // Use cached value if available
      if (streamThumbCache[stream.id_stream]) {
        streamThumbnail = streamThumbCache[stream.id_stream];
      } else {
        // Default to jpg, will try other extensions via onerror
        streamThumbnail = `assets/images/streams/${stream.id_stream}.jpg`;
      }
    }
    
    return { 
      avatar: primary,
      fallback,
      thumbnail: streamThumbnail || primary,
      streamId: stream.id_stream
    };
  }

  // Try different image extensions for stream thumbnails
  function tryNextExtension(img, streamId, fallback) {
    const extensions = ['jpg', 'png', 'webp'];
    const currentSrc = img.src;
    let currentExt = null;
    
    for (const ext of extensions) {
      if (currentSrc.endsWith('.' + ext)) {
        currentExt = ext;
        break;
      }
    }
    
    const currentIndex = currentExt ? extensions.indexOf(currentExt) : -1;
    const nextIndex = currentIndex + 1;
    
    if (nextIndex < extensions.length) {
      const basePath = `assets/images/streams/${streamId}`;
      img.src = basePath + '.' + extensions[nextIndex];
    } else {
      // All extensions failed, use fallback
      img.onerror = null;
      img.src = fallback;
    }
  }

  // Make function globally available for inline onerror handlers
  window.tryStreamThumbExtension = tryNextExtension;

  async function fetchStreams() {
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
      allStreams = json.data || [];
      populateFilters();
      renderTopStreamers();
      renderLiveStreams();
      renderStreams(getFilteredStreams());
    } catch (err) {
      console.error('Fetch error:', err);
      const empty = document.getElementById('streams-empty');
      if (empty) {
        empty.style.display = 'block';
        empty.textContent = err.message;
      }
    }
  }

  function renderTopStreamers() {
    console.log('renderTopStreamers called with', allStreams.length, 'streams');
    const streamerStats = {};
    allStreams.forEach(stream => {
      const pseudo = stream.streamer_name || stream.streamer_pseudo || 'Unknown';
      console.log('Processing stream:', stream.titre, 'by', pseudo);
      if (!streamerStats[pseudo]) {
        streamerStats[pseudo] = {
          pseudo,
          platform: stream.plateforme || stream.streamer_platform,
          views: 0,
          likes: 0,
          streams: 0
        };
      }
      streamerStats[pseudo].views += stream.nb_vues || 0;
      streamerStats[pseudo].likes += stream.nb_likes || 0;
      streamerStats[pseudo].streams += 1;
    });

    const topStreamers = Object.values(streamerStats)
      .sort((a, b) => {
        // Score composite: views + (likes * 10) + (streams * 5)
        const scoreA = a.views + (a.likes * 10) + (a.streams * 5);
        const scoreB = b.views + (b.likes * 10) + (b.streams * 5);
        return scoreB - scoreA;
      })
      .slice(0, 5);

    console.log('Top streamers calculated:', topStreamers);
    const container = document.getElementById('top-streamers');
    if (!container) {
      console.error('top-streamers container not found');
      return;
    }
    container.innerHTML = '';
    topStreamers.forEach((streamer, idx) => {
      const card = document.createElement('div');
      card.style.cssText = `
          flex: 0 0 calc(20% - 16px);
          min-width: 200px;
          background: linear-gradient(135deg, rgba(255, 107, 107, 0.2), rgba(200, 69, 105, 0.2));
          border: 2px solid #ff6b6b;
          border-radius: 12px;
          padding: 20px;
          text-align: center;
          cursor: pointer;
          transition: all 0.3s;
          position: relative;
        `;
      card.onmouseover = () => card.style.transform = 'scale(1.05) translateY(-5px)';
      card.onmouseout = () => card.style.transform = 'scale(1)';
      card.innerHTML = `
          <div style="font-size: 2.5em; margin-bottom: 10px;">🏆</div>
          <div style="position: absolute; top: 10px; right: 10px; background: #ff6b6b; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2em;">#${idx + 1}</div>
          <h3 style="color: #ff6b6b; font-weight: bold; margin: 0 0 10px 0; font-size: 1.2em;">${streamer.pseudo}</h3>
          <div style="color: #999; font-size: 0.9em; margin-bottom: 15px;">${streamer.platform || 'Multi'}</div>
          <div style="display: flex; justify-content: space-around; font-size: 0.9em;">
            <div><span style="color: #ff6b6b; font-weight: bold;">👁️ ${streamer.views}</span></div>
            <div><span style="color: #28a745; font-weight: bold;">👍 ${streamer.likes}</span></div>
            <div><span style="color: #ffc107; font-weight: bold;">📡 ${streamer.streams}</span></div>
          </div>
        `;
      container.appendChild(card);
    });
  }

  function renderLiveStreams() {
    const liveStreams = allStreams.filter(s => s.statut === 'en_cours');
    const liveCount = document.getElementById('live-count');
    if (liveCount) liveCount.textContent = liveStreams.length;

    if (liveStreams.length === 0) {
      const noLive = document.getElementById('no-live');
      if (noLive) noLive.style.display = 'block';
      const ls = document.getElementById('live-streams');
      if (ls) ls.innerHTML = '';
      return;
    }
    const noLive = document.getElementById('no-live');
    if (noLive) noLive.style.display = 'none';

    const container = document.getElementById('live-streams');
    if (!container) return;
    container.innerHTML = '';
    liveStreams.forEach(stream => {
      const thumbs = getStreamerThumbs(stream);
      const col = document.createElement('div');
      col.className = 'col-lg-3 col-md-6 mb-3';
      col.innerHTML = `
          <div style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.3), rgba(255, 107, 107, 0.3)); border: 2px solid #28a745; border-radius: 12px; overflow: hidden; cursor: pointer; transition: all 0.3s;" class="open-stream" data-id="${stream.id_stream}" data-url="${stream.url || '#'}">
            <div style="position: relative; height: 180px; overflow: hidden;">
              <img src="${thumbs.thumbnail}" onerror="window.tryStreamThumbExtension(this, ${stream.id_stream}, '${thumbs.fallback}')" alt="${stream.titre}" style="width: 100%; height: 100%; object-fit: cover;">
              <div style="position: absolute; top: 10px; left: 10px; background: #28a745; color: white; padding: 8px 12px; border-radius: 50px; font-size: 0.85em; font-weight: bold; animation: pulse 1s infinite;">
                🔴 LIVE
              </div>
            </div>
            <div style="padding: 15px;">
              <h4 style="margin: 0 0 10px 0; color: #ff6b6b; font-weight: bold; font-size: 1.1em;">${stream.titre}</h4>
              <p style="margin: 0 0 10px 0; color: #999; font-size: 0.9em;">🎮 ${stream.streamer_name || stream.streamer_pseudo || 'Streamer'}</p>
              <div style="display: flex; gap: 8px; margin-top: 10px;">
                <span style="flex: 1; background: rgba(255,107,107,0.2); padding: 5px; border-radius: 5px; font-size: 0.85em;">👁️ ${stream.nb_vues || 0}</span>
                <span style="flex: 1; background: rgba(40,167,69,0.2); padding: 5px; border-radius: 5px; font-size: 0.85em;">👍 ${stream.nb_likes || 0}</span>
              </div>
            </div>
          </div>
        `;
      container.appendChild(col);
    });
    attachActions();
  }

  function populateFilters() {
    const platforms = [...new Set(allStreams.map(s => s.plateforme || s.streamer_platform).filter(Boolean))];
    const platformSelect = document.getElementById('filterPlatform');
    if (!platformSelect) return;
    platforms.forEach(platform => {
      const option = document.createElement('option');
      option.value = platform.toLowerCase();
      option.textContent = platform;
      platformSelect.appendChild(option);
    });
  }

  function getFilteredStreams() {
    const platform = (document.getElementById('filterPlatform')?.value || '').toLowerCase();
    const status = document.getElementById('filterStatus')?.value || '';
    const sort = document.getElementById('filterSort')?.value || 'recent';

    let filtered = allStreams.filter(stream => {
      if (platform && (stream.plateforme || '').toLowerCase() !== platform && (stream.streamer_platform || '').toLowerCase() !== platform) {
        return false;
      }
      if (status && stream.statut !== status) {
        return false;
      }
      return true;
    });

    if (sort === 'views') {
      filtered.sort((a, b) => (b.nb_vues || 0) - (a.nb_vues || 0));
    } else if (sort === 'likes') {
      filtered.sort((a, b) => (b.nb_likes || 0) - (a.nb_likes || 0));
    } else {
      filtered.sort((a, b) => new Date(b.date_debut) - new Date(a.date_debut));
    }

    return filtered;
  }

  function renderStreams(streams) {
    const grid = document.getElementById('streams-grid');
    const empty = document.getElementById('streams-empty');
    if (!grid) return;
    grid.innerHTML = '';
    if (!streams.length) {
      if (empty) {
        empty.style.display = 'block';
        empty.textContent = 'Aucun stream ne correspond à vos critères.';
      }
      return;
    }
    if (empty) empty.style.display = 'none';

    streams.forEach(stream => {
      const thumbs = getStreamerThumbs(stream);
      const col = document.createElement('div');
      col.className = 'col-lg-4 col-md-6 mb-3';

      const debut = new Date(stream.date_debut);
      const fin = new Date(stream.date_fin);
      const now = new Date();
      let statusLabel = '📅 À venir';
      let statusColor = '#0dcaf0';
      let statusBg = 'rgba(13, 202, 240, 0.2)';
      let borderColor = '#0dcaf0';

      // Check if dates are valid before comparing
      if (debut instanceof Date && !isNaN(debut) && fin instanceof Date && !isNaN(fin)) {
        if (debut <= now && now <= fin) {
          statusLabel = '🔴 EN DIRECT';
          statusColor = '#28a745';
          statusBg = 'rgba(40, 167, 69, 0.2)';
          borderColor = '#28a745';
        } else if (fin < now) {
          statusLabel = '✅ Terminé';
          statusColor = '#6c757d';
          statusBg = 'rgba(108, 117, 125, 0.2)';
          borderColor = '#6c757d';
        }
      }

      const card = document.createElement('div');
      card.style.cssText = `
          background: ${statusBg};
          border: 2px solid ${borderColor};
          border-radius: 15px;
          overflow: hidden;
          transition: all 0.3s;
          cursor: pointer;
          height: 100%;
          display: flex;
          flex-direction: column;
        `;
      card.onmouseover = () => card.style.transform = 'translateY(-8px)';
      card.onmouseout = () => card.style.transform = 'translateY(0)';

      card.innerHTML = `
          <div style="position: relative; height: 200px; overflow: hidden; background: rgba(0,0,0,0.3);" class="open-stream" data-id="${stream.id_stream}" data-url="${stream.url || '#'}">
            <img src="${thumbs.thumbnail}" onerror="window.tryStreamThumbExtension(this, ${stream.id_stream}, '${thumbs.fallback}')" alt="${stream.titre}" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; top: 12px; right: 12px; background: ${statusColor}; color: white; padding: 8px 12px; border-radius: 8px; font-size: 0.85em; font-weight: bold; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
              ${statusLabel}
            </div>
            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 20px 15px 15px 15px; text-align: center;">
              <p style="color: #ff6b6b; font-weight: bold; margin: 0; font-size: 1.1em;">${stream.plateforme || stream.streamer_platform || 'Stream'}</p>
            </div>
          </div>
          <div style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
            <div style="display: flex; align-items: center; margin-bottom: 12px;">
              <img src="${thumbs.avatar}" onerror="this.onerror=null;this.src='${thumbs.fallback}';" alt="${stream.streamer_name || stream.streamer_pseudo || 'Streamer'}" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; border: 2px solid #ff6b6b; object-fit: cover;">
              <div>
                <p style="margin: 0; color: #ff6b6b; font-weight: bold; font-size: 0.95em;">${stream.streamer_name || stream.streamer_pseudo || 'Streamer'}</p>
                <p style="margin: 0; color: #999; font-size: 0.8em;">Streamer</p>
              </div>
            </div>
            <h4 style="margin: 0 0 15px 0; color: #fff; font-weight: bold; font-size: 1.15em; flex: 1;">${stream.titre}</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 15px;">
              <div style="background: rgba(255,107,107,0.2); padding: 10px; border-radius: 8px; text-align: center; font-size: 0.85em;">
                <p style="margin: 0; color: #ff6b6b; font-weight: bold;">👁️ ${stream.nb_vues || 0}</p>
              </div>
              <div style="background: rgba(40,167,69,0.2); padding: 10px; border-radius: 8px; text-align: center; font-size: 0.85em;">
                <p style="margin: 0; color: #28a745; font-weight: bold;">👍 ${stream.nb_likes || 0}</p>
              </div>
            </div>
            <div class="stats" data-id="${stream.id_stream}" style="display: flex; gap: 8px; margin-top: auto;">
              <button class="btn btn-sm like-btn" style="border: 1px solid #28a745; color: #28a745; background: rgba(40,167,69,0.1); cursor: pointer; flex: 1; padding: 8px; border-radius: 6px; font-weight: bold;">👍 <span class="count">${stream.nb_likes || 0}</span></button>
              <button class="btn btn-sm dislike-btn" style="border: 1px solid #dc3545; color: #dc3545; background: rgba(220,53,69,0.1); cursor: pointer; flex: 1; padding: 8px; border-radius: 6px; font-weight: bold;">👎 <span class="count">${stream.nb_dislikes || 0}</span></button>
              <button class="btn btn-sm comment-btn" style="border: 1px solid #ffc107; color: #ffc107; background: rgba(255,193,7,0.1); cursor: pointer; flex: 1; padding: 8px; border-radius: 6px; font-weight: bold;">💬 <span class="count">${stream.nb_commentaires || 0}</span></button>
              <a class="open-stream-link" data-id="${stream.id_stream}" data-url="${stream.url || '#'}" href="${stream.url || '#'}" target="_blank" style="flex: 1; background: linear-gradient(135deg, #ff6b6b, #ff8c42); border: none; border-radius: 6px; color: white; cursor: pointer; padding: 8px; text-align: center; text-decoration: none; font-weight: bold; font-size: 0.9em;">Voir</a>
            </div>
          </div>
        `;

      col.appendChild(card);
      grid.appendChild(col);
    });

    attachActions();
  }

  function attachActions() {
    document.querySelectorAll('.open-stream').forEach(el => {
      el.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        const id = el.dataset.id;
        if (!id) return;
        try {
          await postAction('view', id);
          const container = el.closest('.item')?.querySelector('.stats');
          if (container) incrementCount(container, '.count');
        } catch (err) { console.warn(err); }
      });
    });

    document.querySelectorAll('.open-stream-link').forEach(link => {
      link.addEventListener('click', async (e) => {
        e.stopPropagation();
        const id = link.dataset.id;
        const url = link.dataset.url || link.href;
        if (!id) return;
        
        // Don't prevent default if URL is valid - let the link open
        if (url && url !== '#') {
          // Record view in background, don't wait
          postAction('view', id).catch(err => console.warn(err));
          // Link will open naturally via href and target="_blank"
        } else {
          e.preventDefault();
          alert('Aucun lien disponible pour ce stream.');
        }
      });
    });

    document.querySelectorAll('.like-btn').forEach(btn => btn.addEventListener('dblclick', async (e) => {
      e.stopPropagation();
      const parent = e.target.closest('.stats');
      const id = parent?.dataset.id;
      if (!id) return;
      
      if (!userActions[id]) userActions[id] = { liked: false, disliked: false };
      
      // Double click to undo like
      if (userActions[id].liked) {
        btn.disabled = true;
        try {
          await postAction('unlike', id);
          userActions[id].liked = false;
          decrementCount(btn, '.count');
        } catch (err) { alert(err.message); }
        finally { btn.disabled = false; }
      }
    }));

    document.querySelectorAll('.like-btn').forEach(btn => btn.addEventListener('click', async (e) => {
      e.stopPropagation();
      const parent = e.target.closest('.stats');
      const id = parent?.dataset.id;
      if (!id) return;
      
      if (!userActions[id]) userActions[id] = { liked: false, disliked: false };
      
      // Can't like if already liked or if disliked
      if (userActions[id].liked || userActions[id].disliked) return;
      
      btn.disabled = true;
      try {
        await postAction('like', id);
        userActions[id].liked = true;
        incrementCount(btn, '.count');
      } catch (err) { alert(err.message); }
      finally { btn.disabled = false; }
    }));

    document.querySelectorAll('.dislike-btn').forEach(btn => btn.addEventListener('dblclick', async (e) => {
      e.stopPropagation();
      const parent = e.target.closest('.stats');
      const id = parent?.dataset.id;
      if (!id) return;
      
      if (!userActions[id]) userActions[id] = { liked: false, disliked: false };
      
      // Double click to undo dislike
      if (userActions[id].disliked) {
        btn.disabled = true;
        try {
          await postAction('undislike', id);
          userActions[id].disliked = false;
          decrementCount(btn, '.count');
        } catch (err) { alert(err.message); }
        finally { btn.disabled = false; }
      }
    }));

    document.querySelectorAll('.dislike-btn').forEach(btn => btn.addEventListener('click', async (e) => {
      e.stopPropagation();
      const parent = e.target.closest('.stats');
      const id = parent?.dataset.id;
      if (!id) return;
      
      if (!userActions[id]) userActions[id] = { liked: false, disliked: false };
      
      // Can't dislike if already disliked or if liked
      if (userActions[id].disliked || userActions[id].liked) return;
      
      btn.disabled = true;
      try {
        await postAction('dislike', id);
        userActions[id].disliked = true;
        incrementCount(btn, '.count');
      } catch (err) { alert(err.message); }
      finally { btn.disabled = false; }
    }));

    document.querySelectorAll('.comment-btn').forEach(btn => btn.addEventListener('click', async (e) => {
      e.stopPropagation();
      const parent = e.target.closest('.stats');
      const id = parent?.dataset.id;
      if (!id) return;
      btn.disabled = true;
      try {
        await postAction('comment', id);
        incrementCount(btn, '.count');
      } catch (err) { alert(err.message); }
      finally { btn.disabled = false; }
    }));
  }

  async function postAction(action, id) {
    const form = new FormData();
    form.append('action', action);
    form.append('id', id);
    const res = await fetch(API_URL, { method: 'POST', body: form });
    const text = await res.text();
    let json;
    try {
      json = JSON.parse(text);
    } catch (e) {
      console.error('API action raw response:', text);
      throw new Error('Réponse invalide du serveur');
    }
    if (!json.success) throw new Error(json.message || 'Erreur');
    return json;
  }

  function incrementCount(element, selector) {
    const countEl = element.querySelector ? element.querySelector(selector) : element.closest('button')?.querySelector(selector);
    if (countEl) {
      const current = parseInt(countEl.textContent || '0', 10);
      countEl.textContent = current + 1;
    }
  }

  function decrementCount(element, selector) {
    const countEl = element.querySelector ? element.querySelector(selector) : element.closest('button')?.querySelector(selector);
    if (countEl) {
      const current = parseInt(countEl.textContent || '0', 10);
      countEl.textContent = Math.max(0, current - 1);
    }
  }

  document.getElementById('filterPlatform')?.addEventListener('change', () => renderStreams(getFilteredStreams()));
  document.getElementById('filterStatus')?.addEventListener('change', () => renderStreams(getFilteredStreams()));
  document.getElementById('filterSort')?.addEventListener('change', () => renderStreams(getFilteredStreams()));
  document.getElementById('resetFilters')?.addEventListener('click', () => {
    const fp = document.getElementById('filterPlatform');
    const fs = document.getElementById('filterStatus');
    const fo = document.getElementById('filterSort');
    if (fp) fp.value = '';
    if (fs) fs.value = '';
    if (fo) fo.value = 'recent';
    renderStreams(getFilteredStreams());
  });

  document.addEventListener('DOMContentLoaded', fetchStreams);
})();

// clips.js - Handle clips display in frontoffice

let clipsAutoRefreshInterval = null;

// Utiliser les utilitaires de chemin centralisés
const CLIPS_API_URL = window.PathUtils.resolveApiUrl('clip_actions.php');

document.addEventListener('DOMContentLoaded', function() {
    loadTopClips();
    // Refresh clips every 30 seconds to show new clips automatically
    clipsAutoRefreshInterval = setInterval(loadTopClips, 30000);
});

// Stop auto-refresh when page is hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        clearInterval(clipsAutoRefreshInterval);
    } else {
        clipsAutoRefreshInterval = setInterval(loadTopClips, 30000);
        loadTopClips();
    }
});

function loadTopClips() {
    fetch(`${CLIPS_API_URL}?action=top&limit=6`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.clips) {
                renderTopClips(data.clips);
            } else if (data.error) {
                console.error('API Error:', data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            // Try to handle CORS or other issues gracefully
        });
}

function renderTopClips(clips) {
    const container = document.getElementById('top-clips');
    if (!container) return;

    if (!clips || clips.length === 0) {
        container.innerHTML = '<div class="col-12 text-center text-muted" style="padding: 40px;"><p>Aucun clip disponible pour le moment.</p></div>';
        return;
    }

    container.innerHTML = clips.map(clip => `
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="clip-card" style="background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(200, 69, 105, 0.1)); border: 2px solid #ff6b6b; border-radius: 15px; overflow: hidden; cursor: pointer; transition: all 0.3s; height: 100%;">
                <div style="position: relative; width: 100%; padding-bottom: 56.25%; background: #000; overflow: hidden;">
                    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
                        ${getVideoThumbnail(clip.url_video)}
                        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); opacity: 0; transition: opacity 0.3s;" class="play-overlay">
                            <div style="width: 60px; height: 60px; background: #ff6b6b; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 30px;">▶</div>
                        </div>
                    </div>
                </div>
                <div style="padding: 15px;">
                    <h5 style="color: #ff6b6b; font-weight: bold; margin: 0 0 8px 0; font-size: 1em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(clip.titre)}</h5>
                    <p style="color: #ccc; font-size: 0.85em; margin: 0 0 10px 0; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${escapeHtml(clip.description || '')}</p>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9em; color: #999;">
                        <div>
                            <span style="margin-right: 15px;">👁️ ${clip.nb_vues || 0}</span>
                            <span>❤️ ${clip.nb_likes || 0}</span>
                        </div>
                        <div style="color: #ff6b6b; font-weight: bold; font-size: 0.8em;">${clip.streamer_pseudo || 'Unknown'}</div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');

    // Add hover effects
    document.querySelectorAll('.clip-card').forEach(card => {
        card.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.boxShadow = '0 12px 30px rgba(255, 107, 107, 0.3)';
            const overlay = this.querySelector('.play-overlay');
            if (overlay) overlay.style.opacity = '1';
        });
        card.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
            const overlay = this.querySelector('.play-overlay');
            if (overlay) overlay.style.opacity = '0';
        });
        card.addEventListener('click', function() {
            const clip = clips[Array.from(document.querySelectorAll('.clip-card')).indexOf(this)];
            playClip(clip);
        });
    });
}

function getVideoThumbnail(url) {
    if (!url) return '<div style="width: 100%; height: 100%; background: #333; display: flex; align-items: center; justify-content: center; color: #666;"><i class="fas fa-film" style="font-size: 40px;"></i></div>';
    
    // YouTube
    if (url.includes('youtube.com') || url.includes('youtu.be')) {
        const videoId = extractYoutubeId(url);
        if (videoId) {
            return `<img src="https://img.youtube.com/vi/${videoId}/hqdefault.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="thumbnail">`;
        }
    }
    
    // Twitch
    if (url.includes('twitch.tv')) {
        return '<div style="width: 100%; height: 100%; background: linear-gradient(135deg, #9146ff, #772ce8); display: flex; align-items: center; justify-content: center; color: white;"><i class="fab fa-twitch" style="font-size: 50px;"></i></div>';
    }
    
    return '<div style="width: 100%; height: 100%; background: #333; display: flex; align-items: center; justify-content: center; color: #666;"><i class="fas fa-film" style="font-size: 40px;"></i></div>';
}

function extractYoutubeId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
}

function playClip(clip) {
    // Créer une modal pour afficher la vidéo
    const modal = document.createElement('div');
    modal.id = 'clip-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    `;
    
    // Extraire l'ID YouTube ou déterminer le type de vidéo
    const youtubeId = extractYoutubeId(clip.url_video);
    const isTwitch = clip.url_video.includes('twitch.tv');
    
    let videoEmbed = '';
    if (youtubeId) {
        videoEmbed = `<iframe width="100%" height="100%" src="https://www.youtube.com/embed/${youtubeId}?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 10px;"></iframe>`;
    } else if (isTwitch) {
        // Pour Twitch, on extrait l'ID du clip
        const twitchId = extractTwitchId(clip.url_video);
        if (twitchId) {
            videoEmbed = `<iframe src="https://clips.twitch.tv/embed?clip=${twitchId}&parent=localhost" height="100%" width="100%" style="border-radius: 10px;" allowfullscreen=""></iframe>`;
        } else {
            videoEmbed = `<a href="${clip.url_video}" target="_blank" style="color: #ff6b6b; text-decoration: underline; font-size: 18px;">Ouvrir la vidéo Twitch</a>`;
        }
    } else {
        // Lien direct si format non reconnu
        videoEmbed = `<a href="${clip.url_video}" target="_blank" style="color: #ff6b6b; text-decoration: underline; font-size: 18px;">Ouvrir la vidéo</a>`;
    }
    
    modal.innerHTML = `
        <div style="position: relative; width: 90%; max-width: 1000px; aspect-ratio: 16/9;">
            <button id="close-modal" style="position: absolute; top: -45px; right: 0; background: #ff6b6b; border: none; color: white; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 20px; z-index: 10001; display: flex; align-items: center; justify-content: center;">✕</button>
            <div style="width: 100%; height: 100%;">
                ${videoEmbed}
            </div>
            <div style="position: absolute; bottom: -60px; left: 0; right: 0; text-align: center;">
                <h4 style="color: #ff6b6b; margin: 10px 0 0 0; font-size: 1.2em;">${escapeHtml(clip.titre)}</h4>
                <p style="color: #ccc; font-size: 0.9em; margin: 5px 0 0 0;">Par ${clip.streamer_pseudo || 'Unknown'}</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Incrémenter les vues
    fetch(`${CLIPS_API_URL}?action=get&id_clip=${clip.id_clip}`);
    
    // Fermer la modal
    document.getElementById('close-modal').addEventListener('click', function() {
        modal.remove();
    });
    
    // Fermer en cliquant en dehors
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
    
    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('clip-modal')) {
            document.getElementById('clip-modal').remove();
        }
    });
}

function extractTwitchId(url) {
    // Extraire l'ID du clip Twitch
    const regExp = /twitch\.tv\/\w+\/clip\/([a-zA-Z0-9_-]+)/;
    const match = url.match(regExp);
    return match ? match[1] : null;
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

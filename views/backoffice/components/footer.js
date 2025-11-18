class CustomFooter extends HTMLElement {
  connectedCallback() {
    this.attachShadow({ mode: 'open' });
    this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: block;
          width: 100%;
          background-color: #111827;
          border-top: 1px solid #10b981;
          padding: 2rem 0;
          margin-top: 3rem;
        }
        .footer-content {
          max-width: 1200px;
          margin: 0 auto;
          padding: 0 2rem;
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
          gap: 2rem;
        }
        .footer-section h3 {
          color: #10b981;
          margin-bottom: 1rem;
          font-size: 1.1rem;
        }
        .footer-links {
          list-style: none;
          padding: 0;
        }
        .footer-links li {
          margin-bottom: 0.5rem;
        }
        .footer-links a {
          color: #9ca3af;
          text-decoration: none;
          transition: color 0.3s;
        }
        .footer-links a:hover {
          color: #10b981;
        }
        .social-links {
          display: flex;
          gap: 1rem;
          margin-top: 1rem;
        }
        .social-links a {
          color: #9ca3af;
          transition: color 0.3s;
        }
        .social-links a:hover {
          color: #10b981;
        }
        .copyright {
          text-align: center;
          margin-top: 2rem;
          padding-top: 1rem;
          border-top: 1px solid #374151;
          color: #6b7280;
          font-size: 0.875rem;
        }
        @media (max-width: 768px) {
          .footer-content {
            grid-template-columns: 1fr;
          }
        }
      </style>
      <div class="footer-content">
        <div class="footer-section">
          <h3>About Play2Help</h3>
          <p class="text-gray-400">Connecting gamers and streamers with charitable causes through the power of play.</p>
        </div>
        <div class="footer-section">
          <h3>Quick Links</h3>
          <ul class="footer-links">
            <li><a href="/">Home</a></li>
            <li><a href="/dashboard">Dashboard</a></li>
            <li><a href="/events">Events</a></li>
            <li><a href="/contact">Contact</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h3>Connect With Us</h3>
          <div class="social-links">
            <a href="#"><i data-feather="twitter"></i></a>
            <a href="#"><i data-feather="instagram"></i></a>
            <a href="#"><i data-feather="twitch"></i></a>
            <a href="#"><i data-feather="youtube"></i></a>
            <a href="#"><i data-feather="discord"></i></a>
          </div>
        </div>
      </div>
      <div class="copyright">
        &copy; ${new Date().getFullYear()} Play2Help. All rights reserved.
      </div>
    `;
  }
}

customElements.define('custom-footer', CustomFooter);
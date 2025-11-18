class CustomNavbar extends HTMLElement {
  connectedCallback() {
    this.attachShadow({ mode: 'open' });
    this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: block;
          width: 100%;
          background-color: #111827;
          border-bottom: 1px solid #10b981;
          box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
        }
        .navbar {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 1rem 2rem;
          max-width: 1200px;
          margin: 0 auto;
        }
        .logo {
          font-size: 1.5rem;
          font-weight: bold;
          color: #10b981;
          text-decoration: none;
          display: flex;
          align-items: center;
        }
        .logo-icon {
          margin-right: 0.5rem;
        }
        .nav-links {
          display: flex;
          gap: 2rem;
        }
        .nav-link {
          color: #9ca3af;
          text-decoration: none;
          font-weight: 500;
          transition: color 0.3s;
          display: flex;
          align-items: center;
        }
        .nav-link:hover {
          color: #10b981;
        }
        .nav-link i {
          margin-right: 0.5rem;
        }
        @media (max-width: 768px) {
          .navbar {
            flex-direction: column;
            padding: 1rem;
          }
          .nav-links {
            margin-top: 1rem;
            gap: 1rem;
          }
        }
      </style>
      <nav class="navbar">
        <a href="/" class="logo">
          <i data-feather="heart" class="logo-icon"></i>
          Play2Help
        </a>
        <div class="nav-links">
          <a href="/dashboard" class="nav-link">
            <i data-feather="home"></i>
            Dashboard
          </a>

        </div>
      </nav>
    `;
  }
}

customElements.define('custom-navbar', CustomNavbar);
/* ==========================================================
   Vote Admin — Shared Layout (Sidebar + Topbar)
   Separate from js/layout.js (which is for the voter side).
   ========================================================== */

const ADMIN_ICONS = {
  home: `<svg class="icon" viewBox="0 0 24 24"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9"/></svg>`,
  flag: `<svg class="icon" viewBox="0 0 24 24"><path d="M5 21V4"/><path d="M5 4h13l-3 4.5L18 13H5"/></svg>`,
  users: `<svg class="icon" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c.5-3.5 3-5.5 6.5-5.5s6 2 6.5 5.5"/><circle cx="17" cy="8.5" r="2.6"/><path d="M15.5 14.7c2.6.5 4.3 2.3 4.7 5.3"/></svg>`,
  vote: `<svg class="icon" viewBox="0 0 24 24"><rect x="4" y="9" width="16" height="11" rx="1.5"/><path d="M8 9V6a4 4 0 0 1 8 0v3"/><path d="M12 13v3"/></svg>`,
  logout: `<svg class="icon" viewBox="0 0 24 24"><path d="M15 4h3a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-3"/><path d="M10 8l-4 4 4 4"/><path d="M14 12H3"/></svg>`,
  bell: `<svg class="icon" viewBox="0 0 24 24"><path d="M6 9a6 6 0 0 1 12 0c0 4 1.5 5.5 1.5 5.5H4.5S6 13 6 9Z"/><path d="M10 19a2 2 0 0 0 4 0"/></svg>`,
  chevron: `<svg class="icon" viewBox="0 0 24 24" style="width:14px;height:14px;"><path d="m6 9 6 6 6-6"/></svg>`,
  menu: `<svg class="icon" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M3 12h18"/><path d="M3 18h18"/></svg>`,
  check: `<svg class="icon" viewBox="0 0 24 24"><path d="m4 12.5 5 5 11-11"/></svg>`,
};

const ADMIN_NAV_ITEMS = [
  { key: "dashboard", label: "Dashboard", icon: "home", href: "dashboard.php" },
  { key: "elections", label: "Manage Elections", icon: "flag", href: "elections.php" },
  { key: "candidates", label: "Manage Candidates", icon: "vote", href: "candidates.php" },
  { key: "results", label: "Results", icon: "check", href: "results.php" },
  { key: "users", label: "Manage Users", icon: "users", href: "#" },
];

function renderAdminLayout() {
  const activePage = document.body.dataset.page || "";
  const adminName = document.body.dataset.user || "Admin";

  const navHtml = ADMIN_NAV_ITEMS.map(item => `
    <a class="nav-item ${item.key === activePage ? "active" : ""}" href="${item.href}">
      ${ADMIN_ICONS[item.icon]}
      <span>${item.label}</span>
    </a>
  `).join("");

  const html = `
    <div class="overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
      <div class="sidebar-logo">
        <div class="logo-mark">🛡️</div>
        <div class="logo-text">Admin<span> Panel</span></div>
      </div>
      <nav class="sidebar-nav">
        ${navHtml}
      </nav>
      <div class="sidebar-footer">
        <a class="nav-item" href="logout.php" style="color:var(--red);">
          ${ADMIN_ICONS.logout}
          <span>Logout</span>
        </a>
      </div>
    </aside>

    <header class="topbar">
      <div class="topbar-left">
        <button class="menu-toggle" id="menuToggle">${ADMIN_ICONS.menu}</button>
        <div class="topbar-welcome">Welcome, ${adminName}</div>
      </div>
    </header>
  `;

  const root = document.getElementById("layout-root");
  root.insertAdjacentHTML("beforebegin", html);

  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");
  const toggle = document.getElementById("menuToggle");

  function closeSidebar() {
    sidebar.classList.remove("open");
    overlay.classList.remove("open");
  }

  toggle.addEventListener("click", () => {
    sidebar.classList.toggle("open");
    overlay.classList.toggle("open");
  });
  overlay.addEventListener("click", closeSidebar);
}

document.addEventListener("DOMContentLoaded", renderAdminLayout);

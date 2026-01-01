<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mon Espace') - {{ $client->coach->business_name ?? 'Coach' }}</title>
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      
      body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #f8fafc;
        min-height: 100vh;
        overflow-x: hidden;
      }

      .dashboard-container {
        display: flex;
        min-height: 100vh;
      }

      /* Sidebar */
      .sidebar {
        width: 280px;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(20px);
        border-right: 1px solid rgba(148, 163, 184, 0.2);
        display: flex;
        flex-direction: column;
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
        z-index: 100;
        transition: transform 0.3s ease;
      }

      .sidebar-header {
        padding: 1.5rem 1.25rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.15);
      }

      .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
      }

      .sidebar-logo img {
        height: 40px;
        width: auto;
        object-fit: contain;
      }

      .sidebar-logo-text {
        font-size: 1.25rem;
        font-weight: 700;
        background: linear-gradient(135deg, #a855f7, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }

      .sidebar-user {
        margin-top: 1rem;
        padding: 0.75rem;
        background: rgba(15, 23, 42, 0.6);
        border-radius: 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
      }

      .sidebar-user-name {
        font-weight: 600;
        font-size: 0.95rem;
        color: #f8fafc;
      }

      .sidebar-user-code {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.25rem;
        letter-spacing: 0.1em;
      }

      .sidebar-nav {
        flex: 1;
        padding: 1.5rem 0.75rem;
        overflow-y: auto;
      }

      .nav-section {
        margin-bottom: 1.5rem;
      }

      .nav-section-title {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #94a3b8;
        padding: 0 0.75rem;
        margin-bottom: 0.5rem;
      }

      .nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        margin-bottom: 0.25rem;
        border-radius: 0.75rem;
        color: #cbd5e1;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.95rem;
        font-weight: 500;
      }

      .nav-item:hover {
        background: rgba(99, 102, 241, 0.1);
        color: #a5b4fc;
      }

      .nav-item.active {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
        color: #f8fafc;
        border: 1px solid rgba(129, 140, 248, 0.3);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
      }

      .nav-item-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
      }

      .nav-item-badge {
        margin-left: auto;
        background: rgba(99, 102, 241, 0.2);
        color: #a5b4fc;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
      }

      .sidebar-footer {
        padding: 1.25rem;
        border-top: 1px solid rgba(148, 163, 184, 0.15);
      }

      .coach-contact {
        font-size: 0.85rem;
        color: #94a3b8;
        line-height: 1.5;
      }

      .coach-contact a {
        color: #a5b4fc;
        text-decoration: none;
      }

      .coach-contact a:hover {
        text-decoration: underline;
      }

      /* Main Content */
      .main-content {
        flex: 1;
        margin-left: 280px;
        display: flex;
        flex-direction: column;
      }

      .top-bar {
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        padding: 1.25rem 2rem;
        position: sticky;
        top: 0;
        z-index: 50;
      }

      .top-bar-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
      }

      .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #f8fafc, #cbd5e1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }

      .mobile-menu-btn {
        display: none;
        background: rgba(99, 102, 241, 0.15);
        border: 1px solid rgba(129, 140, 248, 0.3);
        border-radius: 0.5rem;
        padding: 0.5rem;
        cursor: pointer;
        color: #a5b4fc;
      }

      .content-area {
        flex: 1;
        padding: 2rem;
        overflow-y: auto;
      }

      .content-wrapper {
        max-width: 1400px;
        margin: 0 auto;
      }

      /* Mobile Styles */
      @media (max-width: 768px) {
        .sidebar {
          transform: translateX(-100%);
        }

        .sidebar.open {
          transform: translateX(0);
        }

        .main-content {
          margin-left: 0;
        }

        .mobile-menu-btn {
          display: block;
        }

        .content-area {
          padding: 1.5rem 1rem;
        }

        .top-bar {
          padding: 1rem 1.25rem;
        }

        .page-title {
          font-size: 1.25rem;
        }
      }

      /* Overlay for mobile */
      .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 99;
      }

      .sidebar-overlay.open {
        display: block;
      }

      @media (max-width: 768px) {
        .sidebar-overlay.open {
          display: block;
        }
      }
    </style>
    @stack('head')
  </head>
  <body>
    <div class="dashboard-container">
      <!-- Sidebar Overlay -->
      <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

      <!-- Sidebar -->
      <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
          <div class="sidebar-logo">
            @if($client->coach->logo_url ?? false)
              <img src="{{ $client->coach->logo_url }}" alt="Logo">
            @else
              <span class="sidebar-logo-text">{{ $client->coach->business_name ?? 'COACH' }}</span>
            @endif
          </div>
          <div class="sidebar-user">
            <div class="sidebar-user-name">{{ $client->first_name }} {{ $client->last_name }}</div>
            <div class="sidebar-user-code">Code: {{ $client->share_code }}</div>
          </div>
        </div>

        <nav class="sidebar-nav">
          <div class="nav-section">
            <div class="nav-section-title">Programmes</div>
            <a href="{{ route('clients.dashboard.program', $client->share_token) }}" class="nav-item {{ request()->routeIs('clients.dashboard.program') ? 'active' : '' }}">
              <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
              Programme sportif
              @if($programCount ?? 0)
                <span class="nav-item-badge">{{ $programCount }}</span>
              @endif
            </a>
            <a href="{{ route('clients.dashboard.nutrition', $client->share_token) }}" class="nav-item {{ request()->routeIs('clients.dashboard.nutrition') ? 'active' : '' }}">
              <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
              Programme alimentaire
              @if($nutritionCount ?? 0)
                <span class="nav-item-badge">{{ $nutritionCount }}</span>
              @endif
            </a>
          </div>

          <div class="nav-section">
            <div class="nav-section-title">Suivi</div>
            <a href="{{ route('clients.dashboard.analytics', $client->share_token) }}" class="nav-item {{ request()->routeIs('clients.dashboard.analytics') ? 'active' : '' }}">
              <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
              </svg>
              Mon Évolution
            </a>
            <a href="{{ route('clients.dashboard.assessment', $client->share_token) }}" class="nav-item {{ request()->routeIs('clients.dashboard.assessment') ? 'active' : '' }}">
              <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
              Bilans
              @if($assessmentCount ?? 0)
                <span class="nav-item-badge">{{ $assessmentCount }}</span>
              @endif
            </a>
            <a href="{{ route('clients.dashboard.notes', $client->share_token) }}" class="nav-item {{ request()->routeIs('clients.dashboard.notes') ? 'active' : '' }}">
              <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
              </svg>
              Notes du coach
              @if($notesCount ?? 0)
                <span class="nav-item-badge">{{ $notesCount }}</span>
              @endif
            </a>
          </div>

          <div class="nav-section">
            <div class="nav-section-title">Communication</div>
            <a href="{{ route('clients.dashboard.messages', $client->share_token) }}" class="nav-item {{ request()->routeIs('clients.dashboard.messages') ? 'active' : '' }}">
              <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
              Messagerie
              @php
                $unreadCount = $client->unreadMessagesCount();
              @endphp
              @if($unreadCount > 0)
                <span class="nav-item-badge">{{ $unreadCount }}</span>
              @endif
            </a>
          </div>

          <div class="nav-section">
            <div class="nav-section-title">Paramètres</div>
            <a href="{{ route('clients.dashboard.profile', $client->share_token) }}" class="nav-item {{ request()->routeIs('clients.dashboard.profile') ? 'active' : '' }}">
              <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              Mon profil
            </a>
          </div>
        </nav>

        <div class="sidebar-footer">
          <div class="coach-contact">
            <strong>Besoin d'aide ?</strong><br>
            <a href="mailto:{{ $client->coach->user->email ?? '' }}">{{ $client->coach->user->email ?? '' }}</a>
          </div>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="main-content">
        <div class="top-bar">
          <div class="top-bar-content">
            <button class="mobile-menu-btn" onclick="toggleSidebar()">
              <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
            <h1 class="page-title">@yield('page-title', 'Mon Espace')</h1>
          </div>
        </div>

        <div class="content-area">
          <div class="content-wrapper">
            @yield('content')
          </div>
        </div>
      </main>
    </div>

    <script>
      function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
      }
    </script>
    @stack('scripts')
  </body>
</html>

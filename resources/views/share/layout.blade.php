<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Documents partagés · FEA Coach</title>
    <style>
      :root {
        color-scheme: dark;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      }
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      body {
        min-height: 100vh;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .container {
        width: 100%;
        max-width: 960px;
        padding: 3rem 1.5rem;
      }
      .card {
        background: rgba(15, 23, 42, 0.96);
        border-radius: 1.5rem;
        padding: 2.25rem 2.5rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.65);
      }
      .title {
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        margin-bottom: 0.5rem;
      }
      .subtitle {
        color: #94a3b8;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.75rem;
      }
      .eyebrow {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.18em;
        color: #64748b;
        margin-bottom: 0.5rem;
      }
      a {
        color: #a5b4fc;
        text-decoration: none;
      }
      a:hover {
        text-decoration: underline;
      }
      button,
      input[type='submit'] {
        background: linear-gradient(135deg, rgba(99, 102, 241, 1), rgba(129, 140, 248, 1));
        border: 1px solid rgba(129, 140, 248, 0.85);
        color: #f9fafb;
        font-weight: 600;
        border-radius: 999px;
        padding: 0.85rem 1.75rem;
        cursor: pointer;
        font-size: 0.95rem;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.35);
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
      }
      button:hover,
      input[type='submit']:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(79, 70, 229, 0.45);
      }
      button:disabled,
      input:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        box-shadow: none;
      }
      .input {
        width: 100%;
        border-radius: 0.9rem;
        border: 1px solid rgba(148, 163, 184, 0.35);
        padding: 0.9rem 1rem;
        background: rgba(15, 23, 42, 0.9);
        color: #f8fafc;
        font-size: 0.95rem;
        letter-spacing: 0.18em;
        text-align: center;
        font-weight: 600;
        transition: border 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
      }
      .input:focus {
        outline: none;
        border-color: rgba(129, 140, 248, 0.9);
        box-shadow: 0 0 0 1px rgba(129, 140, 248, 0.9);
        background: rgba(15, 23, 42, 1);
      }
      .alert {
        margin-top: 1.25rem;
        padding: 0.9rem 1rem;
        border-radius: 0.9rem;
        background: rgba(248, 113, 113, 0.12);
        border: 1px solid rgba(248, 113, 113, 0.35);
        color: #fecaca;
        font-size: 0.9rem;
      }
      .success {
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.4);
        color: #bbf7d0;
      }
      .secure-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(148, 163, 184, 0.3);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        color: #94a3b8;
      }
      .secure-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.16);
        animation: breathe 1.8s ease-in-out infinite;
      }
      @keyframes breathe {
        0%, 100% {
          transform: scale(1);
          box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.16);
        }
        50% {
          transform: scale(1.04);
          box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.26);
        }
      }
      .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        margin-bottom: 2rem;
      }
      .page-header-main {
        flex: 1;
        min-width: 0;
      }
      .form-actions {
        margin-top: 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
      }
      .form-hint {
        font-size: 0.8rem;
        color: #64748b;
      }
      @media (max-width: 640px) {
        .card {
          padding: 1.85rem 1.6rem;
        }
        .page-header {
          flex-direction: column;
          align-items: flex-start;
        }
        .code-chip {
          align-self: flex-start;
        }
        .form-actions {
          flex-direction: column;
          align-items: stretch;
        }
        button,
        input[type='submit'] {
          width: 100%;
          justify-content: center;
          text-align: center;
        }
      }
    </style>
    @stack('head')
  </head>
  <body>
    <div class="container">
      @yield('content')
    </div>
  </body>
</html>

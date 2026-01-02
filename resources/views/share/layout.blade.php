<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Documents partagés · FEA Coach</title>
    <style>
      :root {
        color-scheme: dark;
        font-family: 'Inter', 'General Sans', 'Space Grotesk', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      }
      * {
        box-sizing: border-box;
      }
      body {
        margin: 0;
        min-height: 100vh;
        background: radial-gradient(circle at top, rgba(99,102,241,0.25), transparent 40%), radial-gradient(circle at 20% 20%, rgba(14,165,233,0.35), transparent 35%), #020617;
        color: #f8fafc;
        display: flex;
        align-items: flex-start;
        justify-content: center;
      }
      .container {
        width: 100%;
        max-width: 760px;
        padding: 3.5rem 1.5rem 4rem;
      }
      .card {
        background: rgba(2, 6, 23, 0.8);
        border: 1px solid rgba(99, 102, 241, 0.35);
        border-radius: 1.75rem;
        padding: 2.5rem;
        box-shadow: 0 25px 60px rgba(2, 6, 23, 0.75);
        backdrop-filter: blur(24px);
        position: relative;
      }
      .card::after {
        content: '';
        position: absolute;
        inset: 12px;
        border-radius: 1.3rem;
        border: 1px solid rgba(255, 255, 255, 0.06);
        pointer-events: none;
      }
      .title {
        font-size: clamp(1.5rem, 2vw, 1.9rem);
        font-weight: 600;
        letter-spacing: -0.02em;
        margin-bottom: 0.5rem;
      }
      .subtitle {
        color: #94a3b8;
        font-size: 1rem;
        line-height: 1.5;
        margin-bottom: 2rem;
      }
      a {
        color: #38bdf8;
        text-decoration: none;
      }
      a:hover {
        text-decoration: underline;
      }
      button,
      input[type='submit'] {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none;
        color: white;
        font-weight: 600;
        border-radius: 999px;
        padding: 0.9rem 1.75rem;
        cursor: pointer;
        font-size: 0.95rem;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.35);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
      }
      button:hover,
      input[type='submit']:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 35px rgba(99, 102, 241, 0.45);
      }
      button:disabled,
      input:disabled {
        opacity: 0.7;
        cursor: not-allowed;
      }
      .input {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.35);
        padding: 1rem 1.25rem;
        background: rgba(2, 6, 23, 0.75);
        color: #f8fafc;
        font-size: 1rem;
        transition: border 0.2s ease, box-shadow 0.2s ease;
      }
      .input:focus {
        outline: none;
        border-color: rgba(129, 140, 248, 0.7);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
      }
      .alert {
        margin-top: 1.25rem;
        padding: 1rem 1.25rem;
        border-radius: 1rem;
        background: rgba(248, 113, 113, 0.15);
        border: 1px solid rgba(248, 113, 113, 0.35);
        color: #fecaca;
        font-size: 0.95rem;
      }
      .success {
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.35);
        color: #bbf7d0;
      }
      .share-code-pill {
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.8);
        padding: 0.65rem 1.5rem;
        color: #f8fafc;
        font-weight: 600;
        font-size: 1.05rem;
        letter-spacing: 0.4em;
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        cursor: pointer;
        transition: border 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
      }
      .share-code-pill:focus-visible {
        outline: none;
        border-color: rgba(129, 140, 248, 0.9);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
      }
      .share-code-value {
        filter: blur(0.55em);
        transition: filter 0.25s ease;
      }
      .share-code-pill:hover .share-code-value,
      .share-code-pill:focus-visible .share-code-value,
      .share-code-pill:active .share-code-value {
        filter: blur(0);
      }
      .share-code-hint {
        font-size: 0.7rem;
        letter-spacing: 0.2em;
        color: #94a3b8;
        text-transform: uppercase;
        transition: opacity 0.2s ease;
      }
      .share-code-pill:hover .share-code-hint,
      .share-code-pill:focus-visible .share-code-hint {
        opacity: 0.35;
      }
      @media (max-width: 640px) {
        .card {
          padding: 2rem;
        }
        .share-code-pill {
          width: 100%;
          justify-content: center;
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

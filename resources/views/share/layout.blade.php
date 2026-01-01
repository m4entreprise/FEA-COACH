<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Documents partagés · FEA Coach</title>
    <style>
      :root {
        color-scheme: dark;
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      }
      body {
        margin: 0;
        min-height: 100vh;
        background: radial-gradient(circle at top, #1f2937, #0f172a);
        color: #f8fafc;
      }
      .container {
        max-width: 640px;
        margin: 0 auto;
        padding: 2.5rem 1.5rem 3rem;
      }
      .card {
        background: rgba(15, 23, 42, 0.85);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(18px);
      }
      .title {
        font-size: 1.35rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
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
        padding: 0.75rem 1.5rem;
        cursor: pointer;
      }
      button:disabled,
      input:disabled {
        opacity: 0.7;
        cursor: not-allowed;
      }
      .input {
        width: 100%;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.4);
        padding: 0.85rem 1.25rem;
        background: rgba(15, 23, 42, 0.8);
        color: #f8fafc;
        font-size: 1rem;
      }
      .input:focus {
        outline: 2px solid rgba(129, 140, 248, 0.6);
      }
      .alert {
        margin-top: 1rem;
        padding: 1rem;
        border-radius: 0.85rem;
        background: rgba(248, 113, 113, 0.15);
        border: 1px solid rgba(248, 113, 113, 0.4);
        color: #fecaca;
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

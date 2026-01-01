@extends('share.layout')

@section('content')
  <div class="card">
    <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Documents sécurisés</p>
    <h1 class="title">Bonjour {{ $client->first_name }},</h1>
    <p class="text-slate-300 mb-6">
      Voici les documents partagés par votre coach. Vous pouvez télécharger la version la plus récente ou consulter l'historique.
    </p>

    @foreach ($types as $key => $label)
      @php
        $documents = $documentsByType->get($key, collect());
      @endphp
      <div style="margin-bottom: 2rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
          <h2 style="font-size:1.05rem; font-weight:600;">{{ $label }}</h2>
          @if ($documents->count())
            <span style="font-size:0.85rem; color:#94a3b8;">{{ $documents->count() }} version(s)</span>
          @endif
        </div>

        @if ($documents->isEmpty())
          <p style="color:#94a3b8; margin-top:0.5rem;">Aucun document partagé pour l'instant.</p>
        @else
          <div style="margin-top:0.75rem; border-left:2px solid rgba(148,163,184,0.3); padding-left:1rem; display:flex; flex-direction:column; gap:0.75rem;">
            @foreach ($documents as $document)
              <div style="display:flex; flex-wrap:wrap; align-items:center; gap:0.75rem; justify-content:space-between;">
                <div>
                  <p style="margin:0; font-weight:500;">Version {{ $document->version }} · {{ $document->title ?: $document->filename }}</p>
                  <p style="margin:0.2rem 0 0; font-size:0.85rem; color:#94a3b8;">
                    Publié le {{ $document->created_at->translatedFormat('d F Y à H\hi') }} · {{ number_format($document->filesize / 1024, 1) }} Ko
                  </p>
                </div>
                <a
                  href="{{ route('clients.share.download', [$token, $document]) }}"
                  style="padding:0.5rem 1.25rem; border-radius:999px; border:1px solid rgba(148,163,184,0.5); color:#f8fafc; text-decoration:none;"
                >
                  Télécharger
                </a>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach

    <div style="margin-top:1.5rem; font-size:0.85rem; color:#94a3b8;">
      <p style="margin:0;">Besoin d'aide ? Contactez directement votre coach.</p>
      <a href="mailto:{{ $client->coach->user->email ?? '' }}">{{ $client->coach->user->email ?? '' }}</a>
    </div>
  </div>
@endsection

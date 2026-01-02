@extends('share.layout')

@section('content')
  <div class="card">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:1.5rem; flex-wrap:wrap;">
      <div>
        <p class="text-sm uppercase tracking-[0.35em] text-slate-400">Documents sécurisés</p>
        <h1 class="title">Bonjour {{ $client->first_name }},</h1>
        <p class="subtitle">
          Votre coach met à jour vos programmes depuis cette interface. Téléchargez la version la plus récente ou consultez l’historique complet.
        </p>
      </div>
      <div style="display:flex; flex-direction:column; align-items:flex-end; gap:0.4rem;">
        <div style="font-size:0.85rem; letter-spacing:0.25em; color:#94a3b8;">CODE</div>
        <div style="padding:0.6rem 1.4rem; border-radius:999px; border:1px solid rgba(148,163,184,0.35); color:#f8fafc; font-weight:600; font-size:1.1rem;">
          {{ $client->share_code }}
        </div>
      </div>
    </div>

    @foreach ($types as $key => $label)
      @php
        $documents = $documentsByType->get($key, collect());
      @endphp
      <div style="margin-bottom: 2.5rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
          <h2 style="font-size:1.15rem; font-weight:600; letter-spacing:-0.01em;">{{ $label }}</h2>
          @if ($documents->count())
            <span style="font-size:0.9rem; color:#a5b4fc; background:rgba(79,70,229,0.15); border:1px solid rgba(79,70,229,0.4); border-radius:999px; padding:0.25rem 0.9rem;">
              {{ $documents->count() }} version(s)
            </span>
          @endif
        </div>

        @if ($documents->isEmpty())
          <div style="margin-top:0.9rem; padding:1rem 1.2rem; border-radius:1rem; border:1px dashed rgba(148,163,184,0.4); color:#94a3b8; font-size:0.95rem;">
            Aucun document partagé pour l'instant.
          </div>
        @else
          <div style="margin-top:1rem; display:flex; flex-direction:column; gap:1rem;">
            @foreach ($documents as $document)
              <div style="border:1px solid rgba(148,163,184,0.2); border-radius:1.2rem; padding:1.1rem 1.25rem; background:rgba(15,23,42,0.65); display:flex; flex-wrap:wrap; gap:1rem; justify-content:space-between; align-items:flex-start;">
                <div>
                  <p style="margin:0; font-weight:600; font-size:1rem;">
                    Version {{ str_pad($document->version, 2, '0', STR_PAD_LEFT) }}
                    <span style="color:#94a3b8; font-weight:400;">· {{ $document->title ?: $document->filename }}</span>
                  </p>
                  <p style="margin:0.35rem 0 0; font-size:0.9rem; color:#94a3b8;">
                    Publié le {{ $document->created_at->translatedFormat('d F Y à H\hi') }}
                    · {{ number_format($document->filesize / 1024, 1) }} Ko
                  </p>
                  @if ($document->description)
                    <p style="margin-top:0.6rem; font-size:0.9rem; color:#cbd5f5; line-height:1.5;">
                      {{ $document->description }}
                    </p>
                  @endif
                </div>
                <a
                  href="{{ route('clients.share.download', [$token, $document]) }}"
                  style="padding:0.6rem 1.6rem; border-radius:999px; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#f8fafc; font-weight:600; text-decoration:none; box-shadow:0 12px 25px rgba(99,102,241,0.35);"
                >
                  Télécharger
                </a>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach

    @if ($client->notes->count())
      <div style="margin-top:1.8rem;">
        <h2 style="font-size:1.1rem; font-weight:600; letter-spacing:-0.01em; margin-bottom:0.8rem;">Notes du coach</h2>
        <div style="display:flex; flex-direction:column; gap:0.85rem;">
          @foreach ($client->notes->take(5) as $note)
            <div style="border:1px solid rgba(148,163,184,0.2); border-radius:1.1rem; padding:1rem 1.2rem; background:rgba(15,23,42,0.55);">
              <div style="font-size:0.9rem; color:#f8fafc; line-height:1.55;">
                {!! nl2br(e($note->content)) !!}
              </div>
              <p style="margin:0.75rem 0 0; font-size:0.8rem; color:#94a3b8;">
                Ajouté le {{ $note->created_at->translatedFormat('d F Y à H\hi') }}
              </p>
            </div>
          @endforeach
        </div>
        @if ($client->notes->count() > 5)
          <p style="margin-top:0.6rem; font-size:0.85rem; color:#94a3b8;">
            (Affichage des 5 dernières notes)
          </p>
        @endif
      </div>
    @endif

    <div style="margin-top:2rem; padding:1.2rem 1.4rem; border-radius:1.2rem; border:1px solid rgba(148,163,184,0.25); background:rgba(15,23,42,0.6); font-size:0.95rem; color:#94a3b8;">
      <p style="margin:0 0 0.3rem; font-weight:600; color:#cbd5f5;">Besoin d'aide ?</p>
      <p style="margin:0;">
        Contactez directement votre coach :
        <a href="mailto:{{ $client->coach->user->email ?? '' }}">{{ $client->coach->user->email ?? '' }}</a>
      </p>
    </div>
  </div>
@endsection

@extends('client-dashboard.layout')

@section('page-title', 'Bilans')

@section('content')
<style>
  .section-header {
    margin-bottom: 2rem;
  }

  .section-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #f8fafc, #cbd5e1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .section-subtitle {
    color: #94a3b8;
    font-size: 1rem;
  }

  .documents-grid {
    display: grid;
    gap: 1.5rem;
  }

  .document-card {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 1.25rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
  }

  .document-card:hover {
    border-color: rgba(129, 140, 248, 0.5);
    box-shadow: 0 12px 30px rgba(99, 102, 241, 0.2);
    transform: translateY(-2px);
  }

  .document-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
  }

  .document-info {
    flex: 1;
  }

  .document-version {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(251, 146, 60, 0.15);
    border: 1px solid rgba(251, 146, 60, 0.4);
    border-radius: 999px;
    padding: 0.35rem 0.9rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #fdba74;
    margin-bottom: 0.75rem;
  }

  .document-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #f8fafc;
    margin-bottom: 0.5rem;
  }

  .document-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.9rem;
    color: #94a3b8;
    margin-bottom: 1rem;
  }

  .document-meta-item {
    display: flex;
    align-items: center;
    gap: 0.35rem;
  }

  .document-description {
    color: #cbd5e1;
    line-height: 1.6;
    margin-bottom: 1.25rem;
    font-size: 0.95rem;
  }

  .document-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.5rem;
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
  }

  .btn-primary {
    background: linear-gradient(135deg, #f97316, #fb923c);
    color: #f8fafc;
    box-shadow: 0 8px 20px rgba(249, 115, 22, 0.35);
  }

  .btn-primary:hover {
    box-shadow: 0 12px 28px rgba(249, 115, 22, 0.5);
    transform: translateY(-1px);
  }

  .btn-secondary {
    background: rgba(148, 163, 184, 0.15);
    border: 1px solid rgba(148, 163, 184, 0.3);
    color: #cbd5e1;
  }

  .btn-secondary:hover {
    background: rgba(148, 163, 184, 0.25);
    border-color: rgba(148, 163, 184, 0.5);
  }

  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(15, 23, 42, 0.4);
    border: 1px dashed rgba(148, 163, 184, 0.3);
    border-radius: 1.25rem;
  }

  .empty-state-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1.5rem;
    opacity: 0.5;
  }

  .empty-state-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
  }

  .empty-state-text {
    color: #94a3b8;
    font-size: 0.95rem;
  }

  .version-history {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(148, 163, 184, 0.2);
  }

  .version-history-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #94a3b8;
    margin-bottom: 0.75rem;
  }

  .version-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .version-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 0.85rem;
    background: rgba(15, 23, 42, 0.5);
    border: 1px solid rgba(148, 163, 184, 0.15);
    border-radius: 0.75rem;
    font-size: 0.875rem;
  }

  .version-item-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #94a3b8;
  }

  .version-item-date {
    font-size: 0.8rem;
  }
</style>

<div class="section-header">
  <h2 class="section-title">Bilans</h2>
  <p class="section-subtitle">Vos évaluations et rapports de progression</p>
</div>

<div class="documents-grid">
  @forelse($documents as $document)
    <div class="document-card">
      <div class="document-header">
        <div class="document-info">
          <div class="document-version">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Version {{ str_pad($document->version, 2, '0', STR_PAD_LEFT) }}
          </div>
          <h3 class="document-title">{{ $document->title ?: $document->filename }}</h3>
          <div class="document-meta">
            <div class="document-meta-item">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              {{ $document->created_at->translatedFormat('d F Y') }}
            </div>
            <div class="document-meta-item">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
              </svg>
              {{ number_format($document->filesize / 1024, 1) }} Ko
            </div>
          </div>
          @if($document->description)
            <p class="document-description">{{ $document->description }}</p>
          @endif
        </div>
      </div>
      <div class="document-actions">
        <a href="{{ route('clients.share.download', [$client->share_token, $document]) }}" class="btn btn-primary">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Télécharger
        </a>
      </div>

      @if($olderVersions->get($document->id)?->count())
        <div class="version-history">
          <div class="version-history-title">Versions précédentes ({{ $olderVersions->get($document->id)->count() }})</div>
          <div class="version-list">
            @foreach($olderVersions->get($document->id) as $oldDoc)
              <div class="version-item">
                <div class="version-item-info">
                  <span>Version {{ str_pad($oldDoc->version, 2, '0', STR_PAD_LEFT) }}</span>
                  <span class="version-item-date">{{ $oldDoc->created_at->translatedFormat('d/m/Y') }}</span>
                </div>
                <a href="{{ route('clients.share.download', [$client->share_token, $oldDoc]) }}" class="btn-secondary" style="padding: 0.4rem 0.9rem; font-size: 0.8rem;">
                  Télécharger
                </a>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  @empty
    <div class="empty-state">
      <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
      <h3 class="empty-state-title">Aucun bilan disponible</h3>
      <p class="empty-state-text">Votre coach n'a pas encore partagé de bilan.</p>
    </div>
  @endforelse
</div>
@endsection

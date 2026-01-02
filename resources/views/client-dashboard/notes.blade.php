@extends('client-dashboard.layout')

@section('page-title', 'Notes du coach')

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

  .notes-grid {
    display: grid;
    gap: 1.25rem;
  }

  .note-card {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 1.25rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
  }

  .note-card:hover {
    border-color: rgba(129, 140, 248, 0.4);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.15);
  }

  .note-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.15);
  }

  .note-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
    border: 1px solid rgba(129, 140, 248, 0.3);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .note-meta {
    flex: 1;
  }

  .note-date {
    font-size: 0.95rem;
    font-weight: 600;
    color: #f8fafc;
    margin-bottom: 0.25rem;
  }

  .note-time {
    font-size: 0.8rem;
    color: #94a3b8;
  }

  .note-content {
    color: #cbd5e1;
    line-height: 1.7;
    font-size: 0.95rem;
    white-space: pre-wrap;
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

  .notes-count {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(99, 102, 241, 0.15);
    border: 1px solid rgba(99, 102, 241, 0.4);
    border-radius: 999px;
    padding: 0.4rem 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #a5b4fc;
    margin-bottom: 1.5rem;
  }
</style>

<div class="section-header">
  <h2 class="section-title">Notes du coach</h2>
  <p class="section-subtitle">Messages et recommandations de votre coach</p>
</div>

@if($notes->count())
  <div class="notes-count">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
    </svg>
    {{ $notes->count() }} note{{ $notes->count() > 1 ? 's' : '' }}
  </div>
@endif

<div class="notes-grid">
  @forelse($notes as $note)
    <div class="note-card">
      <div class="note-header">
        <div class="note-icon">
          <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </div>
        <div class="note-meta">
          <div class="note-date">{{ $note->created_at->translatedFormat('d F Y') }}</div>
          <div class="note-time">{{ $note->created_at->translatedFormat('H\hi') }}</div>
        </div>
      </div>
      <div class="note-content">{{ $note->content }}</div>
    </div>
  @empty
    <div class="empty-state">
      <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
      </svg>
      <h3 class="empty-state-title">Aucune note disponible</h3>
      <p class="empty-state-text">Votre coach n'a pas encore ajouté de notes.</p>
    </div>
  @endforelse
</div>
@endsection

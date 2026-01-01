@extends('client-dashboard.layout')

@section('page-title', 'Mon Évolution')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
  }

  .stat-card {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 1rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
  }

  .stat-card:hover {
    border-color: rgba(129, 140, 248, 0.4);
    transform: translateY(-2px);
  }

  .stat-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .stat-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
    border: 1px solid rgba(129, 140, 248, 0.3);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .stat-label {
    font-size: 0.85rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #f8fafc;
    margin-bottom: 0.5rem;
  }

  .stat-change {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.85rem;
    font-weight: 600;
  }

  .stat-change.positive {
    color: #6ee7b7;
  }

  .stat-change.negative {
    color: #f87171;
  }

  .stat-change.neutral {
    color: #94a3b8;
  }

  .chart-card {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 1.25rem;
    padding: 2rem;
    margin-bottom: 2rem;
  }

  .chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .chart-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #f8fafc;
  }

  .chart-filters {
    display: flex;
    gap: 0.5rem;
  }

  .filter-btn {
    padding: 0.5rem 1rem;
    background: rgba(148, 163, 184, 0.15);
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 0.5rem;
    color: #cbd5e1;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .filter-btn:hover,
  .filter-btn.active {
    background: rgba(99, 102, 241, 0.2);
    border-color: rgba(129, 140, 248, 0.5);
    color: #a5b4fc;
  }

  .chart-container {
    position: relative;
    height: 350px;
  }

  .photos-section {
    margin-top: 2rem;
  }

  .photos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
  }

  .photo-timeline-card {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 1.25rem;
    padding: 1.25rem;
    transition: all 0.3s ease;
  }

  .photo-timeline-card:hover {
    border-color: rgba(129, 140, 248, 0.4);
    transform: translateY(-2px);
  }

  .photo-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #a5b4fc;
  }

  .photo-date svg {
    width: 16px;
    height: 16px;
  }

  .photo-views {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
  }

  .photo-view {
    position: relative;
    aspect-ratio: 3/4;
    border-radius: 0.75rem;
    overflow: hidden;
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(148, 163, 184, 0.2);
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .photo-view:hover {
    transform: scale(1.05);
    border-color: rgba(129, 140, 248, 0.5);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
  }

  .photo-view img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .photo-view-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    padding: 0.5rem;
    font-size: 0.75rem;
    color: #f8fafc;
    text-align: center;
    font-weight: 500;
  }

  .photo-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 0.75rem;
  }

  .photo-metrics {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(148, 163, 184, 0.2);
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
  }

  .photo-metric {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }

  .photo-metric-label {
    font-size: 0.75rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .photo-metric-value {
    font-size: 1rem;
    font-weight: 600;
    color: #f8fafc;
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
    margin-bottom: 1.5rem;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
  }

  .btn-primary {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #f8fafc;
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35);
  }

  .btn-primary:hover {
    box-shadow: 0 12px 28px rgba(99, 102, 241, 0.5);
    transform: translateY(-1px);
  }

  /* Lightbox */
  .lightbox {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.95);
    z-index: 9999;
    padding: 2rem;
    align-items: center;
    justify-content: center;
  }

  .lightbox.active {
    display: flex;
  }

  .lightbox-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  .lightbox-image {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 1rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
  }

  .lightbox-info {
    text-align: center;
    color: #f8fafc;
  }

  .lightbox-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
  }

  .lightbox-date {
    font-size: 0.9rem;
    color: #94a3b8;
  }

  .lightbox-close {
    position: absolute;
    top: -3rem;
    right: 0;
    background: rgba(15, 23, 42, 0.9);
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 0.75rem;
    padding: 0.75rem;
    cursor: pointer;
    color: #f8fafc;
    transition: all 0.2s ease;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .lightbox-close:hover {
    background: rgba(99, 102, 241, 0.3);
    border-color: rgba(129, 140, 248, 0.6);
  }

  .lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(15, 23, 42, 0.9);
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 0.75rem;
    padding: 1rem;
    cursor: pointer;
    color: #f8fafc;
    transition: all 0.2s ease;
  }

  .lightbox-nav:hover {
    background: rgba(99, 102, 241, 0.3);
    border-color: rgba(129, 140, 248, 0.6);
  }

  .lightbox-nav.prev {
    left: 1rem;
  }

  .lightbox-nav.next {
    right: 1rem;
  }

  @media (max-width: 768px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }

    .chart-container {
      height: 280px;
    }

    .photos-grid {
      grid-template-columns: 1fr;
    }

    .chart-filters {
      width: 100%;
      justify-content: stretch;
    }

    .filter-btn {
      flex: 1;
    }

    .lightbox {
      padding: 1rem;
    }

    .lightbox-nav {
      padding: 0.5rem;
    }

    .lightbox-close {
      top: auto;
      bottom: -4rem;
      right: 50%;
      transform: translateX(50%);
    }
  }
</style>
@endpush

@section('content')
<div class="section-header">
  <h2 class="section-title">Mon Évolution</h2>
  <p class="section-subtitle">Suivez vos progrès et visualisez vos transformations</p>
</div>

@if($measurements->isEmpty())
  <div class="empty-state">
    <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    <h3 class="empty-state-title">Aucune donnée disponible</h3>
    <p class="empty-state-text">Commencez à enregistrer vos mesures pour voir votre progression</p>
    <a href="{{ route('clients.dashboard.profile', $client->share_token) }}" class="btn btn-primary">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
      </svg>
      Ajouter mes mesures
    </a>
  </div>
@else
  {{-- Stats Cards --}}
  <div class="stats-grid">
    @if($latestMeasurement && $firstMeasurement)
      {{-- Poids --}}
      @if($latestMeasurement->weight)
        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-icon">
              <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
              </svg>
            </div>
            <span class="stat-label">Poids</span>
          </div>
          <div class="stat-value">{{ number_format($latestMeasurement->weight, 1) }} kg</div>
          @php
            $weightDiff = $latestMeasurement->weight - $firstMeasurement->weight;
            $changeClass = $weightDiff < 0 ? 'positive' : ($weightDiff > 0 ? 'negative' : 'neutral');
          @endphp
          <div class="stat-change {{ $changeClass }}">
            @if($weightDiff < 0)
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
              </svg>
              {{ number_format(abs($weightDiff), 1) }} kg
            @elseif($weightDiff > 0)
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
              </svg>
              +{{ number_format($weightDiff, 1) }} kg
            @else
              Stable
            @endif
          </div>
        </div>
      @endif

      {{-- IMC --}}
      @if($latestMeasurement->bmi)
        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-icon">
              <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
              </svg>
            </div>
            <span class="stat-label">IMC</span>
          </div>
          <div class="stat-value">{{ number_format($latestMeasurement->bmi, 1) }}</div>
          @php
            $bmiDiff = $latestMeasurement->bmi - ($firstMeasurement->bmi ?? 0);
          @endphp
          @if($bmiDiff != 0)
            <div class="stat-change {{ $bmiDiff < 0 ? 'positive' : 'negative' }}">
              {{ $bmiDiff > 0 ? '+' : '' }}{{ number_format($bmiDiff, 1) }} depuis le début
            </div>
          @endif
        </div>
      @endif

      {{-- Mesures --}}
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
          <span class="stat-label">Relevés</span>
        </div>
        <div class="stat-value">{{ $measurements->count() }}</div>
        <div class="stat-change neutral">
          Depuis {{ $firstMeasurement->created_at->translatedFormat('F Y') }}
        </div>
      </div>

      {{-- Photos --}}
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <span class="stat-label">Photos</span>
        </div>
        <div class="stat-value">{{ $photosCount }}</div>
        <div class="stat-change neutral">
          {{ $measurementsWithPhotos }} relevé(s) avec photos
        </div>
      </div>
    @endif
  </div>

  {{-- Graphiques --}}
  @if($measurements->count() >= 2)
    {{-- Poids --}}
    @if($measurements->where('weight', '!=', null)->count() >= 2)
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Évolution du Poids</h3>
        </div>
        <div class="chart-container">
          <canvas id="weightChart"></canvas>
        </div>
      </div>
    @endif

    {{-- Mensurations --}}
    @if($measurements->filter(fn($m) => $m->chest || $m->waist || $m->hips)->count() >= 2)
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Évolution des Mensurations</h3>
        </div>
        <div class="chart-container">
          <canvas id="measurementsChart"></canvas>
        </div>
      </div>
    @endif
  @endif

  {{-- Photos Timeline --}}
  @if($measurementsWithPhotos > 0)
    <div class="photos-section">
      <h3 style="font-size: 1.5rem; font-weight: 600; color: #f8fafc; margin-bottom: 1.5rem;">Galerie Photos</h3>
      <div class="photos-grid">
        @foreach($measurements->filter(fn($m) => $m->photo_front || $m->photo_side || $m->photo_back)->reverse() as $measurement)
          <div class="photo-timeline-card">
            <div class="photo-date">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              {{ $measurement->created_at->translatedFormat('d F Y') }}
            </div>
            <div class="photo-views">
              <div class="photo-view" @if($measurement->photo_front) onclick="openLightbox('{{ route('clients.dashboard.photo', [$client->share_token, $measurement->id, 'front']) }}', 'Face', '{{ $measurement->created_at->translatedFormat('d F Y') }}')" @endif>
                @if($measurement->photo_front)
                  <img src="{{ route('clients.dashboard.photo', [$client->share_token, $measurement->id, 'front']) }}" alt="Face">
                @else
                  <div class="photo-placeholder">-</div>
                @endif
                <div class="photo-view-label">Face</div>
              </div>
              <div class="photo-view" @if($measurement->photo_side) onclick="openLightbox('{{ route('clients.dashboard.photo', [$client->share_token, $measurement->id, 'side']) }}', 'Profil', '{{ $measurement->created_at->translatedFormat('d F Y') }}')" @endif>
                @if($measurement->photo_side)
                  <img src="{{ route('clients.dashboard.photo', [$client->share_token, $measurement->id, 'side']) }}" alt="Profil">
                @else
                  <div class="photo-placeholder">-</div>
                @endif
                <div class="photo-view-label">Profil</div>
              </div>
              <div class="photo-view" @if($measurement->photo_back) onclick="openLightbox('{{ route('clients.dashboard.photo', [$client->share_token, $measurement->id, 'back']) }}', 'Dos', '{{ $measurement->created_at->translatedFormat('d F Y') }}')" @endif>
                @if($measurement->photo_back)
                  <img src="{{ route('clients.dashboard.photo', [$client->share_token, $measurement->id, 'back']) }}" alt="Dos">
                @else
                  <div class="photo-placeholder">-</div>
                @endif
                <div class="photo-view-label">Dos</div>
              </div>
            </div>
            @if($measurement->weight || $measurement->bmi)
              <div class="photo-metrics">
                @if($measurement->weight)
                  <div class="photo-metric">
                    <span class="photo-metric-label">Poids</span>
                    <span class="photo-metric-value">{{ number_format($measurement->weight, 1) }} kg</span>
                  </div>
                @endif
                @if($measurement->bmi)
                  <div class="photo-metric">
                    <span class="photo-metric-label">IMC</span>
                    <span class="photo-metric-value">{{ number_format($measurement->bmi, 1) }}</span>
                  </div>
                @endif
              </div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  @endif
@endif

{{-- Lightbox --}}
<div class="lightbox" id="photoLightbox" onclick="closeLightbox(event)">
  <div class="lightbox-content" onclick="event.stopPropagation()">
    <button class="lightbox-close" onclick="closeLightbox()">
      <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
    <img id="lightboxImage" class="lightbox-image" src="" alt="">
    <div class="lightbox-info">
      <div class="lightbox-title" id="lightboxTitle"></div>
      <div class="lightbox-date" id="lightboxDate"></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Lightbox functions
  function openLightbox(imageSrc, title, date) {
    document.getElementById('lightboxImage').src = imageSrc;
    document.getElementById('lightboxTitle').textContent = title;
    document.getElementById('lightboxDate').textContent = date;
    document.getElementById('photoLightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox(event) {
    if (!event || event.target.id === 'photoLightbox' || event.currentTarget.classList.contains('lightbox-close')) {
      document.getElementById('photoLightbox').classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  // Fermer avec Escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeLightbox();
    }
  });

  // Données pour les graphiques
  const measurementsData = @json($measurements->values());
  
  // Configuration globale Chart.js
  Chart.defaults.color = '#94a3b8';
  Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.2)';
  Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';

  // Graphique Poids
  @if($measurements->where('weight', '!=', null)->count() >= 2)
    const weightCtx = document.getElementById('weightChart');
    if (weightCtx) {
      new Chart(weightCtx, {
        type: 'line',
        data: {
          labels: measurementsData.map(m => new Date(m.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })),
          datasets: [{
            label: 'Poids (kg)',
            data: measurementsData.map(m => m.weight),
            borderColor: 'rgb(99, 102, 241)',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: 'rgb(99, 102, 241)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'rgba(15, 23, 42, 0.95)',
              titleColor: '#f8fafc',
              bodyColor: '#cbd5e1',
              borderColor: 'rgba(148, 163, 184, 0.3)',
              borderWidth: 1,
              padding: 12,
              displayColors: false,
              callbacks: {
                label: function(context) {
                  return context.parsed.y + ' kg';
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: false,
              grid: {
                color: 'rgba(148, 163, 184, 0.1)'
              },
              ticks: {
                callback: function(value) {
                  return value + ' kg';
                }
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
    }
  @endif

  // Graphique Mensurations
  @if($measurements->filter(fn($m) => $m->chest || $m->waist || $m->hips)->count() >= 2)
    const measurementsCtx = document.getElementById('measurementsChart');
    if (measurementsCtx) {
      new Chart(measurementsCtx, {
        type: 'line',
        data: {
          labels: measurementsData.map(m => new Date(m.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })),
          datasets: [
            {
              label: 'Poitrine',
              data: measurementsData.map(m => m.chest),
              borderColor: 'rgb(16, 185, 129)',
              backgroundColor: 'rgba(16, 185, 129, 0.1)',
              borderWidth: 2,
              tension: 0.4,
              pointRadius: 4,
              pointHoverRadius: 6,
            },
            {
              label: 'Taille',
              data: measurementsData.map(m => m.waist),
              borderColor: 'rgb(251, 146, 60)',
              backgroundColor: 'rgba(251, 146, 60, 0.1)',
              borderWidth: 2,
              tension: 0.4,
              pointRadius: 4,
              pointHoverRadius: 6,
            },
            {
              label: 'Hanches',
              data: measurementsData.map(m => m.hips),
              borderColor: 'rgb(236, 72, 153)',
              backgroundColor: 'rgba(236, 72, 153, 0.1)',
              borderWidth: 2,
              tension: 0.4,
              pointRadius: 4,
              pointHoverRadius: 6,
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'top',
              labels: {
                boxWidth: 12,
                boxHeight: 12,
                padding: 15,
                usePointStyle: true,
                color: '#cbd5e1'
              }
            },
            tooltip: {
              backgroundColor: 'rgba(15, 23, 42, 0.95)',
              titleColor: '#f8fafc',
              bodyColor: '#cbd5e1',
              borderColor: 'rgba(148, 163, 184, 0.3)',
              borderWidth: 1,
              padding: 12,
              callbacks: {
                label: function(context) {
                  return context.dataset.label + ': ' + context.parsed.y + ' cm';
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: false,
              grid: {
                color: 'rgba(148, 163, 184, 0.1)'
              },
              ticks: {
                callback: function(value) {
                  return value + ' cm';
                }
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
    }
  @endif
</script>
@endpush

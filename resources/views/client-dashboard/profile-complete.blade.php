@extends('client-dashboard.layout')

@section('page-title', 'Ma Fiche Personnage')

@push('head')
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

  .profile-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }

  .form-card {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 1.25rem;
    padding: 2rem;
    transition: all 0.3s ease;
  }

  .form-card:hover {
    border-color: rgba(129, 140, 248, 0.4);
  }

  .card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.75rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
  }

  .card-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
    border: 1px solid rgba(129, 140, 248, 0.3);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .card-title-wrapper {
    flex: 1;
  }

  .card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #f8fafc;
    margin-bottom: 0.25rem;
  }

  .card-subtitle {
    font-size: 0.9rem;
    color: #94a3b8;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .form-group.full-width {
    grid-column: 1 / -1;
  }

  .form-label {
    font-size: 0.95rem;
    font-weight: 600;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .form-label-required::after {
    content: '*';
    color: #f87171;
    margin-left: 0.25rem;
  }

  .form-input,
  .form-select,
  .form-textarea {
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 0.75rem;
    padding: 0.85rem 1rem;
    color: #f8fafc;
    font-size: 0.95rem;
    transition: all 0.2s ease;
  }

  .form-input:focus,
  .form-select:focus,
  .form-textarea:focus {
    outline: none;
    border-color: rgba(129, 140, 248, 0.6);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }

  .form-input::placeholder,
  .form-textarea::placeholder {
    color: #64748b;
  }

  .form-textarea {
    min-height: 100px;
    resize: vertical;
    font-family: inherit;
  }

  .form-hint {
    font-size: 0.85rem;
    color: #94a3b8;
    font-style: italic;
  }

  .input-unit {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .input-unit .form-input {
    flex: 1;
  }

  .unit-label {
    font-size: 0.9rem;
    color: #94a3b8;
    font-weight: 500;
    min-width: 40px;
  }

  .checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 0.75rem;
  }

  .checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: rgba(15, 23, 42, 0.5);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .checkbox-item:hover {
    border-color: rgba(129, 140, 248, 0.4);
    background: rgba(99, 102, 241, 0.1);
  }

  .checkbox-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }

  .checkbox-item label {
    cursor: pointer;
    color: #cbd5e1;
    font-size: 0.95rem;
  }

  .switch-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(15, 23, 42, 0.5);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 0.75rem;
  }

  .switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
  }

  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(148, 163, 184, 0.3);
    transition: 0.3s;
    border-radius: 34px;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
  }

  input:checked + .slider {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
  }

  input:checked + .slider:before {
    transform: translateX(22px);
  }

  .switch-label {
    color: #cbd5e1;
    font-size: 0.95rem;
    font-weight: 500;
  }

  .photo-upload-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
  }

  .photo-upload-item {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .photo-preview {
    width: 100%;
    aspect-ratio: 3/4;
    background: rgba(15, 23, 42, 0.5);
    border: 2px dashed rgba(148, 163, 184, 0.3);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
  }

  .photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .photo-placeholder {
    text-align: center;
    color: #94a3b8;
    padding: 1rem;
  }

  .photo-placeholder svg {
    width: 48px;
    height: 48px;
    margin: 0 auto 0.5rem;
    opacity: 0.5;
  }

  .file-input-wrapper {
    position: relative;
  }

  .file-input-wrapper input[type="file"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
  }

  .file-input-label {
    display: block;
    padding: 0.75rem 1rem;
    background: rgba(99, 102, 241, 0.15);
    border: 1px solid rgba(99, 102, 241, 0.4);
    border-radius: 0.75rem;
    color: #a5b4fc;
    text-align: center;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
  }

  .file-input-label:hover {
    background: rgba(99, 102, 241, 0.25);
    border-color: rgba(129, 140, 248, 0.6);
  }

  .form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1rem;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.85rem 2rem;
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

  .alert {
    padding: 1rem 1.25rem;
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
  }

  .alert-success {
    background: rgba(16, 185, 129, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.35);
    color: #6ee7b7;
  }

  .alert-error {
    background: rgba(248, 113, 113, 0.15);
    border: 1px solid rgba(248, 113, 113, 0.35);
    color: #fca5a5;
  }

  @media (max-width: 768px) {
    .form-card {
      padding: 1.5rem;
    }

    .form-grid {
      grid-template-columns: 1fr;
    }

    .checkbox-group {
      grid-template-columns: 1fr;
    }

    .photo-upload-grid {
      grid-template-columns: 1fr;
    }

    .form-actions {
      flex-direction: column;
    }

    .btn {
      width: 100%;
      justify-content: center;
    }
  }
</style>
@endpush

@section('content')
<div class="section-header">
  <h2 class="section-title">Ma Fiche Personnage</h2>
  <p class="section-subtitle">Complétez votre profil pour un coaching 100% personnalisé</p>
</div>

@if(session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
@endif

@if($errors->any())
  <div class="alert alert-error">
    <ul style="margin: 0; padding-left: 1.25rem;">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('clients.dashboard.profile.update', $client->share_token) }}" class="profile-form" enctype="multipart/form-data">
  @csrf
  @method('PATCH')

  {{-- Bloc A : Suivi Physique --}}
  <div class="form-card">
    <div class="card-header">
      <div class="card-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
      </div>
      <div class="card-title-wrapper">
        <h3 class="card-title">Suivi Physique</h3>
        <p class="card-subtitle">Vos mesures seront historisées pour suivre votre progression</p>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label for="weight" class="form-label">Poids</label>
        <div class="input-unit">
          <input type="number" id="weight" name="weight" class="form-input" value="{{ old('weight', $latestMeasurement?->weight) }}" step="0.1" min="0" max="500" placeholder="75.5">
          <span class="unit-label">kg</span>
        </div>
      </div>

      <div class="form-group">
        <label for="height" class="form-label">Taille</label>
        <div class="input-unit">
          <input type="number" id="height" name="height" class="form-input" value="{{ old('height', $latestMeasurement?->height) }}" step="0.1" min="0" max="300" placeholder="175">
          <span class="unit-label">cm</span>
        </div>
      </div>

      <div class="form-group">
        <label for="chest" class="form-label">Tour de poitrine</label>
        <div class="input-unit">
          <input type="number" id="chest" name="chest" class="form-input" value="{{ old('chest', $latestMeasurement?->chest) }}" step="0.1" min="0" placeholder="95">
          <span class="unit-label">cm</span>
        </div>
      </div>

      <div class="form-group">
        <label for="waist" class="form-label">Tour de taille</label>
        <div class="input-unit">
          <input type="number" id="waist" name="waist" class="form-input" value="{{ old('waist', $latestMeasurement?->waist) }}" step="0.1" min="0" placeholder="80">
          <span class="unit-label">cm</span>
        </div>
      </div>

      <div class="form-group">
        <label for="hips" class="form-label">Tour de hanches</label>
        <div class="input-unit">
          <input type="number" id="hips" name="hips" class="form-input" value="{{ old('hips', $latestMeasurement?->hips) }}" step="0.1" min="0" placeholder="95">
          <span class="unit-label">cm</span>
        </div>
      </div>

      <div class="form-group">
        <label for="arm" class="form-label">Tour de bras</label>
        <div class="input-unit">
          <input type="number" id="arm" name="arm" class="form-input" value="{{ old('arm', $latestMeasurement?->arm) }}" step="0.1" min="0" placeholder="35">
          <span class="unit-label">cm</span>
        </div>
      </div>

      <div class="form-group">
        <label for="thigh" class="form-label">Tour de cuisse</label>
        <div class="input-unit">
          <input type="number" id="thigh" name="thigh" class="form-input" value="{{ old('thigh', $latestMeasurement?->thigh) }}" step="0.1" min="0" placeholder="55">
          <span class="unit-label">cm</span>
        </div>
      </div>
    </div>

    <div class="form-group full-width" style="margin-top: 1.5rem;">
      <label class="form-label">Photos d'évolution (optionnel)</label>
      <span class="form-hint">Ces photos sont privées et ne sont visibles que par votre coach</span>
      
      @if($latestMeasurement && ($latestMeasurement->photo_front || $latestMeasurement->photo_side || $latestMeasurement->photo_back))
        <div style="margin: 1rem 0; padding: 1rem; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 0.75rem;">
          <div style="font-size: 0.85rem; color: #a5b4fc; margin-bottom: 0.75rem; font-weight: 500;">
            📸 Vos dernières photos ({{ $latestMeasurement->created_at->translatedFormat('d F Y') }})
          </div>
          <div class="photo-upload-grid">
            @if($latestMeasurement->photo_front)
              <div class="photo-upload-item">
                <div class="photo-preview">
                  <img src="{{ route('clients.dashboard.photo', [$client->share_token, $latestMeasurement->id, 'front']) }}" alt="Face">
                </div>
                <div style="text-align: center; font-size: 0.85rem; color: #94a3b8; margin-top: 0.5rem;">Vue de Face</div>
              </div>
            @endif
            @if($latestMeasurement->photo_side)
              <div class="photo-upload-item">
                <div class="photo-preview">
                  <img src="{{ route('clients.dashboard.photo', [$client->share_token, $latestMeasurement->id, 'side']) }}" alt="Profil">
                </div>
                <div style="text-align: center; font-size: 0.85rem; color: #94a3b8; margin-top: 0.5rem;">Vue de Profil</div>
              </div>
            @endif
            @if($latestMeasurement->photo_back)
              <div class="photo-upload-item">
                <div class="photo-preview">
                  <img src="{{ route('clients.dashboard.photo', [$client->share_token, $latestMeasurement->id, 'back']) }}" alt="Dos">
                </div>
                <div style="text-align: center; font-size: 0.85rem; color: #94a3b8; margin-top: 0.5rem;">Vue de Dos</div>
              </div>
            @endif
          </div>
        </div>
      @endif

      <div class="photo-upload-grid" style="margin-top: 1rem;">
        <div class="photo-upload-item">
          <div class="photo-preview" id="preview-front">
            <div class="photo-placeholder">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <div>Vue de Face</div>
            </div>
          </div>
          <div class="file-input-wrapper">
            <input type="file" name="photo_front" accept="image/*" onchange="previewImage(this, 'preview-front')">
            <label class="file-input-label">Choisir une photo</label>
          </div>
        </div>

        <div class="photo-upload-item">
          <div class="photo-preview" id="preview-side">
            <div class="photo-placeholder">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <div>Vue de Profil</div>
            </div>
          </div>
          <div class="file-input-wrapper">
            <input type="file" name="photo_side" accept="image/*" onchange="previewImage(this, 'preview-side')">
            <label class="file-input-label">Choisir une photo</label>
          </div>
        </div>

        <div class="photo-upload-item">
          <div class="photo-preview" id="preview-back">
            <div class="photo-placeholder">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <div>Vue de Dos</div>
            </div>
          </div>
          <div class="file-input-wrapper">
            <input type="file" name="photo_back" accept="image/*" onchange="previewImage(this, 'preview-back')">
            <label class="file-input-label">Choisir une photo</label>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Bloc B : Santé & Physiologie --}}
  <div class="form-card">
    <div class="card-header">
      <div class="card-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
      </div>
      <div class="card-title-wrapper">
        <h3 class="card-title">Santé & Physiologie</h3>
        <p class="card-subtitle">Informations médicales et bien-être général</p>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group full-width">
        <label for="injuries" class="form-label">Blessures / Douleurs</label>
        <textarea id="injuries" name="injuries" class="form-textarea" placeholder="Décrivez vos blessures actuelles, douleurs chroniques, historique médical pertinent...">{{ old('injuries', $client->injuries) }}</textarea>
        <span class="form-hint">Important pour adapter votre programme et éviter les blessures</span>
      </div>

      <div class="form-group">
        <label for="stress_level" class="form-label">Niveau de Stress</label>
        <select id="stress_level" name="stress_level" class="form-select">
          <option value="">Sélectionnez...</option>
          @for($i = 1; $i <= 10; $i++)
            <option value="{{ $i }}" {{ old('stress_level', $client->stress_level) == $i ? 'selected' : '' }}>
              {{ $i }} {{ $i <= 3 ? '(Faible)' : ($i <= 7 ? '(Modéré)' : '(Élevé)') }}
            </option>
          @endfor
        </select>
      </div>

      <div class="form-group">
        <label for="sleep_quality" class="form-label">Qualité du Sommeil</label>
        <select id="sleep_quality" name="sleep_quality" class="form-select">
          <option value="">Sélectionnez...</option>
          <option value="bad" {{ old('sleep_quality', $client->sleep_quality) == 'bad' ? 'selected' : '' }}>Mauvais</option>
          <option value="average" {{ old('sleep_quality', $client->sleep_quality) == 'average' ? 'selected' : '' }}>Moyen</option>
          <option value="good" {{ old('sleep_quality', $client->sleep_quality) == 'good' ? 'selected' : '' }}>Bon</option>
          <option value="excellent" {{ old('sleep_quality', $client->sleep_quality) == 'excellent' ? 'selected' : '' }}>Excellent</option>
        </select>
      </div>

      <div class="form-group full-width">
        <div class="switch-wrapper">
          <label class="switch">
            <input type="checkbox" id="menstrual_tracking" name="menstrual_tracking" value="1" {{ old('menstrual_tracking', $client->menstrual_tracking) ? 'checked' : '' }} onchange="toggleMenstrualFields()">
            <span class="slider"></span>
          </label>
          <span class="switch-label">Suivre mon cycle menstruel</span>
        </div>
      </div>

      <div class="form-group" id="last_period_field" style="display: {{ old('menstrual_tracking', $client->menstrual_tracking) ? 'block' : 'none' }};">
        <label for="last_period" class="form-label">Date des dernières règles</label>
        <input type="date" id="last_period" name="last_period" class="form-input" value="{{ old('last_period', $client->last_period?->format('Y-m-d')) }}">
      </div>
    </div>
  </div>

  {{-- Bloc C : Nutrition & Cuisine --}}
  <div class="form-card">
    <div class="card-header">
      <div class="card-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
      </div>
      <div class="card-title-wrapper">
        <h3 class="card-title">Nutrition & Cuisine</h3>
        <p class="card-subtitle">Vos habitudes et contraintes alimentaires</p>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group full-width">
        <label for="allergies" class="form-label">Allergies / Intolérances</label>
        <textarea id="allergies" name="allergies" class="form-textarea" placeholder="Ex: lactose, gluten, fruits à coque, crustacés...">{{ old('allergies', $client->allergies) }}</textarea>
        <span class="form-hint">⚠️ Crucial pour votre sécurité et votre plan nutritionnel</span>
      </div>

      <div class="form-group full-width">
        <label for="dislikes" class="form-label">Aliments que vous n'aimez pas</label>
        <textarea id="dislikes" name="dislikes" class="form-textarea" placeholder="Ex: épinards, poisson, champignons...">{{ old('dislikes', $client->dislikes) }}</textarea>
        <span class="form-hint">Pour personnaliser votre plan sans frustration</span>
      </div>

      <div class="form-group">
        <label for="grocery_budget" class="form-label">Budget Courses</label>
        <select id="grocery_budget" name="grocery_budget" class="form-select">
          <option value="">Sélectionnez...</option>
          <option value="eco" {{ old('grocery_budget', $client->grocery_budget) == 'eco' ? 'selected' : '' }}>Économique</option>
          <option value="standard" {{ old('grocery_budget', $client->grocery_budget) == 'standard' ? 'selected' : '' }}>Standard</option>
          <option value="premium" {{ old('grocery_budget', $client->grocery_budget) == 'premium' ? 'selected' : '' }}>Bio / Premium</option>
        </select>
      </div>

      <div class="form-group full-width">
        <label class="form-label">Équipement Cuisine disponible</label>
        <div class="checkbox-group">
          @php
            $kitchenItems = ['microwave' => 'Micro-ondes', 'oven' => 'Four', 'stove' => 'Plaques', 'blender' => 'Mixeur/Blender', 'airfryer' => 'Airfryer'];
            $selectedKitchen = old('kitchen_equipment', $client->kitchen_equipment ?? []);
          @endphp
          @foreach($kitchenItems as $key => $label)
            <div class="checkbox-item">
              <input type="checkbox" id="kitchen_{{ $key }}" name="kitchen_equipment[]" value="{{ $key }}" {{ in_array($key, $selectedKitchen) ? 'checked' : '' }}>
              <label for="kitchen_{{ $key }}">{{ $label }}</label>
            </div>
          @endforeach
        </div>
      </div>

      <div class="form-group full-width">
        <label for="supplements" class="form-label">Compléments Alimentaires</label>
        <input type="text" id="supplements" name="supplements" class="form-input" value="{{ old('supplements', $client->supplements) }}" placeholder="Ex: Whey, Créatine, Multivitamines...">
        <span class="form-hint">Ce que vous prenez actuellement</span>
      </div>
    </div>
  </div>

  {{-- Bloc D : Contexte Sportif & Logistique --}}
  <div class="form-card">
    <div class="card-header">
      <div class="card-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
      </div>
      <div class="card-title-wrapper">
        <h3 class="card-title">Contexte Sportif & Logistique</h3>
        <p class="card-subtitle">Vos moyens et disponibilités pour l'entraînement</p>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group full-width">
        <label class="form-label">Matériel disponible</label>
        <div class="checkbox-group">
          @php
            $equipmentItems = [
              'bodyweight' => 'Poids du corps',
              'resistance_bands' => 'Élastiques',
              'dumbbells' => 'Haltères',
              'bench' => 'Banc',
              'barbell' => 'Barre',
              'full_gym' => 'Salle de sport complète'
            ];
            $selectedEquipment = old('available_equipment', $client->available_equipment ?? []);
          @endphp
          @foreach($equipmentItems as $key => $label)
            <div class="checkbox-item">
              <input type="checkbox" id="equip_{{ $key }}" name="available_equipment[]" value="{{ $key }}" {{ in_array($key, $selectedEquipment) ? 'checked' : '' }}>
              <label for="equip_{{ $key }}">{{ $label }}</label>
            </div>
          @endforeach
        </div>
      </div>

      <div class="form-group">
        <label for="training_frequency" class="form-label">Fréquence d'entraînement</label>
        <select id="training_frequency" name="training_frequency" class="form-select">
          <option value="">Sélectionnez...</option>
          <option value="2x" {{ old('training_frequency', $client->training_frequency) == '2x' ? 'selected' : '' }}>2x / semaine</option>
          <option value="3x" {{ old('training_frequency', $client->training_frequency) == '3x' ? 'selected' : '' }}>3x / semaine</option>
          <option value="4x" {{ old('training_frequency', $client->training_frequency) == '4x' ? 'selected' : '' }}>4x / semaine</option>
          <option value="5x+" {{ old('training_frequency', $client->training_frequency) == '5x+' ? 'selected' : '' }}>5x+ / semaine</option>
        </select>
      </div>

      <div class="form-group">
        <label for="session_duration" class="form-label">Durée par séance</label>
        <select id="session_duration" name="session_duration" class="form-select">
          <option value="">Sélectionnez...</option>
          <option value="30min" {{ old('session_duration', $client->session_duration) == '30min' ? 'selected' : '' }}>30 minutes</option>
          <option value="45min" {{ old('session_duration', $client->session_duration) == '45min' ? 'selected' : '' }}>45 minutes</option>
          <option value="1h" {{ old('session_duration', $client->session_duration) == '1h' ? 'selected' : '' }}>1 heure</option>
          <option value="1h30" {{ old('session_duration', $client->session_duration) == '1h30' ? 'selected' : '' }}>1h30</option>
        </select>
      </div>

      <div class="form-group">
        <label for="daily_activity" class="form-label">Activité hors sport</label>
        <select id="daily_activity" name="daily_activity" class="form-select">
          <option value="">Sélectionnez...</option>
          <option value="sedentary" {{ old('daily_activity', $client->daily_activity) == 'sedentary' ? 'selected' : '' }}>Sédentaire (bureau)</option>
          <option value="active" {{ old('daily_activity', $client->daily_activity) == 'active' ? 'selected' : '' }}>Actif (marche régulière)</option>
          <option value="very_active" {{ old('daily_activity', $client->daily_activity) == 'very_active' ? 'selected' : '' }}>Très physique (métier actif)</option>
        </select>
      </div>
    </div>
  </div>

  {{-- Bloc E : Psychologie & Profiling --}}
  <div class="form-card">
    <div class="card-header">
      <div class="card-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
      </div>
      <div class="card-title-wrapper">
        <h3 class="card-title">Psychologie & Profiling</h3>
        <p class="card-subtitle">Vos objectifs et votre état d'esprit</p>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group full-width">
        <label for="main_goal" class="form-label">Objectif Principal</label>
        <textarea id="main_goal" name="main_goal" class="form-textarea" placeholder="Ex: Perdre 10kg, Prise de masse, Performance sportive, Remise en forme...">{{ old('main_goal', $client->main_goal) }}</textarea>
        <span class="form-hint">Soyez précis et mesurable</span>
      </div>

      <div class="form-group full-width">
        <label for="deep_motivation" class="form-label">Le "Pourquoi" (Motivation profonde)</label>
        <textarea id="deep_motivation" name="deep_motivation" class="form-textarea" placeholder="Qu'est-ce qui vous pousse vraiment ? Pourquoi est-ce important pour vous ?">{{ old('deep_motivation', $client->deep_motivation) }}</textarea>
        <span class="form-hint">La vraie raison derrière votre objectif - essentiel pour rester motivé</span>
      </div>

      <div class="form-group full-width">
        <label for="general_comments" class="form-label">Commentaires Généraux</label>
        <textarea id="general_comments" name="general_comments" class="form-textarea" placeholder="Tout autre information que votre coach devrait connaître...">{{ old('general_comments', $client->general_comments) }}</textarea>
      </div>

      <div class="form-group">
        <label for="coaching_style" class="form-label">Style de Coaching préféré</label>
        <select id="coaching_style" name="coaching_style" class="form-select">
          <option value="">Sélectionnez...</option>
          <option value="strict" {{ old('coaching_style', $client->coaching_style) == 'strict' ? 'selected' : '' }}>Militaire / Strict</option>
          <option value="supportive" {{ old('coaching_style', $client->coaching_style) == 'supportive' ? 'selected' : '' }}>Pédagogue / Bienveillant</option>
          <option value="autonomous" {{ old('coaching_style', $client->coaching_style) == 'autonomous' ? 'selected' : '' }}>Autonome</option>
        </select>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      Enregistrer ma fiche personnage
    </button>
  </div>
</form>
@endsection

@push('scripts')
<script>
function previewImage(input, previewId) {
  const preview = document.getElementById(previewId);
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
    }
    reader.readAsDataURL(input.files[0]);
  }
}

function toggleMenstrualFields() {
  const checkbox = document.getElementById('menstrual_tracking');
  const dateField = document.getElementById('last_period_field');
  if (checkbox.checked) {
    dateField.style.display = 'block';
  } else {
    dateField.style.display = 'none';
  }
}
</script>
@endpush

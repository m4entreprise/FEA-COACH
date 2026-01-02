@extends('client-dashboard.layout')

@section('page-title', 'Mon profil')

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

  .profile-form {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 1.25rem;
    padding: 2rem;
  }

  .form-section {
    margin-bottom: 2.5rem;
  }

  .form-section:last-child {
    margin-bottom: 0;
  }

  .form-section-title {
    font-size: 1.15rem;
    font-weight: 600;
    color: #f8fafc;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
  }

  .form-section-icon {
    width: 24px;
    height: 24px;
    color: #a5b4fc;
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

  .form-label-icon {
    width: 16px;
    height: 16px;
    color: #94a3b8;
  }

  .form-input,
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
    min-height: 120px;
    resize: vertical;
    font-family: inherit;
  }

  .form-hint {
    font-size: 0.85rem;
    color: #94a3b8;
    font-style: italic;
  }

  .form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(148, 163, 184, 0.2);
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

  @media (max-width: 768px) {
    .profile-form {
      padding: 1.5rem;
    }

    .form-grid {
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

<div class="section-header">
  <h2 class="section-title">Mon profil</h2>
  <p class="section-subtitle">Complétez votre fiche personnelle pour un suivi optimal</p>
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

<form method="POST" action="{{ route('clients.dashboard.profile.update', $client->share_token) }}" class="profile-form">
  @csrf
  @method('PATCH')

  <div class="form-section">
    <h3 class="form-section-title">
      <svg class="form-section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
      </svg>
      Informations personnelles
    </h3>
    
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">
          <svg class="form-label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          Prénom
        </label>
        <input type="text" class="form-input" value="{{ $client->first_name }}" readonly>
      </div>

      <div class="form-group">
        <label class="form-label">
          <svg class="form-label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          Nom
        </label>
        <input type="text" class="form-input" value="{{ $client->last_name }}" readonly>
      </div>

      <div class="form-group">
        <label class="form-label">
          <svg class="form-label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          Email
        </label>
        <input type="email" class="form-input" value="{{ $client->email }}" readonly>
      </div>
    </div>
  </div>

  <div class="form-section">
    <h3 class="form-section-title">
      <svg class="form-section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
      Mesures physiques
    </h3>
    
    <div class="form-grid">
      <div class="form-group">
        <label for="weight" class="form-label">
          <svg class="form-label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
          </svg>
          Poids
        </label>
        <div class="input-unit">
          <input 
            type="number" 
            id="weight" 
            name="weight" 
            class="form-input" 
            value="{{ old('weight', $client->weight) }}" 
            step="0.1" 
            min="0" 
            max="500"
            placeholder="Ex: 75.5"
          >
          <span class="unit-label">kg</span>
        </div>
      </div>

      <div class="form-group">
        <label for="height" class="form-label">
          <svg class="form-label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
          </svg>
          Taille
        </label>
        <div class="input-unit">
          <input 
            type="number" 
            id="height" 
            name="height" 
            class="form-input" 
            value="{{ old('height', $client->height) }}" 
            step="0.1" 
            min="0" 
            max="300"
            placeholder="Ex: 175"
          >
          <span class="unit-label">cm</span>
        </div>
      </div>
    </div>
  </div>

  <div class="form-section">
    <h3 class="form-section-title">
      <svg class="form-section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
      Préférences alimentaires
    </h3>
    
    <div class="form-grid">
      <div class="form-group full-width">
        <label for="allergies" class="form-label">
          <svg class="form-label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          Allergies alimentaires
        </label>
        <textarea 
          id="allergies" 
          name="allergies" 
          class="form-textarea"
          placeholder="Listez vos allergies (ex: lactose, gluten, fruits à coque...)"
        >{{ old('allergies', $client->allergies) }}</textarea>
        <span class="form-hint">Important pour la création de votre programme alimentaire</span>
      </div>

      <div class="form-group full-width">
        <label for="dislikes" class="form-label">
          <svg class="form-label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
          </svg>
          Aliments que vous n'aimez pas
        </label>
        <textarea 
          id="dislikes" 
          name="dislikes" 
          class="form-textarea"
          placeholder="Listez les aliments que vous n'appréciez pas (ex: épinards, poisson...)"
        >{{ old('dislikes', $client->dislikes) }}</textarea>
        <span class="form-hint">Pour personnaliser au mieux votre plan nutritionnel</span>
      </div>
    </div>
  </div>

  <div class="form-section">
    <h3 class="form-section-title">
      <svg class="form-section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
      </svg>
      Informations complémentaires
    </h3>
    
    <div class="form-grid">
      <div class="form-group full-width">
        <label for="general_comments" class="form-label">
          <svg class="form-label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          Commentaires généraux
        </label>
        <textarea 
          id="general_comments" 
          name="general_comments" 
          class="form-textarea"
          placeholder="Informations complémentaires pour votre coach (objectifs, contraintes, disponibilités...)"
        >{{ old('general_comments', $client->general_comments) }}</textarea>
        <span class="form-hint">Partagez toute information utile à votre suivi</span>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      Enregistrer mon profil
    </button>
  </div>
</form>
@endsection

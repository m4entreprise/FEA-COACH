@extends('share.layout')

@section('content')
  <div class="card">
    <div class="page-header">
      <div class="page-header-main">
        <div class="secure-pill">
          <span class="secure-dot"></span>
          <span>Espace sécurisé</span>
        </div>
        <h1 class="title">Accéder aux documents de {{ $client->first_name }}</h1>
        <p class="subtitle">
          Merci de saisir le code à 6 chiffres fourni par votre coach pour consulter les documents.
        </p>
      </div>
      <div class="code-chip">
        {{ substr($client->share_code, 0, 2) }}
      </div>
    </div>

    <form method="POST" action="{{ route('clients.share.unlock', $token) }}">
      @csrf
      <input
        type="text"
        name="share_code"
        maxlength="6"
        pattern="[0-9]{6}"
        inputmode="numeric"
        placeholder="Code à 6 chiffres"
        class="input"
        required
      />

      <div class="form-actions">
        <p class="form-hint">
          Le code est strictement personnel et permet d'accéder à l'ensemble de vos documents.
        </p>
        <input type="submit" value="Déverrouiller" />
      </div>
    </form>

    @if ($errors->any())
      <div class="alert">
        {{ $errors->first('share_code') }}
      </div>
    @endif

    @if (session('status'))
      <div class="alert success">
        {{ session('status') }}
      </div>
    @endif
  </div>
@endsection

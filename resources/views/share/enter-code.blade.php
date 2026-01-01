@extends('share.layout')

@section('content')
  <div class="card">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
      <div>
        <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Espace sécurisé</p>
        <h1 class="title">Accéder aux documents de {{ $client->first_name }}</h1>
        <p class="subtitle">
          Merci de saisir le code à 6 chiffres fourni par votre coach pour consulter les documents.
        </p>
      </div>
      <div style="width:60px; height:60px; border-radius:1rem; background:linear-gradient(135deg,#38bdf8,#6366f1); display:flex; align-items:center; justify-content:center; font-weight:600; font-size:1.4rem; box-shadow:0 12px 30px rgba(56,189,248,0.4);">
        {{ substr($client->share_code, 0, 2) }}
      </div>
    </div>

    <form method="POST" action="{{ route('clients.share.unlock', $token) }}" class="space-y-4">
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

      <input type="submit" value="Déverrouiller" />
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

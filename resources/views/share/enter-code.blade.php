@extends('share.layout')

@section('content')
  <div class="card">
    <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Espace sécurisé</p>
    <h1 class="title">Accéder aux documents de {{ $client->first_name }}</h1>
    <p class="text-slate-300 mb-6">
      Merci de saisir le code à 6 chiffres fourni par votre coach pour consulter les documents.
    </p>

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
      <div class="alert" style="background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.5); color: #bbf7d0;">
        {{ session('status') }}
      </div>
    @endif
  </div>
@endsection

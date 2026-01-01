@extends('client-dashboard.layout')

@section('page-title', 'Messagerie')

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

  .messages-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 280px);
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 1.25rem;
    overflow: hidden;
  }

  .messages-list {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .message-item {
    display: flex;
    gap: 1rem;
    animation: messageSlideIn 0.3s ease;
  }

  @keyframes messageSlideIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .message-item.from-client {
    flex-direction: row-reverse;
  }

  .message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f8fafc;
    font-weight: 600;
    font-size: 0.9rem;
    flex-shrink: 0;
  }

  .message-item.from-coach .message-avatar {
    background: linear-gradient(135deg, #f97316, #fb923c);
  }

  .message-content {
    max-width: 70%;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .message-bubble {
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 1rem;
    padding: 1rem 1.25rem;
    color: #f8fafc;
    line-height: 1.6;
    word-wrap: break-word;
  }

  .message-item.from-client .message-bubble {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.3), rgba(139, 92, 246, 0.3));
    border-color: rgba(129, 140, 248, 0.4);
  }

  .message-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #94a3b8;
    padding: 0 0.5rem;
  }

  .message-item.from-client .message-meta {
    justify-content: flex-end;
  }

  .message-attachment {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: rgba(15, 23, 42, 0.5);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 0.75rem;
    margin-top: 0.5rem;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
  }

  .message-attachment:hover {
    background: rgba(99, 102, 241, 0.2);
    border-color: rgba(129, 140, 248, 0.4);
  }

  .attachment-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .attachment-info {
    flex: 1;
    min-width: 0;
  }

  .attachment-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: #cbd5e1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .attachment-size {
    font-size: 0.75rem;
    color: #94a3b8;
  }

  .message-form {
    padding: 1.5rem;
    border-top: 1px solid rgba(148, 163, 184, 0.2);
    background: rgba(15, 23, 42, 0.4);
  }

  .form-wrapper {
    display: flex;
    gap: 0.75rem;
    align-items: flex-end;
  }

  .input-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .message-input {
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 1rem;
    padding: 0.85rem 1rem;
    color: #f8fafc;
    font-size: 0.95rem;
    resize: none;
    min-height: 50px;
    max-height: 150px;
    transition: all 0.2s ease;
    font-family: inherit;
  }

  .message-input:focus {
    outline: none;
    border-color: rgba(129, 140, 248, 0.6);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }

  .message-input::placeholder {
    color: #64748b;
  }

  .file-input-container {
    position: relative;
  }

  .file-input-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(148, 163, 184, 0.15);
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 0.75rem;
    color: #cbd5e1;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .file-input-label:hover {
    background: rgba(99, 102, 241, 0.2);
    border-color: rgba(129, 140, 248, 0.5);
  }

  .file-input-label input {
    display: none;
  }

  .file-preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: rgba(99, 102, 241, 0.15);
    border: 1px solid rgba(99, 102, 241, 0.4);
    border-radius: 0.75rem;
    font-size: 0.85rem;
    color: #a5b4fc;
  }

  .file-preview-remove {
    background: none;
    border: none;
    color: #f87171;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
  }

  .send-button {
    padding: 0.85rem 1.5rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: none;
    border-radius: 999px;
    color: #f8fafc;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .send-button:hover:not(:disabled) {
    box-shadow: 0 12px 28px rgba(99, 102, 241, 0.5);
    transform: translateY(-1px);
  }

  .send-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #94a3b8;
    text-align: center;
    padding: 2rem;
  }

  .empty-state-icon {
    width: 64px;
    height: 64px;
    margin-bottom: 1rem;
    opacity: 0.5;
  }

  .empty-state-text {
    font-size: 1rem;
    margin-bottom: 0.5rem;
  }

  .empty-state-subtext {
    font-size: 0.85rem;
    color: #64748b;
  }

  @media (max-width: 768px) {
    .messages-container {
      height: calc(100vh - 220px);
    }

    .message-content {
      max-width: 85%;
    }

    .form-wrapper {
      flex-direction: column;
      align-items: stretch;
    }

    .send-button {
      width: 100%;
      justify-content: center;
    }
  }
</style>
@endpush

@section('content')
<div class="section-header">
  <h2 class="section-title">Messagerie</h2>
  <p class="section-subtitle">Échangez avec votre coach {{ $client->coach->business_name ?? '' }}</p>
</div>

<div class="messages-container">
  <div class="messages-list" id="messagesList">
    @forelse($messages as $message)
      <div class="message-item from-{{ $message->sender_type }}">
        <div class="message-avatar">
          @if($message->sender_type === 'coach')
            {{ substr($client->coach->business_name ?? 'C', 0, 1) }}
          @else
            {{ substr($client->first_name, 0, 1) }}
          @endif
        </div>
        <div class="message-content">
          <div class="message-bubble">
            {{ $message->message }}
            
            @if($message->hasAttachment())
              <a href="{{ route('clients.dashboard.message.download', [$client->share_token, $message->id]) }}" class="message-attachment">
                <div class="attachment-icon">
                  <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                  </svg>
                </div>
                <div class="attachment-info">
                  <div class="attachment-name">{{ $message->attachment_name }}</div>
                  <div class="attachment-size">{{ $message->formatted_attachment_size }}</div>
                </div>
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
              </a>
            @endif
          </div>
          <div class="message-meta">
            <span>{{ $message->created_at->translatedFormat('d M Y à H\hi') }}</span>
            @if($message->sender_type === 'client' && $message->is_read)
              <span>· Lu</span>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="empty-state">
        <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <div class="empty-state-text">Aucun message pour le moment</div>
        <div class="empty-state-subtext">Commencez la conversation avec votre coach</div>
      </div>
    @endforelse
  </div>

  <form action="{{ route('clients.dashboard.messages.send', $client->share_token) }}" method="POST" enctype="multipart/form-data" class="message-form" id="messageForm">
    @csrf
    <div class="form-wrapper">
      <div class="input-wrapper">
        <textarea 
          name="message" 
          class="message-input" 
          placeholder="Écrivez votre message..."
          required
          rows="2"
          id="messageInput"
        ></textarea>
        
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <label class="file-input-label">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
            </svg>
            Joindre un fichier
            <input type="file" name="attachment" id="attachmentInput" onchange="previewFile(this)">
          </label>
          
          <div id="filePreview" style="display: none;" class="file-preview">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span id="fileName"></span>
            <button type="button" class="file-preview-remove" onclick="removeFile()">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
      
      <button type="submit" class="send-button">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
        </svg>
        Envoyer
      </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  // Auto-scroll vers le bas au chargement
  const messagesList = document.getElementById('messagesList');
  if (messagesList && messagesList.children.length > 0) {
    messagesList.scrollTop = messagesList.scrollHeight;
  }

  // Preview du fichier sélectionné
  function previewFile(input) {
    const preview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    
    if (input.files && input.files[0]) {
      fileName.textContent = input.files[0].name;
      preview.style.display = 'flex';
    } else {
      preview.style.display = 'none';
    }
  }

  // Retirer le fichier
  function removeFile() {
    const input = document.getElementById('attachmentInput');
    input.value = '';
    document.getElementById('filePreview').style.display = 'none';
  }

  // Auto-resize du textarea
  const messageInput = document.getElementById('messageInput');
  messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 150) + 'px';
  });

  // Rafraîchir les messages toutes les 10 secondes (polling simple)
  setInterval(function() {
    // Optionnel : implémenter un refresh via AJAX
  }, 10000);
</script>
@endpush

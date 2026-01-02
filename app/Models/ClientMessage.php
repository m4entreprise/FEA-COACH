<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'sender_type',
        'message',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'attachment_size' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Vérifier si le message a une pièce jointe
     */
    public function hasAttachment(): bool
    {
        return !is_null($this->attachment_path);
    }

    /**
     * Obtenir la taille formatée de la pièce jointe
     */
    public function getFormattedAttachmentSizeAttribute(): ?string
    {
        if (!$this->attachment_size) {
            return null;
        }

        $size = $this->attachment_size;
        
        if ($size < 1024) {
            return $size . ' B';
        } elseif ($size < 1048576) {
            return round($size / 1024, 1) . ' KB';
        } else {
            return round($size / 1048576, 1) . ' MB';
        }
    }

    /**
     * Marquer comme lu
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Scope pour les messages non lus
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope pour les messages d'un certain expéditeur
     */
    public function scopeFromSender($query, string $senderType)
    {
        return $query->where('sender_type', $senderType);
    }
}

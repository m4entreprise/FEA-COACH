<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ClientDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'category',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Relation avec le client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * URL de téléchargement du document
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('dashboard.clients.documents.download', ['client' => $this->client_id, 'document' => $this->id]);
    }

    /**
     * Taille formatée en Ko ou Mo
     */
    public function getFormattedSizeAttribute(): string
    {
        if ($this->file_size < 1024) {
            return $this->file_size . ' octets';
        } elseif ($this->file_size < 1048576) {
            return round($this->file_size / 1024, 2) . ' Ko';
        } else {
            return round($this->file_size / 1048576, 2) . ' Mo';
        }
    }

    /**
     * Supprimer le fichier du stockage lors de la suppression du document
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
        });
    }
}

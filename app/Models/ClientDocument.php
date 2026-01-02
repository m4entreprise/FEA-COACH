<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type',
        'version',
        'title',
        'description',
        'filename',
        'mime_type',
        'filesize',
        'storage_path',
        'file_uuid',
        'available_at',
    ];

    protected $casts = [
        'version' => 'integer',
        'filesize' => 'integer',
        'available_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ClientDocumentLog::class);
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

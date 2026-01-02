<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDocumentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_document_id',
        'action',
        'actor',
        'ip',
        'user_agent',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(ClientDocument::class, 'client_document_id');
    }
}

<?php

namespace App\Models\Certificate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected static string $factory = \Database\Factories\Certificate\CertificateFactory::class;

    protected $fillable = [
        'user_id',
        'level_id',
        'certificate_number',
        'file_path',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(\App\Models\System\Level::class);
    }
}

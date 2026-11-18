<?php

namespace App\Models\Certificate;

use App\Models\System\Level;
use App\Models\User;
use Database\Factories\Certificate\CertificateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected static string $factory = CertificateFactory::class;

    protected $fillable = [
        'user_id',
        'level_id',
        'certificate_uuid',
        'certificate_number',
        'file_path',
        'issued_at',
        'metadata',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        // uuid общий для обоих путей выдачи (ручного из админки и
        // автоматического), чтобы публичная страница проверки работала
        // для любого сертификата независимо от того, кто его создал.
        static::creating(function (Certificate $certificate) {
            $certificate->certificate_uuid ??= (string) Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}

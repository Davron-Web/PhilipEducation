<?php

namespace App\Http\Controllers\Public\Gamification;

use App\Http\Controllers\Controller;
use App\Models\Certificate\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CertificateDownloadController extends Controller
{
    /**
     * Тот же макет, что и превью на /achievements (certificates/pdf.blade.php,
     * $isPdf=true переключает шрифт на DejaVu Sans — см. комментарий в
     * шаблоне). Имя — то же правило, что и на превью: латиница из профиля,
     * если есть, иначе обычное name как есть.
     */
    public function __invoke(Certificate $certificate): Response
    {
        // Сертификат — не публичный документ по прямой ссылке: только
        // владелец, id в URL не проверяется отдельно нигде выше.
        abort_unless($certificate->user_id === Auth::id(), 403);

        $certificate->loadMissing('level');
        $user = Auth::user();

        $pdf = Pdf::loadView('certificates.pdf', [
            'levelCode' => $certificate->level->code,
            'levelName' => $certificate->level->name,
            'recipientName' => filled($user->name_latin) ? $user->name_latin : $user->name,
            'certificateNumber' => $certificate->certificate_number,
            'issuedAtLabel' => $certificate->issued_at->translatedFormat('d M Y'),
            'isPdf' => true,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('certificate-'.$certificate->certificate_number.'.pdf');
    }
}

<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Console\Command;

/**
 * Автовыдача (CertificateService::checkAndIssue) срабатывает только на
 * свежие события — урок/тест/слово, — а не задним числом. Эта команда
 * догоняет пользователей, которые выполнили условия уровня ДО того, как
 * появилась автовыдача. Безопасно перезапускать: checkAndIssue сам
 * пропускает уровни, которые уже выданы.
 */
class BackfillCertificates extends Command
{
    protected $signature = 'certificates:backfill {--dry-run : Показать, кому что будет выдано, без записи в БД}';

    protected $description = 'Выдаёт сертификаты всем пользователям, уже выполнившим условия уровня';

    public function handle(CertificateService $certificates): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $issuedTotal = 0;
        $usersAffected = 0;

        if ($dryRun) {
            $this->info('Режим dry-run: в базу ничего не пишем.');
        }

        User::query()->orderBy('id')->chunkById(200, function ($users) use ($certificates, $dryRun, &$issuedTotal, &$usersAffected) {
            foreach ($users as $user) {
                if ($dryRun) {
                    $pending = collect($certificates->progressFor($user))
                        ->filter(fn (array $level) => $level['is_available'] && $level['is_complete'] && ! $level['is_earned']);

                    foreach ($pending as $level) {
                        $this->line("#{$user->id} {$user->email} -> {$level['code']}");
                        $issuedTotal++;
                    }

                    if ($pending->isNotEmpty()) {
                        $usersAffected++;
                    }

                    continue;
                }

                $issued = $certificates->checkAndIssue($user);

                foreach ($issued as $certificate) {
                    $this->line("#{$user->id} {$user->email} -> {$certificate->certificate_number}");
                    $issuedTotal++;
                }

                if ($issued !== []) {
                    $usersAffected++;
                }
            }
        });

        $this->newLine();
        $this->info(($dryRun ? '[dry-run] ' : '')."Готово: {$issuedTotal} сертификатов, {$usersAffected} пользователей.");

        return self::SUCCESS;
    }
}

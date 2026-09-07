<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

/**
 * Пишет в лог каждое отправленное письмо.
 *
 * Без этого вопрос «письмо вообще ушло?» неотвечаем: успешная отправка не
 * оставляла следов, а сбой виден только как ошибка запроса. Теперь в логе
 * есть строка на каждое письмо с адресом и Message-ID, который присвоил
 * почтовый сервер, — по нему письмо ищется в «Отправленных» у провайдера.
 *
 * Тему пишем, тело — нет: в письме с кодом подтверждения тело содержит сам
 * код, и класть его в лог значит хранить готовый ключ к аккаунту.
 */
class LogSentMail
{
    public function handleSending(MessageSending $event): void
    {
        Log::info('Отправка письма', [
            'to' => $this->addresses($event->message->getTo()),
            'subject' => $event->message->getSubject(),
            'mailer' => config('mail.default'),
        ]);
    }

    public function handleSent(MessageSent $event): void
    {
        Log::info('Письмо принято сервером', [
            'to' => $this->addresses($event->sent->getOriginalMessage()->getTo()),
            'message_id' => $event->sent->getMessageId(),
        ]);
    }

    /** @return array<int, string> */
    private function addresses(array $recipients): array
    {
        return array_map(fn ($a) => $a->getAddress(), $recipients);
    }
}

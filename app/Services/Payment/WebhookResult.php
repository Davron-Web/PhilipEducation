<?php

namespace App\Services\Payment;

/**
 * Результат разбора вебхука: какой платёж и чем закончился.
 */
class WebhookResult
{
    public function __construct(
        public readonly string $reference,
        public readonly bool $paid,
        public readonly ?string $providerPaymentId = null,
        public readonly array $payload = [],
    ) {}
}

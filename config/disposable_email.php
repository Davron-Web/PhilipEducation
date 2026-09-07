<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Домены одноразовой почты
    |--------------------------------------------------------------------------
    |
    | Адреса на этих доменах живут минуты и нужны, чтобы обойти подтверждение
    | почты: восстановить пароль на такой ящик уже нельзя, письма о подписке
    | и напоминания уходят в никуда.
    |
    | Список пополняется без правки кода — просто допишите домен сюда.
    | Сравнение идёт по точному совпадению домена и по его поддоменам, то есть
    | «mailinator.com» перекрывает и «team.mailinator.com».
    |
    | Полным этот список не будет никогда: сервисы заводят новые домены
    | быстрее, чем их успевают вносить. Он отсекает самые ходовые, а не
    | заменяет подтверждение почты.
    |
    */

    'blocked_domains' => [
        '0-mail.com',
        '10minutemail.com',
        '10minutemail.net',
        '20minutemail.com',
        '33mail.com',
        'anonbox.net',
        'byom.de',
        'discard.email',
        'dispostable.com',
        'ドメイン.email',
        'emailondeck.com',
        'fakeinbox.com',
        'fakemail.net',
        'getairmail.com',
        'getnada.com',
        'guerrillamail.biz',
        'guerrillamail.com',
        'guerrillamail.de',
        'guerrillamail.net',
        'guerrillamail.org',
        'guerrillamailblock.com',
        'inboxbear.com',
        'incognitomail.com',
        'jetable.org',
        'mail-temporaire.fr',
        'mail7.io',
        'mailcatch.com',
        'maildrop.cc',
        'mailinator.com',
        'mailnesia.com',
        'mailsac.com',
        'mintemail.com',
        'mohmal.com',
        'moakt.com',
        'mytemp.email',
        'nowmymail.com',
        'sharklasers.com',
        'spam4.me',
        'spamgourmet.com',
        'temp-mail.io',
        'temp-mail.org',
        'tempail.com',
        'tempinbox.com',
        'tempmail.net',
        'tempmail.plus',
        'tempmailo.com',
        'tempr.email',
        'throwawaymail.com',
        'trashmail.com',
        'trashmail.de',
        'trbvm.com',
        'yopmail.com',
        'yopmail.fr',
        'yopmail.net',
    ],

];

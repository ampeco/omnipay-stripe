<?php

declare(strict_types=1);

namespace Omnipay\Stripe\Message\PaymentIntents;

class IncrementAuthorizationResponse extends Response
{
    private const array NOT_SUPPORTED_ERROR_CODES = [
        'charge_exceeds_transaction_limit',
    ];

    public function isSuccessful(): bool
    {
        return parent::isSuccessful()
            && $this->getStatus() === 'requires_capture';
    }

    public function isNotSupported(): bool
    {
        $errorCode = $this->data['error']['code'] ?? null;

        return in_array($errorCode, self::NOT_SUPPORTED_ERROR_CODES, true);
    }
}

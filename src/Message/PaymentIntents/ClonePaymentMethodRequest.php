<?php

namespace Omnipay\Stripe\Message\PaymentIntents;

class ClonePaymentMethodRequest extends CreatePaymentMethodRequest
{
    public function getData(): array
    {
        return [
            'customer' => $this->getCustomerReference(),
            'payment_method' => $this->getToken(),
        ];
    }
}

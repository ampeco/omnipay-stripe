<?php

declare(strict_types=1);

namespace Omnipay\Stripe\Message\PaymentIntents;

class IncrementAuthorizationRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount', 'currency', 'paymentIntentReference');

        return [
            'amount' => $this->getAmountInteger(),
        ];
    }

    public function getEndpoint()
    {
        return $this->endpoint . '/payment_intents/' . $this->getPaymentIntentReference() . '/increment_authorization';
    }

    /**
     * @inheritdoc
     */
    protected function createResponse($data, $headers = [])
    {
        return $this->response = new IncrementAuthorizationResponse($this, $data, $headers);
    }
}

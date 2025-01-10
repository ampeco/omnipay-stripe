<?php

namespace Omnipay\Stripe\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class NotificationResponse extends AbstractResponse
{
    /** @var int */
    public $statusCode;

    /**
     * @param RequestInterface $request
     * @param array $data
     * @param int $statusCode
     */
    public function __construct(RequestInterface $request, array $data, int $statusCode)
    {
        parent::__construct($request, $data);
        $this->statusCode = $statusCode;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->statusCode < 400;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->statusCode;
    }

    public function getPaymentToken()
    {
        return $this->data['data']['object']['payment_method'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function getTransactionReference()
    {
        return $this->data['data']['object']['id'];
    }

    /**
     * @return string|null
     */
    public function getEventType()
    {
        return $this->data['type'] ?? null;
    }

    public function getMessage()
    {
        return $this->data['data']['object']['last_payment_error']['code'] ?? null;
    }

    public function getTransactionStatus()
    {
        return $this->data['data']['object']['outcome']['type'] ?? null;
    }
}

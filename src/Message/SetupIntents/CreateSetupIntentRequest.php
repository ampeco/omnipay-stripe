<?php

/**
 * Stripe Create Payment Method Request.
 */

namespace Omnipay\Stripe\Message\SetupIntents;

/**
 * Stripe create setup intent
 *
 * ### Example
 *
 * <code>
 *
 * </code>
 *
 * @see \Omnipay\Stripe\Message\PaymentIntents\AttachPaymentMethodRequest
 * @see \Omnipay\Stripe\Message\PaymentIntents\DetachPaymentMethodRequest
 * @see \Omnipay\Stripe\Message\PaymentIntents\UpdatePaymentMethodRequest
 * @link https://stripe.com/docs/api/setup_intents/create
 */
class CreateSetupIntentRequest extends AbstractRequest
{
    /**
     * @return array|null
     */
    public function getPaymentMethodTypes()
    {
        return $this->getParameter('paymentMethodTypes');
    }

    /**
     * @param array|null $value
     * @return self
     */
    public function setPaymentMethodTypes($value): self
    {
        return $this->setParameter('paymentMethodTypes', $value);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $data = [];

        if ($this->getCustomerReference()) {
            $data['customer'] = $this->getCustomerReference();
        }
        if ($this->getDescription()) {
            $data['description'] = $this->getDescription();
        }

        if ($this->getMetadata()) {
            $this['metadata'] = $this->getMetadata();
        }
        if ($this->getPaymentMethod()) {
            $this['payment_method'] = $this->getPaymentMethod();
        }

        $data['usage'] = 'off_session';
        if ($this->getPaymentMethodTypes() !== null) {
            $data['payment_method_types'] = $this->getPaymentMethodTypes();
        } else {
            $data['payment_method_types'][] = 'card';
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getEndpoint()
    {
        return $this->endpoint . '/setup_intents';
    }

    /**
     * @inheritdoc
     */
    protected function createResponse($data, $headers = [])
    {
        return $this->response = new Response($this, $data, $headers);
    }
}

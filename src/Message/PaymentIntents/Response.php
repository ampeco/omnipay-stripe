<?php

/**
 * Stripe Payment Intents Response.
 */

namespace Omnipay\Stripe\Message\PaymentIntents;

use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Stripe\Message\Response as BaseResponse;

/**
 * Stripe Payment Intents Response.
 *
 * This is the response class for all payment intents related responses.
 *
 * @see \Omnipay\Stripe\PaymentIntentsGateway
 */
class Response extends BaseResponse implements RedirectResponseInterface
{
    /**
     * Get the status of a payment intents response.
     *
     * @return string|null
     */
    public function getStatus()
    {
        if (isset($this->data['object']) && 'payment_intent' === $this->data['object']) {
            return $this->data['status'];
        }

        return null;
    }

    /**
     * Return true if the payment intent requires confirmation.
     *
     * @return bool
     */
    public function requiresConfirmation()
    {
        return $this->getStatus() === 'requires_confirmation';
    }

    /**
     * @inheritdoc
     */
    public function getCardReference()
    {
        if (isset($this->data['object']) && 'payment_method' === $this->data['object']) {
            if (!empty($this->data['id'])) {
                return $this->data['id'];
            }
        }

        if (isset($this->data['object']) && 'payment_intent' === $this->data['object']) {
            if (!empty($this->data['payment_method'])) {
                return $this->data['payment_method'];
            }
        }

        return parent::getCardReference();
    }

    /**
     * @inheritdoc
     */
    public function getCustomerReference()
    {
        if (isset($this->data['object']) && 'payment_method' === $this->data['object']) {
            if (!empty($this->data['customer'])) {
                return $this->data['customer'];
            }
        }

        if (isset($this->data['object']) && 'payment_intent' === $this->data['object']) {
            if (!empty($this->data['customer'])) {
                return $this->data['customer'];
            }
        }

        return parent::getCustomerReference();
    }

    /**
     * Get the capture method of a payment intents response.
     *
     * @return string|null
     */
    public function getCaptureMethod()
    {
        if (isset($this->data['object']) && 'payment_intent' === $this->data['object']) {
            return $this->data['capture_method'];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getTransactionReference()
    {
        if (isset($this->data['object']) && 'payment_intent' === $this->data['object']) {
            return $this->data['id'];
        }

        if (isset($this->data['error']['payment_intent'])) {
            return $this->data['error']['payment_intent']['id'];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function isSuccessful()
    {
        if (empty($this->data)) {
            return false;
        }

        if (isset($this->data['object']) && 'payment_intent' === $this->data['object']) {
            return in_array($this->getStatus(), ['succeeded', 'requires_capture']);
        }

        return parent::isSuccessful();
    }

    /**
     * @inheritdoc
     */
    public function isCancelled()
    {
        if (isset($this->data['object']) && 'payment_intent' === $this->data['object']) {
            return $this->getStatus() === 'canceled';
        }

        return parent::isCancelled();
    }

    /**
     * @inheritdoc
     */
    public function isRedirect()
    {
        /**
         * From Stripe docs:
         *
         * Since 2019, card issuers can provide an authentication_required decline code when declining an attempted payment. Typically, this means that a customer should retry the payment using 3D Secure authentication. The bank may request authentication to comply with regulation, such as Strong Customer Authentication in Europe, or to validate that the customer is legitimate.
         * However, card issuers occasionally provide an authentication_required decline code on a payment that has already gone through 3D Secure. This usually occurs on Mastercard branded cards where the card issuer has not yet updated their systems, and occurs because the indicators for the authentication_required code previously signaled a different decline code (withdrawal_count_limit_exceeded).
         *
         * We treat `authentication_required` as redirect because the user will be redirected with the stripe sdk to complete this payment
         */
        if (isset($this->data['error']['code']) && $this->data['error']['code'] === 'authentication_required') {
            return true;
        }
        if ($this->getStatus() === 'requires_payment_method') {
            return true;
        }
        if ($this->getStatus() === 'requires_action' || $this->getStatus() === 'requires_source_action') {
            // Currently this gateway supports only manual confirmation, so any other
            // next action types pretty much mean a failed transaction for us.
            return !empty($this->data['next_action']) && $this->data['next_action']['type'] === 'redirect_to_url';
        }

        return parent::isRedirect();
    }

    /**
     * @inheritdoc
     */
    public function getRedirectUrl()
    {
        return $this->isRedirect() ? $this->data['next_action']['redirect_to_url']['url'] : parent::getRedirectUrl();
    }

    public function getClientSecretFromError()
    {
        return $this->data['error']['payment_intent']['client_secret'] ?? $this->data['client_secret'] ?? null;
    }

    public function isPending()
    {
        return (isset($this->data['error']['code']) && $this->data['error']['code'] === 'authentication_required')
            || (isset($this->data['status']) && $this->data['status'] === 'processing')
            || (isset($this->data['status']) && $this->data['status'] === 'requires_payment_method');
    }
}

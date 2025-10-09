<?php

/**
 * Stripe Capture Request.
 */

namespace Omnipay\Stripe\Message;

use Money\Formatter\DecimalMoneyFormatter;

/**
 * Stripe Capture Request.
 *
 * Use this request to capture and process a previously created authorization.
 *
 * Example -- note this example assumes that the authorization has been successful
 * and that the authorization ID returned from the authorization is held in $auth_id.
 * See AuthorizeRequest for the first part of this example transaction:
 *
 * <code>
 *   // Once the transaction has been authorized, we can capture it for final payment.
 *   $transaction = $gateway->capture(array(
 *       'amount'        => '10.00',
 *       'currency'      => 'AUD',
 *   ));
 *   $transaction->setTransactionReference($auth_id);
 *   $response = $transaction->send();
 * </code>
 *
 * @see AuthorizeRequest
 */
class CaptureRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('transactionReference');

        $data = [];

        if ($amount = $this->getAmountInteger()) {
            $data['amount'] = $amount;
        }

        return $data;
    }

    public function getEndpoint()
    {
        return $this->endpoint . '/charges/' . $this->getTransactionReference() . '/capture';
    }

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @return string
     */
    public function getApplicationFee()
    {
        $money = $this->getMoney('applicationFee');

        if ($money !== null) {
            $moneyFormatter = new DecimalMoneyFormatter($this->getCurrencies());

            return $moneyFormatter->format($money);
        }

        return '';
    }

    /**
     * Get the payment amount as an integer.
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @return int
     */
    public function getApplicationFeeInteger()
    {
        $money = $this->getMoney('applicationFee');

        if ($money !== null) {
            return (int) $money->getAmount();
        }

        return 0;
    }

    /**
     * @param string $value
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setApplicationFee($value)
    {
        return $this->setParameter('applicationFee', $value);
    }
}

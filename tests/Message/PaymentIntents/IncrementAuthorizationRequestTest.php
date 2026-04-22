<?php

namespace Omnipay\Stripe\Message\PaymentIntents;

use Omnipay\Stripe\PaymentIntentsGateway;
use Omnipay\Tests\TestCase;

class IncrementAuthorizationRequestTest extends TestCase
{
    /** @var IncrementAuthorizationRequest */
    private $request;

    public function setUp()
    {
        $this->request = new IncrementAuthorizationRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setPaymentIntentReference('pi_valid_intent');
        $this->request->setAmount('150.00');
        $this->request->setCurrency('usd');
    }

    public function testEndpoint()
    {
        $this->assertSame(
            'https://api.stripe.com/v1/payment_intents/pi_valid_intent/increment_authorization',
            $this->request->getEndpoint()
        );
    }

    public function testAmountIsConvertedToInteger()
    {
        $data = $this->request->getData();

        $this->assertSame(15000, $data['amount']);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('IncrementAuthorizationSuccess.txt');
        $response = $this->request->send();

        $this->assertInstanceOf(IncrementAuthorizationResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isNotSupported());
        $this->assertSame('pi_1Euf5UFSbr6xR4YAp9PPTxza', $response->getTransactionReference());
        $this->assertSame('pm_1Euf5RFSbr6xR4YAwZ5fP28B', $response->getCardReference());
        $this->assertNull($response->getMessage());
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('IncrementAuthorizationFailure.txt');
        $response = $this->request->send();

        $this->assertInstanceOf(IncrementAuthorizationResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isNotSupported());
        $this->assertSame(
            'This PaymentIntent could not be incremented because it has a status of succeeded.',
            $response->getMessage()
        );
    }

    public function testSendNotSupported()
    {
        $this->setMockHttpResponse('IncrementAuthorizationNotSupported.txt');
        $response = $this->request->send();

        $this->assertInstanceOf(IncrementAuthorizationResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isNotSupported());
        $this->assertSame(
            'The charge amount exceeds the maximum amount that can be charged on this PaymentIntent.',
            $response->getMessage()
        );
    }

    public function testGatewayCreatesCorrectRequestType()
    {
        $gateway = new PaymentIntentsGateway($this->getHttpClient(), $this->getHttpRequest());
        $request = $gateway->incrementAuthorization([
            'amount' => '150.00',
            'currency' => 'usd',
            'paymentIntentReference' => 'pi_valid_intent',
        ]);

        $this->assertInstanceOf(IncrementAuthorizationRequest::class, $request);
        $this->assertSame('150.00', $request->getAmount());
        $this->assertSame('pi_valid_intent', $request->getPaymentIntentReference());
    }
}

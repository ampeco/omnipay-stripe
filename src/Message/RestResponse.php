<?php
namespace Omnipay\Stripe\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class RestResponse extends AbstractResponse
{
    /**
     * @var int
     */
    public $statusCode;

    /**
     * @param RequestInterface $request
     * @param array $data
     * @param int $statusCode
     */
    public function __construct(RequestInterface $request, array $data, int $statusCode)
    {
        \Illuminate\Support\Facades\Log::info('we are in __construct' . $statusCode);
        parent::__construct($request, $data);
        $this->statusCode = $statusCode;
    }

    /**
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->statusCode < 400;
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        \Illuminate\Support\Facades\Log::info('we are in getCode' . $this->statusCode);
        return $this->statusCode;
    }
}

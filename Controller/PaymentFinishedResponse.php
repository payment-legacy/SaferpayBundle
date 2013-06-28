<?php

namespace Payment\Bundle\SaferpayBundle\Controller;

class PaymentFinishedResponse
{
    const STATUS_OK = 'ok';
    const STATUS_ERROR = 'error';

    const ERROR_CONFIRMATION = 1;
    const ERROR_COMPLETION = 2;
    const ERROR_VALIDATION = 3;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var int
     */
    protected $errorCode;

    /**
     * @param string $status
     * @param int $errorCode
     */
    public function __construct($status = self::STATUS_OK, $errorCode = null)
    {
        $this->setStatus($status);
        $this->setErrorCode($errorCode);
    }

    /**
     * @return null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     * @return PaymentFinishedResponse
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return PaymentFinishedResponse
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}
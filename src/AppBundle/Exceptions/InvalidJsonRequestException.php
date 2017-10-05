<?php
namespace AppBundle\Exceptions;

class InvalidJsonRequestException extends \Exception
{

    private $httpStatusCode;

    public function __construct($message, $code, $httpStatusCode = 400)
    {
        parent::__construct($message, $code, null);
        $this->httpStatusCode = $httpStatusCode;

    }

    /**
     * @return mixed
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * @param mixed $httpStatusCode
     *
     * @return self
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;

        return $this;
    }
}

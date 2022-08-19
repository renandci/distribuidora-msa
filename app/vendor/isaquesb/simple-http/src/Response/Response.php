<?php
namespace Simple\Http\Response;

/**
 * Response Object
 * @author Isaque de Souza <isaquesb@gmail.com>
 */
class Response implements ResponseInterface
{
    /**
     * @var string
     */
    protected $rawBody;

    /**
     * @var int
     */
    protected $httpStatus;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @return string
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }

    /**
     * @return int
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $rawBody
     * @return ResponseInterface
     */
    public function setRawBody($rawBody)
    {
        $this->rawBody = $rawBody;
        return $this;
    }

    /**
     * @param int $httpStatus
     * @return ResponseInterface
     */
    public function setHttpStatus($httpStatus)
    {
        $this->httpStatus = $httpStatus;
        return $this;
    }

    /**
     * @param array $errors
     * @return ResponseInterface
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }
}

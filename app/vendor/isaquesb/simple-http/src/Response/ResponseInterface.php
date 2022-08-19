<?php
namespace Simple\Http\Response;

/**
 * Response Interface
 * @author Isaque de Souza <isaquesb@gmail.com>
 */
interface ResponseInterface
{
    /**
     * @return string Raw Body
     */
    public function getRawBody();

    /**
     * @return integer HTTP Status
     */
    public function getHttpStatus();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @param string $body Raw Body
     */
    public function setRawBody($body);

    /**
     * @param integer $status HTTP Status
     */
    public function setHttpStatus($status);

    /**
     * @param array $errors
     * @return ResponseInterface
     */
    public function setErrors(array $errors);
}

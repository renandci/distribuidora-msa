<?php
namespace Simple\Http\Request\Adapter;

use Simple\Http\Request\RequestInterface;

/**
 * Adapter Interface
 * @author Isaque de Souza <isaquesb@gmail.com>
 */
interface AdapterInterface
{
    /**
     * @param RequestInterface $request
     * @return boolean Successful request
     */
    public function dispatch(RequestInterface $request);

    /**
     * @return \Simple\Http\Response\ResponseInterface Response
     */
    public function getResponse();
}

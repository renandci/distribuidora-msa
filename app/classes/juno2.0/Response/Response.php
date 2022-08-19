<?php
/**
 * Class Response
 *
 * @author Fernando Campos de Oliveira <fernando@odesenvolvedor.net>
 * 
 * @package ODJuno\Response;
 */

namespace ODJuno\Response;

use ODJuno\Interfaces\ODJunoInterface;

class Response implements ODJunoInterface
{
    /**
     * @param $json
     *
     * @return self
     */
    public static function fromJson($json)
    {
        $object = json_decode($json);
        $self = new self();
        $self->populate($object);

        foreach ($self as $k => $v) {
            if (empty($v)) {
                unset($self->$k);
            }
        }

        return $self;
    }

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data)
    {
        $dataProps = get_object_vars($data);
        if (!empty($dataProps)) {
            foreach ($dataProps as $k => $v) {
                $this->$k = $v;
            }
        }
    }
}
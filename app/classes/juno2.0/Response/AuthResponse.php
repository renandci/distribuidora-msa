<?php
/**
 * Class AuthResponse
 *
 * @author Fernando Campos de Oliveira <fernando@odesenvolvedor.net>
 * 
 * @package ODJuno\Response;
 */

namespace ODJuno\Response;

class AuthResponse extends Response
{
    /**
     * @var string $access_token
     */
    protected $access_token;
    /**
     * @var string $token_type
     */
    protected $token_type;
    /**
     * @var string $expires_in
     */
    protected $expires_in;
    /**
     * @var string $scope
     */
    protected $scope;
    /**
     * @var string $user_name
     */
    protected $user_name;

    protected $jti;

    


    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    /**
     * @return string
     */
    public function getTokenType(): string
    {
        return $this->token_type;
    }

    /**
     * @return int
     */
    public function getExpiresIn(): int
    {
        return $this->expires_in;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->user_name;
    }

    /**
     * @return string
     */
    public function getJti(): string
    {
        return $this->jti;
    }


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

}

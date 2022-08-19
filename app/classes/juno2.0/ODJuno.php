<?php
/**
 * Emita cobranças para cartão de crédito ou boleto, com ou sem split de pagamento.
 * Para cobranças na modalidade split, é possível informar um ou mais destinatários para divisão, 
 * na qual o recipientToken corresponde a cada conta digital envolvida.
 * Caso o emissor delimitado no X-Resource-Token esteja envolvido na divisão,
 * este também deve ser informado em um dos objetos desse array, além dos demais destinatários.
 * Os parâmetros amount e percentage definem, respectivamente,
 * a divisão do valor do split de maneira fixa ou percentual,
 * não podendo ser enviados juntos na requisição.

 * Caso a divisão de valores resulte em um número com mais de
 *  2 casas decimais, a partilha de valores não ocorre de maneira exata,
 *  desse modo é preciso definir quem ficará com o remanescente em amountRemainder.
 * 
 * @author Fernando Campos de Oliveira <fernando@odesenvolvedor.net>
 *
 * @package ODJuno; 
 */
namespace ODJuno;

use ODJuno\Exception\ODJunoException;
use ODJuno\Request\Client;
use ODJuno\Services\ChargeService;

class ODJuno 
{

    /**
     * @var Client
     */
    protected $client;

    public function __construct($accessToken, $privateToken, $sandbox = true)
    {
        if (empty($accessToken)) {
            throw new ODJunoException('Provide an access token');
        }

        if (empty($privateToken)) {
            throw new ODJunoException('Provide your private token');
        }

        $this->client = new Client($accessToken, $privateToken, $sandbox);
    }


    public function charges(): ChargeService
    {
        return new ChargeService($this->client);
    }

}

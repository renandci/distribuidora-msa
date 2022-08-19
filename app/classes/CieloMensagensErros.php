<?php

interface MensagensVendasError
{
	public static function getMensagem($code);
    
	public static function getStatus($code);
    
	public static function getStatusSite($code);
}

class CieloMensagensErros
{
    var $code;
    
    static protected $status = [
        0 => 'Falha ao processar o pagamento',
        1 => 'Meio de pagamento apto a ser capturado ou pago(Boleto)',
        2 => 'Pagamento confirmado e finalizado',
        // 3 => 'Cartão de Crédito e Débito / Transferência eletrônica',
        3 => 'Autorizacao negada',
        10 => 'Pagamento cancelado',
        11 => 'Pagamento Cancelado/Estornado',
        12 => 'Esperando retorno da instituição financeira',
        13 => 'Pagamento cancelado por falha no processamento',
        20 => 'Recorrência agendada',
    ];
    
    static protected $messages = [
        0 => 'Dado enviado excede o tamanho do campo',
        100 => 'Campo enviado está vazio ou inválido',
        101 => 'Campo enviado está vazio ou inválido',
        102 => 'Campo enviado está vazio ou inválido',
        103 => 'Caracteres especiais não permitidos',
        104 => 'Campo enviado está vazio ou inválido',
        105 => 'Campo enviado está vazio ou inválido',
        106	=> 'Campo enviado está vazio ou inválido',
        107 => 'Campo enviado excede o tamanho ou contem caracteres especiais',
        108 => 'Valor da transação não pode ser vazio',
        109 => 'Campo enviado está vazio ou inválido',
        110 => 'Campo enviado está vazio ou inválido',
        111 => 'Campo enviado está vazio ou inválido',
        112 => 'Campo enviado está vazio ou inválido',
        113 => 'Campo enviado está vazio ou inválido',
        114 => 'O MerchantId enviado não é um GUID',
        115 => 'O MerchantID não existe ou pertence a outro ambiente (EX: Sandbox)',
        116	=> 'Loja bloqueada, entre em contato com o suporte Cielo',
        117	=> 'Campo enviado está vazio ou inválido',
        118	=> 'Campo enviado está vazio ou inválido',
        119	=> 'Nó "Payment" não enviado',
        120	=> 'IP bloqueado por questões de segurança',
        121	=> 'Nó “Customer” não enviado',
        122	=> 'Campo enviado está vazio ou inválido',
        123	=> 'Numero de parcelas deve ser superior a 1',
        124	=> 'Campo enviado está vazio ou inválido',
        125	=> 'Campo enviado está vazio ou inválido',
        126	=> 'Campo enviado está vazio ou inválido',
        127	=> 'Numero do cartão de crédito é obrigatório',
        128	=> 'Numero do cartão superiro a 16 digitos',
        129	=> 'Meio de pagamento não vinculado a loja ou Provider inválido',
        130	=> '-',
        131	=> 'Campo enviado está vazio ou inválido',
        132	=> 'O Merchantkey enviado não é um válido',
        133	=> 'Provider enviado não existe',
        134	=> 'Dado enviado excede o tamanho do campo',
        135	=> 'Dado enviado excede o tamanho do campo',
        136	=> 'Dado enviado excede o tamanho do campo',
        137	=> 'Dado enviado excede o tamanho do campo',
        138	=> 'Dado enviado excede o tamanho do campo',
        139	=> 'Dado enviado excede o tamanho do campo',
        140	=> 'Dado enviado excede o tamanho do campo',
        141	=> 'Dado enviado excede o tamanho do campo',
        142	=> 'Dado enviado excede o tamanho do campo',
        143	=> 'Dado enviado excede o tamanho do campo',
        144	=> 'Dado enviado excede o tamanho do campo',
        145	=> 'Dado enviado excede o tamanho do campo',
        146	=> 'Dado enviado excede o tamanho do campo',
        147	=> 'Dado enviado excede o tamanho do campo',
        148	=> 'Dado enviado excede o tamanho do campo',
        149	=> 'Dado enviado excede o tamanho do campo',
        150	=> 'Dado enviado excede o tamanho do campo',
        151	=> 'Dado enviado excede o tamanho do campo',
        152	=> 'Dado enviado excede o tamanho do campo',
        153	=> 'Dado enviado excede o tamanho do campo',
        154	=> 'Dado enviado excede o tamanho do campo',
        155	=> 'Dado enviado excede o tamanho do campo',
        156	=> 'Dado enviado excede o tamanho do campo',
        157	=> 'Dado enviado excede o tamanho do campo',
        158	=> 'Dado enviado excede o tamanho do campo',
        159	=> 'Dado enviado excede o tamanho do campo',
        160	=> 'Dado enviado excede o tamanho do campo',
        161	=> 'Dado enviado excede o tamanho do campo',
        162	=> 'Dado enviado excede o tamanho do campo',
        163	=> 'Não é aceito paginação ou extenções (EX .PHP) na URL de retorno',
        166	=> '-',
        167	=> 'Antifraude não vinculado ao cadastro do lojista',
        168	=> 'Recorrencia não encontrada',
        169	=> 'Recorrencia não está ativa. Execução paralizada',
        170	=> 'Cartão protegido não vinculado ao cadastro do lojista',
        171	=> 'Falha no processamento do pedido - Entre em contato com o suporte Cielo',
        172	=> 'Falha na validação das credenciadas enviadas',
        173	=> 'Meio de pagamento não vinculado ao cadastro do lojista',
        174	=> 'Campo enviado está vazio ou inválido',
        175	=> 'Campo enviado está vazio ou inválido',
        176	=> 'Campo enviado está vazio ou inválido',
        177	=> 'Campo enviado está vazio ou inválido',
        178	=> 'Campo enviado está vazio ou inválido',
        179	=> 'Campo enviado está vazio ou inválido',
        180	=> 'Token do Cartão protegido não encontrado',
        181	=> 'Token do Cartão protegido bloqueado',
        182	=> 'Bandeira do cartão não enviado',
        183	=> 'Data de nascimento invalida ou futura',
        184	=> 'Falha no formado ta requisição. Verifique o código enviado',
        185	=> 'Bandeira não suportada pela API Cielo',
        186	=> 'Meio de pagamento não suporta o comando enviado',
        187	=> '-',
        188	=> '-',
        189 => 'Dado enviado excede o tamanho do campo',
        190 => 'Dado enviado excede o tamanho do campo',
        190 => 'Dado enviado excede o tamanho do campo',
        191 => 'Dado enviado excede o tamanho do campo',
        192 => 'CEP enviado é inválido',
        193 => 'Valor para realização do SPLIT deve ser superior a 0',
        194 => 'SPLIT não habilitado para o cadastro da loja',
        195 => 'Validados de plataformas não enviado',
        196 => 'Campo obrigatório não enviado',
        197 => 'Campo obrigatório não enviado',
        198 => 'Campo obrigatório não enviado',
        199 => 'Campo obrigatório não enviado',
        200 => 'Campo obrigatório não enviado',
        201 => 'Campo obrigatório não enviado',
        202 => 'Campo obrigatório não enviado',
        203 => 'Campo obrigatório não enviado',
        204 => 'Campo obrigatório não enviado',
        205 => 'Campo obrigatório não enviado',
        206 => 'Dado enviado excede o tamanho do campo',
        207 => 'Dado enviado excede o tamanho do campo',
        208 => 'Dado enviado excede o tamanho do campo',
        209 => 'Dado enviado excede o tamanho do campo',
        210 => 'Campo obrigatório não enviado',
        211 => 'Dados da Visa Checkout inválidos',
        212 => 'Dado de Wallet enviado não é valido',
        213 => 'Cartão de crédito enviado é inválido',
        214 => 'Portador do cartão não deve conter caracteres especiais',
        215 => 'Campo obrigatório não enviado',
        216 => 'IP bloqueado por questões de segurança',
        300 => '-',
        301 => '-',
        302 => '-',
        303 => '-',
        304 => '-',
        306 => '-',
        307 => 'Transação não encontrada ou não existente no ambiente.',
        308 => 'Transação não pode ser capturada - Entre em contato com o suporte Cielo',
        309 => 'Transação não pode ser Cancelada - Entre em contato com o suporte Cielo',
        310 => 'Comando enviado não suportado pelo meio de pagamento',
        311 => 'Cancelamento após 24 horas não liberado para o lojista',
        312 => 'Transação não permite cancelamento após 24 horas',
        313 => 'Transação recorrente não encontrada ou não disponivel no ambiente',
        314 => '-',
        315 => '-',
        316 => 'Não é permitido alterada dada da recorrencia para uma data passada',
        317 => '-',
        318 => '-',
        319 => 'Recorrencia não vinculada ao cadastro do lojista',
        320 => '-',
        321 => '-',
        322 => 'Dollar não vinculado ao cadastro do lojista',
        323 => 'Consulta de Bins não vinculada ao cadastro do lojista',
    ]; 
    
    public static function getStatusSite($code) {
        switch ($code) 
        {
            case 1 : case 2 :
                return 3;
            case 3 : case 10 : case 11 : case 12 : 
                return 10;
            case 13 :
                return 2;
        }
    }
    
    public static function getMensagem( $code ) {     
        return self::$messages[ $code ];
    }
    
    public static function getStatusTransacao($param) {
        return self::$messages[ $param ];
    }

    /**
     * Retornar status dos pagamentos
     * @param int $code Codigo int
     * @return string Frase do pagamento
     */
    public static function getStatus($code = '') {
        return self::$status[ $code ];
    }
}
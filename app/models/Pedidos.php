<?php

class Pedidos extends ActiveRecord
{
  static $table = 'pedidos';

  /**
   * Chama a funcao dentro de ActiveRecord para update store
   * @var type
   */
  static $after_save = ['in_store', 'normalize_estoque', 'enviar_emails'];

  static $before_save = ['in_store'];

  static $has_one = [
    [
      'pedido_transacao',
      'class_name' => 'PedidosTransacoes',
      'foreign_key' => 'pedidos_id',
      'primary_key' => 'id',
      'order' => 'id desc'
    ], [
      'cliente',
      'class_name' => 'Clientes',
      'primary_key' => 'id_cliente',
      'foreign_key' => 'id',
    ], [
      'indicacao',
      'class_name' => 'ClientesIndicacoes',
      'foreign_key' => 'id_cliente',
      'primary_key' => 'id_cliente',
    ], [
      'pedido_cliente',
      'class_name' => 'Clientes',
      'primary_key' => 'id_cliente',
      'foreign_key' => 'id',
    ], [
      'pedido_endereco',
      'class_name' => 'PedidosEnderecos',
      'primary_key' => 'id',
      'foreign_key' => 'id_pedido'
    ], [
      'questionario',
      'class_name' => 'PedidosQuestionario',
      'foreign_key' => 'id_pedido',
      'primary_key' => 'id',
    ], [
      'nfe_notas',
      'class_name' => 'NfeNotas',
      'foreign_key' => 'id_pedido',
      'primary_key' => 'id',
      'conditions' => ['status IN(1, 3)'],
      'order' => 'id desc'
    ], [
      'correio_etiqueta',
      'class_name' => 'CorreiosEtiquetas',
      'foreign_key' => 'id_pedidos',
      'primary_key' => 'id',
    ], [
      'jadlog_etiqueta',
      'class_name' => 'JadLogEtiqueta',
      'foreign_key' => 'id_pedido',
      'primary_key' => 'id',
      'conditions' => ['excluir = 0'],
      'order' => 'id desc'
    ], [
      'pedido_log',
      'class_name' => 'PedidosLogs',
      'foreign_key' => 'id_pedido',
      'primary_key' => 'id',
      'order' => 'id asc'
    ], [
      'pedido_cupom',
      'class_name' => 'Cupons',
      'primary_key' => 'id_cupom',
      'foreign_key' => 'id',
    ],

  ];

  // static $belongs_to = [ [
  // questionario',
  // 'class_name' => 'PedidosQuestionario',
  // 'primary_key' => 'id_pedido',
  // 'foreign_key' => 'id',
  // ]
  // ];

  static $has_many = [
    [
      'pedidos_vendas',
      'class_name' => 'PedidosVendas',
      'foreign_key' => 'id_pedido',
      'primary_key' => 'id',
      'order' => 'id_pedido desc'
    ], [
      'pedidos_logs',
      'class_name' => 'PedidosLogs',
      'foreign_key' => 'id_pedido',
      'primary_key' => 'id',
      'order' => 'id desc'
    ], [
      'pedido_transacoes',
      'class_name' => 'PedidosTransacoes',
      'foreign_key' => 'pedidos_id',
      'primary_key' => 'id'
    ], [
      'correios_etiquetas',
      'class_name' => 'CorreiosEtiquetas',
      'foreign_key' => 'id_pedidos',
      'primary_key' => 'id'
    ], [
      'nfes_notas',
      'class_name' => 'NfeNotas',
      'foreign_key' => 'id_pedido',
      'primary_key' => 'id',
      'conditions' => ['status IN(1, 3)'],
      'order' => 'id desc'
    ]
  ];

  /**
   * Gera um novo código de vendas para os pedidos
   */
  public static function getCodidoVenda($type = 'NUM')
  {

    if ($type == 'ALF') {
      $return = self::first(['select' => 'CONCAT("P", LPAD((COUNT(id) + 1), 9, "0")) AS codigo']);
      return $return->codigo ? $return->codigo : 'P000000001';
    }

    $return = self::first(['select' => 'CONCAT(LPAD((MAX(id) + 1), 10, "0")) AS codigo']);
    return $return->codigo ? $return->codigo : '0000000001';
  }

  /**
   * Gerar um novo pedido no sistema
   */
  public static function gerarPedido(
    $DataVenda = '',
    $CoddigoVenda = '',
    $ClienteId = '',
    $IpVenda = '',
    $FreteTipo = '',
    $FreteValor = '',
    $FretePrazo = '',
    $VlCompra = '0.00',
    $DescCupom = '0.00',
    $DescBol = '0.00',
    $FormPgto = '',
    $Brand = '',
    $Parcelas = 0,
    $PLATFORM = '',
    $BROWSER = '',
    $VERSION = '',
    $IdCupom = 0,
    $PedidosOff = 0,
    $FretePudoId = null
  ) {
    $p = new Pedidos();
    $p->data_venda = $DataVenda;
    $p->codigo = $CoddigoVenda;
    $p->id_cliente = $ClienteId;
    $p->frete_tipo = $FreteTipo;
    $p->frete_valor = $FreteValor;
    $p->frete_prazo = $FretePrazo;
    $p->valor_compra = $VlCompra;
    $p->id_cupom = $IdCupom;
    $p->desconto_cupom = $DescCupom;
    $p->desconto_boleto = $DescBol;
    $p->forma_pagamento = $FormPgto;
    $p->cartao = $Brand;
    $p->parcelas = (int)$Parcelas;
    $p->status = 0;

    $p->ip = $IpVenda;
    $p->platform = $PLATFORM;
    $p->browser = $BROWSER;
    $p->version = $VERSION;
    $p->pedido_off = $PedidosOff;
    $p->frete_pudoid = $FretePudoId;
    $p->save();

    return $p;
  }

  /**
   * Busca todas as informacoes do pedido
   * @param type $pedido_id
   * @return type object
   */
  public static function getVendasAll($pedido_id = 0)
  {

    return self::all([
      'select' => ''
        . 'pedidos.id as pedido_id, '
        . 'date_format(pedidos.data_venda, "%d/%m/%Y - %H:%i") as data_compra, '
        . 'pedidos.codigo, '
        . 'pedidos.frete_tipo, '
        . 'pedidos.frete_valor, '
        . 'pedidos.frete_prazo, '
        . 'pedidos.frete_pudoid, '
        . 'pedidos.valor_compra, '
        . 'pedidos.rastreio, '
        . 'pedidos.motivos, '
        . 'pedidos.desconto_cupom, '
        . 'pedidos.desconto_boleto, '
        . 'pedidos.forma_pagamento, '
        . 'pedidos.cartao, '
        . 'pedidos.parcelas, '
        . 'pedidos.status, '

        . 'clientes.id as cliente_id, '
        . 'clientes.nome, '
        . 'clientes.email, '
        . 'clientes.telefone, '
        . 'clientes.celular, '

        . 'pedidos_enderecos.nome as nomeendereco, '
        . 'pedidos_enderecos.endereco, '
        . 'pedidos_enderecos.numero, '
        . 'pedidos_enderecos.bairro, '
        . 'pedidos_enderecos.referencia, '
        . 'pedidos_enderecos.complemento, '
        . 'pedidos_enderecos.cidade, '
        . 'pedidos_enderecos.uf, '
        . 'pedidos_enderecos.cep, '

        . 'produtos.nome_produto, '
        . 'produtos.id as id_produto, '
        . 'produtos.codigo_id as codigo_id, '

        . 'pedidos_vendas.quantidade, '
        . 'pedidos_vendas.valor_pago, '
        . 'pedidos_vendas.personalizado, '
        . 'produtos_imagens.imagem, '

        . 'cores.nomecor, '
        . 'tamanhos.nometamanho, '
        . 'produtos_kits.codigo_id as prod_kit ',

      'joins' => ''
        . 'inner join pedidos_vendas on pedidos_vendas.id_pedido = pedidos.id '
        . 'inner join pedidos_enderecos on pedidos_enderecos.id_pedido = pedidos.id '
        . 'inner join clientes on pedidos.id_cliente = clientes.id '
        . 'inner join produtos on produtos.id = pedidos_vendas.id_produto '
        . 'inner join produtos_imagens on produtos_imagens.codigo_id = produtos.codigo_id '
        . 'inner join cores on produtos.id_cor = cores.id '
        . 'inner join tamanhos on produtos.id_tamanho = tamanhos.id '
        . 'left join produtos_kits on produtos.codigo_id = produtos_kits.codigo_id ',

      'group' => 'pedidos_vendas.id',
      'conditions' => ['md5(pedidos.id)=? OR pedidos.id=? AND produtos_imagens.capa = 1 and produtos_imagens.cor_id = produtos.id_cor', $pedido_id, $pedido_id]
    ]);
  }

  /**
   * Busca todas as informacoes do pedido
   * @param type $pedido_id
   * @return type object
   */
  public static function getVendas(
    $NrPedido = '',
    $Cliente = '',
    $DataIni = '',
    $DataFin = '',
    $Status = ''
  ) {
    $Campos = '';
    $Valores = '';

    $Campos[] = $NrPedido != '' ? ' pedidos.codigo like ?' : '';
    $Valores[] = $NrPedido != '' ? "%{$NrPedido}%" : '';

    $Campos[] = $Cliente != '' ? ' pedidos.id_cliente=?' : '';
    $Valores[] = $Cliente != '' ? $Cliente : '';

    // Se data final não existir, valerá a do ultimo mes atual
    $Campos[] = $DataIni != '' && $DataFin == '' ? ' pedidos.data_venda BETWEEN ? AND ? ' : '';
    $Valores[] = $DataIni != '' && $DataFin == '' ? $DataIni : '';
    $Valores[] = $DataIni != '' && $DataFin == '' ? date('Y-m-d H:i:s') : '';

    // Se data final não existir, valerá a do ultimo mes atual
    $Campos[] = $DataIni == '' && $DataFin != '' ? ' pedidos.data_venda BETWEEN ? AND ? ' : '';
    $Valores[] = $DataIni == '' && $DataFin != '' ? date('Y-m-01 H:i:s') : '';
    $Valores[] = $DataIni == '' && $DataFin != '' ? $DataFin : '';

    // Se data final não existir, valerá a do ultimo mes atual
    $Campos[] = $DataIni != '' && $DataFin != '' ? ' pedidos.data_venda BETWEEN ? AND ? ' : '';
    $Valores[] = $DataIni != '' && $DataFin != '' ? $DataIni : '';
    $Valores[] = $DataIni != '' && $DataFin != '' ? $DataFin : '';


    $Campos[] = $Status != '' ? ' pedidos.status =? ' : '';
    $Valores[] = $Status != '' ? $DataFin : '';

    $Campos = implode(array_filter($Campos), ' AND ');
    $Valores = implode(array_filter($Valores), ',');

    return self::all([
      'select' => ''
        . 'pedidos.id as pedido_id, '
        . 'date_format(pedidos.data_venda, "%d/%m/%Y - %H:%i") as data_compra, '
        . 'pedidos.codigo, '
        . 'pedidos.frete_tipo, '
        . 'pedidos.frete_valor, '
        . 'pedidos.rastreio, '
        . 'pedidos.motivos, '
        . 'pedidos.desconto_cupom, '
        . 'pedidos.desconto_boleto, '
        . 'pedidos.forma_pagamento, '
        . 'pedidos.valor_compra, '
        . 'pedidos.cartao, '
        . 'pedidos.parcelas, '
        . 'pedidos.status, '
        . 'pedidos.frete_prazo, '

        . 'clientes.nome, '
        . 'clientes.email ',

      'joins' => ''
        . 'LEFT JOIN clientes ON pedidos.id_cliente = clientes.id ',
      'group' => 'pedidos.id',

      'conditions' => [$Campos, $Valores]
    ]);
  }

  public function enviar_emails()
  {
    $status_old = 0;
    $this->pedidos_logs[0]->status;
    $status_new = $this->status;

    // verificar para mandar e-mails
    // somentes se os status forem diferentes
    if (($status_old == $status_new)) return;

    if ($status_new > 0 && $this->excluir == 0) {
      EmailComfirmacaoCompra($this->id, $this->status, $this->rastreio, $this->motivos);
    }
  }

  public function normalize_estoque()
  {

    $status_new = $this->status;

    $status_old = $this->pedidos_logs[0]->status;

    $stock_all = $this->global_store('stock_all');

    // Retornar estoque
    if (($status_new == 4 || $status_new == 5 || $status_new == 10) && ($status_old != 4 && $status_old != 5 && $status_old != 10)) {
      foreach ($this->pedidos_vendas as $values) {
        $estoque_vend = $values->quantidade;

        // Tentar retornar o estoque para todos produtos de cores
        if (!empty($stock_all) && $stock_all == 'in_color') {
          foreach ($values->produto->produtos_all as $values_all) {
            $estoque_prod_all = $values_all->estoque;
            $values_all->estoque = (($values->quantidade > 0) ? $estoque_prod_all + $estoque_vend : 0);
            $values_all->save();
          }
        } else {
          $estoque_prod = $values->produto->estoque;
          $values->produto->estoque = (($values->quantidade > 0) ? $estoque_prod + $estoque_vend : 0);
          $values->produto->save();
        }
      }
    }

    // Diminuir o estoque
    if (($status_new != 4 && $status_new != 5 && $status_new != 10) && ($status_old == 4 || $status_old == 5 || $status_old == 10) || count($this->pedidos_logs) == 0) {
      foreach ($this->pedidos_vendas as $values) {
        $estoque_vend = $values->quantidade;

        // Tentar diminuir o estoque para todos produtos de cores
        if (!empty($stock_all) && $stock_all == 'in_color') {
          foreach ($values->produto->produtos_all as $values_all) {
            $estoque_prod_all = $values_all->estoque;
            $values_all->estoque = (($values->quantidade > 0) ? $estoque_prod_all - $estoque_vend : 0);
            $values_all->save();
          }
        } else {
          $estoque_prod = $values->produto->estoque;
          $values->produto->estoque = (($values->quantidade > 0) ? $estoque_prod - $estoque_vend : 0);
          $values->produto->save();
        }
      }
    }
  }
}

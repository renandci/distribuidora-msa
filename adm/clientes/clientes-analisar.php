<?php
include '../topo.php';

/**
 * Editar dados do cliente
 */
if (isset($POST['id_cliente']) && $POST['id_cliente'] > 0 && !isset($POST['id_endereco'])) {

  $id  = $POST['id_cliente'];
  $mensagem = null;

  // if( isset($id) && $id > 0) :
  // 	$Clientes = Clientes::find($id);
  // 	$params['id'] = $Clientes->id;
  // else :
  // 	$Clientes = new Clientes();
  // 	$params['id'] = $POST['id_cliente'];
  // endif;

  // $params['email'] = ! empty( $POST['email'] ) || $STORE['config']['cadastro']['email']['required'] == true ? $POST['email'] : ' ';
  // $params['nome'] = ! empty( $POST['nome'] ) || $STORE['config']['cadastro']['nome']['required'] == true ? $POST['nome'] : ' ';
  // $params['cpfcnpj'] = ! empty( $POST['cpfcnpj'] ) || $STORE['config']['cadastro']['cpfcnpj']['required'] == true ? $POST['cpfcnpj'] : ' ';
  // $params['rg'] = ! empty( $POST['rg'] ) || $STORE['config']['cadastro']['rg']['required'] == true ? $POST['rg'] : ' ';
  // $params['data_nascimento'] = ! empty( $POST['data_nascimento'] ) || $STORE['config']['cadastro']['data_nascimento']['required'] == true ? $POST['data_nascimento'] : ' ';

  // $params['telefone'] = ! empty( $POST['telefone'] ) || $STORE['config']['cadastro']['telefone']['required'] == true ? $POST['telefone'] : ' ';

  // $params['celular'] = !empty( $POST['celular'] ) || $STORE['config']['cadastro']['celular']['required'] == true ? $POST['celular'] : ' ';

  // $params['operadora'] = ! empty( $POST['operadora'] ) || $STORE['config']['cadastro']['operadora']['required'] == true ? $POST['operadora'] : ' ';

  // $params['sexo'] = ! empty( $POST['sexo'] ) || $STORE['config']['cadastro']['sexo']['required'] == true ? $POST['sexo'] : ' ';

  // $params['cidade'] = ! empty( $POST['cidade'] ) || $STORE['config']['cadastro']['cidade']['required'] == true ? $POST['cidade'] : ' ';

  // $params['uf'] = ! empty( $POST['uf'] ) || $STORE['config']['cadastro']['uf']['required'] == true ? $POST['uf'] : ' ';

  // $params['atacadista_desconto'] = $POST['atacadista_desconto'];

  // $params['atacadista_min'] = dinheiro($POST['atacadista_min']);

  // $params['atacadista_max'] = dinheiro($POST['atacadista_max']);

  // $params['atacadista_text'] = $POST['atacadista_text'];

  // $params['excluir'] = $POST['excluir'];

  // // verificar se a senha real existe
  // // e em seguida checa se ela esta vazia
  // $params['senha_real'] = 'nullnull';

  // // verificar se a senha real existe
  // // e em seguida checa se ela esta vazia
  // $params['senha_confirm'] = 'nullnull';

  // if( isset( $POST['senha'] ) && $POST['senha'] !== '' )
  // 	$params['senha'] = Bcrypt::hash( $POST['senha'] );

  // $return = $Clientes->my_save( $params );

  // if( isset( $POST['is_send'] ) && $POST['is_send'] != '' ) {
  // 	$html = '';
  // 	$html .= sprintf(''
  // 		  . '<tr><td>'
  // 		  . '<div style="text-align: center; font-size: 16px">%s</div>'
  // 		  . '</td></tr>', nl2br($POST['atacadista_text']));
  // 	$CONTEUDO_MAIL = email_body($CONFIG, $html);

  // 	$mail->setFrom($CONFIG['email_contato'], $CONFIG['nome_fantasia'] . ' - Venda no Atacado');
  // 	$mail->addAddress($params['email'], $params['nome']);

  // 	$mail->Subject = $params['nome'] . ', já análisamos seu cadastro';
  // 	$mail->Body = $CONTEUDO_MAIL;

  // 	$mail->send();
  // 	$mail->SmtpClose();

  // }

  $mensagem = null;

  $id = $POST['id_cliente'];
  $email = $POST['email'];
  $senha = $POST['senha'];
  $nome = $POST['nome'];
  $sobrenome = $POST['sobrenome'];
  $cpfcnpj = $POST['cpfcnpj'];
  $rg = $POST['rg'];
  $telefone = $POST['telefone'];
  $celular = $POST['celular'];
  $operadora = $POST['operadora'];
  $apelido = $POST['apelido'];
  $data_nascimento = $POST['data_nascimento'];
  $sexo = $POST['sexo'];
  $cidade = $POST['cidade'];
  $uf = $POST['uf'];
  $status = $POST['status'];

  $atacadista = $POST['atacadista'];
  $atacadista_desconto = $POST['atacadista_desconto'];
  $atacadista_min = $POST['atacadista_min'];
  $atacadista_max = $POST['atacadista_max'];
  $atacadista_text = $POST['atacadista_text'];

  if (isset($POST['senha']) && $POST['senha'] !== '') {
    $senha = Bcrypt::hash($POST['senha']);
  }

  Clientes::$validates_presence_of = [];
  Clientes::$validates_format_of = [];
  Clientes::$validates_size_of = [];

  $Clientes = Clientes::action_cadastrar_editar(['Clientes' => [
    $id => [
      'email' => $email,
      'senha' => $senha,
      'nome' => $nome,
      'sobrenome' => $sobrenome,
      'cpfcnpj' => $cpfcnpj,
      'rg' => $rg,
      'telefone' => $telefone,
      'celular' => $celular,
      'operadora' => $operadora,
      'apelido' => $apelido,
      'data_nascimento' => $data_nascimento,
      'sexo' => $sexo,
      'cidade' => $cidade,
      'uf' => $uf,
      'atacadista' => $atacadista,
      'atacadista_desconto' => $atacadista_desconto,
      'atacadista_min' => $atacadista_min,
      'atacadista_max' => $atacadista_max,
      'atacadista_text' => $atacadista_text,
      'status' => $status
    ]
  ]]);

  if (isset($POST['is_send']) && $POST['is_send'] != '') {
    $html = '';
    $html .= sprintf(''
      . '<tr><td>'
      . '<div style="text-align: center; font-size: 16px">%s</div>'
      . '</td></tr>', nl2br($POST['atacadista_text']));
    $CONTEUDO_MAIL = email_body($CONFIG, $html);

    $mail->setFrom($CONFIG['email_contato'], $CONFIG['nome_fantasia'] . ' - Venda no Atacado');
    $mail->addAddress($email, $nome);

    $mail->Subject = $nome . ', já análisamos seu cadastro';
    $mail->Body = $CONTEUDO_MAIL;

    $mail->send();
    $mail->SmtpClose();
  }

  // $mensagem .= @var_export($return, true);
  $mensagem = 'Dados alterados com sucesso.';
}

if (isset($POST['id_endereco']) && $POST['id_endereco'] > 0) {

  $id        = $POST['id_endereco'];
  $id_cliente    = $POST['id_cliente'];
  $nomeendereco   = $POST['nome'];
  $receber     = $POST['receber'];
  $endereco     = $POST['endereco'];
  $numero     = $POST['numero'];
  $bairro     = $POST['bairro'];
  $complemento  = $POST['complemento'];
  $referencia    = $POST['referencia'];
  $cidade     = $POST['cidade'];
  $estado     = trim(strtoupper($POST['uf']));
  $cep       = trim($POST['cep']);

  ClientesEnderecos::$validates_presence_of = [];
  ClientesEnderecos::$validates_format_of = [];

  ClientesEnderecos::update_all([
    'set' => 'status = null',
    'conditions' => sprintf('id_cliente=%u', $id_cliente)
  ]);

  $ClientesEnderecos = ClientesEnderecos::action_cadastrar_editar(['ClientesEnderecos' => [$id => [
    'nome' => $nomeendereco,
    'receber' => $receber,
    'endereco' => $endereco,
    'numero' => $numero,
    'complemento' => $complemento,
    'referencia' => $referencia,
    'bairro' => $bairro,
    'cidade' => $cidade,
    'uf' => $estado,
    'cep' => $cep,
    'status' => 'ativo',
  ]]], 'alterar', 'endereco');

  $GET['id'] = $id_cliente;

  if (!empty($ClientesEnderecos['id'])) {
    $mensagemEndereco[$id] = 'Endereço alterado com sucesso...';
  } else {
    $mensagemEndereco[$id] = 'Erro ao salvar o cadastro';
  }
}

$id_cliente = isset($GET['id']) && (int)$GET['id'] > 0 ? (int)$GET['id'] : 0;

$Clientes = Clientes::find($id_cliente);
?>
<style>
  body {
    background-color: #f1f1f1
  }
</style>

<div id="div-edicao" class="row">
  <?php echo isset($mensagem) ? "<b class='mt5 mb5 show clearfix w100'>$mensagem</b>" : ''; ?>
  <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
    <div class="panel panel-default">
      <div class="panel-heading panel-store text-uppercase">
        Dados do cliente
        <a href='#' class="btn btn-xs btn-warning pull-right" toggletext='toggletext' data-id="dadoscliente<?php echo $Clientes->id ?>" <?php echo _P('clientes-analisar', $_SESSION['admin']['id_usuario'], 'alterar') ?>>
          alterar
        </a>
      </div>
      <div class="panel-body">
        <form id="dadoscliente<?php echo $Clientes->id ?>" method="post">
          <input type="hidden" name="id_cliente" value="<?php echo $Clientes->id ?>" disabled="disabled">
          <table width='100%' cellpadding='5' align='left'>
            <tr>
              <td align='right' nowrap='nowrap' width='1%'>Nome:</td>
              <td width='30%'><input type="text" name="nome" class="input-text w100" disabled="disabled" value="<?php echo $Clientes->nome; ?>" /></td>
            </tr>
            <tr>
              <td class='column' align='right' nowrap='nowrap' width='1%'>E-mail:</td>
              <td colspan="1"><input type="text" name="email" class="input-text w100" disabled="disabled" value="<?php echo $Clientes->email; ?>" /></td>
            </tr>
            <tr>
              <td class='column' align='right' nowrap='nowrap' width='1%'>Sexo:</td>
              <td colspan="1"><input type="text" name="sexo" class="input-text w100" disabled="disabled" value="<?php echo $Clientes->sexo; ?>" /></td>
            </tr>
            <tr>
              <td class='column' align='right' nowrap='nowrap' width='1%'>CPF:</td>
              <td colspan="1"><input type="text" name="cpfcnpj" class="input-text w100 input-cpfcnpj" disabled="disabled" value="<?php echo $Clientes->cpfcnpj; ?>" maxlength="21" /></td>
            </tr>
            <tr>
              <td class='column' align='right' nowrap='nowrap' width='1%'>RG:</td>
              <td colspan="1"><input type="text" name="rg" class="input-text w100" disabled="disabled" value="<?php echo $Clientes->rg; ?>" /></td>
            </tr>
            <tr>
              <td class='column' align='right' nowrap='nowrap' width='1%'>Nascimento:</td>
              <td colspan="1"><input type="text" name="data_nascimento" class="input-text w100 input-nascimento" disabled="disabled" value="<?php echo $Clientes->data_nascimento; ?>" /></td>
            </tr>
            <tr>
              <td class='column' align='right' nowrap='nowrap' width='1%'>Telefone:</td>
              <td colspan="1"><input type="text" name="telefone" class="input-text w100 input-telefones" disabled="disabled" value="<?php echo $Clientes->telefone; ?>" /></td>
            </tr>
            <tr>
              <td class='column' align='right' nowrap='nowrap' width='1%'>Celular:</td>
              <td>
                <input type="text" name="celular" class="input-text w55 input-telefones" disabled="disabled" value="<?php echo $Clientes->celular; ?>" />
                <input type="text" name="operadora" class="input-text w40" disabled="disabled" value="<?php echo $Clientes->operadora; ?>" />
              </td>
            </tr>
            <tr>
              <td class='column' align='right' nowrap='nowrap' width='1%'>ALTERA A SENHA:</td>
              <td>
                <input type="text" name="senha" class="input-text w100" disabled="disabled" />
                <font class='ft11px show'>Deixando em branco permanecerá a senha atual</font>
              </td>
            </tr>

            <tr>
              <td class='column bold' align='left' colspan="2">OPÇÕES</td>
            </tr>
            <tr>
              <td class='column' align='right' nowrap='nowrap' width='1%'>Status:</td>
              <td>
                <select name="excluir" class="input-text w100" disabled="disabled">
                  <option value="0" <?php echo $Clientes->excluir == 0 ? 'selected' : '' ?>>Ativo</option>
                  <option value="1" <?php echo $Clientes->excluir == 1 ? 'selected' : '' ?>>Inativo</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class='column bold' align='left' colspan="2">INDICAÇÃO DO CLIENTE</td>
            </tr>
            <tr>
              <td align='center' colspan="2"><?php echo $Clientes->indicacao->indicacao; ?></td>
            </tr>
            <tr>
              <td align='center' colspan="2"><?php echo $Clientes->indicacao->outros; ?></td>
            </tr>
          </table>
        </form>
      </div>
    </div>
  </div>
  <?php

  $pedidos_count = (int)count($Clientes->pedidos);
  $pedidos_prod = 0;
  $pedidos_venda = 0;
  $TOTAL = null;
  foreach ($Clientes->pedidos as $rped) {
    $pedidos_sum_price = 0;
    foreach ($rped->pedidos_vendas as $rped_v) {
      $pedidos_prod += $rped_v->quantidade;
      $pedidos_sum_price += ($rped_v->valor_pago * $rped_v->quantidade);
    }
    $TOTAL = valor_pagamento($pedidos_sum_price, $rped->frete_valor, $rped->desconto_cupom, '$', $rped->desconto_boleto);
    $TOTAL_BOLETO += $TOTAL['TOTAL_COMPRA_C_BOLETO'];
  }
  ?>
  <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
    <div class="panel panel-default">
      <div class="panel-heading panel-store text-uppercase">MÉTRICA</div>
      <div class="panel-body">
        <div class="row">
          <div class="alert-success text-center col-md-4 col-xs-12">
            <strong class="show ft50px"><?php echo $pedidos_count ?></strong>
            <small class="mb15 show">Pedidos</small>
          </div>
          <div class="alert-info text-center col-md-4 col-xs-12">
            <strong class="show ft50px"><?php echo $pedidos_prod ?></strong>
            <small class="mb15 show">Produtos</small>
          </div>
          <div class="alert-warning text-center col-md-4 col-xs-12">
            <strong class="show ft50px"><?php echo !empty($Clientes->created_at) ? $Clientes->created_at->format('d/m') : '--' ?></strong>
            <small class="mb15 show">Cliente - <?php echo !empty($Clientes->created_at) ? $Clientes->created_at->format('Y') : '--' ?></small>
          </div>
          <div class="alert-danger text-center col-md-12 col-xs-12">
            <strong class="show ft50px">R$: <?php echo number_format($TOTAL_BOLETO, 2, ',', '.') ?></strong>
            <small class="mb15 show">Total de Compras</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="row">
      <?php
      $x = 1;
      foreach ($Clientes->enderecos as $rF) { ?>
        <form id="endereco<?php echo $rF->id ?>" method="POST" class="col-md-6 col-xs-12">
          <input type="hidden" name="id_endereco" value="<?php echo $rF->id ?>" disabled="disabled" />
          <div class="panel panel-default">
            <div class="panel-heading panel-store text-uppercase">
              Endereço: <?php echo strtoupper($rF->nome) ?>
              <a href='#' class="btn btn-xs btn-warning pull-right" toggletext='toggletext' data-id="endereco<?php echo $rF->id ?>" <?php echo _P('clientes-analisar', $_SESSION['admin']['id_usuario'], 'alterar') ?>>alterar</a>
              <?php echo $rF->status == 'ativo' ? sprintf('<span class="pull-right btn btn-xs btn-danger mr5">%s</span>', 'ATIVO') : null; ?>
            </div>
            <div class="panel-body">
              <?php echo isset($mensagemEndereco[$rF->id]) ? "<b class='mt5 mb5'>{$mensagemEndereco[$rF->id]}</b>" : ''; ?>
              <table width="100%" cellpadding="5" align="left" class="mt5">
                <tr>
                  <td class='column clearfix'>
                    <p>Quem irá receber?:</p>
                    <input type="text" name="receber" class="input-text w75" disabled="disabled" value="<?php echo $rF->receber; ?>" />
                    <input type="hidden" name="nome" class="hidden" disabled="disabled" value="<?php echo $rF->nome; ?>" />
                    <input type="hidden" name="id_cliente" class="hidden" disabled="disabled" value="<?php echo $rF->id_cliente; ?>" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p>Endereço:</p>
                    <input type="text" name="endereco" class="input-text w75" disabled="disabled" value="<?php echo $rF->endereco; ?>" />
                    <input type="text" name="numero" value="<?php echo $rF->numero; ?>" class="input-text w20" disabled="disabled">
                  </td>
                </tr>
                <tr>
                  <td>
                    <p>Bairro:</p>
                    <input type="text" name="bairro" value="<?php echo $rF->bairro; ?>" class="input-text w100" disabled="disabled" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p>Complemento:</p>
                    <input type="text" name="complemento" value="<?php echo $rF->complemento != '' ? $rF->complemento : null; ?>" class="input-text w100" disabled="disabled" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p>Refêrencias:</p>
                    <input type="text" name="referencia" value="<?php echo $rF->referencia != '' ? $rF->referencia : null; ?>" class="input-text w100" disabled="disabled" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p>Cidade/UF:</p>
                    <input type="text" name="cidade" id="cidadecep<?php echo $rF->id ?>" value="<?php echo $rF->cidade; ?>" class="input-text w75" disabled="disabled" />
                    <input type="text" name="uf" id="ufcep<?php echo $rF->id ?>" value="<?php echo $rF->uf; ?>" class="input-text w20" disabled="disabled" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <p>CEP:</p>
                    <input type="text" name="cep" value="<?php echo $rF->cep; ?>" id="cep<?php echo $rF->id ?>" class="input-text w55" disabled="disabled" />
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </form>
        <?php echo (($x % 2) == 0) ? '<div class="col-md-12 mt15"></div>' : '' ?>
      <?php $x++;
      } ?>
    </div>
  </div>
  <style type="text/css">
    .input-text,
    .input-text:disabled {
      border: none;
      background-color: #fff !important;
    }

    .input-text-edit {
      border: solid 1px #ccc;
      background-color: #fff !important;
    }

    #conteudos-filho p {
      margin: 0;
      line-height: 17px;
    }
  </style>

  <script>
    $.fn.toggleText = function(t1, t2) {
      if (this.text() === t1)
        this.text(t2);
      else
        this.text(t1);
      return this;
    };

    $.fn.toggleAttr = function(a, b) {
      var c = (b === undefined);
      return this.each(function() {
        if ((c && !$(this).is("[" + a + "]")) || (!c && b)) $(this).attr(a, a);
        else $(this).removeAttr(a);
      });
    };

    $('[toggletext=toggletext]').click(function(e) {
      var even = $(this),
        dataId = even.data('id');

      if (even.text() === 'Salvar') {
        var dataStr = $("#" + dataId).serialize();
        $.ajax({
          url: "/adm/clientes/clientes-analisar.php?acao=analisar&id=<?php echo $GET['id']; ?>",
          type: "post",
          data: dataStr,
          dataType: "html",
          success: function(str) {
            var list = $("<div/>", {
              html: str
            });
            console.log(list.find("#div-edicao").html());
            JanelaModal.html(list.find("#div-edicao").html());
          },
          error: function(x, t, m) {
            console.log(x.responseText + '\\n' + t + '\\n' + m);
          }
        });
      }

      $(this).toggleText('Salvar', 'Alterar');

      $.each($('#' + dataId + ' input, #' + dataId + ' select, #' + dataId + ' textarea'), function(index, el) {
        $(el).toggleClass('input-text input-text-edit');
        $(el).toggleAttr('disabled');
      });

      e.preventDefault();
    });
  </script>
</div>
<?php
include '../rodape.php';

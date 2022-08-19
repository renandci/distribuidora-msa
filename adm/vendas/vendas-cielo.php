<?php 
use Cielo\API30\Merchant;
use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Request\CieloRequestException;

include '../topo.php';

$pedido_id = filter_input(INPUT_GET, 'pedido_id', FILTER_SANITIZE_NUMBER_INT);
$motivos = filter_input(INPUT_GET, 'motivos', FILTER_SANITIZE_STRING);
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_NUMBER_INT);
$acao = filter_input(INPUT_GET, 'acao', FILTER_SANITIZE_STRING);

$Pedido = Pedidos::find( $pedido_id );

// Configure o ambiente
$environment = $CONFIG['cielo_mode'] == '1' ? Environment::production() : Environment::sandbox();

// Configure seu merchant
$merchant = new Merchant($CONFIG['cielo_merchantid'], $CONFIG['cielo_merchantkey']);

// Pegar as informacoes do pagamento
$StatusSale = (new CieloEcommerce($merchant, $environment))->getSale( $Pedido->pedido_transacao->cielo_paymentid );

?>
<div id="checkout-cielo">
   
    <div class="clearfix mb15">
        <div class="plano-fundo-adm-001 cor-branco ft16px mb5" style="padding: 5px;">Dados do comprador - TID: <?php echo $Pedido->pedido_transacao->cielo_tid?></div>
        <div class="row">
           <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
                <p class="ml15">
					Cielo TID: <?php echo $StatusSale->getPayment()->getTid()?><br/>
					Cielo Pagamento ID: <?php echo $StatusSale->getPayment()->getPaymentId()?>
				</p>
                <span class="show ml15">E-mail: <?php echo $StatusSale->getCustomer()->getEmail()?></span>
                <span class="show ml15">Data nasc.: <?php echo date('d / m / Y', strtotime($StatusSale->getCustomer()->getBirthDate()))?></span>
                <span class="show ml15"><?php echo ( CieloMensagensErros::getStatus( $StatusSale->getPayment()->getStatus() ) ); ?></span>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 text-center">
                <button type="button" class="btn btn-cielo btn-block mb5" id="cielo-capture">capturar</button>
                <button type="button" class="btn btn-danger btn-block" id="cielo-cancela">cancelar</button>
            </div>
        </div>
        <div class="clearfix text-center mt5" id="recarregar-infos">
            <span class="plano-fundo-adm-001 show cor-branco ft16px mb5" style="padding: 5px;">Status do Pagamento</span>
            <?php
            switch ($acao)
            {
                // Tenta capturar o pagamento
                case 'CapitureSale':
                    try {
                        // inicia o servico
                        $CaptureSale = (new CieloEcommerce($merchant, $environment))->captureSale( $StatusSale->getPayment()->getPaymentId() );

                        // Pegar o novo status do pagamento atual
                        $NewStatusSale = (new CieloEcommerce($merchant, $environment))->getSale( $Pedido->pedido_transacao->cielo_paymentid );

                        $Descricao = CieloMensagensErros::getStatus( $NewStatusSale->getPayment()->getStatus() );
                        $Status = CieloMensagensErros::getStatusSite( $NewStatusSale->getPayment()->getStatus() );
                    } 
                    catch (CieloRequestException $e) {
                        $Descricao = ( ( new CieloMensagensErros() )->getMensagem( $e->getCieloError()->getCode() ) );
                        $Status = 10;
                    }
                    PedidosLogs::action_cadastrar_editar([
                        'PedidosLogs' => [
                            0 => [
                                'id_pedido' => $pedido_id,
                                'id_adm' => $_SESSION['admin']['id_usuario'],
                                'data_envio' => date('Y-m-d H:i:s'),
                                'descricao' => $Descricao,
                                'status' => $Status,
                            ]
                        ] 
                    ], 'cadastrar', '');
                    $Pedido->motivos = $motivos;
                    $Pedido->status = $Status;
                    $Pedido->save();
                break;
                // Tenta cancelar o pagamento
                case 'CancelSale' :
                    try {    
                        // Cancela o pagamento atual
                        $CaptureSale = (new CieloEcommerce($merchant, $environment))->cancelSale( $StatusSale->getPayment()->getPaymentId() );

                        // Pegar o novo status do pagamento atual
                        $NewStatusSale = (new CieloEcommerce($merchant, $environment))->getSale( $Pedido->pedido_transacao->cielo_paymentid );

                        $Descricao = CieloMensagensErros::getStatus( $NewStatusSale->getPayment()->getStatus() );
                        $Status = CieloMensagensErros::getStatusSite( $NewStatusSale->getPayment()->getStatus() );
                        
                    } catch (CieloRequestException $e) {

                        $Descricao = ((new CieloMensagensErros())->getMensagem( $e->getCieloError()->getCode() )) . "<br/>Motivos: {$motivos}";
                        $Status = 10;
                    }
                    PedidosLogs::action_cadastrar_editar([
                        'PedidosLogs' => [
                            0 => [
                                'id_pedido' => $pedido_id,
                                'id_adm' => $_SESSION['admin']['id_usuario'],
                                'data_envio' => date('Y-m-d H:i:s'),
                                'descricao' => $Descricao,
                                'status' => $Status,
                            ]
                        ] 
                    ], 'cadastrar', '');
                    $Pedido->motivos = $motivos;
                    $Pedido->status = $Status;
                    $Pedido->save();
                break;
                default :
                    
                break;
            }
//           echo  CieloMensagensErros::getStatusSite( $StatusSale->getPayment()->getStatus() );
//            echo ( CieloMensagensErros::getStatusTransacaoSandbox( $StatusSale->getPayment()->getReturnCode() ) ); 
           echo $StatusSale->getPayment()->getStatus() ? CieloMensagensVendas::getMensagem($StatusSale->getPayment()->getStatus()) : 'Não foi possivel ver os status atual';
           ?>
        </div>
    </div>
    
    <div class="clearfix mb15">
        <span class="plano-fundo-adm-001 show cor-branco ft16px mb5" style="padding: 5px;">Dados do endereço de entrega</span>
        <?php if($StatusSale->getCustomer()->getAddress()->getStreet()) : ?>
        <span class="show ml15">
            Endereço: <?php echo $StatusSale->getCustomer()->getAddress()->getStreet()?>, <?php echo $StatusSale->getCustomer()->getAddress()->getNumber()?>
        </span>
        <?php endif; ?>
        <span class="show ml15">CEP: <?php echo $StatusSale->getCustomer()->getAddress()->getZipCode()?></span>
        <span class="show ml15">
            Cidade/Uf: <?php echo $StatusSale->getCustomer()->getAddress()->getCity()?>/<?php echo $StatusSale->getCustomer()->getAddress()->getState()?>
        </span>
    </div>

    <div class="clearfix mb15">
        <span class="plano-fundo-adm-001 show cor-branco ft16px mb5" style="padding: 5px;">Dados do Pagamento</span>
        <span class="show ml15">TOTAL: R$: <?php echo number_format(($StatusSale->getPayment()->getAmount()/100), 2, ',', '.')?></span>
        <span class="show ml15">Parcelas: <?php echo $StatusSale->getPayment()->getInstallments()?>x</span>
        <span class="show ml15">Número do Cartão: <?php echo $StatusSale->getPayment()->getCreditCard()->getCardNumber()?></span>
        <span class="show ml15">Titular do Cartão: <?php echo $StatusSale->getPayment()->getCreditCard()->getHolder()?></span>
        <span class="show ml15">Validade: <?php echo $StatusSale->getPayment()->getCreditCard()->getExpirationDate()?></span>
        <span class="show ml15">Bandeira: <?php echo $StatusSale->getPayment()->getCreditCard()->getBrand()?></span>
        <span class="show ml15">Gerado: <?php echo date('d/m/Y H:i', strtotime($StatusSale->getPayment()->getReceivedDate()))?></span>
    </div>
</div>

<?php 
include '../rodape.php';
<?php

/**
 * Se não hover session do clientes, o mesmo será redirecionado para login
 */
if (!isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] == '') {
  header('Location: /identificacao/login?_u=' . URL_BASE . 'identificacao/paga-novamente/?pedido=' . $GET['pedido']);
  return;
?>
  <h2 class="text-center mt35 mb50">Aguarde so um momento...</h2>
  <p class="mb50 text-center">Estamos redirecionando você para realizar o login!</p>
  <script type="text/javascript">
    window.location.href = "/identificacao/login?_u=<?php echo URL_BASE ?>identificacao/paga-novamente/?pedido=<?php echo $GET['pedido'] ?>"
  </script>

<?php
} else if (!empty($_SESSION['cliente']['id_cliente']) && Clientes::count(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]) > 0) {
  /**
   * Buscar o pedido e inseri os produtos no carrinho de compras
   */
  $count = 0;
  $Pedidos = Pedidos::all(['conditions' => ['id=?', (int)$GET['pedido']]]);

  foreach ($Pedidos as $pedidos_vendas) {
    foreach ($pedidos_vendas->pedidos_vendas as $values) {
      $attributes = [
        'id_produto' => $values->id_produto,
        'quantidade' => $values->quantidade,
        'id_session' => session_id(),
        'pedidos_id' => $values->id_pedido,
        'personalizado' => $values->personalizado
      ];
      if (Carrinho::count(['conditions' => $attributes]) == 0) {
        Carrinho::create($attributes);
      }
    }
    // $pedidos_vendas->excluir = 0;
    // if( $pedidos_vendas->save() ){
    // $count++;
    // }
    $count++;
  }

  if ($count > 0) {
    header('Location: /identificacao/carrinho');
    return;
  } else {
    header('Location: /identificacao/carrinho');
    return;
  }
} else { ?>
  <h2 class='text-center mt35 mb50'>Desculpe algo deu errado!</h2>
<?php }

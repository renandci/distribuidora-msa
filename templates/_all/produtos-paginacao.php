<!--[ PAGINAÇÃO ]-->
<li class="cx-fitros-produtos col-lg-12 col-md-12 col-sm-12 col-xs-12">
  <div>
    <span class="total-produtos">
      TOTAL: <?php echo $TotalProdutos; ?>
    </span>
    <?php
    // Apenas limpa a string com dois elementos de $_GET 'pag'
    // $GET_FILTER = preg_replace('/&pag=[^&]*/', '', $GET_FILTER);

    // Apenas limpa a string com dois elementos de $_GET 'pag'
    // $GET_FILTER = preg_replace('/([a-z_&])\1+/', '$1', $GET_FILTER);
    $GET_FILTER = preg_replace('/pag=[^&]*/', '', $GET_FILTER);
    $GET_FILTER = ltrim($GET_FILTER, '&');
    ?>
    <span class="paginacao-site">
      <span class="hidden paginacao" href="/produtos<?php echo $GET_PAGINACAO ?>?<?php echo $GET_FILTER ?>"></span>

      <?php if ($ProdutosTotal > 0) { ?>

        <?php if ($pag > 1) { ?>
          <a href="/produtos<?php echo $GET_PAGINACAO ?>?<?php echo $GET_FILTER ?>pag=<?php echo ($pag - 1) ?>" class="fa fa-chevron-left" ajax></a>
        <?php } ?>

        <?php for ($i = $pag - 2, $limiteDeLinks = $i + 4; $i <= $limiteDeLinks; ++$i) {
          if ($i < 1) {
            $i = 1;
            $limiteDeLinks = 3;
          }

          if ($limiteDeLinks > $ProdutosTotal) {
            $limiteDeLinks = $ProdutosTotal;
            $i = $limiteDeLinks - 4;
          }

          if ($i < 1) {
            $i = 1;
            $limiteDeLinks = $ProdutosTotal;
          }
        ?>

          <?php if ($i == $pag) { ?>
            <span class="semcor"><?php echo $i ?></span>
          <?php } else { ?>
            <a href="/produtos<?php echo $GET_PAGINACAO ?>?<?php echo $GET_FILTER ?>pag=<?php echo $i ?>" ajax><?php echo $i ?></a>
          <?php } ?>

        <?php } ?>

        <?php if ($pag != $ProdutosTotal) { ?>
          <a href="/produtos<?php echo $GET_PAGINACAO ?>?<?php echo $GET_FILTER ?>pag=<?php echo ($pag + 1) ?>" class="fa fa-chevron-right" ajax></a>
        <?php } ?>

      <?php } ?>
    </span>

    <span class="ordem-produtos" for="filter_price">
      <select name="filter_price" id="filter_price">
        <option value="">Todos</option>
        <option value="data" <?php echo $GET['preco'] == 'data' ? ' selected' : ''; ?>>Últimos Lançamentos</option>
        <option value="asc" <?php echo $GET['preco'] == 'asc' ? ' selected' : ''; ?>>Menor Preço</option>
        <option value="desc" <?php echo $GET['preco'] == 'desc' ? ' selected' : ''; ?>>Maior Preço</option>
      </select>
    </span>
  </div>
</li>
<!--[ END PAGINAÇÃO ]-->

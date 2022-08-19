<ul class="clearfix">
  <li class="mb15 produtos-menus">
    <span class="menus-lateral-title font-bold">CATEGORIAS</span>
    <div class="menus-lateral-tab" style="height: 350px;">
      <?php
      if (count($menus['grupos']) > 0) {
        foreach ($menus['grupos'] as $gi => $rTopo) {
          if (count($rTopo['subgrupos']) > 0) {
            foreach ($rTopo['subgrupos'] as $sgi => $rSubM) {
              if ($rSubM['subgrupos_id'] > 0) { ?>
                <a ajax class="ml15 mb5 show<?php echo $rSubM['subgrupos_id'] == $GET_ID_SUB_GRUPO ? ' font-bold' : ''; ?>" href="/produtos/<?php echo converter_texto($rTopo['grupo']) ?>/<?php echo $rTopo['grupo_id'] ?>/<?php echo converter_texto($rSubM['subgrupos']) ?>/<?php echo $rSubM['subgrupos_id']; ?>">
                  <?php echo $rSubM['subgrupos']; ?>
                </a>
      <?php
              }
            }
          }
        }
      }
      ?>
    </div>
  </li>
</ul>

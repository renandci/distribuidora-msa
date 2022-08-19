<?php if ($MobileDetect->isMobile()) { // Menus para dispositivos Mobile
?>

  <?php
  // echo ' teste batendo 123';
  // die();
  // Deixa apenas o menus que foi selecionado
  if (!empty($GET['grupo'])) {
    $all = $menus;
    unset($menus);
    $array = explode(',', str_replace(['[', ']'], '', $GET['grupo']));
    foreach ($array as $add)
      $menus['grupos'][$add] = $all['grupos'][$add];
  }

  // print_r($menus['grupos']);
  // die();

  foreach ($menus['grupos'] as $gi => $grupo) { ?>

    <li class="produtos-menus mb15">
      <div class="menus-lateral-title">
        <!-- <input type="checkbox" name="filtro[grupo][]" value="<?php echo $grupo['grupo_id'] ?>" id="g_<?php echo $gi ?>" <?php echo checked('[' . implode(',', explode(',', str_replace(array('[', ']'), '', $GET['grupo']))) . ']', $grupo['grupo_id']) ? 'checked' : '' ?>/>
				<label for="g_<?php echo $gi ?>"><?php echo $grupo['grupo'] ?></label> -->
        <a href="/produtos/<?php echo converter_texto($grupo['grupo']) ?>/<?php echo $grupo['grupo_id'] ?>" class="<?php echo $grupo['grupo_id'] == $GET_ID_GRUPO ? 'bold' : ''; ?>">
          <?php echo $grupo['grupo'] ?>
        </a>
      </div>
      <?php if (!empty($grupo['subgrupos'])) { ?>
        <div class="menus-lateral-tab mb5" style="height: auto; max-height: 350px; overflow: auto;">
          <?php foreach ($grupo['subgrupos'] as $sgi => $subgrupo) { ?>
            <!-- <input type="checkbox" name="filtro[subgrupo][]" value="<?php echo $subgrupo['subgrupos_id'] ?>" id="sg_<?php echo $gi . $sgi ?>" <?php echo checked('[' . implode(',', explode(',', str_replace(array('[', ']'), '', $GET['subgrupo']))) . ']', $subgrupo['subgrupos_id']) ? 'checked' : '' ?> data-check="g_<?php echo $gi ?>"/>
					<label for="sg_<?php echo $gi . $sgi ?>"><?php echo $subgrupo['subgrupos'] ?></label> -->

            <a href="/produtos/<?php echo converter_texto($grupo['grupo']) ?>/<?php echo $grupo['grupo_id'] ?>/<?php echo converter_texto($subgrupo['subgrupos']) ?>/<?php echo $subgrupo['subgrupos_id']; ?>" class="ml15 mb5 show<?php echo $subgrupo['subgrupos_id'] == $GET_ID_SUB_GRUPO ? ' font-bold' : ''; ?>" ajax>
              <?php echo $subgrupo['subgrupos'] ?>
            </a>
          <?php } ?>
        </div>
      <?php } ?>
    </li>

  <?php } ?>

  <?php } else { // Menus para dispositivos Desktops
  if (count($menus['grupos']) > 0) { ?>
    <?php if (!empty($GET_ID_GRUPO) && $GET_ID_GRUPO > 0) { ?>
      <li class="produtos-menus mb15<?php echo empty($menus['grupos'][$GET_ID_GRUPO]['subgrupos']) ? ' hidden' : '' ?>">
        <span class="menus-lateral-title">CATEGORIAS</span>
        <div class="menus-lateral-tab" style="height: auto; max-height: 350px; overflow: auto;">
          <?php foreach ($menus['grupos'][$GET_ID_GRUPO]['subgrupos'] ?? [] as $sgi => $rs_subm) { ?>
            <?php if ($rs_subm['subgrupos_id'] > 0) { ?>
              <a href="/produtos/<?php echo converter_texto($menus['grupos'][$GET_ID_GRUPO]['grupo']) ?>/<?php echo $menus['grupos'][$GET_ID_GRUPO]['grupo_id'] ?>/<?php echo converter_texto($rs_subm['subgrupos']) ?>/<?php echo $rs_subm['subgrupos_id']; ?>" class="ml15 mb5 show<?php echo $rs_subm['subgrupos_id'] == $GET_ID_SUB_GRUPO ? ' bold' : ''; ?>">
                <?php echo $rs_subm['subgrupos']; ?>
              </a>
            <?php } ?>
          <?php } ?>
        </div>
      </li>
    <?php } else { ?>
      <?php foreach ($menus['grupos'] as $si => $gr) { ?>
        <li class="produtos-menus mb15">
          <span class="menus-lateral-title">
            <a href="/produtos/<?php echo converter_texto($gr['grupo']) ?>/<?php echo $gr['grupo_id'] ?>" class="<?php echo $gr['grupo_id'] == $GET_ID_GRUPO ? 'bold' : ''; ?>">
              <?php echo $gr['grupo'] ?>
            </a>
          </span>
          <div class="menus-lateral-tab" style="height: auto; max-height: 350px; overflow: auto;">
            <?php
            foreach ($gr['subgrupos'] ?? [] as $sgi => $rgs) { ?>
              <a href="/produtos/<?php echo converter_texto($gr['grupo']) ?>/<?php echo $gr['grupo_id'] ?>/<?php echo converter_texto($rgs['subgrupos']) ?>/<?php echo $rgs['subgrupos_id']; ?>" class="ml15 mb5 show<?php echo $rgs['subgrupos_id'] == $GET_ID_SUB_GRUPO ? ' font-bold' : ''; ?>" ajax>
                <?php echo $rgs['subgrupos'] ?>
              </a>
            <?php } ?>
          </div>
        </li>
      <?php } ?>
    <?php } ?>
<?php
  }
}

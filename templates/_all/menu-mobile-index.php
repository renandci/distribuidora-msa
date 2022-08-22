<?php
// Menus para dispositivos Mobile
if ($MobileDetect->isMobile()) { ?>
  <h4 class="white text-center" style="padding: 7px;background-color: #002e5d!important;">Produtos</h4>
  <div class="row menu-mobile-index">
    <?php
    // Deixa apenas o menus que foi selecionado
    if (!empty($GET['grupo'])) {
      $all = $menus;
      unset($menus);
      $array = explode(',', str_replace(['[', ']'], '', $GET['grupo']));
      foreach ($array as $add)
        $menus['grupos'][$add] = $all['grupos'][$add];
    }

    foreach ($menus['grupos'] as $gi => $grupo) { ?>
      <li class="col-xs-6 list-menu-index">
        <div class="">
          <a href="/produtos/<?php echo converter_texto($grupo['grupo']) ?>/<?php echo $grupo['grupo_id'] ?>" class="<?php echo $grupo['grupo_id'] == $GET_ID_GRUPO ? 'bold' : ''; ?>">
            <?php echo $grupo['grupo'] ?>
          </a>
        </div>
      </li>
    <?php } ?>
  </div>
<?php } ?>

<style>
  .list-menu-index {
    list-style: none;
    text-align: center;
    padding: 10px;
    background-color: #a8b5c3;
    color: white;
    border: 1px solid white;
  }
</style>

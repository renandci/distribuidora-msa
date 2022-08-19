<?php
$FILTROS = null;
$count_array_filter = @array_filter($filtros, 'is_not_null');
$count_array_filter = count($count_array_filter);
if ($count_array_filter > 0) { ?>
  <?php foreach ($filtros as $i => $filter) { ?>
    <!--[ FILTRO1S <?php echo strtoupper($i) ?> ]-->

    <?php foreach ($filter as $iii => $vll) { ?>
      <?php if (!empty($iii)) { ?>
        <li class="mb15 produtos-menus">
          <span class="menus-lateral-title font-bold">
            <?php echo strtoupper($iii) ?>
          </span>
          <div class="menus-lateral-tab">
            <?php foreach ($vll as $ii => $vl) { ?>
              <?php $keys = array_keys($vl); ?>
              <?php if ($FILTROS != $vl[$keys[0]]) { ?>
                <?php $FILTROS = $vl[$keys[0]]; ?>
                <?php if (!empty($FILTROS)) { ?>

                  <input type="checkbox" name="filtro[<?php echo converter_texto($i) ?>][]" value="<?php echo $vl[$keys[0]] ?>" id="e_<?php echo $ii . $iii ?>" <?php echo checked('[' . implode(',', explode(',', str_replace(array('[', ']'), '', $GET[$i]))) . ']', $FILTROS) ? 'checked' : '' ?> />
                  <label class="clearfix" for="e_<?php echo $ii . $iii ?>">
                    <?php if ($vl['hex1'] || $vl['hex2']) { ?>
                      <span style="background-color: #<?php echo $vl['hex1'] ?>">
                        <span style="border-bottom-color: #<?php echo $vl['hex2'] ?>"></span>
                      </span>
                      <?php print($vl[$keys[1]]); ?>
                    <?php } else { ?>
                      <?php print($vl[$keys[1]]); ?>
                    <?php } ?>
                  </label>

                <?php } ?>
              <?php } ?>
            <?php } ?>
          </div>
        </li>
      <?php } ?>
    <?php } ?>
    <!--[ END FILTROS <?php echo strtoupper($i) ?> ]-->
  <?php } ?>
<?php } ?>

<?php
/**
 * Definimos um novo header somente para os dispositivos mobile
 */
$head_all = sprintf('%s/templates/_layout/personalize/head-mobile-all.php', PATH_ROOT);

$head_personalize_template = sprintf('%s/templates/%s/personalize/head-mobile-%s.php', PATH_ROOT, ASSETS, ASSETS);

if( file_exists ( $head_personalize_template ) )
	require $head_personalize_template;
else
	require $head_all;

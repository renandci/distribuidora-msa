<?php
include realpath(__DIR__) . '/_layout/layout-header.php';
ob_start(); 
?>
<script>
	$("html").css({"height":"100%"});
	$("body").css({
		"background-image": "url(<?php echo Imgs::src('construcao.jpg', 'imgs')?>)",
		"background-position": "center center",
		"background-repeat": "no-repeat",
		// "background-color": "#fff",
		"height":"100%"
	});
</script>
<?php 
$str['script_manual'] .= ob_get_clean();
include realpath(__DIR__) . '/_layout/layout-footer.php';
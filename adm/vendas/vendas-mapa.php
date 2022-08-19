<?php
/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */
include '../topo.php';
?>
<div id="div-edicao" style='height:100%;width:100%;'>
    <?php 
    try {
		$get_contents = file_get_contents_utf8(sprintf('http://www.localizaip.com.br/api/iplocation.php?ip=%s', $GET['ip']));
		// $get_contents = file_get_contents(sprintf('http://www.localizaip.com.br/api/iplocation.php?ip=%s', $GET['ip']));
		$json = json_decode($get_contents, true); 
		
        $Pedidos = Pedidos::find($GET['id_pedido']);
		?>
        <div style='overflow:hidden;width:100%;min-height:400px;background-color: #f3f3f3;'>
			<div id='gmap_canvas' style='min-height:400px;width:100%;'></div>
            <style>#gmap_canvas img{max-width:none!important;background:none!important}</style>
        </div>
		<!--
		<script src='https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyCarGFF_WSsunQec6-H-yF9dPgh2kCL_dM'></script>
		-->
        <script>
            init_map = function (){
				
                var myOptions = {
                    zoom: 12,
                    center: new google.maps.LatLng("<?php echo dinheiro($json['latitude'])?>", "<?php echo dinheiro($json['longitude'])?>"),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById('gmap_canvas'), myOptions);
                marker = new google.maps.Marker({
                    map: map,
                    position: new google.maps.LatLng("<?php echo dinheiro($json['latitude'])?>", "<?php echo dinheiro($json['longitude'])?>")
                });
                infowindow = new google.maps.InfoWindow({
                    content:  '<strong><?php echo $Pedidos->pedido_cliente->nome;?></strong><br/>' +
                              'Endereço: <?php echo $Pedidos->pedido_endereco->endereco?> - <?php echo $Pedidos->pedido_endereco->numero;?><br/>' +
                              'Cidade/UF: <?php echo $Pedidos->pedido_endereco->cidade?>/<?php echo $Pedidos->pedido_endereco->uf;?><br/>' +
                              'CEP: <?php echo $Pedidos->pedido_endereco->cep;?>'
                });
                google.maps.event.addListener(marker, 'click', function(){
                    infowindow.open(map,marker);
                });
                infowindow.open(map,marker);

            };
            <?php echo ! isset( $GET['ip'] ) && $GET['ip'] == '' ? 'google.maps.event.addDomListener(window, "load", init_map);' :''; ?>
            init_map();
        </script>
    <?php } catch (Exception $ex) { ?>
        <h4>Desculpe!</h4>
        <p>Encontamos os seguintes erros: <b><?php echo $json->errorMsg;?></b></p>
        <p>Encontamos os seguintes erros: <b><?php echo $ex->getMessage();?></b></p>
    <?php } ?>
</div>
<?php
include '../rodape.php';
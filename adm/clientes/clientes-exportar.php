<?php
/**
 * Exporta os dados dos clientes
 */
if(isset($_POST['acao']) && $_POST['acao'] == 'ExportarClientes') 
{
    // Define o caminho base do aplicativo
    defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
    include PATH_ROOT . 'app/settings.php';
    include PATH_ROOT . 'app/vendor/autoload.php';
    include PATH_ROOT . 'app/settings-config.php';
    include PATH_ROOT . 'assets/' . ASSETS .  '/settings.php';
    
    // Create new PHPExcel object
    $PHPExcel = new PHPExcel();

//    // Set document properties
    $PHPExcel->getProperties()
                ->setCreator($CONFIG['nome_fantasia'])
                    ->setLastModifiedBy($CONFIG['nome_fantasia'])
                    ->setTitle("Office 2007 XLSX Document")
                    ->setSubject("Office 2007 XLSX Document")
                    ->setDescription("Documneto de clientes")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("TABELA DE CLIENTES");
    
    $col = 0;
    $row = 1;
    $names = '';
    /**
     * Define os nomes dos campos 
     */
    foreach ( $POST['Clientes'] as $keys => $vals ) {
        if( $names != $keys ) {
            $names = $keys;
            $PHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, str_replace('_', ' ', $names));
			// $PHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'value ' . str_replace('_', ' ', $names));
            $col++;
        }
    }
    
    $row = 2;
    $result = Clientes::all(['conditions' => ['id > 0 and loja_id=?', $CONFIG['loja_id']]]);
    foreach ( $result as $r ) {
        if( count($POST['Clientes'] > 0) ) {
            $col = 0;
            foreach ( $POST['Clientes'] as $keys => $vals ) {
                $PHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $r->{$keys});
				// $PHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'value ' . $r->{$keys});
                $col++;
            }
        }
        $row++;
    }
    
    // Rename worksheet
    $PHPExcel->getActiveSheet()->setTitle('Tabela de Clientes');
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $PHPExcel->setActiveSheetIndex(0);
    
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="clientes_' . date('d.m.Y') . '.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
    $objWriter->save('php://output');
    return;
}
include '../topo.php';
?>
<style>
	body{ background-color: #f1f1f1 }
</style>
<div id="conteudos-filho" class="container">
	<div class="panel panel-default">
    	<div class="panel-heading panel-store text-uppercase">EXPORTAR CLIENTE</div>
		<div class="panel-body">
			<p>Você pode definir na sua planilha, quais os dados dos cliente que apareceram.</p>
			<form class="clearfix" method="post" action="/adm/clientes/clientes-exportar.php" target="_blank">
				<div class="container">
					<div class="row">
						<div class="col-md-6">
							
							<div class="checkbox">
								<label>
									<input type="checkbox" name="Clientes[nome]" value="1" id="nome" checked="" readonly=""/>
									<span for="nome" class="input-checkbox"></span> Nome
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="Clientes[email]" value="1" id="email"/>
									<span for="email" class="input-checkbox"></span> E-mail
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="Clientes[cpfcnpj]" value="1" id="cpfcnpj"/>
									<span for="cpfcnpj" class="input-checkbox"></span> Cpf/Cnpj
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="Clientes[data_nascimento]" value="1" id="data_nascimento"/>
									<span for="data_nascimento" class="input-checkbox"></span> Data de Nascimento
								</label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="Clientes[sexo]" value="1" id="sexo"/>
									<span for="sexo" class="input-checkbox"></span> Sexo
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="Clientes[telefone]" value="1" id="telefone"/>
									<span for="telefone" class="input-checkbox"></span> Telefone
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="Clientes[celular]" value="1" id="celular"/>
									<span for="celular" class="input-checkbox"></span> Celular
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="Clientes[operadora]" value="1" id="operadora"/>
									<span for="operadora" class="input-checkbox"></span> Operadora de Celular
								</label>
							</div>
						</div>                
					</div>
					<hr/>
					<button type="submit" class="btn btn-primary">exportar</button>
				</div>
				<input type="hidden" name="acao" value="ExportarClientes"/>
			</form>
		</div>
	</div>
</div>

<?php
include '../rodape.php';
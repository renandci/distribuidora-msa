<?php
include '../topo.php';

switch($POST['acao']) 
{
	case 'btnacoes' :
        $logs = '';
		$codigo_id 		= (INT)$GET['codigo_id'];
		$id_grupo 		= (INT)$GET['id_grupo'];
		$id_subgrupo 	= (INT)$GET['id_subgrupo'];
		
		switch( $GET['opcao'] )
        {
			case '1' :
                /**
                 * Remove o grupo com os subgrupos do produto
                 */
                $ProdutosMenus = ProdutosMenus::all(['conditions' => ['codigo_id=? and id_grupo=?', $codigo_id, $id_grupo]]);
                foreach ( $ProdutosMenus as $rs ) {
                    $rs->delete_log(['id' => $rs->id]);
                }
			break;
			case '2' :
                /**
                 * Remove o grupo com os subgrupos do produto
                 */
                $Menus = ProdutosMenus::all(['conditions' => ['codigo_id=? and id_grupo=? and id_subgrupo=?', $codigo_id, $id_grupo, $id_subgrupo]]);
                foreach ( $Menus as $rs ) {
                    $rs->delete_log(['id' => $rs->id]);
                }
			break;
		}
	break;
	
	case 'adicionar-grupos':	
	case 'adicionar-subgrupos' :
		$logs='';
		$codigo_id 		= isset( $POST['codigo_id'] ) && $POST['codigo_id'] != '' ? $POST['codigo_id'] : '0';
		$id_grupo 		= isset( $POST['id_grupo'] ) && $POST['id_grupo'] != '' ? $POST['id_grupo'] : '0';
		$id_subgrupo 	= isset( $POST['id_subgrupo'] ) && $POST['id_subgrupo'] != '' ? $POST['id_subgrupo'] : '0';

		if( ProdutosMenus::find_num_rows('SELECT 1 FROM produtos_menus where codigo_id=? AND id_grupo=? and id_subgrupo=0', [$codigo_id, $id_grupo]) > 0 ) {
			try {
                $r = ProdutosMenus::query('' 
                        . 'REPLACE INTO produtos_menus ( id, loja_id, codigo_id, id_grupo, id_subgrupo ) ' 
                            . 'SELECT id, ?, ?, ?, ? FROM produtos_menus WHERE codigo_id=? AND id_grupo=? and id_subgrupo=0 ORDER BY id DESC', 
                                [$CONFIG['loja_id'], $codigo_id, $id_grupo, $id_subgrupo, $codigo_id, $id_grupo]);
                $result = ProdutosMenus::all(['order'=>'id desc', 'limit'=>1]);
                foreach ( $result as $rs ) {
                    $logs .= "Adicionou SubMenu {{$rs->subgrupo->subgrupo}} no Menu {{$rs->grupo->grupo}} ao Produto {{$rs->produto->nome_produto}}" . PHP_EOL;
                }
                Logs::create_logs($logs, $_SESSION['admin']['id_usuario'], 'insert', 'produtos_menus');
            } 
            catch (Exception $ex) {}
		} 
        else {
            try {
                ProdutosMenus::query(''
                                    . 'INSERT INTO produtos_menus (loja_id, codigo_id, id_grupo, id_subgrupo) values (?, ?, ?, ?)', 
                                        [$CONFIG['loja_id'], $codigo_id, $id_grupo, $id_subgrupo]);
                $result = ProdutosMenus::all(['order'=>'id desc', 'limit'=>1]);
                foreach ( $result as $rs ){
                    $logs .= "Adicionou Menu {{$rs->grupo->grupo}} ao Produto {{$rs->produto->nome_produto}}" . PHP_EOL;
                }
                Logs::create_logs($logs, $_SESSION['admin']['id_usuario'], 'insert', 'produtos_menus');
            } catch (Exception $ex) {
               
            }
		}
	break;
}

$CODIGO_ID = $GET['codigo_id'];
$conditions['order'] = 'ordem asc, grupo asc';
$conditions['conditions'] = sprintf('loja_id=%u AND excluir = 0 AND id NOT IN(select id_grupo from produtos_menus where produtos_menus.codigo_id=%u)', $CONFIG['loja_id'], $GET['codigo_id']);
?>
<div id="aba3">
	<div class="clearfix">
		<span class="pull-left w90">
			<p>SELECIONE OS MENUS:</p>
			<select id="id_grupo" class="w90">
				<option value="">Selecione um grupo</option>
				<?php $Grupos = Grupos::all($conditions);
				foreach ( $Grupos as $rsMenus ) { ?>
					<option value='<?php echo $rsMenus->id?>'><?php echo $rsMenus->grupo?></option>";
				<?php } ?>
			</select> 
			<a href="javascript:void(0);" class="btn fa fa-plus-square fa-1x" id="btn-adicionar-novoGrupo" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>></a>
			<a href="/adm/grupos.php?codigo_id=<?php echo $GET['codigo_id'];?>" class="btn fa fa-folder-open fa-1x" id="btn-cadastrar-grupos" <?php echo _P( 'grupos', $_SESSION['admin']['id_usuario'], 'acessar' )?>></a>
		</span>
	</div>
	<div class="clearfix mt10 mb10" id="carregar_menus">
        <table width="100%" cellpadding="8" cellspacing="1" border="0" bgcolor="f3f3f3">
            <tbody>
            <?php
            $group = 0;
            $conditions = null;
            $conditions['select'] = '*,'
                . 'grupos.grupo, '         
                . 'subgrupos.subgrupo';
            $conditions['joins'] = ['grupo', 'subgrupo'];
            $conditions['order'] = 'grupos.ordem asc, grupos.grupo asc, subgrupos.ordem asc, subgrupos.subgrupo asc';
            $conditions['conditions'] = sprintf('codigo_id=%s', $GET['codigo_id']);
            $ProdutosMenus = ProdutosMenus::all($conditions);
            
            foreach( $ProdutosMenus as $rws ) { ?>
                <?php if($group != $rws->id_grupo) { $group = $rws->id_grupo; ?>
                <tr class="plano-fundo-adm-001 cor-branco" id="grupos<?php echo $rws->id?>">
                    <td valign="middle" class="clearfix" width="100%">
                        <span class="pull-left mt5"><?php echo $rws->grupo?></span>
                
                        <a <?php echo _P('produtos-grupos-subgrupos', $_SESSION['admin']['id_usuario'], 'excluir')?> href="/adm/produtos/produtos-grupos-subgrupos.php?id_grupo=<?php echo $rws->id_grupo?>&opcao=1&codigo_id=<?php echo $GET['codigo_id'];?>" class="pull-right btn btn-danger btn-sm deletar_grupos" >remover</a>

                        <a <?php echo _P('sub-grupos', $_SESSION['admin']['id_usuario'], 'incluir' )?> href="/adm/sub-grupos.php?codigo_id=<?php echo $GET['codigo_id'];?>&id_grupo=<?php echo $rws->id_grupo?>" class="btn btn btn-success btn-sm mr5 btn-adicionar-subgrupos pull-right">adicionar</a>
                    </td>
                </tr>
                <?php } ?>

                <?php if($rws->id_subgrupo > 0) { ?>
                <tr id="subgrupos<?php echo $rws->id?>" bgcolor="ffffff">
                    <td valign="middle" class="clearfix">
                        <span class="pull-left mt5 ml35"><?php echo $rws->subgrupo?></span>
                        <a <?php echo _P('produtos-grupos-subgrupos', $_SESSION['admin']['id_usuario'], 'excluir')?> href="/adm/produtos/produtos-grupos-subgrupos.php?id_grupo=<?php echo $rws->id_grupo?>&id_subgrupo=<?php echo $rws->id_subgrupo?>&opcao=2&codigo_id=<?php echo $GET['codigo_id']?>" class="pull-right btn btn-warning btn-sm deletar_grupos">remover</a>
                    </td>
                </tr>
                <tr bgcolor="ffffff"><td></td></tr>
                <?php } ?>
		        <?php } ?>
            </tbody>
        </table>
	</div>
</div>
<?php
include '../rodape.php';
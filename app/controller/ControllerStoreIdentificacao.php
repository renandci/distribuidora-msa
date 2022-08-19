<?php


class ControllerStoreIdentificacao extends Store
{
	function __construct($view_pg) {
		parent::__construct($view_pg);
	}

	public function identificacao () {
		global $LOJA, $CONFIG, $STORE, $UA_INFO, $MobileDetect, $WebService, $settings, $str, $Images;
		$this->view($this->view_pg, [
			'LOJA' => $LOJA, 
			'STORE' => $STORE, 
			'CONFIG' => $CONFIG, 
			'UA_INFO' => $UA_INFO, 
			'MobileDetect' => $MobileDetect, 
			'WebService' => $WebService, 
			'Images' => $Images, 
			'settings' => $settings, 
			'str' => $str,
			'Produtos' => ProdutosViewsTemp::all([
				'conditions' => ['loja_id=?', $CONFIG['loja_id']],
				'group' => 'codigo_id, id_cor',
				'order' => 'rand(), estoque DESC', 
				'limit' => 12, 
			]),
			'ProdutosMaisVendidos' => ProdutosViewsTemp::all([
				'conditions' => ['loja_id=? AND EXISTS(SELECT pr.id, count(pv.id_produto) FROM produtos pr INNER JOIN pedidos_vendas pv ON pv.id_produto=pr.id WHERE view_produtos_all.id = pr.id GROUP BY 1)', $CONFIG['loja_id']],
				'group' => 'codigo_id, id_cor',
				'order' => 'rand(), estoque DESC', 
				'limit' => 12, 
			])
		]);
	}
	
	public final function run() {
		self::{$this->view_pg}();
	}
}
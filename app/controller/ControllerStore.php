<?php


class ControllerStore extends Store
{
	function __construct($view_pg) {
		parent::__construct($view_pg);
	}

	public function index () 
	{
		// global $LOJA, $CONFIG, $STORE, $UA_INFO, $MobileDetect, $WebService, $settings, $str, $Images;
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
	
	public function produtos(  ) 
	{
		// global $LOJA, $CONFIG, $STORE, $UA_INFO, $MobileDetect, $WebService, $settings, $str, $Images;
		
		$GET_NOME_GRUPO	 	= Url::getURL(1);
		$GET_ID_GRUPO 		= (INT)Url::getURL(2);
		$GET_NOME_SUB_GRUPO	= Url::getURL(3);
		$GET_ID_SUB_GRUPO 	= (INT)Url::getURL(4);

		$GET_PAGINACAO = $GET_ID_GRUPO > 0 ? '/'.$GET_NOME_GRUPO .'/'. $GET_ID_GRUPO : '';
		$GET_PAGINACAO = $GET_ID_SUB_GRUPO > 0 ? '/'.$GET_NOME_GRUPO.'/'.$GET_ID_GRUPO.'/'.$GET_NOME_SUB_GRUPO.'/'.$GET_ID_SUB_GRUPO : $GET_PAGINACAO;

		/**
		 * Converter a pesquisa dos filtro do sistema de busca
		 */
		$key_filter = null;
		$GET_FILTER = null;

		if( is_array( $GET['filtro'] ) ) 
		{
			foreach ( $GET['filtro'] as $key => $values ) 
			{
				if( $key_filter != $key )
					$GET[$key] .= '[';
				
				$GET[$key] .= implode(',', $values);
				
				if( $key_filter != $key )
					$GET[$key] .= ']';
			}
			unset($GET['filtro']);
			unset($GET['pag']);
			unset($GET['_']);
		}

		if ( is_array( $GET ) ) 
		{
			foreach ( $GET as $k => $v ) 
			{
				if ( ! in_array($k, $GET) ) 
				{
					$GET[ $k ] = queryInjection('%s', $v);
					$GET_FILTER .= queryInjection('%s=%s&', $k, $v);
				} 
				else 
				{
					$GET[ $k ] = queryInjection('%s', $v);
					$GET_FILTER = queryInjection('%s=%s&', $k, $v);
				}
			}

		}

		$Conditions = '';
		$ConditionsFilters = '';
		$Conditions['conditions'] = ' 1 = 1 ';
		$ConditionsFilters['conditions'] = ' 1 = 1 ';

		/**
		 * conditions de Pesquisa no site
		 */
		if (!empty($GET['pesquisar']) && $GET['pesquisar']!= '') 
		{
			$A = queryInjection('%%%s%%', $GET['pesquisar']);
			$B = implode('%" OR nome_produto like "%', 
					explode(' ', 
						queryInjection('%%%s%%', 
							str_replace( 
								array(' de', ' para', ' com', ' a', ' o', ' da'), '', $GET['pesquisar'] ) ) ) ) . '"';
			
			
			
			$Conditions['conditions'] .= 'AND(nome_produto like "%s" OR(nome_produto like "%s OR (codigo_produto like "%s"))) ';
			$Conditions['conditions'] = sprintf($Conditions['conditions'], $A, $B, $A);
			
			$ConditionsFilters['conditions'] .= 'AND(nome_produto like "%s" OR(nome_produto like "%s OR (codigo_produto like "%s"))) ';
			$ConditionsFilters['conditions'] = sprintf($ConditionsFilters['conditions'], $A, $B, $A);
		}

		/**
		 * conditions Grupos e Subgrupos
		 */
		if (isset($GET_ID_GRUPO, $GET_ID_SUB_GRUPO) && $GET_ID_SUB_GRUPO > 0 && $GET_ID_GRUPO > 0) {
			$Conditions['conditions'] .= 'AND id_grupo=%u AND id_subgrupo=%u ';
			$Conditions['conditions'] = queryInjection($Conditions['conditions'], $GET_ID_GRUPO, $GET_ID_SUB_GRUPO);
			
			$ConditionsFilters['conditions'] .= 'AND id_grupo=%u AND id_subgrupo=%u ';
			$ConditionsFilters['conditions'] = queryInjection($ConditionsFilters['conditions'], $GET_ID_GRUPO, $GET_ID_SUB_GRUPO);
		}

		/**
		 * conditions Grupos
		 */
		if (isset($GET_ID_GRUPO) && $GET_ID_SUB_GRUPO == 0 && $GET_ID_GRUPO > 0) {
			$Conditions['conditions'] .= ' AND id_grupo=%u ';
			$Conditions['conditions'] = queryInjection($Conditions['conditions'], $GET_ID_GRUPO);
			
			$ConditionsFilters['conditions'] .= ' AND id_grupo=%u ';
			$ConditionsFilters['conditions'] = queryInjection($ConditionsFilters['conditions'], $GET_ID_GRUPO);
		}

		/**
		 * conditions para Categoria (Generos)
		 */
		if ( ! empty( $GET['genero'] ) && $GET['genero'] != '' ) { 
			$loop_genero = null;
			$GET_GENERO = explode(',', str_replace([ '[', ']' ], null, $GET['genero']));
			foreach( $GET_GENERO as $V_GET_GENERO ){
				$loop_genero[] = checkCategoria($V_GET_GENERO);
			}
			// New genero
			$GET_GENERO = '[' . implode(',', $loop_genero) . ']';
			
			$Conditions['conditions'] .= sprintf('AND categoria like %s', implode('" or categoria like "', explode(',', str_replace([ '[', ']'], '"', $GET_GENERO))));
		}

		/**
		 * conditions para Busca de Cores
		 */
		if ( ! empty( $GET['cores'] ) && $GET['cores'] != '') { 
			$Conditions['conditions'] .= sprintf('AND nomecor like %s', implode('" or nomecor like "', explode(',', str_replace([ '[', ']'], '"', $GET['cores']))));
			// $ConditionsFilters['conditions'] .= sprintf('AND nomecor like %s', implode('" or nomecor like "', explode(',', str_replace([ '[', ']'], '"', $GET['cores']))));
		}

		/**
		 * conditions para Busca de Tamanhos
		 */
		if ( ! empty( $GET['tamanhos'] ) && $GET['tamanhos'] != '') { 
			$Conditions['conditions'] .= sprintf('AND nometamanho like %s', implode('" or nometamanho like "', explode(',', str_replace([ '[', ']'], '"', $GET['tamanhos']))));
		}

		/**
		 * conditions para Busca de Marcas
		if ( ! empty( $GET['marcas'] ) && $GET['marcas'] != '') { 
			$Conditions['conditions'] .= sprintf('AND marcas like %s', implode('" or marcas like "', explode(',', str_replace(['[', ']'], '"', $GET['marcas']))));
		}
		 */


		$Conditions['order'] = '';

		if( ! empty( $settings['config']['sql']['order'] ) ){
			$Conditions['order'] .= $settings['config']['sql']['order'];
		}
		else {
			$Conditions['order'] .= 'estoque DESC, id DESC';	
		}

		if(!empty($GET['preco']) && ($GET['preco'] === 'asc' || $GET['preco'] === 'desc')) {
			$Conditions['order'] = queryInjection('preco_promo %s, id desc, estoque desc ', $GET['preco']);
		}
		if(!empty($GET['preco']) && (($GET['preco'] !== 'asc' || $GET['preco'] !== 'desc') && $GET['preco'] === 'data')) {
			$Conditions['order'] = queryInjection('id desc ', $GET['preco']);
		}

		// Busca somente os dados da loja ativa no dominio principal
		$Conditions['conditions'] .= queryInjection('AND loja_id=%u ', $CONFIG['loja_id']);
		$ConditionsFilters['conditions'] .= queryInjection('AND loja_id=%u ', $CONFIG['loja_id']);

		// Configuração direto no arquivo para cada loja
		$Conditions['group'] = 'codigo_id';
		if( ! $STORE['product']['group'] || $settings['config']['sql']['group'] ) {
			$Conditions['group'] = 'codigo_id, id_cor';
		}

		$maximo = 18;	

		$pag = ! empty( $GET['pag'] ) && $GET['pag'] > 0 ? (INT)$GET['pag'] : 1;

		$inicio = (($pag * $maximo) - $maximo);

		$TotalProdutos = count( ProdutosViewsTemp::all( $Conditions ) );

		$ProdutosTotal = ceil( $TotalProdutos / $maximo );

		$Conditions['limit'] = $maximo;
		$Conditions['offset'] = ($maximo * ($pag - 1));

		/**
		 * Produtos
		 * @description Gerado uma camada no mysql com uma view
		 * @bkp $Produtos = Produtos::all( $Conditions );
		 */
		$Produtos = ProdutosViewsTemp::all( $Conditions );

		/**
		 * Filtros
		 * CORES|TAMANHOS|MARCAS|CATEGORIA
		 */
		$filtros = '';
		$ConditionsFilters['group'] = 'categoria';
		$ProdutosFiltersGeneros = ProdutosViewsTemp::all( $ConditionsFilters );
		foreach ($ProdutosFiltersGeneros as $rsFiltros) {   
			if ( ! empty( $rsFiltros->categoria ) ) {
				
				$filtros['genero']['generos'][] = [
					'id' => $rsFiltros->id,
					'categoria' => checkCategoria($rsFiltros->categoria, true)
				];
			}
		}

		/**
		 * conditions para Busca generos
		 */
		if ( ! empty( $GET['genero'] ) && $GET['genero'] != '') { 
			$ConditionsFilters['conditions'] .= sprintf('AND categoria like %s', implode('" or categoria like "', explode(',', str_replace([ '[', ']'], '"', $GET_GENERO))));
		}

		$ConditionsFilters['group'] = 'nomecor, id_cor';
		$ProdutosFiltersCores = ProdutosViewsTemp::all( $ConditionsFilters );
		foreach ($ProdutosFiltersCores as $rsFiltros) {
			if ($rsFiltros->id_cor > 0) {
				$filtros['cores'][$rsFiltros->opc_tipo_a][] = [
					'id_cor' => $rsFiltros->id_cor,
					'cor' => $rsFiltros->nomecor,
					'hex1' => $rsFiltros->cor1,
					'hex2' => $rsFiltros->cor2
				];
			}
		}

		/**
		 * conditions para Busca generos
		 */
		if ( ! empty( $GET['cores'] ) && $GET['cores'] != '') { 
			$ConditionsFilters['conditions'] .= sprintf('AND nomecor like %s', implode('" or nomecor like "', explode(',', str_replace([ '[', ']'], '"', $GET['cores']))));
		}

		$ConditionsFilters['group'] = 'nometamanho, id_tamanho';
		$ProdutosFiltersTamanhos = ProdutosViewsTemp::all( $ConditionsFilters );
		foreach ($ProdutosFiltersTamanhos as $rsFiltros) {
			if ($rsFiltros->id_tamanho > 0) {
				$filtros['tamanhos'][$rsFiltros->opc_tipo_b][] = [
					'id_tamanho' => $rsFiltros->id_tamanho,
					'tamanhos' => $rsFiltros->nometamanho,
					// 'hex1' => $rsFiltros->hex1,
					// 'hex2' => $rsFiltros->hex2
				];
			}
		}

		// $ConditionsFilters['group'] = 'id_marca';
		// $ProdutosFiltersMarcas = ProdutosViewsTemp::all( $ConditionsFilters );
		// foreach ($ProdutosFiltersMarcas as $rsFiltros) {   
			// if ($rsFiltros->id_marca > 0) {
				// $filtros['marcas']['marcas'][] = [
					// 'id_marca' => $rsFiltros->id_marca,
					// 'marcas' => $rsFiltros->marcas
				// ];
			// }
		// }

		$codigo_id_array = '';
		foreach ($Produtos as $codigo_id) {
			$codigo_id_array[] = $codigo_id->codigo_id;
		}

		$ConditionsMetasTags = ['conditions' => ['codigo_id IN(?) AND id_grupo=? AND id_subgrupo=?', $codigo_id_array, $GET_ID_GRUPO, $GET_ID_SUB_GRUPO ]];
		$ConditionsMetasTags['group'] = 'codigo_id';
		//$ConditionsMetasTags['order'] = 'rand()';
		$MetasTags = current(ProdutosMenus::all( $ConditionsMetasTags ));

		$STORE_KEYWORDS = $GET_ID_GRUPO > 0 && $GET_ID_SUB_GRUPO > 0 ? $MetasTags->subgrupo->subgrupo_keywords : $MetasTags->grupo->grupo_keywords;
		$STORE_DESCRIPTION = $GET_ID_GRUPO > 0 && $GET_ID_SUB_GRUPO > 0 ? $MetasTags->subgrupo->subgrupo_description : $MetasTags->grupo->grupo_description;
		$STORE_TITULO_PAGINA = $GET_ID_GRUPO > 0 && $GET_ID_SUB_GRUPO > 0 ? $MetasTags->grupo->grupo . ' - ' . $MetasTags->subgrupo->subgrupo : $MetasTags->grupo->grupo;

		$BREACRUMB_PESQUISAR = !empty($GET['pesquisar']) ? ['pesquisar' => $GET['pesquisar']] : null;

		$BREACRUMB = array_merge([
				'grupo_id' => $MetasTags->grupo->id, 
				'grupo' => $MetasTags->grupo->grupo, 
				'subgrupo_id' => $MetasTags->subgrupo->id,
				'subgrupo' => $MetasTags->subgrupo->subgrupo
			], (array)$BREACRUMB_PESQUISAR);

		$STORE['TITULO_PAGINA'] = ! empty($STORE_TITULO_PAGINA) ? $STORE_TITULO_PAGINA  . ' | ' . $STORE['TITULO_PAGINA'] : $STORE['TITULO_PAGINA'];
		$STORE['keywords'] = $STORE_KEYWORDS;
		$STORE['description'] = $STORE_DESCRIPTION;
		
		$this->view($this->view_pg, [
			'menus' => $menus,
			'GET_NOME_GRUPO' => $GET_NOME_GRUPO,
			'GET_ID_GRUPO' => $GET_ID_GRUPO,
			'GET_NOME_SUB_GRUPO' => $GET_NOME_SUB_GRUPO,
			'GET_ID_SUB_GRUPO' => $GET_ID_SUB_GRUPO,
			'GET_PAGINACAO' => $GET_PAGINACAO,
			'LOJA' => $LOJA, 
			'STORE' => $STORE, 
			'CONFIG' => $CONFIG, 
			'UA_INFO' => $UA_INFO, 
			'BREACRUMB' => $BREACRUMB, 
			'MobileDetect' => $MobileDetect, 
			'WebService' => $WebService, 
			'Images' => $Images, 
			'settings' => $settings, 
			'str' => $str,
			'Produtos' => $Produtos,
			'filtros' => $filtros
		]);
	}
	
	public function produto(  ) 
	{
		global $LOJA, $CONFIG, $STORE, $UA_INFO, $MobileDetect, $WebService, $settings, $str, $Images;
		
		$URL_PRODUTO_GET = Url::getURL( 2 );
		$ID_PRODUTO_GET = !empty( $URL_PRODUTO_GET ) && $URL_PRODUTO_GET != '' ? (INT) $URL_PRODUTO_GET : '';

		$Produto = Produtos::find( $ID_PRODUTO_GET , 
			array( 
				'select' => ''
					. 'produtos.id as id_produto, '
					. 'produtos.codigo_id, '
					. 'produtos.codigo_produto, '
					. 'produtos.nome_produto, '
					. 'produtos.subnome_produto, '
					. 'produtos.postagem, '
					. 'produtos.descricao_produto, '
					. 'produtos.estoque, '
					. 'produtos.estoque_min, '
					. 'produtos.preco_venda, '
					. 'produtos.preco_promo, '
					. 'produtos.placastatus, '
					. 'produtos.promocao, '
					. 'produtos.video_youtube, '
					. 'produtos.id_cor, '
					. 'produtos.id_tamanho, '
					. 'produtos_descricoes.descricao, '
					. 'marcas.marcas, '
					. 'cores.nomecor, '
					. 'tamanhos.nometamanho, '

					. 'cor.tipo AS cortipo, '
					. 'TAM.tipo AS tamanhotipo, '

					. 'grupos.grupo, '
					. 'grupos.grupo_keywords, '
					. 'grupos.id as grupo_id, '
					. 'subgrupos.subgrupo, '
					. 'subgrupos.subgrupo_keywords, '
					. 'subgrupos.id as subgrupo_id '
					. '',
				'joins' => array(
					'INNER JOIN produtos_descricoes ON (produtos.id_descricao = produtos_descricoes.id) ',
					'INNER JOIN marcas ON (produtos.id_marca = marcas.id) ',
					'INNER JOIN cores ON (IFNULL(cores.id, 0) = IFNULL(produtos.id_cor, 0)) ',
					'INNER JOIN tamanhos ON (tamanhos.id = produtos.id_tamanho) ',
					'LEFT JOIN opcoes_tipo cor ON (cor.id = cores.opcoes_id) ',
					'LEFT JOIN opcoes_tipo TAM ON (TAM.id = tamanhos.opcoes_id) ',
					'LEFT JOIN produtos_menus ON (produtos_menus.codigo_id = produtos.codigo_id) ',
					'LEFT JOIN grupos ON (grupos.id = produtos_menus.id_grupo) ',
					'LEFT JOIN subgrupos ON (subgrupos.id = produtos_menus.id_subgrupo) '
				),
				'group' => 'produtos.codigo_id',
			) 
		);

		$imagem_produto = array();
		$result = ProdutosImagens::all( array( 'conditions' => array( 'codigo_id=? and IFNULL(cor_id, 0)=?', $Produto->codigo_id, $Produto->id_cor ), 'order' => 'capa desc' ) );
		foreach ($result as $f)
		{
			// ativa a imagem na tab
			if( $f->ordem < 0 ){
				$imagem_produto_tab[$f->id] = $f->imagem;
			}
			// ativa uma capa para o produto
			else if ($f->capa == '1' ) {
				$imagem_produto['capa'] = $f->imagem;
			} 
			// listgem de fotos
			else {
				$imagem_produto[$f->id] = $f->imagem;
			}
		}

		$STORE['TITULO_PAGINA'] = $Produto->nome_produto . ' | ' . $STORE['TITULO_PAGINA'];
		$STORE['description'] = $Produto->subnome_produto;
		$STORE['keywords'] = $Produto->subgrupo_id > 0 ? $Produto->subgrupo_keywords : $Produto->grupo_keywords;
		$STORE['image'] = $Images->src($imagem_produto['capa'], 'smalls');

		$tamanhoSmall = '63px;'; 
		$BREACRUMB_GRUPO = $Produto->grupo_id > 0 ? array('grupo_id'=>$Produto->grupo_id,'grupo'=>$Produto->grupo) : null;
		$BREACRUMB_SUBGRUPO = $Produto->subgrupo_id > 0 ? array('subgrupo_id' => $Produto->subgrupo_id, 'subgrupo' => $Produto->subgrupo) : null;
		$BREACRUMB_NOMEPRODUTO = $Produto->nome_produto ? array('nome_produto' => $Produto->nome_produto) : null;

		$BREACRUMB_PESQUISAR = !empty($GET['pesquisar']) ? array('pesquisar' => $GET['pesquisar']) : null;

		$BREACRUMB = array_merge(
			array(
				'grupo_id' => $Produto->grupo_id, 
				'grupo' => $Produto->grupo, 
				'subgrupo_id' => $Produto->subgrupo_id,
				'subgrupo' => $Produto->subgrupo,
				'nome_produto' => $Produto->nome_produto
			),(array)$BREACRUMB_PESQUISAR
		);
		
		$this->view($this->view_pg, [
			'URL_PRODUTO_GET' => $URL_PRODUTO_GET,
			'ID_PRODUTO_GET' => $ID_PRODUTO_GET,
			'BREACRUMB_PESQUISAR' => $BREACRUMB_PESQUISAR,
			'GET_ID_SUB_GRUPO' => $GET_ID_SUB_GRUPO,
			'GET_PAGINACAO' => $GET_PAGINACAO,
			'LOJA' => $LOJA, 
			'STORE' => $STORE, 
			'CONFIG' => $CONFIG, 
			'UA_INFO' => $UA_INFO, 
			'BREACRUMB' => $BREACRUMB, 
			'MobileDetect' => $MobileDetect, 
			'WebService' => $WebService, 
			'Images' => $Images, 
			'settings' => $settings, 
			'str' => $str,
			'Produto' => $Produto,
			'imagem_produto' => $imagem_produto
		]);
	}
	
	public final function run() {
		self::{$this->view_pg}();
	}
}
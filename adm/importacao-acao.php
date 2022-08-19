<?php
include 'topo.php';

$file = current($_FILES);

if( $file['size'] > 0 && $file['error'] == 0 ) {

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
    $workSheet = $spreadsheet->getActiveSheet();
    $data_array =  $workSheet->toArray();

    $CountArray = (int)count($data_array);

    $test = 0;
    $array = [];
    $opcoes = [];
    for($i = 2; $i < $CountArray; $i++) {
        
        $slug_id = $workSheet->getCell('C' . $i)->getValue();
        $slug_id .= $workSheet->getCell('C' . ($i+1))->getValue();
        
        $codigo = trim($workSheet->getCell('AJ' . ($i+1))->getValue());
        $nome_produto = trim($workSheet->getCell('C' . $i)->getValue());
        $descricao = trim($workSheet->getCell('AP' . $i)->getValue());
        $marca = trim($workSheet->getCell('AM' . $i)->getValue());
        
        $strpos = strpos($nome_produto, ';');
        
        $preco_venda = trim($workSheet->getCell('G' . ($i+1))->getValue());
        $estoque = trim($workSheet->getCell('K' . ($i+1))->getValue()) * 1;
        $cest = trim($workSheet->getCell('AN' . ($i+1))->getValue());
        $excluir = trim($workSheet->getCell('J' . ($i+1))->getValue())!='Ativo'?1:0;
        $unid = trim($workSheet->getCell('D' . ($i+1))->getValue());
        
        // $csosn = trim($workSheet->getCell('G' . ($i+1))->getValue());
        // $cfop = trim($workSheet->getCell('G' . ($i+1))->getValue());
        // $cst = trim($workSheet->getCell('G' . ($i+1))->getValue());
        // $ncm = trim($workSheet->getCell('G' . ($i+1))->getValue());
        
        $A = null;
        $B = null;
        if( ! $strpos ) 
        {
            $aaaa = $workSheet->getCell('C' . ($i+1))->getValue();
            $array_tipos = explode(';', $aaaa);
            
            $tipo_a = strstr($array_tipos[0], ':', true);
            $tipo_b = strstr($array_tipos[1], ':', true);
            
            $text_a = ltrim(strstr($array_tipos[0], ':'), ':');
            $text_b = ltrim(strstr($array_tipos[1], ':'), ':');

            $A[$tipo_a] = $text_a;
            $B[$tipo_b] = $text_b;
            
            unset($B[0], $A[0]);

            $slug_id = md5(converter_texto($slug_id, ''));

            $array[ $slug_id ] = [
                'id_cor' => $B,
                'id_tamanho' => $A,
                'id_frete' => 0,
                'id_marca' => $marca,
                'id_descricao' => $descricao,
                'codigo_produto' => $codigo,
                'nome_produto' => $nome_produto,
                'estoque' => $estoque,
                'preco_promo' => (soNumero($preco_venda) / 100),
                'unid' => $unid,
                'csosn' => '',
                'cfop' => '',
                'cst' => '',
                'ncm' => '',
                'cest' => $cest,
                'excluir' => $excluir
            ];
        }
    }
    
    array_multisort(array_map(function($rw) {
        return $rw['nome_produto'];
    }, $array), SORT_ASC, $array);
    
    array_splice($array, 0, 2);
    
    $it = 0;
    $time = time();
    $group_time = null;
    foreach ($array as $key => $rws) 
    {
        $CoresFirstId = 0;
        $MarcasFirstId = 0;
        $TamanhosFirstId = 0;
        $ProdutosDescricoesFirstId = 0;
        foreach ($rws['id_cor'] as $ka => $cor) 
        {
            // *************************************************************************************************
            $OpcoesTipo = new OpcoesTipo();
            $OpcoesTipoFirstId = (int)(OpcoesTipo::first(['conditions' => ['loja_id=? and tipo like ?', $CONFIG['loja_id'], $ka]]))->id;
            
            if( $OpcoesTipoFirstId > 0 ) 
                $OpcoesTipo->id = $OpcoesTipoFirstId;

            $OpcoesTipo->tipo = $ka;
            $OpcoesTipoSave = $OpcoesTipo->save_log();

            $OpcoesTipoFirstId = $OpcoesTipoSave['id'];

            // *************************************************************************************************
            $Cores = new Cores();
            $CoresFirstId = (int)(Cores::first(['conditions' => ['loja_id=? and opcoes_id=? and nomecor like ?', $CONFIG['loja_id'], $OpcoesTipoFirstId, $cor]]))->id;

            if( $CoresFirstId > 0 ) 
                $Cores->id = $CoresFirstId;    

            $Cores->opcoes_id = $OpcoesTipoFirstId;
            $Cores->nomecor = $cor;
            $CoresSave = $Cores->save_log();

            $CoresFirstId = $CoresSave['id'];
        }
        
        foreach ($rws['id_tamanho'] as $kt => $tamanho) 
        {
            // *************************************************************************************************
            $OpcoesTipo = new OpcoesTipo();
            $OpcoesTipoFirstId = (int)(OpcoesTipo::first(['conditions' => ['loja_id=? and tipo like ?', $CONFIG['loja_id'], $kt]]))->id;
            
            if( $OpcoesTipoFirstId > 0 ) 
                $OpcoesTipo->id = $OpcoesTipoFirstId;

            $OpcoesTipo->tipo = $kt;
            $OpcoesTipoSave = $OpcoesTipo->save_log();

            $OpcoesTipoFirstId = $OpcoesTipoSave['id'];

            // *************************************************************************************************
            $Tamanhos = new Tamanhos();
            $TamanhosFirstId = (int)(Tamanhos::first(['conditions' => ['loja_id=? and opcoes_id=? and nometamanho like ?', $CONFIG['loja_id'], $OpcoesTipoFirstId, $tamanho]]))->id;

            if( $TamanhosFirstId > 0 ) 
                $Tamanhos->id = $TamanhosFirstId;    

            $Tamanhos->opcoes_id = $OpcoesTipoFirstId;
            $Tamanhos->nometamanho = $tamanho;
            $TamanhosSave = $Tamanhos->save_log();

            $TamanhosFirstId = $TamanhosSave['id'];
        }

        // *************************************************************************************************
        $Marcas = new Marcas();
        $MarcasFirstId = (int)(Marcas::first(['conditions' => ['loja_id=? and marcas like ?', $CONFIG['loja_id'], $rws['id_marca']]]))->id;

        if( $MarcasFirstId > 0 ) 
            $Marcas->id = $MarcasFirstId;    

        $Marcas->marcas = $rws['id_marca'];
        $MarcasSave = $Marcas->save_log();

        $MarcasFirstId = $MarcasSave['id'];
        
        // *************************************************************************************************
        $ProdutosDescricoes = new ProdutosDescricoes();
        $ProdutosDescricoesFirstId = (int)(ProdutosDescricoes::first(['conditions' => ['loja_id=? and nome like ?', $CONFIG['loja_id'], $rws['nome_produto']]]))->id;

        if( $ProdutosDescricoesFirstId > 0 ) 
            $ProdutosDescricoes->id = $ProdutosDescricoesFirstId;    

        $ProdutosDescricoes->nome = $rws['nome_produto'];
        $ProdutosDescricoes->descricao = $rws['id_descricao'];
        $ProdutosDescricoesSave = $ProdutosDescricoes->save_log();

        $ProdutosDescricoesFirstId = $ProdutosDescricoesSave['id'];

        // ***********************************************************************************************************************************************************
        if($group_time != $rws['nome_produto']) {
            $group_time = $rws['nome_produto'];
            $time = ($time + $it);
            $it++;
        }
        
        $Produtos = new Produtos();
        $ProdutosFirst = Produtos::first(['conditions' => ['loja_id=? and id_cor=? and id_tamanho=? and nome_produto like ?', $CONFIG['loja_id'], $CoresFirstId, $TamanhosFirstId, $rws['nome_produto']]]);
        
        if( (int)$ProdutosFirst->id > 0 ) {
            $Produtos->id = (int)$ProdutosFirst->id;
            $Produtos->codigo_id = (int)$ProdutosFirst->codigo_id;
        } 
        else {
            $Produtos->codigo_id = $time;
        }
        
        $Produtos->id_cor = $CoresFirstId;
        $Produtos->id_tamanho = $TamanhosFirstId;
        $Produtos->id_marca = $MarcasFirstId;
        $Produtos->id_descricao = $ProdutosDescricoesFirstId;
        $Produtos->id_frete = 0;
        $Produtos->codigo_produto = $rws['codigo_produto'];
        $Produtos->codigo_referencia = CodProduto( $rws['nome_produto'] );
        $Produtos->nome_produto = $rws['nome_produto'];
        $Produtos->subnome_produto = $rws['nome_produto'];
        $Produtos->descricao_produto = '';
        $Produtos->descricao_produto2 = '';
        $Produtos->postagem = '1 a 5 dias';
        $Produtos->estoque = $rws['estoque'];
        $Produtos->preco_custo = '0';
        $Produtos->preco_venda = '0';
        $Produtos->preco_promo = $rws['preco_promo'];
        $Produtos->placastatus = null;
        $Produtos->utilidades = null;
        $Produtos->categoria = null;
        $Produtos->unid = $rws['unid'];
        $Produtos->csosn = '';
        $Produtos->cfop = '';
        $Produtos->cst = '';
        $Produtos->ncm = '';
        $Produtos->cest = $rws['cest'];
        $Produtos->status = 0;
        $Produtos->excluir = $rws['excluir'];
        $Produtos->save_log();
    }

    header('location: /adm/produtos/produtos.php');
    return;    
}


include 'rodape.php';
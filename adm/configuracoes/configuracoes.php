<?php
include '../topo.php';

switch( $POST['acao'] )
{
	case 'configuracoes':
		$id                     = (int)$POST['id'];
		$ativo		 			= $POST['ativo'];
		$razao_social 			= $POST['razao_social'];
		$nome_fantasia 			= $POST['nome_fantasia'];
		
		$cnpj					= $POST['cnpj'];
		$telefone 				= $POST['telefone'];
		$celular 				= $POST['celular'];
		$operadora 				= $POST['operadora'];
		$cep 					= $POST['cep'];
		$endereco 				= $POST['endereco'];
		$bairro 				= $POST['bairro'];
		$numero 				= $POST['numero'];
		$cidade 				= $POST['cidade'];
		$uf 					= $POST['uf'];
		$email_contato 			= $POST['email_contato'];
		$horario_atendimentos 	= $POST['horario_atendimentos'];
		
		
        if( Configuracoes::action_cadastrar_editar([
            'Configuracoes' => [ $id => [ 
               'ativo' => $ativo,
               'razao_social' => $razao_social,
               'nome_fantasia' => $nome_fantasia,
               'horario_atendimentos' => $horario_atendimentos,
              
               'cnpj' => $cnpj,
               'telefone' => $telefone,
               'celular' => $celular,
               'operadora' => $operadora,
               'cep' => $cep,
               'endereco' => $endereco,
               'bairro' => $bairro,
               'numero' => $numero,
               'cidade' => $cidade,
               'uf' => $uf,
               'email_contato' => $email_contato
            ] ] ], 'alterar', 'nome_fantasia') ) {
            header('Location: /adm/configuracoes/configuracoes.php#configuracao');
            return;
        }
	break;

	case 'configuracoes_seo':
		$id                     = (int)$POST['id'];
		$keywords    			= $POST['keywords'];
		$description 			= $POST['description'];
		$google_tag_manager 	= $POST['google_tag_manager'];
		$google_tag_verification= $POST['google_tag_verification'];
		$google_tag_analytics 	= $POST['google_tag_analytics'];
		$fb_id 					= $POST['fb_id'];
		$fb_link 				= $POST['fb_link'];
		$fb_verification		= $POST['fb_verification'];
		$insta_link 			= $POST['insta_link'];
		
        if( Configuracoes::action_cadastrar_editar([
            'Configuracoes' => [ $id => [ 
			 	'keywords' => $keywords,
				'description' => $description,
				'google_tag_manager' => $google_tag_manager,
				'google_tag_verification' => $google_tag_verification,
				'google_tag_analytics' => $google_tag_analytics,
				'horario_atendimentos' => $horario_atendimentos,
				'insta_link' => $insta_link,
				'fb_id' => $fb_id,
				'fb_link' => $fb_link,
				'fb_verification' => $fb_verification,
            ] ] ], 'alterar', 'nome_fantasia') ) {
            header('Location: /adm/configuracoes/configuracoes.php#configuracao_seo');
            return;
        }
	break;
	
	case 'configuracao_loja':
		unset($POST['acao']);
		
		$POST['config']['btn-compra'] = [
			$POST['config']['btn-compra']['status'],
			'text' => $POST['config']['btn-compra']['text'],
			'class' => $POST['config']['btn-compra']['class'],
			'class-icon' => $POST['config']['btn-compra']['class-icon'],
		];

		$POST['config']['btn-espiar'] = [
			$POST['config']['btn-espiar']['status'],
			'text' => $POST['config']['btn-espiar']['text'],
			'class' => $POST['config']['btn-espiar']['class'],
			'class-icon' => $POST['config']['btn-espiar']['class-icon'],
		];

		$POST['config']['pedido']['obs'] = [
			$POST['config']['pedido']['obs']['status']??0,
			'text' => $POST['config']['pedido']['obs']['text'],
		];
		
		// $POST['config']['pedido']['obs']['date'] = [
		// 	$POST['config']['pedido']['date']??0,
		// 	'text' => $POST['config']['date']['text'],
		// ];
		
		$arquivo = sprintf('%s/assets/%s/settings_store.inc', PATH_ROOT, ASSETS);

		$var =  PHP_EOL . '// AS CONFIGURAÇÕES SETADAS AQUI, APENAS FAZEM MOSTRA CORES DE EMAIL, MOBILE THEMA, CALCULO DE FRETE COMPRA DIRETA NA LOJA' . PHP_EOL;
		$var .= 'return ' . var_export($POST, true);

		if( file_put_contents($arquivo, '<?php ' . $var . ';' ) ) {
			header('Location: /adm/configuracoes/configuracoes.php#configuracao_loja'); 
			return;
		}
	break;
	
	case 'logos':
		
		$CAMINHO = URL_VIEWS_BASE_PUBLIC_UPLOAD . '/imgs/';

        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
	
		$Configuracoes = Configuracoes::find(['conditions' => ['id=?', $id]]);
		extract($Configuracoes->to_array());
	
        $logo_temp = $_FILES;
		
		if( ! empty( $logo_temp['logo_desktop']['size'] ) ) {
			$logo = $logo_temp['logo_desktop'];
		}
		
		if( ! empty( $logo_temp['logo_mobile']['size'] ) ) {
			$logo = $logo_temp['logo_mobile'];
		} 
		
		if( ! empty( $logo_temp['logo_favicon_ico']['size'] ) ) {
			$logo = $logo_temp['logo_favicon_ico'];
		} 
		
		if( ! empty( $logo_temp['logo_favicon_png']['size'] ) ) {
			$logo = $logo_temp['logo_favicon_png'];
		} 
		
		if( empty( $logo_temp['logo_mobile']['size'] ) && 
			empty( $logo_temp['logo_desktop']['size'] ) && 
			empty( $logo_temp['logo_favicon_ico']['size'] ) && 
			empty( $logo_temp['logo_favicon_png']['size'] ) ) {
			header('Location: /adm/configuracoes/configuracoes.php?error=error_image');
			return;
		}
		
		$ext_pathinfo = pathinfo( ( ! empty( $logo_desktop ) ? $logo_desktop : $logo['name'] ) );
		$ext = $ext_pathinfo['extension'];
		
		$WideImageTmpName = WideImage\WideImage::load( $logo['tmp_name'] );
		
		// Envia o logo desktop
		if( ! empty( $logo_temp['logo_desktop']['size'] ) ){
			
			$NOVO_NOME_IMAGEM = ( ! empty( $logo_desktop ) ? $logo_desktop : uniqid( time() ) . '.' . $ext );
		
			$WideImageTmpName->saveToFile( $CAMINHO . $NOVO_NOME_IMAGEM );
			$WideImageTmpName->destroy();
			
			if( Configuracoes::action_cadastrar_editar([ 'Configuracoes' => [ $id => [ 'logo_desktop' => $NOVO_NOME_IMAGEM ] ] ], 'alterar', 'nome_fantasia') ) {
				header('Location: /adm/configuracoes/configuracoes.php#configuracao_logo');
				return;
			}
		}
		
		// Envia o logo mobile
		if( ! empty( $logo_temp['logo_mobile']['size'] ) ) {
			
			$NOVO_NOME_IMAGEM = ( ! empty( $logo_mobile ) ? $logo_mobile : uniqid( time() ) . '-mobile.' . $ext );
			
			$WideImageTmpName->saveToFile( $CAMINHO . $NOVO_NOME_IMAGEM );
			$WideImageTmpName->destroy();
			
			if( Configuracoes::action_cadastrar_editar([ 'Configuracoes' => [ $id => [ 'logo_mobile' => $NOVO_NOME_IMAGEM ] ] ], 'alterar', 'nome_fantasia') ) {
				header('Location: /adm/configuracoes/configuracoes.php#configuracao_logo');
				return;
			}
		}
		
		// Envia o logo favicon png
		if( ! empty( $logo_temp['logo_favicon_png']['size'] ) ){
			
			$NOVO_NOME_IMAGEM = ( ! empty( $logo_favicon_png ) ? $logo_favicon_png : uniqid( time() ) . '.' . $ext );
		
			$WideImageTmpName->saveToFile( $CAMINHO . $NOVO_NOME_IMAGEM );
			$WideImageTmpName->destroy();
			
			if( Configuracoes::action_cadastrar_editar([ 'Configuracoes' => [ $id => [ 'logo_favicon_png' => $NOVO_NOME_IMAGEM ] ] ], 'alterar', 'nome_fantasia') ) {
				header('Location: /adm/configuracoes/configuracoes.php#configuracao_logo');
				return;
			}
		}
		
		// Envia o logo favicon ico
		if( ! empty( $logo_temp['logo_favicon_ico']['size'] ) ) {
			
			$NOVO_NOME_IMAGEM = ( ! empty( $logo_favicon_ico ) ? $logo_favicon_ico : uniqid( time() ) . '.' . $ext );
			
			$WideImageTmpName->saveToFile( $CAMINHO . $NOVO_NOME_IMAGEM );
			$WideImageTmpName->destroy();
			
			if( Configuracoes::action_cadastrar_editar([ 'Configuracoes' => [ $id => [ 'logo_favicon_ico' => $NOVO_NOME_IMAGEM ] ] ], 'alterar', 'nome_fantasia') ) {
				header('Location: /adm/configuracoes/configuracoes.php#configuracao_logo');
				return;
			}
		}
		
	break;
}

$Configuracoes = Configuracoes::first(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
extract($Configuracoes->to_array());
?>
<style>
	body{ background-color: #f1f1f1 }
</style>
<div class="row">
	<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12 mt50">
		<div class="panel panel-default mb25">
			<div class="panel-heading panel-store text-uppercase" style="border-bottom: none; padding-bottom: 0;">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist" id="tabs">
					<li role="presentation" class="active"><a href="#configuracao" aria-controls="home" role="tab" data-toggle="tab">Configurações</a></li>
					<li role="presentation"><a href="#configuracao_seo" aria-controls="profile" role="tab" data-toggle="tab">seo</a></li>
					<li role="presentation"><a href="#configuracao_logo" aria-controls="configuracao_logo" role="tab" data-toggle="tab">logo</a></li>
					<li role="presentation"><a href="#configuracao_loja" aria-controls="configuracao_loja" role="tab" data-toggle="tab">loja</a></li>
				</ul>
			</div>
			<div class="panel-body">
				<div class="row tab-content">
					<!--[CONFIGURAÇÃO DA LOJA]-->
					<form role="tabpanel" class="col-md-12 tab-pane active" id="configuracao" action="/adm/configuracoes/configuracoes.php" method="post">
						<input type="hidden" name="id" value="<?php echo $id;?>"/>
						<input type="hidden" name="acao" value="configuracoes"/>
						<h4 class="neo-sans-medium mb0">Loja - <?php echo $nome_fantasia??'Digite Titulo/Nome loja'?></h4>
						<hr class="mt5"/>
						<div class="row">
							<div class="form-group col-md-2 col-xs-12">
								<label>Loja Ativa:</label>
								<select name="ativo" class="form-control">
									<option value="1"<?php echo ($ativo == 1 ? ' selected': '');?>>Não</option>
									<option value="0"<?php echo ($ativo == 0 ? ' selected': '');?>>Sim</option>
								</select>
							</div>						
							<div class="form-group col-md-8 col-xs-12">
								<label>Razão Social:</label>
								<input type="text" name="razao_social" value="<?php echo $razao_social;?>" class="form-control"/>
							</div>
							
							<div class="form-group col-md-2 col-xs-12"></div>

							<div class="form-group col-md-7 col-xs-12">
								<label>Titulo/Nome loja:</label>
								<input type="text" name="nome_fantasia" value="<?php echo $nome_fantasia;?>" class="form-control"/>
							</div>
							<div class="form-group col-md-5 col-xs-12">
								<label>CNPJ:</label>
								<input type="text" name="cnpj" onkeypress="return mascara(this)"  value="<?php echo $cnpj;?>" class="form-control"/>
							</div>
							<div class="form-group col-md-3 col-xs-12">
								<label>Telefone/Celular:</label>
								<input type="text" name="telefone" value="<?php echo $telefone;?>" class="form-control"/>
							</div>
							<div class="form-group col-md-3 col-xs-12">
								<label>Celular</label>
								<input type="text" name="celular" value="<?php echo $celular;?>" class="form-control"/>
							</div>
							<div class="form-group col-md-4 col-xs-12">
								<label>Operadora</label>
								<input type="text" name="operadora" value="<?php echo $operadora;?>" class="form-control"/>
							</div>
							<div class="form-group col-md-5 col-xs-12">
								<label>Email:</label>
								<input type="text" name="email_contato" value="<?php echo $email_contato;?>" class="form-control"/>
							</div>
							<div class="form-group col-md-7 col-xs-12">
								<label>Horario de atendimentos:</label>
								<input type="text" name="horario_atendimentos" value="<?php echo $horario_atendimentos;?>" class="form-control"/>
							</div>
							<div class="form-group col-xs-12 mb0 mt15">
								<h4 class="neo-sans-medium mb0">Endereço - <?php echo $nome_fantasia??'Digite Titulo/Nome loja'?></h4>
								<hr class="mt5"/>
							</div>
							<div class="form-group col-md-2 col-xs-12">
								<label>Cep:</label>
								<input type="text" name="cep" value="<?php echo $cep;?>" class="form-control"/>
							</div>
							<div class="form-group col-md-8 col-xs-12">
								<label>Endereço:</label>
								<input type="text" name="endereco" value="<?php echo $endereco;?>" class="form-control"/>
							</div>
							<div class="form-group col-md-2 col-xs-12">
								<label>Número:</label>
								<input type="text" name="numero" value="<?php echo $numero;?>" class="form-control"/>
							</div>
							<div class="form-group col-md-3 col-xs-12">
								<label>Bairro:</label>
								<input type="text" name="bairro" value="<?php echo $bairro;?>" class="form-control"/>
							</div>
							<div class="form-group col-md-5 col-xs-12">
								<label>Cidade:</label>
								<input type="text" name="cidade" value="<?php echo $cidade;?>" class="form-control"/>
							</div>
							<div class="form-group col-md-2 col-xs-12">
								<label>UF:</label>
								<input type="text" name="uf" value="<?php echo $uf;?>" class="form-control" maxlength="2"/>
							</div>
						</div>
						<div class="col-md-12 mt15">
							<button type="submit" class="btn btn-primary">Salvar</button>
						</div>
					</form>
					<!--[\CONFIGURAÇÃO DA LOJA]-->

					<!--[CONFIGURAÇÃO DA LOJA SEO]-->
					<form role="tabpanel" class="tab-pane col-md-12 col-xs-12" id="configuracao_seo" action="/adm/configuracoes/configuracoes.php" method="post">
						<input type="hidden" name="id" value="<?php echo $id;?>"/>
						<input type="hidden" name="acao" value="configuracoes_seo"/>
						<div class="row">
							<div class="col-md-12 col-xs-12">
								<div class="form-group">
									<label>Palavras chave: <span class="info-title tooltip" title="Palavras chaves para sistemas de buscas (google).">?</span></label>
									<input type="text" value='<?php echo $keywords;?>' name='keywords' class="form-control count-input" maxlength="200"/>
								</div>
								<div class="form-group">
									<label>Descrição: <span class="info-title tooltip" title="Prévia descrição para os sistema de buscas (google).">?</span></label>
									<textarea name='description' class="form-control count-input" maxlength="250"><?php echo $description;?></textarea>
								</div>
								<hr/>
							</div>
							<div class="col-md-6 col-xs-12">
								<h4 class="neo-sans-medium mb0">Informações para SEO</h4>
								<hr class="mt5"/>
								<div class="form-group">
									<label>Google Tag Manager: <span class="info-title tooltip" title="Google Tag Manager é uma ferramenta gratuita do Google, pela qual é possível inserir apenas um código em um site, para depois instalar diversos serviços sem precisar mexer nesse código.">?</span></label>
									<input type="text" value='<?php echo $google_tag_manager;?>' name='google_tag_manager' class="form-control count-input" maxlength="15"/>
								</div>
								<div class="form-group">
									<label>Google Tag Verification: <span class="info-title tooltip" title="Google Tag Manager é uma ferramenta gratuita do Google, pela qual é possível inserir apenas um código em um site, para depois instalar diversos serviços sem precisar mexer nesse código.">?</span></label>
									<input type="text" value='<?php echo $google_tag_verification;?>' name='google_tag_verification' class="form-control count-input" maxlength="45"/>
								</div>
								<div class="form-group">
									<label>Google Tag Analytics: <span class="info-title tooltip" title="Google Tag Manager é uma ferramenta gratuita do Google, pela qual é possível inserir apenas um código em um site, para depois instalar diversos serviços sem precisar mexer nesse código.">?</span></label>
									<input type="text" value='<?php echo $google_tag_analytics;?>' name='google_tag_analytics' class="form-control count-input" maxlength="15"/>
								</div>
							</div>
							<div class="col-md-6 col-xs-12">
								<h4 class="neo-sans-medium mb0">Redes Sociais</h4>
								<hr class="mt5"/>
								<div class="form-group">
									<label>FB Link: <span class="info-title tooltip" title="Adicione o link de sua página oficial do seu Facebook">?</span></label>
									<input type="text" value='<?php echo $fb_link;?>' name='fb_link' class="form-control"/>
								</div>
								<div class="form-group">
									<label>FB ID: <span class="info-title tooltip" title="Adicione o ID de sua página para vendas via Facebook">?</span></label>
									<input type="text" value='<?php echo $fb_id;?>' name='fb_id' class="form-control"/>
								</div>
								<div class="form-group">
									<label>FB Verification: <span class="info-title tooltip" title="Adicione a tag verificação do Facebook">?</span></label>
									<input type="text" value='<?php echo $fb_verification;?>' name='fb_verification' class="form-control"/>
								</div>
								<div class="form-group">
									<label>Instagram link: <span class="info-title tooltip" title="Adicione o link de sua página oficial do seu Instagram">?</span></label>
									<input type="text" value='<?php echo $insta_link;?>' name='insta_link' class="form-control"/>
								</div>
							</div>
						</div>
						<div class="col-md-12 mt15">
							<button type="submit" class="btn btn-primary">Salvar</button>
						</div>
					</form>
					<!--[\CONFIGURAÇÃO DA LOJA SEO]-->

					<!--[CONFIGURAÇÃO DA LOJA IMAGE]-->
					<form role="tabpanel" class="tab-pane col-md-12 col-xs-12" id="configuracao_logo" action="/adm/configuracoes/configuracoes.php" method="post" enctype="multipart/form-data" >
						<input type="hidden" name="id" value="<?php echo $id;?>"/>
						<input type="hidden" name="acao" value="logos"/>
						<div class="row">
							<div class="form-group col-md-3 col-xs-12 text-center">
								<label for="logo_desktop">
									<img src="<?php echo Imgs::src($logo_desktop??'logo.png', $logo_desktop?'imgs':'imagens')?>" class="block-center img-responsive" style="cursor: pointer">
									Logo site
								</label>
								<input type="file" class="hidden" name="logo_desktop" id="logo_desktop"/>
							</div>
							<div class="form-group col-md-12 col-xs-12 text-center"></div>
							<div class="form-group col-md-3 col-xs-12 text-center">
								<label for="logo_mobile">
									<img src="<?php echo Imgs::src($logo_mobile??'logo-mobile.png', $logo_mobile?'imgs':'imagens')?>" class="block-center img-responsive" style="cursor: pointer">
									<br/>
									Logo mobile
								</label>
								<input type="file" class="hidden" name="logo_mobile" id="logo_mobile"/>
							</div>
							<div class="form-group col-md-12 col-xs-12 text-center"></div>
							<div class="form-group col-md-2 col-xs-12 text-center">
								<label for="logo_favicon_png">
									<img src="<?php echo Imgs::src($logo_favicon_png??'logo-favicon.png', $logo_favicon_png?'imgs':'imagens')?>" class="block-center img-responsive" style="cursor: pointer">
									favicon ico
								</label>
								<input type="file" class="hidden" name="logo_favicon_png" id="logo_favicon_png"/>
							</div>
						</div>
						<button type="submit" class="mt15 btn btn-primary">Salvar</button>
					</form>
					<!--[\CONFIGURAÇÃO DA LOJA IMAGE]-->

					<!--[OUTRAS CONFIGURAÇÃO]-->
					<form role="tabpanel" class="tab-pane col-md-12 col-xs-12" id="configuracao_loja" action="/adm/configuracoes/configuracoes.php" method="post">
						<input type="hidden" value="configuracao_loja" name="acao"/>
						<div class="row">
							<div class="col-md-12 col-xs-12 mb0 mt0">
								<h4 class="neo-sans-medium mb5">Outras Configurações</h4>
								<small>Habilite o cupom desconto, calculo de frete e observação do pedido</small>
								<hr class="mt5"/>
							</div>
							
							<div class="form-group col-md-4 col-xs-12">
								<label class="show">Produto:</label>
								<select name="frete_prod" class="form-control" style="width: 100%;">
									<option value="0"<?php echo $STORE['frete_prod'] == false ? ' selected':''?>>Não</option>
									<option value="1"<?php echo $STORE['frete_prod'] == true ? ' selected':''?>>Sim</option>
								</select>
								<small class="show">Habilite a função para calcular o frete na tela do produto</small>
							</div>
						
							<div class="form-group col-md-4 col-xs-12">
								<label class="show">Carrinho:</label>
								<select name="config[cart][frete]" class="form-control" style="width: 100%;">
									<option value="0"<?php echo $STORE['config']['cart']['frete'] == false ? ' selected':''?>>Não</option>
									<option value="1"<?php echo $STORE['config']['cart']['frete'] == true ? ' selected':''?>>Sim</option>
								</select>
								<small class="show">Habilite a função para calcular o frete no carrinho de compras</small>
							</div>
							
							<div class="form-group col-md-4 col-xs-12">
								<label class="show">Cupom:</label>
								<!--NOTA: se for falso deve mostra para todas as loja, pois ha muitas config para mexe, então o esquema esta ao contrario-->
								<select name="config[cupom]" class="form-control" style="width: 100%;">
									<option value="1"<?php echo $STORE['config']['cupom'] == true ? ' selected':''?>>Não</option>
									<option value="0"<?php echo $STORE['config']['cupom'] == false ? ' selected':''?>>Sim</option>
								</select>
								<small class="show">Habilite a função para cupom de desconto</small>
							</div>

							<div class="form-group col-md-4 col-xs-12">
								<label class="show">Botão de Orçamento:</label>
								<select name="config[product][buy]" class="form-control" style="width: 100%;">
									<option value="0"<?php echo $STORE['config']['product']['buy'] == false ? ' selected':''?>>Não</option>
									<option value="1"<?php echo $STORE['config']['product']['buy'] == true ? ' selected':''?>>Sim</option>
								</select>
								<small class="show">Habilite a função para o site realizar somente orçamento</small>
							</div>
							
							<div class="form-group col-md-4 col-xs-12">
								<label class="show">Comprar direto:</label>
								<select name="config[cart][direct]" class="form-control" style="width: 100%;">
									<option value="0"<?php echo $STORE['config']['cart']['direct'] == false ? ' selected':''?>>Não</option>
									<option value="1"<?php echo $STORE['config']['cart']['direct'] == true ? ' selected':''?>>Sim</option>
								</select>
								<small class="show">Habilite a função para não mostrar uma pre visualização na tela do produto</small>
							</div>
							
							<div class="col-md-12 col-xs-12 mt15 mb0">
								<h4 class="neo-sans-medium mb0">Observação</h4>
								<small>Ative essa função para adicionar um campo texto para uma pequena observação na tela final de pagamento</small>
								<hr class="mt5"/>
							</div>

							<div class="form-group col-md-2 col-xs-12">
								<label class="show">Observações:</label>
								<select name="config[pedido][obs][status]" class="form-control" style="width: 100%;">
									<option value="0"<?php echo $STORE['config']['pedido']['obs'][0] == false ? ' selected':''?>>Não</option>
									<option value="1"<?php echo $STORE['config']['pedido']['obs'][0] == true ? ' selected':''?>>Sim</option>
								</select>
							</div>

							<div class="form-group col-md-5 col-xs-12">
								<label class="show">Nome Descrição:</label>
								<input type="text" name="config[pedido][obs][text]" value="<?php echo $STORE['config']['pedido']['obs']['text']??''?>" class="form-control">
								<small>Defina o tipo da descrição, ex: Qual endereço para entrega</small>
							</div>

							<div class="form-group col-md-5 col-xs-12">
								<label class="show">Entrega Agendada:</label>
								<input type="text" name="config[pedido][date][text]" value="<?php echo $STORE['config']['pedido']['date']['text']??''?>" class="form-control">
								<small>Preencha o campo para uma data de entrega do pedido</small>
							</div>

							<div class="form-group col-md-7 col-xs-12">
								<div class="row">
									<div class="col-md-12 col-xs-12 mt15 mb0">
										<h4 class="neo-sans-medium mb0">E-mail</h4>
										<small>Selecione as cores de fundo e cor de texto para os email da loja</small>
										<hr class="mt5"/>
									</div>
									<div class="col-md-3 col-xs-12">
										<label class="show">Cores primarias:</label>
										<div class="input-group">
											<span class="input-group-addon" style="color: transparent;">...</span>
											<input type="text" name="color001" value="<?php echo $STORE['color001']??color001?>" class="form-control" id="hex-01">
										</div>
									</div>
									<div class="col-md-3 col-xs-12">
										<label class="show">Cores secundarias:</label>
										<div class="input-group">
											<span class="input-group-addon" style="color: transparent;">...</span>
											<input type="text" name="color002" value="<?php echo $STORE['color002']??color002?>" class="form-control" id="hex-02">
										</div>
									</div>
									<div class="col-md-3 col-xs-12">
										<label class="show">Fundo primario:</label>
										<div class="input-group">
											<span class="input-group-addon" style="color: transparent;">...</span>
											<input type="text" name="background001" value="<?php echo $STORE['background001']??background001?>" class="form-control" id="bg-01">
										</div>
									</div>
									<div class="col-md-3 col-xs-12">
										<label class="show">Fundo secundario:</label>
										<div class="input-group">
											<span class="input-group-addon" style="color: transparent;">...</span>
											<input type="text" name="background002" value="<?php echo $STORE['background002']??background002?>" class="form-control" id="bg-02">
										</div>
									</div>
								</div>
							</div>							
							<div class="form-group col-md-5 col-xs-12">
								<div class="row">
									<div class="col-md-12 col-xs-12 mt15 mb0">
										<h4 class="neo-sans-medium mb0">Thema Color</h4>
										<small>Selecione a cor da barra dos dispositivo moveis</small>
										<hr class="mt5"/>
									</div>
									<div class="col-md-5 col-xs-12">
										<label class="show">Thema Color:</label>
										<div class="input-group">
											<span class="input-group-addon" style="color: transparent;">...</span>
											<input type="text" name="theme-color" value="<?php echo $STORE['theme-color']??'#000000'?>" class="form-control" id="bg-03">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-xs-12 mt15 mb0">
								<h4 class="neo-sans-medium mb0">Botão Comprar/Espiar</h4>
								<small>Habilite essa função junto ao desenvolvedor do site</small>
								<hr class="mt5"/>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="col-md-6 col-sm-12 mb15">
										<label class="show mb5">Botão Comprar</label>
										<select name="config[btn-compra][status]" class="form-control">
											<option value="1"<?php echo $STORE['config']['btn-compra'][0] == true ? ' selected':''?>>Sim</option>
											<option value="0"<?php echo $STORE['config']['btn-compra'][0] == false ? ' selected':''?>>Não</option>
										</select>
									</div>
									<div class="form-group col-lg-12 col-md-12 col-sm-12 mb15">
										<label class="show mb5">Descrição</label>
										<input type="text" name="config[btn-compra][text]" class="form-control" value="<?php echo $STORE['config']['btn-compra']['text'];?>"/>
									</div>
									<div class="form-group col-lg-12 col-md-12 col-sm-12 mb15">
										<label class="show mb5">Class do Botão</label>
										<input type="text" name="config[btn-compra][class]" class="form-control" value="<?php echo $STORE['config']['btn-compra']['class'];?>"/>
									</div>
									<div class="form-group col-lg-12 col-md-12 col-sm-12">
										<label class="show mb5">Icone do Botão</label>
										<input type="text" name="config[btn-compra][class-icon]" class="form-control" value="<?php echo $STORE['config']['btn-compra']['class-icon'];?>"/>
									</div>	
								</div>
								<div class="col-md-6">
									<div class="col-md-6 col-sm-12 mb15">
										<label class="show mb5">Botão Espiar</label>
										<select name="config[btn-espiar][status]" class="form-control">
											<option value="1"<?php echo $STORE['config']['btn-espiar'][0] == true ? ' selected':''?>>Sim</option>
											<option value="0"<?php echo $STORE['config']['btn-espiar'][0] == false ? ' selected':''?>>Não</option>
										</select>
									</div>
									<div class="form-group col-lg-12 col-md-12 col-sm-12 mb15">
										<label class="show mb5">Descrição</label>
										<input type="text" name="config[btn-espiar][text]" class="form-control" value="<?php echo $STORE['config']['btn-espiar']['text'];?>"/>
									</div>
									<div class="form-group col-lg-12 col-md-12 col-sm-12 mb15">
										<label class="show mb5">Class do Botão</label>
										<input type="text" name="config[btn-espiar][class]" class="form-control" value="<?php echo $STORE['config']['btn-espiar']['class'];?>"/>
									</div>
									<div class="form-group col-lg-12 col-md-12 col-sm-12">
										<label class="show mb5">Icone do Botão</label>
										<input type="text" name="config[btn-espiar][class-icon]" class="form-control" value="<?php echo $STORE['config']['btn-espiar']['class-icon'];?>"/>
									</div>
								</div>
							</div>
						</div>
						<button type="submit" class="mt15 btn btn-primary">Salvar</button>
					</form>
					<!--[\OUTRAS CONFIGURAÇÃO]-->
				</div>
			</div>
		</div>
	</div>
</div>
<?php ob_start(); ?>
<script>
	<?php require PATH_ROOT . '/public/bootstrap/js/bootstrap.js';?>
	
	$("#hex-01").ColorPicker({
		color: "<?php echo $STORE['color001']??color001?>",
		onChange: function (hsb, hex, rgb) { 
			$("#hex-01").val("#" + hex).prev().css({"background-color" : "#" + hex }); 
		}
	}).prev().css({"background-color" : "<?php echo $STORE['color001']??color001?>" });

	$("#hex-02").ColorPicker({
		color: "<?php echo $STORE['color002']??color002?>",
		onChange: function (hsb, hex, rgb) {
			$("#hex-02").val("#" + hex).prev().css({"background-color" : "#" + hex });
		}
	}).prev().css({"background-color" : "<?php echo $STORE['color002']??color002?>" });

	$("#bg-01").ColorPicker({
		color: "<?php echo $STORE['background001']??background001?>",
		onChange: function (hsb, hex, rgb) {
			$("#bg-01").val("#" + hex).prev().css({"background-color" : "#" + hex });
		}
	}).prev().css({"background-color" : "<?php echo $STORE['background001']??background001?>" });
	
	$("#bg-02").ColorPicker({
		color: "<?php echo $STORE['background002']??background002?>",
		onChange: function (hsb, hex, rgb) {
			$("#bg-02").val("#" + hex).prev().css({"background-color" : "#" + hex });
		}
	}).prev().css({"background-color" : "<?php echo $STORE['background002']??background002?>" });

	$("#bg-03").ColorPicker({
		color: "<?php echo $STORE['theme-color']?>",
		onChange: function (hsb, hex, rgb) {
			$("#bg-03").val("#" + hex).prev().css({"background-color" : "#" + hex });
		}
	}).prev().css({"background-color" : "<?php echo $STORE['theme-color']??'#000'?>" });

	$("#tabs").on("click", "a[href]", function (e) {
		e.preventDefault()
		$(this).tab('show');
	});

	var test_hash = window.location.hash;
	if(test_hash !== "") $("a[href='"+test_hash+"']").trigger("click");

	// $("a[href='#configuracao_loja']").trigger("click");

    busca_cidade = (function ( a, b ) {
        var cep = a;
        $.ajax({
            url: "../../",
            type: "post",
            data: { acao : "BuscaCidade", cep : cep },
            dataType: "json",
            beforeSend: function() {
                $("input[name=cidade]").val("Carregando...");
                $("input[name=uf]").val("");
            }, 
            success: function( str ) {
                $("input[name=cidade]").val( str.cidade );
                $("input[name=uf]").val( str.uf );
            }, 
            error: function( x,m,t ){ 
                alert( x.responseText ); 
            }
        });
    });

    mascara = (function (str) {	
        if (str.value.length > 14)                       
            str.value = cnpj(str.value);
        else                           
            str.value = cpf(str.value);
    });

    cpf = (function (valor) {
        valor = valor.replace(/\D/g, "");                   
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)$/, "$1-$2");     
        return valor;
    })

    cnpj = (function (valor) {
        valor = valor.replace(/\D/g, "");
        valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");
        valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
        valor = valor.replace(/\.(\d{3})(\d)/, ".$1/$2");
        valor = valor.replace(/(\d{4})(\d)/, "$1-$2");              
        return valor;
    });

	var SPMaskBehavior = function (val) {
		return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
	},
	spOptions = {
		onKeyPress: function(val, e, field, options) {
			field.mask(SPMaskBehavior.apply({}, arguments), options);
		}
	};

	$("input[name=cep]").mask("00000-000", { onComplete : busca_cidade });
	$("input[name=celular],input[name=telefone]").mask(SPMaskBehavior, spOptions);
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();

include '../rodape.php';
					<?php if ($modulo != 'identificacao') { ?>
					    <div class="rede-sociais-index mb50 clearfix" id="insta_feed"></div>
					<?php } ?>
					</div>
					</div>
					</div>
					<div class="rodape">
					    <ul>
					        <li>
					            <img src="<?php echo Imgs::src($CONFIG['logo_desktop'], 'imgs'); ?>" />
					            <?php if (!empty($CONFIG['fb_link'])) { ?>
					                <div class="fb-page" data-href="<?php echo $CONFIG['fb_link'] ?>" data-width="500" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true" data-show-posts="false">
					                    <div class="fb-xfbml-parse-ignore">
					                        <blockquote cite="<?php echo $CONFIG['fb_link'] ?>"><a href="<?php echo $CONFIG['fb_link'] ?>"></a></blockquote>
					                    </div>
					                </div>
					            <?php } ?>
					            <a href="<?php echo $CONFIG['fb_link'] ?>" target="_blank" title="Facebook">
					                <i class="fa fa-2x fa-facebook-square"></i>
					            </a>
					            <a href="<?php echo $CONFIG['insta_link'] ?>" target="_blank" title="Instagram">
					                <i class="fa fa-2x fa-instagram"></i>
					            </a>
					            <a href="<?php echo $CONFIG['whatsapp'] ?>" target="_blank" title="Whatsapp">
					                <i class="fa fa-2x fa-whatsapp"></i>
					            </a>
					        </li>
					    </ul>
					    <ul>
					        <li class="container">
					            <div class="row">
					                <div class="col-md-12 col-xs-12 text-center f-h5">
					                    <?php
                                        echo !empty($CONFIG['endereco']) ? "{$CONFIG['endereco']}" : '';
                                        echo !empty($CONFIG['endereco']) ? ", {$CONFIG['numero']}" : '';
                                        echo !empty($CONFIG['bairro']) ? " - {$CONFIG['bairro']}" : '';
                                        echo !empty($CONFIG['cidade']) ? " {$CONFIG['cidade']}" : '';
                                        echo !empty($CONFIG['uf']) ? "/{$CONFIG['uf']}" : '';
                                        echo !empty($CONFIG['cep']) ? ' - ' . preg_replace("/^(\d{5})(\d{3})$/", "\\1-\\2", $CONFIG['cep']) : '';
                                        echo !empty($CONFIG['nome_fantasia']) ? "<br/>{$CONFIG['nome_fantasia']}" : '';
                                        echo !empty($CONFIG['cnpj']) ? " CNPJ: {$CONFIG['cnpj']}" : '';
                                        ?>
					                    <br>
					                    <span style="font-weight: normal;">2022 - MSA Produtos para confeitaria e sorveteria</span>
                                        <a href="https://www.datacontrolinformatica.com.br/">
                                            <img src="<?php echo Imgs::src("rubrica.png", 'imgs'); ?>" class="rubrica" style="position: absolute; right: 16%; bottom: 3px; z-index: 11; width: 70px;" width="16">
                                        </a>
                                    </div>
					            </div>
					        </li>
					    </ul>
					</div>
					<div style="position:fixed;right:15px;bottom:15px;z-index:999999;">
					    <a href="https://<?php echo $MobileDetect->isMobile() ? 'api' : 'web' ?>.whatsapp.com/send?phone=<?php echo soNumero('55' . $CONFIG['celular']) ?>&text=Oi! Estou entrando em contato pelo chat Whatsapp da <?php echo $CONFIG['nome_fantasia'] ?>. Poderia me ajudar?" target="_blank"><img src="<?php echo Imgs::src('whatsapp.png', 'imgs') ?>" width="150"></a>
					</div>
					<?php
                    include dirname(__DIR__) . '/_layout/layout-footer.php';
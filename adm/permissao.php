<?php
require_once 'topo.php';
$permissao_lista = require PATH_ROOT . 'adm/permissao-list.php';

$date = date('Y-m-d H:i:s');
$include_pages = false;

/**
 * Exclui e remove o usuario
 * ExcluirPermissao
 */
if (isset($GET['acao']) && $GET['acao'] == 'ExcluirPermissao') {
    if (AdmPermissoes::delete_all(['conditions' => ['id_adm_grupos=? and id_adm=?', (int)$GET['id_adm_grupos'], (int)$GET['id_adm']]])) {
        header('Location: /adm/permissao.php?acao=EditarPermissao&adm_id=' . (int)$GET['id_adm']);
        return;
    }
}

/**
 * Exclui e remove o usuario
 * ExcluirPermissao
 */
if (isset($GET['acao']) && $GET['acao'] == 'ExcluirAdm') {
    if (Adm::delete_all(['conditions' => ['id=?', $GET['adm_id']]])) {
        header('Location: /adm/permissao.php');
        return;
    }
}

$configure_pages = null;
if (isset($POST['acao']) && $POST['acao'] == 'AlterarPermissaoUsuarios') {
    
    // printf('<div id="div-edicao"><pre>%s', print_r($permissao_lista[$POST['value']], 1));
    // printf('<div id="div-edicao"><pre>%s', print_r($POST, 1));
    // printf('<div id="div-edicao"><pre>');
    // return;

    if (!empty($permissao_lista[$POST['value']])) 
    {
        foreach ($permissao_lista[$POST['value']] as $key => $grupos) 
        {
            $conditions = null;
            if (AdmPermissoes::count(['conditions' => ['pagina=? and id_adm=?', converter_texto($key), $GET['adm_id']]]) > 0) {
                $conditions['conditions']['id_adm'] = $GET['adm_id'];
                $conditions['conditions']['pagina'] = converter_texto($key);

                foreach ($grupos as $k => $outros) {
                    $conditions['set'][$k] = (string)$outros;
                }
                
                try {
                    echo "A: ";
                    print_r($conditions);
                    AdmPermissoes::update_all($conditions);
                    
                } catch (Exception $ex) {
                    echo $k . '<br/>';
                    print_r($ex->getMessage()) . PHP_EOL;
                }
            } 
            else {
                $conditions = null;
                $conditions['id_adm'] = $GET['adm_id'];
                $conditions['pagina'] = converter_texto($key);

                foreach ($grupos as $k => $outros) {
                    $conditions[$k] = (string)$outros;
                }
                
                try {
                    echo "B: ";
                    print_r($conditions);
                    AdmPermissoes::create($conditions);
                    
                } catch (Exception $ex) {
                    echo $k . '<br/>';
                    print_r($ex->getMessage()) . PHP_EOL;
                }
            }
            unset($conditions);
        }
        
        header(sprintf('Location: /adm/permissao.php?acao=EditarPermissao&adm_id=%u&q=%s', $GET['adm_id'], $GET['q']));
        return;
    }

    /**
     * Edita paginas grupos especificos
     */
    if (isset($POST['id'], $POST['campo']) && $POST['id'] > 0) {
        $AdmPermissoes = AdmPermissoes::find($POST['id']);
        $AdmPermissoes->{$POST['campo']} = $POST['value'];
        $AdmPermissoes->save();
        
        header(sprintf('Location: /adm/permissao.php?acao=EditarPermissao&adm_id=%u&q=%s', $GET['adm_id'], $GET['q']));
        return;
    }
}

/**
 * Editar/Cadastrar usuarios
 */
if (isset($POST['acao']) && $POST['acao'] == 'usuario') {
    
    if ($GET['acao'] == 'CadastrarUsuario') {
        $Adm = new Adm();
        $include_pages == true;
    } else {
        $Adm = Adm::find($POST['adm_id']);
    }

    $Adm->apelido = $POST['apelido'];
    $Adm->usuario = $POST['usuario'];
    $Adm->permissao = 1;
    $Adm->data_cadastro = $date;

    $Adm->save();

    $adm_id = isset($POST['adm_id']) && $POST['adm_id'] > 0 ?  $POST['adm_id'] : $Adm->id;

    if ($include_pages == true) {
        AdicionarVerificaPermissao($PgAt, $adm_id, 0);
    }

    if (isset($POST['senha']) && $POST['senha'] != '') {
        $Adm = Adm::find($POST['adm_id']);
        $Adm->senha = $POST['senha'];
        $Adm->save();
    }

    if (isset($_FILES)) {
        $IMG = current($_FILES);
        $CAMINHO = './../public/imgs/usuarios/';

        if ($IMG['size'] > 0) {
            $ext = pathinfo($IMG['name']);
            $ext = $ext['extension'];

            $Adm = Adm::find($POST['adm_id']);

            $NOVO_NOME_IMAGEM = $Adm->foto != '' ? $Adm->foto : uniqid(time()) . '-' . time() . '.' . $ext;

            /**
             * Carregar a imagem no upload
             */
            $WideImageTmpName = WideImage\WideImage::load($IMG['tmp_name']);
            $WideImage105x105 = $WideImageTmpName->resize(105, 105);

            /**
             * Carregar quadro da imagem
             */
            $WideImageSquare = WideImage\WideImage::load('../public/imgs/_quadro.' . $ext);
            $WideImageSquare105x105 = $WideImageSquare->resize(105, 105);

            $WideImageSquare105x105->merge($WideImage105x105, 'center', 'center', 98)->saveToFile($CAMINHO . $NOVO_NOME_IMAGEM);

            $WideImageSquare->destroy();
            $WideImageTmpName->destroy();
            $WideImageSquare105x105->destroy();

            $Adm->foto = $NOVO_NOME_IMAGEM;
            $Adm->save();
        }
    }
    header('Location: /adm/permissao.php');
    return;
}

$usuario = isset($GET['acao'], $GET['adm_id']) && $GET['adm_id'] != '' ? Adm::find($GET['adm_id']) : null;
if (!empty($usuario)) {
    $usuario = $usuario->to_array();
}

?>
<div class="tag-opcoes clearfix" id="div-edicao">
    <?php // print_r($configure_pages) ?>
    <h2>
        <?php echo !empty($usuario['apelido']) ? "Usuário - {$usuario['apelido']}" : 'PERMISSÕES DE USUÁRIOS' ?>
        <a href="/adm/permissao.php" class="btn btn-primary pull-right<?php echo empty($GET['acao']) ? ' hidden' : '';?>" <?php echo _P('permissao', $_SESSION['admin']['id_usuario'], 'incluir') ?>>
            voltar
        </a>
        <a href="/adm/permissao.php?acao=CadastrarUsuario" class="btn btn-primary pull-right<?php echo !empty($GET['acao']) ? ' hidden' : '';?>" <?php echo _P('permissao', $_SESSION['admin']['id_usuario'], 'incluir') ?>>
            adicionar usuário
        </a>
    </h2>
    <table width="100%" border="0" cellpadding="8" cellspacing="0">
        <tbody>
            <!--[ADICIONAR/EDITAR USUÁRIOS]-->
            <?php if (isset($GET['acao']) && ($GET['acao'] == 'CadastrarUsuario' || $GET['acao'] == 'EditarUsuario')) { ?>
                <tr>
                    <td>
                        <form action="/adm/permissao.php?acao=<?php echo $GET['acao']?>" method="post" enctype="multipart/form-data" class="col-md-5 col-md-offset-3 fieldset">
                            <div class="clearfix">
                                <span class="pull-left" style="width: 105px; height: 105px;">
                                    <input type="file" name="foto" class="hidden" id="foto">
                                    <label for="foto" style="width: 105px; height: 105px; position: relative; cursor: pointer">
                                        <img src="<?php echo $usuario['foto'] ? Imgs::src("usuarios-{$usuario['foto']}", 'public') : Imgs::src('sem-foto-produto.png', 'public')?>" class="show" height="105" width="105" style="position: relative;" />
                                        <span style="position: absolute; z-index: 2; font-size: 12px; bottom: 15px; left: 0; width: 105px; text-align: center;">Adicionar/Editar</span>
                                    </label>
                                </span>
                                <span class="pull-left ml15">
                                    <label class="show mb5">Apelido:</label>
                                    <input type="text" name="apelido" size="25" value="<?php echo $usuario['apelido']?>" />
                                </span>
                            </div>
                            <label class="show mb5">E-mail:</label>
                            <input type="text" name="usuario" size="42" value="<?php echo $usuario['usuario']?>"/>

                            <label class="show mb5 mt15">Senha:</label>
                            <input type="password" name="senha"/>

                            <span class="show mt15">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                                <a href="/adm/permissao.php" class="btn btn-danger">Voltar</a>
                            </span>
                            <input type="hidden" name="acao" value="usuario">
                            <input type="hidden" name="adm_id" value="<?php echo $usuario['id']?>">
                        </form>
                    </td>
                </tr>
            <!--[ADICIONAR/EDITAR PERMISSÕES DE USUÁRIOS]-->
            <?php } else if (isset($GET['acao'], $GET['adm_id']) && $GET['acao'] == 'EditarPermissao') { ?>
                <tr>
                    <td nowrap="nowrap" width="1%">
                        <p>Pesquisar páginas</p>
                        <form action="/adm/permissao.php" id="pesquisar-paginas">
                            <input type="hidden" name="acao" value="<?php echo $GET['acao']?>" />
                            <input type="hidden" name="adm_id" value="<?php echo $GET['adm_id']?>" />
                            <input type="text" name="q" style="width: 320px" value="<?php echo !empty($GET['q']) && $GET['q'] != '' ? $GET['q'] : ''?>" />
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i>
                            </button>
                            <?php if (!empty($GET['q']) && $GET['q'] != '') { ?>
                                <a class="btn btn-info" href="/adm/permissao.php?acao=<?php echo $GET['acao'] ?>&adm_id=<?php echo $GET['adm_id']?>">
                                    <i class="fa fa-close"></i> Limpar busca
                                </a>
                            <?php } ?>
                        </form>
                    </td>
                    <td colspan="8">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="bold mb5 mt0">Selecione as funções para o administrador</h4>
                            </div>
                            <?php 
                            $x = 0;
                            foreach($permissao_lista as $gp_k => $group) { ?>
                                <div class="col-md-2 mb5">
                                    <input type="checkbox" id="_gp_<?php echo $x?>" name="<?php echo $gp_k?>" value="<?php echo $gp_k?>"/>
                                    <label for="_gp_<?php echo $x?>" class="input-checkbox"></label>
                                    <strong class="ft12px bold text-capitalize"><?php echo $gp_k?></strong>
                                </div>
                            <?php $x++; } ?>
                        </div>
                    </td>

                    <!-- <td align="center">
                        <input type="checkbox" id="__administrador01" name="__administrador" value="admin" />
                        <label for="__administrador01" class="input-checkbox"></label>
                        Aministrador
                    </td>
                    <td align="center">
                        <input type="checkbox" id="__administrador02" name="__administrador" value="users" />
                        <label for="__administrador02" class="input-checkbox"></label>
                        Usuário
                    </td>
                    <td align="center">
                        <input type="checkbox" id="__nfe" name="__nfe" value="nfe" />
                        <label for="__nfe" class="input-checkbox"></label>
                        Nfe
                    </td> -->
                </tr>
                
                <tr<?php echo empty($GET['q']) && $GET['q'] == '' ? ' class="hidden"' : ' class="plano-fundo-adm-003"' ?>>
                    
                    <td bgcolor="#ffffff">-</td>
                    <td bgcolor="#ffffff">-</td>
                    <td bgcolor="#ffffff">-</td>
                    <td bgcolor="#ffffff">-</td>
                    <td align="center" bgcolor="#ffffff">
                        <input type="checkbox" id="__acessar" name="acessar" value="1" />
                        <label for="__acessar" class="input-checkbox"></label>
                    </td>
                    <td align="center" bgcolor="#ffffff">
                        <input type="checkbox" name="incluir" id="__incluir" value="1" />
                        <label for="__incluir" class="input-checkbox"></label>
                    </td>
                    <td align="center" bgcolor="#ffffff">
                        <input type="checkbox" name="alterar" id="__alterar" value="1" />
                        <label for="__alterar" class="input-checkbox"></label>
                    </td>
                    <td align="center" bgcolor="#ffffff">
                        <input type="checkbox" name="excluir" id="__excluir" value="1" />
                        <label for="__excluir" class="input-checkbox"></label>
                    </td>
                    </tr>
                    <?php
                    $Grupos = null;
                    $AdmGrupos = AdmGrupos::all();

                    $conditions_array = '1 = 1 ';

                    if (!empty($GET['q']) && $GET['q'] != '') {
                        $conditions_array .= sprintf('and adm_permissoes.pagina like "%s%%" ', $GET['q']);
                    }

                    if (!empty($GET['adm_id']) && $GET['adm_id'] != '') {
                        $conditions_array .= sprintf('and adm_permissoes.id_adm = %u ', $GET['adm_id']);
                    }

                    $conditions['conditions'] = $conditions_array;
                    $conditions['joins'] = ['grupo'];
                    $conditions['order'] = 'adm_grupos.grupo desc, adm_grupos.ordem asc, adm_permissoes.ordem asc, adm_permissoes.status desc';

                    $AdmPermissoes = AdmPermissoes::all($conditions);

                    foreach ($AdmPermissoes as $rs) {
                        if ($Grupos != $rs->grupo->grupo) {
                            $Grupos = $rs->grupo->grupo;
                            ?>
                            <tr class="plano-fundo-adm-003 bold" onclick="$('tr.<?php echo $Grupos?>').slideToggle();">
                                <td colspan="9" class="ft14px text-uppercase">
                                    <?php echo $Grupos;?>
                                    <a href="/adm/permissao.php?acao=ExcluirPermissao&id_adm_grupos=<?php echo $rs->id_adm_grupos?>&id_adm=<?php echo $rs->id_adm?>" class="ml15 btn btn-danger btn-xs pull-right">
                                        <i class="fa fa-trash"></i> remover
                                    </a>
                                    <span class="btn btn-success btn-xs pull-right">
                                        <i class="fa fa-plus"></i> <?php echo $Grupos?>
                                    </span>
                                </td>
                            </tr>
                            <tr><td colspan="9"></td></tr>
                            <tr class="plano-fundo-adm-002 <?php echo $Grupos?> ft10px" style="color: #fff; display: none;">
                                <td class="bold text-uppercase">Páginas/Nome</td>
                                <td class="bold text-uppercase">Grupos</td>
                                <td class="bold text-uppercase">Ordem</td>
                                <td class="bold text-uppercase">Status</td>
                                <td class="bold text-uppercase">Acessar</td>
                                <td class="bold text-uppercase">Cadastrar</td>
                                <td class="bold text-uppercase">Editar</td>
                                <td class="bold text-uppercase">Excluir</td>
                            </tr>
                        <?php } ?>

                        <tr class="lista-zebrada in-hover <?php echo $Grupos?>" style="display: none;">
                            <td>
                                <label class="show"><?php echo strtolower(str_replace('-', ' ', $rs->pagina)) ?></label>
                                <input type="text" name="pagina_rename" id="pagina_rename<?php echo $rs->id?>" value="<?php echo $rs->pagina_rename?>" class="form-input pagina_rename" style="width: 100%;" autocomplete="off"/>
                            </td>
                            <td nowrap="nowrap" width="1%">
                                <select name="id_adm_grupos" id="id_adm_grupos<?php echo $rs->id?>" style="width: 305px;">
                                    <option value="0">Selecione...</option>
                                    <?php foreach ($AdmGrupos as $grupos) { ?>
                                        <option value="<?php echo $grupos->id?>" <?php echo ($rs->id_adm_grupos == $grupos->id ? ' selected' : '') ?>>
                                            <?php echo $grupos->grupo ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td nowrap="nowrap" width="1%">
                                <input type="text" name="ordem" id="ordem<?php echo $rs->id?>" value="<?php echo $rs->ordem?>" style="width: 55px" class="ordem" autocomplete="off"/>
                            </td>
                            <td nowrap="nowrap" width="1%" align="center">
                                <input type="checkbox" name="status" id="status<?php echo $rs->id?>" value="<?php echo $rs->status == 0 ? 1 : 0?>" <?php echo $rs->status == 1 ? 'checked':''?>/>
                                <label for="status<?php echo $rs->id?>" class="input-checkbox"></label>
                            </td>
                            <td nowrap="nowrap" width="1%" align="center">
                                <input type="checkbox" name="acessar" id="acessar<?php echo $rs->id?>" value="<?php echo $rs->acessar == 0 ? 1 : 0?>" <?php echo $rs->acessar == 1 ? 'checked':''?>/>
                                <label for="acessar<?php echo $rs->id?>" class="input-checkbox"></label>
                            </td>
                            <td nowrap="nowrap" width="1%" align="center">
                                <input type="checkbox" name="incluir" id="incluir<?php echo $rs->id?>" value="<?php echo $rs->incluir == 0 ? 1 : 0?>" <?php echo $rs->incluir == 1 ? 'checked':''?>/>
                                <label for="incluir<?php echo $rs->id?>" class="input-checkbox"></label>
                            </td>
                            <td nowrap="nowrap" width="1%" align="center">
                                <input type="checkbox" name="alterar" id="alterar<?php echo $rs->id?>" value="<?php echo $rs->alterar == 0 ? 1 : 0?>" <?php echo $rs->alterar == 1 ? 'checked':''?>/>
                                <label for="alterar<?php echo $rs->id?>" class="input-checkbox"></label>
                            </td>
                            <td nowrap="nowrap" width="1%" align="center">
                                <input type="checkbox" name="excluir" id="excluir<?php echo $rs->id?>" value="<?php echo $rs->excluir == 0 ? 1 : 0?>" <?php echo $rs->excluir == 1 ? 'checked':''?>/>
                                <label for="excluir<?php echo $rs->id?>" class="input-checkbox"></label>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <!--[LISTAGEM DE USUÁRIOS]-->
                    <tr class="plano-fundo-adm-003">
                        <td>Apelido</td>
                        <td>Usuário</td>
                        <td>Ações</td>
                    </tr>
                    <?php
                    $result = Adm::all(['conditions' => ['id > 0']]);
                    foreach ($result as $rs) { ?>
                        <tr class="lista-zebrada in-hover">
                            <td nowrap="nowrap" width="1%">
                                <?php echo $rs->apelido ?>
                            </td>
                            <td>
                                <?php echo $rs->usuario ?>
                            </td>
                            <td nowrap="nowrap" width="1%" align="center">
                                <a class="btn btn-primary btn-sm" href="/adm/permissao.php?acao=EditarUsuario&adm_id=<?php echo $rs->id?>">Editar dados</a>
                                <a class="btn btn-danger btn-sm" href="/adm/permissao.php?acao=EditarPermissao&adm_id=<?php echo $rs->id?>">Editar Permissão</a>
                                <a class="btn btn-danger-default btn-sm" href="/adm/permissao.php?acao=ExcluirAdm&adm_id=<?php echo $rs->id?>">Excluir</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
        </tbody>
    </table>
</div>
<?php ob_start(); ?>
<script>
    function ActionPermissoes($id, $text, $value) {
        $.ajax({
            global: false,
            url: window.location.href,
            type: "post",
            data: {
                acao: "AlterarPermissaoUsuarios",
                id: $id,
                campo: $text,
                value: $value
            },
            beforeSend: function (){
            },
            success: function(str) {
                var list = $("<div/>", { html: str });
                if($text == '_gp_') 
                    $("#div-edicao").html(list.find("#div-edicao").html());
            },
            error: function(a, b, c) {
                console.log(a.reponseText + "\n" + b + "\n" + c);
            }
        });
    };
    $(document).on("click", "a", function(e) {
        var href = this.href || e.target.href;
        if (href.search("ExcluirAdm") > 0 || href.search("ExcluirPermissao") > 0)
            if (!confirm("Deseja realmente excluir!")) return false;

    });
    $("#div-edicao").on("change", "select[name], input[class=ordem], input[class=pagina_rename]", function(e) {
        var $click = e.target,
            $id = $click.id.replace(/\D/g, ''),
            $text = $click.id.replace(/\d+/g, ''),
            $value = $click.value;

        ActionPermissoes($id, $text, $value);
    });

    $("#div-edicao").on("click", "input[type=checkbox]", function(e) {
        var $click = e.target,
            $id = $click.id.replace(/\D/g, ''),
            $text = $click.id.replace(/\d+/g, ''),
            $value = $click.value;
        
        console.log($id, $text, $value);

        if ($click.id[0] === '_') {
            $("#div-edicao").find("input[name=" + $click.id.replace("__", "") + "]").click();
        }
        
        ActionPermissoes($id, $text, $value);
    });
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();


require_once 'rodape.php';

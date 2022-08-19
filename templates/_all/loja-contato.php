<form class="col-lg-12 col-md-12 co-sm-12 col-xs-12 form-contato" method="post" action="">
  <div class="row mt15">
    <span class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
      <label>{$nome}</label>
      <input type="text" value="<?php echo !empty($POST['nome']) ? $POST['nome'] : '' ?>" name="nome">
    </span>
  </div>
  <div class="row mt15">
    <span class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
      <label>{$email}</label>
      <input type="text" value="<?php echo !empty($POST['email']) ? $POST['email'] : '' ?>" name="email">
    </span>
  </div>
  <div class="row mt15">
    <span class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
      <label>{$telefone}</label>
      <input type="text" value="<?php echo !empty($POST['telefone']) ? $POST['telefone'] : '' ?>" name="telefone">
    </span>
  </div>
  <div class="row mt15">
    <span class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
      <label>{$celular}</label>
      <input type="text" value="<?php echo !empty($POST['celular']) ? $POST['celular'] : '' ?>" name="celular">
    </span>
  </div>
  <div class="row mt15">
    <span class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
      <label>{$cidade}</label>
      <input type="text" value="<?php echo !empty($POST['cidade']) ? $POST['cidade'] : '' ?>" name="cidade">
    </span>
  </div>
  <div class="row mt15">
    <span class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
      <label>{$assunto}</label>
      <input type="text" value="<?php echo !empty($POST['assunto']) ? $POST['assunto'] : '' ?>" name="assunto">
    </span>
  </div>
  <div class="row mt15">
    <span class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
      <label>{$mensagem}</label>
      <textarea name="mensagem"><?php echo !empty($POST['mensagem']) ? $POST['mensagem'] : '' ?></textarea>
    </span>
  </div>
  {$teste}
  <div class="clearfix mt15">
    <button type="submit" class="btn btn-primary">enviar</button>
  </div>
  <input type="hidden" name="acao" value="enviar" />
</form>

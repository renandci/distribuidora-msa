<div class="clearfix mb15">
  <label class="pull-left w100 mb5">Número do Cartão: <small>(apenas números)</small> *</label>
  <div class="input-falsos">
    <input autocomplete="off" type="tel" class="w80" id="cardNumber" data-checkout="cardNumber" name="pagamento[cardNumber]" tabindex="1" />
  </div>
</div>

<div class="clearfix row mb15">
  <div class="col-md-6 col-sm-6 col-xs-12">
    <label class="pull-left w100 mb5">Validade (mm / aa) *</label>
    <div class="input-falsos">
      <input autocomplete="off" type="tel" id="cardExpiration" data-checkout="cardExpiration" name="pagamento[cardExpiration]" maxlength="7" tabindex="2" />
    </div>
  </div>
  <select id="cardExpirationMonth" data-checkout="cardExpirationMonth" style="display: none;">
    <option value="">Mes</option>
    <?php for ($mes = 1; $mes <= 12; ++$mes) { ?>
      <option value="<?php echo str_pad($mes, 2, '0', STR_PAD_LEFT); ?>"><?php echo str_pad($mes, 2, '0', STR_PAD_LEFT); ?></option>
    <?php } ?>
  </select>

  <select id="cardExpirationYear" data-checkout="cardExpirationYear" style="display: none;">
    <option value="">Ano</option>
    <?php for ($ano = date('Y'); $ano <= (date('Y') + 10); ++$ano) { ?>
      <option value="<?php echo $ano; ?>"><?php echo $ano; ?></option>
    <?php } ?>
  </select>
  <div class="col-md-6 col-sm-6 col-xs-12">
    <label class="pull-left w100 mb5">Código de Segurança *</label>
    <div class="input-falsos">
      <input autocomplete="off" type="tel" id="securityCode" data-checkout="securityCode" maxlength="4" name="pagamento[securityCode]" tabindex="3" />
    </div>
  </div>
</div>

<div class="clearfix mb15">
  <label class="pull-left w100 mb5">Formas de Parcelamento *</label>
  <div class="input-falsos clearfix">
    <select id="installments" data-checkout="installments" class="select-parcels medium select-personalizado" style="width: 100%;" name="pagamento[installments]" tabindex="5" installments>
      <option value="">Selecione...</option>
    </select>
    <select id="issuer" data-checkout="issuer" class="select-parcels medium select-personalizado" style="width: 100%; display:none" name="pagamento[issuer]" tabindex="6">
      <option value="-1">Selecione...</option>
    </select>
  </div>
</div>

<div class="clearfix mb15">
  <label class="pull-left w100 mb5">Titular do Cartão: <small>(nome igual ao que está no cartão)</small>*</label>
  <div class="input-falsos">
    <input id="cardholderName" type="text" data-checkout="cardholderName" name="pagamento[cardholderName]" tabindex="7" style="width: 100%;" autocomplete="off" />
  </div>
</div>

<div class="clearfix mb15" id="card-doc-number" style="display: none;">
  <label class="pull-left w100 mb5">CPF/CNPJ: *</label>
  <div class="input-falsos">
    <?php
    $DocNumber = (Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']], 'select' => 'cpfcnpj']))->cpfcnpj;
    ?>
    <input id="docNumber" type="tel" data-checkout="docNumber" name="pagamento[docNumber]" value="<?php echo soNumero($DocNumber) ?>" style="width: 100%;" autocomplete="off" tabindex="8" />
  </div>
</div>
<input autocomplete="off" type="hidden" id="cardBrand" data-checkout="cardBrand" name="pagamento[cardBrand]" tabindex="-1" />
<select id="docType" data-checkout="docType" style="display: none;"></select>

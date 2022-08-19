<li>
  <div class="clearfix">
    <input type="radio" class="input-radio" id="CartaoMp" name="pagamento[FormaPagamento]" data-pgto="pagamento" value="Mp Cartão" />
    <label for="CartaoMp" class="fa ft22px"></label>
    Mercado Pago <span class="ft18px color-004" total_carrinho></span>
  </div>
  <!--
    <div data-hidden="hidden" style="display: none;">
        <div class="clearfix mb15">
            <label class="pull-left w100 mb5">Número do Cartão: <small>(apenas números)</small> *</label>
            <div class="input-falsos">
                <input autocomplete="off" type="tel" class="w80" id="cardNumber" data-checkout="cardNumber" tabindex="1"/>
            </div>
        </div>
        <div class="clearfix mb15 row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <label class="pull-left w100 mb5">Validade (mm/aaaa) *</label>
                <div class="input-falsos">
                    <input autocomplete="off" type="tel" id="cardExpiration" data-checkout="cardExpiration" maxlength="9" tabindex="2"/>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <label class="pull-left w100 mb5">Código de Segurança *</label>
                <div class="input-falsos">
                    <input autocomplete="off" type="tel" id="securityCode" data-checkout="securityCode" maxlength="4" tabindex="3"/>
                </div>
            </div>
        </div>
        <div class="clearfix mb15">
            <label class="pull-left w100 mb5">Titular do Cartão: <small>(igual ao que está no cartão)</small>*</label>
            <div class="input-falsos">
                <input autocomplete="off" type="text" class="w95" id="cardholderName" data-checkout="cardholderName" tabindex="4"/>
            </div>
        </div>

        <div class="clearfix mb15">
            <label class="pull-left w100 mb5">Número de parcelas *</label>
            <div class="input-falsos clearfix">
                <select
                    id="installments"
                    data-checkout="installments" class="select-parcels medium select-personalizado" name="installments" style="width: 100%;" tabindex="5">
                    <option value="">Selecione...</option>
                </select>
                <select id="issuer" data-checkout="issuer" class="select-parcels medium select-personalizado" style="width: 100%; display:none" tabindex="6">
                    <option value="">Selecione...</option>
                </select>
            </div>
        </div>

        <div class="clearfix mb15">
            <label class="pull-left w100 mb5">CPF: <small>(de preferência do titular do cartão)</small> *</label>
            <div class="input-falsos clearfix">
                <input autocomplete="off" type="text" id="docNumber" data-checkout="docNumber" value="<?php echo soNumero($Cliente->cpfcnpj) ?>" tabindex="7" style="width: 100%;" not_empty="true"/>
                <select id="docType" data-checkout="docType" class="tag-hidden" style="display: none"></select>
            </div>
        </div>
    </div>
    -->
</li>

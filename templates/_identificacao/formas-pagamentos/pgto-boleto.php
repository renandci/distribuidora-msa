<li>
  <div class="clearfix">
    <input type="radio" class="input-radio" id="Boleto" name="pagamento[FormaPagamento]" data-pgto="pagamento" value="Boleto" />
    <label for="Boleto" class="fa ft22px"></label>
    Boleto bancário <span class="ft25px color-004" total_boleto></span>
  </div>
  <div data-hidden="hidden" style="display: none;" class="ft12px mb5">
    <?php echo !empty($CONFIG['desconto_boleto']) ? '(Desconto de ' .  floatval($CONFIG['desconto_boleto']) . '%)' : '' ?>
  </div>
</li>

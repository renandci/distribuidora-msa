<li>
  <div class="clearfix">
    <input type="radio" class="input-radio" id="Pix" name="pagamento[FormaPagamento]" data-pgto="pagamento" value="Pix" />
    <label for="Pix" class="fa ft22px"></label>
    Pagamento Via Pix <span class="ft25px color-004" total_boleto></span>
  </div>
  <div data-hidden="hidden" style="display: none;" class="ft12px mb5">
    <?php echo !empty($CONFIG['desconto_boleto']) ? '(Desconto de ' .  floatval($CONFIG['desconto_boleto']) . '%)' : '' ?>
  </div>
</li>

<li>
  <div class="clearfix">
    <input type="radio" class="input-radio" id="Transferencia" name="pagamento[FormaPagamento]" data-pgto="pagamento" value="Transferência" />
    <label for="Transferencia" class="fa ft22px"></label>
    Transferência <span class="ft25px color-004" total_transferencia></span>
  </div>
  <div data-hidden="hidden" style="display: none;" class="ft12px mb5">
    <?php echo !empty($CONFIG['desconto_boleto']) ? '(Desconto de ' .  floatval($CONFIG['desconto_boleto']) . '%)' : '' ?>
  </div>
</li>

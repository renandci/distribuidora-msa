<li>
  <div class="clearfix">
    <input type="radio" class="input-radio" id="PicPay" name="pagamento[FormaPagamento]" data-pgto="pagamento" value="PicPay" />
    <label for="PicPay" class="fa ft22px"></label>
    PicPay <span class="ft25px color-004" total_carrinho></span>
  </div>
  <div data-hidden="hidden" style="display: none;" class="ft12px mb5">
    <?php // echo !empty($CONFIG['desconto_boleto']) ? '(Desconto de '.  floatval($CONFIG['desconto_boleto']) . '%)': ''
    ?>
  </div>
</li>

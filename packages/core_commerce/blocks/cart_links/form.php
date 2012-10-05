<div class="ccm-block-field-group">
<h2><?php  echo t('Cart Links Options')?></h2>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td valign="top">
		<?php  echo $form->checkbox('showCartLink', 1, $showCartLink)?> <?php  echo $form->label('showCartLink', t('Show link to cart.'))?><br/>
		<span class="cc-cart-link" style="padding-left: 24px<?php  echo ($showCartLink?'':';display:none')?>"><?php  echo $form->label('cartLinkText', t('Cart Link text'))?>: <?php  echo $form->text('cartLinkText', $cartLinkText)?></span>
	</td>
</tr>
<tr>
	<td valign="top">
		<?php  echo $form->checkbox('showItemQuantity', 1, $showItemQuantity)?> <?php  echo $form->label('showItemQuantity', t('Show quantity of items in cart.'))?><br/>
	</td>
</tr>
<tr>
	<td valign="top">
		<?php  echo $form->checkbox('showCheckoutLink', 1, $showCheckoutLink)?> <?php  echo $form->label('showCheckoutLink', t('Show link to checkout directly.'))?><br/>
		<span class="cc-checkout-link" style="padding-left: 24px<?php  echo ($showCheckoutLink?'':';display:none')?>"><?php  echo $form->label('checkoutLinkText', t('Checkout Link text'))?>: <?php  echo $form->text('checkoutLinkText', $checkoutLinkText)?></span>
	</td>
</tr>
</table>
</div>

<div class="ccm-block-field-group">
<h2><?php  echo t('Preview')?></h2>
<div class="cc-cart-links">
	<a href="#" class="cc-cart-link" <?php  echo ($showCartLink?'':'style="display:none"')?>><span class="cc-cart-text"><?php  echo $cartLinkText?></span></a>
	<span class="cc-item-quantity" <?php  echo ($showItemQuantity?'':'style="display:none"')?>>(5 items)</span>
    <span class="cc-cart-links-divider" <?php  echo (($showCartLink||$showItemQuantity)&&$showCheckoutLink?'':'style="display:none"')?>>|</span>
	<a href="#" class="cc-checkout-link" <?php  echo ($showCheckoutLink?'':'style="display:none"')?>><span class="cc-checkout-text"><?php  echo $checkoutLinkText?></span></a>
</div >
</div>

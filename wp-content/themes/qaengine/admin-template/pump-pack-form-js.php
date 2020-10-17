<script type="text/template" id="template_edit_pump_pack">
	<form action="qa-update-badge" class="edit-plan engine-payment-form">
		<input type="hidden" name="id" value="{{= id }}">

		<div class="form payment-plan">
			<div class="form-item">
				<div class="label"><?php _e("Package name", ET_DOMAIN); ?></div>
				<input value="{{= post_title }}" class="bg-grey-input not-empty required" name="post_title" type="text">
			</div>
			<div class="form-item f-left-all clearfix">
				<div class="width33p">
					<div class="label"><?php _e("SKU",ET_DOMAIN);?></div>
					<input class="bg-grey-input width50p not-empty  required" name="sku" type="text" value="{{= sku }}"/>
				</div>

				<div class="width33p">
					<div class="label"><?php _e("Price",ET_DOMAIN);?></div>
					<input class="bg-grey-input width50p not-empty is-number required number" name="et_price" type="text" value="{{= et_price }}"/>
					<?php
					ae_currency_sign();
					?>
				</div>
			</div>
			<div class="form-item f-left-all clearfix">
				<div class="width33p">
					<div class="label"><?php _e("Pump number",ET_DOMAIN);?></div>
					<input class="bg-grey-input width50p not-empty is-number required number" type="text" name="et_pump_number" value="{{= et_pump_number }}"/>
					<?php _e("pumps",ET_DOMAIN);?>
				</div>
			</div>
			<div class="form-item">
				<div class="label"><?php _e("Short description about this package",ET_DOMAIN);?></div>
				<input class="bg-grey-input not-empty" name="post_content" type="text" value="{{= post_content }}" />
			</div>
			<div class="form-item">
				<input type="hidden" name="et_featured" value="0"/>
			</div>
			<div class="submit">
				<button  class="btn-button engine-submit-btn add_payment_plan">
					<span><?php _e( 'Save Package' , ET_DOMAIN ); ?></span><span class="icon" data-icon="+"></span>
				</button>
				or <a href="#" class="cancel-edit"><?php _e( "Cancel" , ET_DOMAIN ); ?></a>
			</div>
		</div>
	</form>
</script>
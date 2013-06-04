<?php defined('C5_EXECUTE') or die(_('Access Denied.')); ?>
<div class="form-horizontal">
	<fieldset>
		<legend><?php echo t('Attribute List Options')?></legend>

		<div class="control-group">
			<?php echo $form->label('attribute_category_id', t('Attribute Category')); ?>
			<div class="controls">
				<?php echo $form->select('attribute_category_id', $attribute_categories_list, $attribute_category_id); ?>
			</div>
		</div>
	</fieldset>
</div>
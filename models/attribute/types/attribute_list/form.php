<?php defined('C5_EXECUTE') or die(_('Access Denied.'));
$index = 0;
$attribute_key_id = $this->attributeKey->getAttributeKeyID();
$selected_keys = array();
$input_name = 'attribute_key_ids';
?>
<div id="attribute-list-form-<?php echo $attribute_key_id; ?>" class="form-horizontal attribute-list-form">
	<fieldset>
		<ul>
			<?php // first output our assigned keys ?>
			<?php foreach ($attribute_keys as $key): $selected_keys[] = $key['attribute_key_id']; ?>
			<li class="assigned"><i class="icon-align-justify"></i>
				<label>
					<?php echo $form->checkbox($input_name . '[' . $index . ']', $key['attribute_key_id'], true); ?>
					<span class="ak-name"><?php echo $key['ak']->getAttributeKeyName(); ?></span>
				</label>
				<span class="label label-success ak-type"><?php echo $text->unhandle($key['ak']->atHandle); ?></span>				
			</li>
			<?php $index++; endforeach; ?>

			<?php foreach ($attribute_keys_list as $key_id => $key): if (!in_array($key_id, $selected_keys)): ?>
			<li class="unassigned"><i class="icon-align-justify"></i>
				<label>
					<?php echo $form->checkbox($input_name . '[' . $index . ']', $key_id, false); ?>
					<span class="ak-name"><?php echo $key->getAttributeKeyName(); ?></span>
				</label>
				<span class="label ak-type"><?php echo $text->unhandle($key->atHandle); ?></span>
			</li>
			<?php endif; $index++; endforeach; ?>
		</ul>
	</fieldset>
	<script>
	$(function(){
		var attribute_list = $('#attribute-list-form-<?php echo $attribute_key_id; ?>');
		attribute_list.find('ul').sortable({
			stop: function(){
				attribute_list.find('input[type="checkbox"]').each(function(index, input){
					var attribs = { name: '<?php echo $input_name; ?>[' + index + ']', id: '<?php echo $input_name; ?>-' + index };
					$(input).attr(attribs);
				});
			}
		});
	});
	</script>
</div>
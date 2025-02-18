<?php
/* ----------------------------------------------------------------------
 * bundles/ca_object_representation_captions.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013-2022 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */ 
$vs_id_prefix 		= $this->getVar('placement_code').$this->getVar('id_prefix');
$t_instance 		= $this->getVar('t_subject');			// representation
$t_caption 			= $this->getVar('t_caption');			// caption	
$settings 			= $this->getVar('settings');
$vs_add_label 		= $this->getVar('add_label');

$vb_read_only		=	((isset($settings['readonly']) && $settings['readonly'])  || ($this->request->user->getBundleAccessLevel($t_instance->tableName(), 'ca_users') == __CA_BUNDLE_ACCESS_READONLY__));

$va_initial_values = $this->getVar('initialValues');
if (!is_array($va_initial_values)) { $va_initial_values = array(); }

print caEditorBundleShowHideControl($this->request, $vs_id_prefix);
print caEditorBundleMetadataDictionary($this->request, $vs_id_prefix, $settings);
?>
<div id="<?= $vs_id_prefix; ?>">
<?php
	//
	// Bundle template for new items
	//
?>
	<textarea class='caNewItemTemplate' style='display: none;'>
		<div id="<?= $vs_id_prefix; ?>Item_{n}" class="labelInfo">
<?php
	if (!$vb_read_only) {
?>	
			<div style="float: right;">
				<a href="#" class="caDeleteItemButton"><?= caNavIcon(__CA_NAV_ICON_DEL_BUNDLE__, 1); ?></a>
			</div>
<?php
	}
?>
			<div class="caListItem">
				<span class="formLabel"><?= _t('VTT or SRT format caption file'); ?></span>
				<?= $t_caption->htmlFormElement('caption_file', '^ELEMENT', array('name' => $vs_id_prefix.'_caption_file{n}', 'id' => $vs_id_prefix.'_caption_file{n}', 'no_tooltips' => true)); ?>
				
				<span class="formLabel"><?= _t('Locale'); ?></span>
				<?= $t_caption->htmlFormElement('locale_id', '^ELEMENT', array('name' => $vs_id_prefix.'_locale_id{n}', 'id' => $vs_id_prefix.'_locale_id{n}', 'no_tooltips' => true, 'dont_show_null_value' => true)); ?>
				
				<input type="hidden" name="<?= $vs_id_prefix; ?>_id{n}" id="<?= $vs_id_prefix; ?>_id{n}" value="{id}"/>
			</div>
		</div>
	</textarea>
<?php
	//
	// Bundle template for existing items
	//
?>		
	<textarea class='caItemTemplate' style='display: none;'>
		<div id="<?= $vs_id_prefix; ?>Item_{n}" class="labelInfo">
<?php
	if (!$vb_read_only) {
?>	
			<div style="float: right;">
				<a href="#" class="caDeleteItemButton"><?= caNavIcon(__CA_NAV_ICON_DEL_BUNDLE__, 1); ?></a>
			</div>
<?php
	}
?>
			<div class="caListItem">
				
				<span class="formLabel">{locale} ({filesize})</span>
				<?= urlDecode(caNavLink($this->request, caNavIcon(__CA_NAV_ICON_DOWNLOAD__, 1,  null, array('align' => 'top')), '', '*', '*', 'downloadCaptionFile', array('representation_id' => $t_instance->getPrimaryKey(), 'caption_id' => "{caption_id}", 'download' => 1), array('id' => "{$vs_id_prefix}download{caption_id}", 'class' => 'attributeDownloadButton'))); ?>
				
				<input type="hidden" name="<?= $vs_id_prefix; ?>_caption_id{n}" id="<?= $vs_id_prefix; ?>_caption_id{n}" value="{caption_id}"/>
			</div>
		</div>
	</textarea>
	
	
	
	<div class="bundleContainer">
		<div class="caItemList">
		
		</div>
<?php
	if (!$vb_read_only) {
?>	
		<div class='button labelInfo caAddItemButton'><a href='#'><?= caNavIcon(__CA_NAV_ICON_ADD__, '15px'); ?> <?= $vs_add_label ? $vs_add_label : _t("Add caption file"); ?></a></div>
<?php
	}
?>
	</div>
</div>
			
<script type="text/javascript">
	jQuery(document).ready(function() {
		caUI.initRelationBundle('#<?= $vs_id_prefix; ?>', {
			fieldNamePrefix: '<?= $vs_id_prefix; ?>_',
			templateValues: ['locale_id', 'locale', 'caption_id', 'filesize'],
			initialValues: <?= json_encode($va_initial_values); ?>,
			initialValueOrder: <?= json_encode(array_keys($va_initial_values)); ?>,
			itemID: '<?= $vs_id_prefix; ?>Item_',
			initialValueTemplateClassName: 'caItemTemplate',
			templateClassName: 'caNewItemTemplate',
			itemListClassName: 'caItemList',
			addButtonClassName: 'caAddItemButton',
			deleteButtonClassName: 'caDeleteItemButton',
			showEmptyFormsOnLoad: 0,
			readonly: <?= $vb_read_only ? "true" : "false"; ?>
		});
	});
</script>

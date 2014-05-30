<?php
$installer = $this;
$installer->startSetup();

$eavConfig = Mage::getSingleton('eav/config');

/**
 * TYPO3 Login Hash
 * Attribute Creation
 */
$installer->addAttribute('customer', 'login_hash', array(
	'group'				=> 'Default',
	'type'				=> 'text',
	'label'				=> 'TYPO3 Temporary Login Hash',
	'input'				=> 'text',
	'source'			=> '',
	'backend'			=> '',
	'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'required'			=> 0,
	'default'			=> '',
	'user_defined'		=> 0
));

$eavConfig->getAttribute('customer', 'login_hash')
    	  ->setData('used_in_forms', array('adminhtml_customer'))
		  ->save();
		  
$installer->endSetup();		  
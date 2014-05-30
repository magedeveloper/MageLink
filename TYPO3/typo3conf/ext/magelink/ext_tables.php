<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Productdisplay',
	'LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_product_integration'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Categorydisplay',
	'LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_category_integration'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Cartdisplay',
	'LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_cart_integration'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Blockdisplay',
	'LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_block_integration'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Loginform',
	'LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_single_sign_on'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Forgotpasswordform',
	'LLL:EXT:magelink/Resources/Private/Language/locallang.xlf:wizarditem_forgot_password'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Magento',
	'Magento Prototype Inclusion'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'MageLink Extension');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_magelink_domain_model_product', 'EXT:magelink/Resources/Private/Language/locallang_csh_tx_magelink_domain_model_product.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_magelink_domain_model_product');
$TCA['tx_magelink_domain_model_product'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product',
		'label' => 'entity_id',
		'label_userFunc' => "MageDeveloper\\Magelink\\Controller\\UserLabelController->productLabelAction",
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'entity_id,sku,name,short_description,description,price,special_price,final_price,qty,auto_refresh,store,currency,is_disabled,manage_stock,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Product.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_magelink_domain_model_product.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_magelink_domain_model_category', 'EXT:magelink/Resources/Private/Language/locallang_csh_tx_magelink_domain_model_category.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_magelink_domain_model_category');
$TCA['tx_magelink_domain_model_category'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category',
		'label' => 'entity_id',
		'label_userFunc' => "MageDeveloper\\Magelink\\Controller\\UserLabelController->categoryLabelAction",
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'entity_id,parent,name,description,page_title,image,url,url_path,product_count,auto_refresh,is_active,user_defined,store,products,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Category.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_magelink_domain_model_category.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_magelink_domain_model_productfilter', 'EXT:magelink/Resources/Private/Language/locallang_csh_tx_magelink_domain_model_productfilter.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_magelink_domain_model_productfilter');
$TCA['tx_magelink_domain_model_productfilter'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_productfilter',
		'label' => 'tags',
		'label_userFunc' => "MageDeveloper\\Magelink\\Controller\\UserLabelController->productfilterLabelAction",
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'tags,categories,skus,store,products,auto_refresh',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Productfilter.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_magelink_domain_model_productfilter.gif'
	),
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_magelink_domain_model_attribute', 'EXT:magelink/Resources/Private/Language/locallang_csh_tx_magelink_domain_model_attribute.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_magelink_domain_model_attribute');
$TCA['tx_magelink_domain_model_attribute'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_attribute',
		'label' => 'code',
		'label_userFunc' => "MageDeveloper\\Magelink\\Controller\\UserLabelController->attributeLabelAction",
		'tstamp' => 'tstamp',
		'hideTable' => true,
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'code,value,relation,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Attribute.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_magelink_domain_model_attribute.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_magelink_domain_model_hash', 'EXT:magelink/Resources/Private/Language/locallang_csh_tx_magelink_domain_model_hash.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_magelink_domain_model_hash');
$TCA['tx_magelink_domain_model_hash'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_hash',
		'label' => 'email',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'hideTable' => true,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'hash,email,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Hash.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_magelink_domain_model_hash.gif'
	),
);

// Add the type to fe_users
$TCA['fe_users']['columns']['tx_extbase_type']['config']['items'][] = array('MageDeveloper Magelink Frontend User', 'MageDeveloper\\Magelink\\Domain\\Model\\FrontendUser');
$TCA['fe_users']['types']['MageDeveloper\\Magelink\\Domain\\Model\\FrontendUser'] = $TCA['fe_users']['types']['0'];


$tempColumns = Array(
	'attributes' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_frontenduser.attributes',
		'config' => array(
			'type' => 'inline',
			'foreign_table' => 'tx_magelink_domain_model_attribute',
			'foreign_field' => 'relation',
			'foreign_match_fields' => array(
				'relation_type' => 'customer',
			),
			'maxitems'      => 9999,
			'appearance' => array(
				'collapseAll' => 1,
				'levelLinksPosition' => 'none',
				'showSynchronizationLink' => 0,
				'showPossibleLocalizationRecords' => 0,
				'showAllLocalizationLink' => 0
			),
		),
	),
);

//\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA("fe_users");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("fe_users", $tempColumns, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("fe_users", "--div--;MageLink Attributes, attributes");





# Aktivierung Flexforms im Backend
$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);

/* Product Display */
$pluginSignature_productdisplay = strtolower($extensionName) . '_productdisplay';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature_productdisplay] 	= 'layout,select_key,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_productdisplay] 		= 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_productdisplay, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_product.xml');


/* Category Display */
$pluginSignature_categorydisplay = strtolower($extensionName) . '_categorydisplay';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature_categorydisplay] 	= 'layout,select_key,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_categorydisplay] 		= 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_categorydisplay, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_category.xml');


/* Block Display */
$pluginSignature_blockdisplay = strtolower($extensionName) . '_blockdisplay';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature_blockdisplay] 	= 'layout,select_key,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_blockdisplay] 		= 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_blockdisplay, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_block.xml');


/* Login Form */
$pluginSignature_loginformdisplay = strtolower($extensionName) . '_loginform';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature_loginformdisplay] 	= 'layout,select_key,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature_loginformdisplay] 		= 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature_loginformdisplay, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_login.xml');

/* Cart Display */
$pluginSignature_cartdisplay = strtolower($extensionName) . '_cartdisplay';

// Hooks for Backend Extension Summary
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$pluginSignature_productdisplay][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Classes/Hooks/CmsLayout.php:MageDeveloper\Magelink\Hooks\CmsLayout->getProductExtensionSummary';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$pluginSignature_categorydisplay][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Classes/Hooks/CmsLayout.php:MageDeveloper\Magelink\Hooks\CmsLayout->getCategoryExtensionSummary';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$pluginSignature_blockdisplay][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Classes/Hooks/CmsLayout.php:MageDeveloper\Magelink\Hooks\CmsLayout->getBlockExtensionSummary';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$pluginSignature_loginformdisplay][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Classes/Hooks/CmsLayout.php:MageDeveloper\Magelink\Hooks\CmsLayout->getLoginExtensionSummary';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$pluginSignature_cartdisplay][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Classes/Hooks/CmsLayout.php:MageDeveloper\Magelink\Hooks\CmsLayout->getCartExtensionSummary';



?>
<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// TSconfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:'.$_EXTKEY.'/Configuration/PageTS/modWizards.ts">');




\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.' . $_EXTKEY,
	'Productdisplay',
	array(
		'Product' 	=> 'index, list, show, grid, inline, dynamic',
		'Flexform'	=> 'populateProductList',
		'Ajax'      => 'ajaxAddToCart',
	),
	// non-cacheable actions
	array(
		'Product' => 'delete, ',
	),
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.' . $_EXTKEY,
	'Categorydisplay',
	array(
		'Category' 	=> 'index, sub, navigation, thumbnail, thumbProducts, navigationProducts, products',
	),
	// non-cacheable actions
	array(
		'Category' => 'delete, ',
	),
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.' . $_EXTKEY,
	'Cartdisplay',
	array(
		'Cart' => 'index, show',
	),
	// non-cacheable actions
	array(
	),
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.' . $_EXTKEY,
	'Blockdisplay',
	array(
		'Block' => 'index, show',
	),
	// non-cacheable actions
	array(
	),
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.' . $_EXTKEY,
	'Loginform',
	array(
		'Login' => 'index, directLogin, ajaxPrepare, ajaxResponse, success, logout, error, forgotPassword, listener',
		'Listener' => 'ajaxPrepare, ajaxResponse',
	),
	// non-cacheable actions
	array(
		'Login' => 'directLogin',
	),
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.' . $_EXTKEY,
	'Forgotpasswordform',
	array(
		'Login' => 'forgotPassword',
	),
	// non-cacheable actions
	array(
		'Login' => '',
	),
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.' . $_EXTKEY,
	'Magento',
	array(
		'Magento' => 'index',
	),
	// non-cacheable actions
	array(
		'Magento' => 'product',
	),
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

// eID Ajax Handling
$TYPO3_CONF_VARS['FE']['eID_include']['magelinkAjax'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY)."Classes/Utility/Ajax/eIDDispatcher.php";

/**
 * register cache for extension
 */
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magelink_cache'])) {
  $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magelink_cache'] = array();
  $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magelink_cache']['frontend'] = "TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend";
  $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magelink_cache']['backend'] = "TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend";
  $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['magelink_cache']['options']['compression'] = 1;
}

/**
 * Realurl Paths
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['magelink'] = 'EXT:'.$_EXTKEY.'/Classes/Hooks/RealUrl.php:&MageDeveloper\Magelink\Hooks\RealUrl->addRealUrlConfig';



?>
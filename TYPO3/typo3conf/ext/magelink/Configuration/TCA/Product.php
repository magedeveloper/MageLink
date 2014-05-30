<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_magelink_domain_model_product'] = array(
	'ctrl' => $TCA['tx_magelink_domain_model_product']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, entity_id, sku, name, short_description, description, image, price, special_price, final_price, qty, auto_refresh, manage_stock, store, currency, attributes',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, entity_id, sku, name, short_description, description, image, price, special_price, final_price, qty, auto_refresh, manage_stock, store, currency, attributes,--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_magelink_domain_model_product',
				'foreign_table_where' => 'AND tx_magelink_domain_model_product.pid=###CURRENT_PID### AND tx_magelink_domain_model_product.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'auto_refresh' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.auto_refresh',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'is_disabled' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.is_disabled',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'entity_id' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.entity_id',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int,required'
			),
		),
		'sku' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.sku',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'short_description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.short_description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'wizards' => array(
					'RTE' => array(
						'icon' => 'wizard_rte2.gif',
						'notNewRecords'=> 1,
						'RTEonly' => 1,
						'script' => 'wizard_rte.php',
						'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
						'type' => 'script'
					)
				)
			),
			'defaultExtras' => 'richtext:rte_transform[flag=rte_enabled|mode=ts]',
		),
		'description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'wizards' => array(
					'RTE' => array(
						'icon' => 'wizard_rte2.gif',
						'notNewRecords'=> 1,
						'RTEonly' => 1,
						'script' => 'wizard_rte.php',
						'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
						'type' => 'script'
					)
				)
			),
			'defaultExtras' => 'richtext:rte_transform[flag=rte_enabled|mode=ts]',
		),
		'image' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.image',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => '10000',
				//'uploadfolder' => 'uploads/tx_magelink',
				'show_thumbs' => 1,
				'size' => 1,
				'autoSizeMax' => 1,
				'maxitems' => 1,
				'minitems' => '0'
			)
		),
		'price' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.price',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'double2,required'
			),
		),
		'special_price' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.special_price',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'double2'
			),
		),
		'final_price' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.final_price',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'double2'
			),
		),
		'manage_stock' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.manage_stock',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'qty' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.qty',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int,required'
			),
		),
		'store' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.store',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'currency' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.currency',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'attributes' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_product.attributes',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_magelink_domain_model_attribute',
				'foreign_field' => 'relation',
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
	),
);

?>
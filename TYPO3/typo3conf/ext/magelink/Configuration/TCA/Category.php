<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_magelink_domain_model_category'] = array(
	'ctrl' => $TCA['tx_magelink_domain_model_category']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, entity_id, parent, sorting, name, description, page_title, image, url, url_path, product_count, auto_refresh, is_active, user_defined, store, products, attributes',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, entity_id, parent, sorting, name, description, page_title, image, url, url_path, product_count, auto_refresh, is_active, user_defined, store, products, attributes,--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime'),
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
				'foreign_table' => 'tx_magelink_domain_model_category',
				'foreign_table_where' => 'AND tx_magelink_domain_model_category.pid=###CURRENT_PID### AND tx_magelink_domain_model_category.sys_language_uid IN (-1,0)',
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
		'entity_id' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.entity_id',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int,required'
			),
		),
		/*'parent' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.parent',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int,required'
			),
		),*/
		'parent' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.parent',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_magelink_domain_model_category',
				'minitems' => 0,
				'maxitems' => 1,
				'appearance' => array(
					'collapseAll' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				),
			),
		),
		'sorting' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.sorting',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			),
		),
		/*'product' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),*/
		'name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.description',
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
		'page_title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.page_title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'image' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.image',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'show_thumbs' => 1,
				'size' => 5,
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'disallowed' => '',
			),
		),
		'url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.url',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'url_path' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.url_path',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'product_count' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.product_count',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			),
		),
		'auto_refresh' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.auto_refresh',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'is_active' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.is_active',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'user_defined' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.user_defined',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'store' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.store',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'products' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.products',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'attributes' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:magelink/Resources/Private/Language/locallang_db.xlf:tx_magelink_domain_model_category.attributes',
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
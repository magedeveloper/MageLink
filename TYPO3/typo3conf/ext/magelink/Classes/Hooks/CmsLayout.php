<?php
namespace MageDeveloper\Magelink\Hooks;

	/***************************************************************
	 *  Copyright notice
	 *
	 *  (c) 2013 Bastian Zagar <zagar@aixdesign.net>, aixdesign.net
	 *
	 *  All rights reserved
	 *
	 *  This script is part of the TYPO3 project. The TYPO3 project is
	 *  free software; you can redistribute it and/or modify
	 *  it under the terms of the GNU General Public License as published by
	 *  the Free Software Foundation; either version 3 of the License, or
	 *  (at your option) any later version.
	 *
	 *  The GNU General Public License can be found at
	 *  http://www.gnu.org/copyleft/gpl.html.
	 *
	 *  This script is distributed in the hope that it will be useful,
	 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *  GNU General Public License for more details.
	 *
	 *  This copyright notice MUST APPEAR in all copies of the script!
	 ***************************************************************/

/**
 *
 *
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */

class CmsLayout
{
	/**
	 * flexFormService
	 *
	 * @var \MageDeveloper\Magelink\Service\FlexFormService
	 * @inject
	 */
	protected $flexFormService;

	/**
	 * settingsService
	 *
	 * @var \MageDeveloper\Magelink\Service\SettingsService
	 * @inject
	 */
	protected $settingsService;

	/**
	 * flexFormService
	 *
	 * @var \MageDeveloper\Magelink\Service\TranslationService
	 * @inject
	 */
	protected $translationService;

	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Object\\ObjectManager");

		// Inject Settings Service
		$this->settingsService	= $this->objectManager->get("MageDeveloper\\Magelink\\Service\\SettingsService");
		// Inject Flexform Service
		$this->flexFormService  = $this->objectManager->get("MageDeveloper\\Magelink\\Service\\FlexFormService");
		// Inject Translation Service
		$this->translationService = $this->objectManager->get("MageDeveloper\\Magelink\\Service\\TranslationService");
		
	}

	/**
	 * Get the extension summary in backend for 
	 * the category plugin
	 * 
	 * @param \TYPO3\CMS\Backend\View\PageLayoutView $config
	 * @return \string
	 */
	public function getCategoryExtensionSummary($config)
	{
		if (array_key_exists("pi_flexform", $config["row"]))
		{
			$rootCategoryId		= $this->flexFormService->extractFlexformConfig($config, "settings.category_root", "category_display");
			$entryDisplayType 	= $this->flexFormService->extractFlexformConfig($config, "settings.entry_appearance", "category_display");
			$subDisplayType		= $this->flexFormService->extractFlexformConfig($config, "settings.sub_appearance", "category_display");
			$productDisplayType = $this->flexFormService->extractFlexformConfig($config, "settings.display_type", "product_display");
			$storeViewCode		= $this->flexFormService->extractFlexformConfig($config, "settings.store_view_code", "refresh_setting");
			
			
			$imageUrl			= $this->getImageUrl("Plugins/categories.gif");

			$displayTypeImageUrl = "";
			switch ($productDisplayType)
			{
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_INLINE:
					$displayTypeImageUrl = $this->getImageUrl("DisplayTypes/icon_display_inline.gif");
					break;
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_GRID:
					$displayTypeImageUrl = $this->getImageUrl("DisplayTypes/icon_display_grid.gif");
					break;
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_LIST:
					$displayTypeImageUrl = $this->getImageUrl("DisplayTypes/icon_display_list.gif");
					break;
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_NO_SELECTION:
				default:
					$displayTypeImageUrl = $this->getImageUrl("DisplayTypes/icon_magento.gif");
					break;

			}

			$displayTypeImage 	= "<img src=\"{$displayTypeImageUrl}\" border=\"0\" style=\"float:right; \" />";
			$image				= "<img src=\"{$imageUrl}\" border=\"0\" style=\"float:left;margin-right:5px;\" />";
			$textPlugin			= "<h4 style=\"line-height:22px;\">" . $this->_getTranslation("wizarditem_category_integration") . "</h4><br />";
			$textSettings		= $this->_getTranslation("flexform_category_display_settings") . "" . "<br />";
			$textCategoryId		= $this->_getTranslation("flexform_category_root") . " " .$this->_getTranslation("flexform_ids")  . ": " . "<strong>" . $rootCategoryId . "</strong>";
			$textDisplayType 	= $this->_getTranslation("flexform_root_display_mode") . ": " . "<strong>" . $this->translationService->translateSetting($entryDisplayType) . "</strong>";
			$textSubDisplayType	= $this->_getTranslation("flexform_continuing_display_mode") . ": " . "<strong>" . $this->translationService->translateSetting($subDisplayType) . "</strong>";
			$textProductDisplayType = $this->_getTranslation("flexform_product_display_setting") . ": " . "<strong>" . $this->translationService->translateSetting($productDisplayType) . "</strong>";
			$textStoreViewCode	= $this->_getTranslation("flexform_store_view_code") . ": " . "<strong>" . $this->_getTranslation("using_global_configuration") . "</strong>";
			if ($storeViewCode) {
				$textStoreViewCode	= $this->_getTranslation("flexform_store_view_code") . ": " . "<strong>" . $storeViewCode . "</strong>";
			}

			$text = 	$image 							.
						$displayTypeImage				.
						$textPlugin 					.
						$textSettings 					. 
						"<hr style=\"border:0;\"/>" 	.
						$textCategoryId 				.
						"<br />" 						. 
						$textDisplayType 				. 
						"<br />" 						. 
						$textSubDisplayType 			.
						"<br />" 						. 
						$textProductDisplayType			.
						"<br />" 						.
						$textStoreViewCode
			;

			return $text;
			
		}
	
		return "";
	}

	/**
	 * Get the extension summary in backend for
	 * the product plugin
	 *
	 * @param \TYPO3\CMS\Backend\View\PageLayoutView $config
	 * @return \string
	 */
	public function getProductExtensionSummary($config)
	{
		if (array_key_exists("pi_flexform", $config["row"]))
		{
			$productDisplayType = $this->flexFormService->extractFlexformConfig($config, "settings.display_type", "product_setting");
			$productSource		= $this->flexFormService->extractFlexformConfig($config, "settings.products_from", "product_setting");
			$storeViewCode		= $this->flexFormService->extractFlexformConfig($config, "settings.store_view_code", "refresh_setting");

			$imageUrl			= $this->getImageUrl("Plugins/products.gif");

			$displayTypeImageUrl = "";
			switch ($productDisplayType)
			{
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_DYNAMIC:
					$displayTypeImageUrl = $this->getImageUrl("DisplayTypes/icon_dynamic_details.gif");
					break;
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_SHOW:
					$displayTypeImageUrl = $this->getImageUrl("DisplayTypes/icon_display_detail.gif");
					break;
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_INLINE:
					$displayTypeImageUrl = $this->getImageUrl("DisplayTypes/icon_display_inline.gif");
					break;
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_GRID:
					$displayTypeImageUrl = $this->getImageUrl("DisplayTypes/icon_display_grid.gif");
					break;
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_LIST:
					$displayTypeImageUrl = $this->getImageUrl("DisplayTypes/icon_display_list.gif");
					break;
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_NO_SELECTION:
				default:
					$displayTypeImageUrl = $this->getImageUrl("DisplayTypes/icon_not_configured.gif");
					break;
				
			}

			$displayTypeImage 	= "<img src=\"{$displayTypeImageUrl}\" border=\"0\" style=\"float:right; \" />"; 



			$image				= "<img src=\"{$imageUrl}\" border=\"0\" style=\"float:left;margin-right:5px;\" />";
			$textPlugin			= "<h4 style=\"line-height:22px;\">" . $this->_getTranslation("wizarditem_product_integration") . "</h4><br />";
			$textSettings		= $this->_getTranslation("flexform_product_display_setting") . "" . "<br />";
			$textDisplayType 	= $this->_getTranslation("flexform_display_type_products") . ": " . "<strong>" . $this->translationService->translateSetting($productDisplayType) . "</strong>";
			$textSource			= $this->_getTranslation("flexform_product_source") . ": " . "<strong>" . $this->translationService->translateSetting($productSource) . "</strong>";
			$textStoreViewCode	= $this->_getTranslation("flexform_store_view_code") . ": " . "<strong>" . $this->_getTranslation("using_global_configuration") . "</strong>";
			if ($storeViewCode) {
				$textStoreViewCode	= $this->_getTranslation("flexform_store_view_code") . ": " . "<strong>" . $storeViewCode . "</strong>";
			}
			//$textSourceId		= $this->_getTranslation("tx_magelink_domain_model_product.entity_id") . ": " . "<strong>"
			
			$textSourceId = "";
			switch ($productSource)
			{
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_MANUAL_SELECTION:
					
					$textSourceId = $this->_getTranslation("tx_magelink_domain_model_product") . " " . $this->_getTranslation("flexform_ids") . ": ";
					
					if ($ids = $this->flexFormService->extractFlexformConfig($config, "settings.product_single", "product_setting"))
					{
						$textSourceId .= "<strong>" . $ids . "</strong>";
					}
					else
					{
						$textSourceId .=  "<strong>" .$this->_getTranslation("flexform_no_selection") . "</strong>";
					}
					
					break;
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_TAGS:

					$textSourceId = $this->_getTranslation("tx_magelink_domain_model_productfilter") . ": ";

					$textSourceId .= "<br />";
					$textSourceId .= "<em>" . $this->_getTranslation("tx_magelink_domain_model_productfilter.tags") . "</em>" . ": " . "<strong>" . $this->flexFormService->extractFlexformConfig($config, "settings.tags", "product_setting") . "</strong>" . "<br />";
					$textSourceId .= "<em>" . $this->_getTranslation("tx_magelink_domain_model_productfilter.categories") . "</em>" . ": " . "<strong>" . $this->flexFormService->extractFlexformConfig($config, "settings.category_names", "product_setting") . "</strong>" . "<br />";
					$textSourceId .= "<em>" . $this->_getTranslation("tx_magelink_domain_model_productfilter.skus") . "</em>" . ": " . "<strong>" . $this->flexFormService->extractFlexformConfig($config, "settings.skus", "product_setting") . "</strong>";
					break;
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_CATEGORY:
				
					$textSourceId = $this->_getTranslation("tx_magelink_domain_model_category") . " " . $this->_getTranslation("flexform_ids") . ": ";

					if ($ids = $this->flexFormService->extractFlexformConfig($config, "settings.category_single", "product_setting"))
					{
						$textSourceId .= "<strong>" . $ids . "</strong>";
					}
					else
					{
						$textSourceId .=  "<strong>" .$this->_getTranslation("flexform_no_selection") . "</strong>";
					}
				
					break;
				case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_NO_SELECTION:
				default:
					break;
			}

			$text = 	$image 							.
						$displayTypeImage				.
						$textPlugin 					.
						$textSettings 					.
						"<hr style=\"border:0;\"/>" 	.
						$textDisplayType 				.
						"<br />" 						.
						$textSource 					.
						"<br />" 						.
						$textSourceId					.
						"<br />" 						.
						$textStoreViewCode
			;

			return $text;
		}

		return "";
	}

	/**
	 * Get the extension summary in backend for
	 * the block plugin
	 *
	 * @param \TYPO3\CMS\Backend\View\PageLayoutView $config
	 * @return \string
	 */
	public function getBlockExtensionSummary($config)
	{
		$imageUrl			= $this->getImageUrl("Plugins/block.gif");

		$image				= "<img src=\"{$imageUrl}\" border=\"0\" style=\"float:left;margin-right:5px;\" />";
		$textPlugin			= "<h4 style=\"line-height:22px;\">" . $this->_getTranslation("wizarditem_block_integration") . "</h4>";
		$block				= $this->flexFormService->extractFlexformConfig($config, "settings.block_id", "block_setting");
		$transBlock			= $this->_getTranslation("flexform_selection_".$block);

		$text = 	$image 					.
					$textPlugin.$transBlock
		;

		return $text;
	}

	/**
	 * Get the extension summary in backend for
	 * the login plugin
	 *
	 * @param \TYPO3\CMS\Backend\View\PageLayoutView $config
	 * @return \string
	 */	
	public function getLoginExtensionSummary($config)
	{
		$imageUrl			= $this->getImageUrl("Plugins/login.gif");

		$image				= "<img src=\"{$imageUrl}\" border=\"0\" style=\"float:left;margin-right:5px;\" />";
		$textPlugin			= "<h4 style=\"line-height:22px;\">" . $this->_getTranslation("wizarditem_single_sign_on") . "</h4>";
		$source 			= $this->settingsService->getUserSource();
		$textSource			= $this->_getTranslation("flexform_user_source") . ": " . "<strong>" . $source . "</strong>";

		$text = 	$image 					.
					$textPlugin				.
					$textSource
		;

		return $text;
	}

	/**
	 * Get the extension summary in backend for
	 * the cart plugin
	 *
	 * @param \TYPO3\CMS\Backend\View\PageLayoutView $config
	 * @return \string
	 */
	public function getCartExtensionSummary($config)
	{
		$imageUrl			= $this->getImageUrl("Plugins/cart.gif");

		$image				= "<img src=\"{$imageUrl}\" border=\"0\" style=\"float:left;margin-right:5px;\" />";
		$textPlugin			= "<h4 style=\"line-height:22px;\">" . $this->_getTranslation("wizarditem_cart_integration") . "</h4>";

		$text = 	$image 		.
					$textPlugin 					
		;

		return $text;
	}

	/**
	 * Gets the extension image url
	 * 
	 * @param \string $filename Filename
	 * @return \string
	 */
	public function getImageUrl($filename)
	{
		return '../../../../' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('magelink') . 'Resources/Public/Icons/' . $filename;
	}

	/**
	 * Gets a translation by a given key
	 * 
	 * @param \string $key Translation Key
	 * @return NULL|string
	 */
	protected function _getTranslation($key)
	{
		return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, "Magelink");
	}
	
}	
<?php
namespace MageDeveloper\Magelink\Service;

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

class TranslationService
{

	/**
	 * Translates a setting configuration
	 * 
	 * @param \string $setting Configuration Setting
	 * @return \string
	 */
	public function translateSetting($setting)
	{
		$translation = "";
		switch ($setting)
		{
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_NO_SELECTION:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_no_selection", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_LIST:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_list", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_GRID:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_grid", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_INLINE:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_inline", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_SHOW:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_details", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_DYNAMIC:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_dynamic", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_NO_SELECTION:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_display_mode_category_information", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_NAVIGATION:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_display_mode_navigation", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_THUMBNAILS:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_display_mode_sub_thumbnails", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_THUMBPRODUCTS:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_display_mode_sub_thumbproducts", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_NAVPRODUCTS:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_display_mode_navproducts", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_PRODUCTS:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_display_mode_products", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_NO_SELECTION:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_no_selection", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_MANUAL_SELECTION:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_product_source_manual_selection", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_CATEGORY:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_product_source_category", "Magelink");
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_TAGS:
				$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("flexform_selection_product_source_filters", "Magelink");
				break;
			default:
				break;
				
		}
		
		return $translation;
	}
	
	
	
	
	
}
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

class SettingsService 
{
	/**
	 * Product Display Types
	 * @var string
	 */
	const PRODUCT_DISPLAY_TYPE_NO_SELECTION 	= "";
	const PRODUCT_DISPLAY_TYPE_LIST 			= "LIST";
	const PRODUCT_DISPLAY_TYPE_GRID				= "GRID";
	const PRODUCT_DISPLAY_TYPE_INLINE			= "INLINE";
	const PRODUCT_DISPLAY_TYPE_SHOW				= "SHOW";
	const PRODUCT_DISPLAY_TYPE_DYNAMIC			= "DYNAMIC";

	/**
	 * Category Display Types
	 * @var string
	 */
	const CATEGORY_DISPLAY_TYPE_NO_SELECTION 	= "INFO";
	const CATEGORY_DISPLAY_TYPE_NAVIGATION		= "NAVIGATION";
	const CATEGORY_DISPLAY_TYPE_THUMBNAILS		= "THUMBNAILS";
	const CATEGORY_DISPLAY_TYPE_THUMBPRODUCTS	= "THUMBPRODUCTS";
	const CATEGORY_DISPLAY_TYPE_NAVPRODUCTS		= "NAVPRODUCTS";
	const CATEGORY_DISPLAY_TYPE_PRODUCTS		= "PRODUCTS";
	
	/**
	 * Product Sources
	 * @var string
	 */
	const PRODUCT_SOURCE_NO_SELECTION		= "";
	const PRODUCT_SOURCE_MANUAL_SELECTION 	= "MANUAL";
	const PRODUCT_SOURCE_CATEGORY			= "CATEGORY";
	const PRODUCT_SOURCE_TAGS				= "TAGS";
	const PRODUCT_SOURCE_NAVIGATION			= "NAVIGATION";

	/**
	 * User Source Setting
	 * @var \string
	 */
	const USER_SOURCE_MAGENTO 	= "Magento";
	const USER_SOURCE_TYPO3		= "TYPO3";

	/**
	 * Redirect Location on Global Logout
	 * @var \string
	 */
	const LOGOUT_REDIRECT_LOCATION_TYPO3	= "TYPO3";
	const LOGOUT_REDIRECT_LOCATION_MAGENTO	= "Magento";
	const LOGOUT_REDIRECT_LOCATION_POSITION	= "POSITION";

	/**
	 * Message Types
	 * @var \string
	 */
	const MESSAGE_TYPE_INFO 	= "info";
	const MESSAGE_TYPE_SUCCESS	= "success";
	const MESSAGE_TYPE_ERROR	= "error";


	/**
	 * @var mixed
	 */
	protected $settings = null;
	
	/**
	 * @var mixed
	 */
	protected $configuration = null;

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * Content Object Renderer
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 * @inject
	 */
	protected $contentObjectRenderer;

	/**
	 * Returns all settings.
	 *
	 * @param \string $extensionName Extension Key
	 * @param \string $pluginName Plugin Name
	 * @return \array
	 */
	public function getSettings($extensionName = null, $pluginName = null) 
	{
		if ($this->settings === NULL)
		{
			$this->settings = $this->configurationManager->getConfiguration(
				\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
				$extensionName,
				$pluginName
			);
		}
		return $this->settings;
	}
	
	/**
	 * Returns configuration.
	 *
	 * @param \string $extensionName Extension Key
	 * @param \string $pluginName Plugin Name
	 * @return \array
	 */
	public function getFullConfiguration($extensionName = null, $pluginName = null)
	{
		if ($this->configuration === NULL)
		{
			$this->configuration = $this->configurationManager->getConfiguration(
				\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,
				$extensionName,
				$pluginName
			);
			
		}	
		
		return $this->configuration;
	}

	/**
	 * Sets the configuration
	 * 
	 * @param \array $configuration
	 * @return void
	 */
	public function setConfiguration(array $configuration)
	{
		$this->configuration = $configuration;
	}
	
	
	/**
	 * Get configuration setting
	 * 
	 * @param \string $path Path to configuration
	 * @return mixed
	 */
	public function getConfiguration($path)
	{
		$nodes = explode('.', $path);
		$fullConfiguration = $this->getFullConfiguration();
		
		$config = null; 
		$current = 0;
		$depth = count($nodes);
		$config = $fullConfiguration["plugin."]["tx_magelink."];
		
		while (is_array($config)) 
		{
			$node = ($current+1 < $depth)?$nodes[$current].'.':$nodes[$current];
			
			if(array_key_exists($node, $config))
			{
				$config = $config[$node];
				$current++;
			} 
			else 
			{
				if (array_key_exists($node.'.', $config))
				{
					$config = $config[$node];
				}
				else
				{
					$config = "";
				}

				break;
			}
			
		}

		return $config;		
	}
	
	/**
	 * Gets a setting by code
	 * 
	 * @param \string $code Setting Code
	 * @return mixed
	 */
	public function getSettingByCode($code)
	{
		$settings = $this->getSettings();
		
		if (is_array($settings) && array_key_exists($code, $settings))
		{
			return $settings[$code];
		}
		
		return "";
	}
	
	/**
	 * Gets the product source selection from the plugin setting
	 * 
	 * @return \string
	 */
	public function getProductSourceSelection()
	{
		return $this->getSettingByCode("products_from");
	}
	
	/**
	 * Gets the selected product ids from the manual selection
	 * 
	 * @return \array
	 */
	public function getSelectedProductIds()
	{
		$configuration = $this->getSettingByCode("product_single");
		if ($configuration)
		{
			return explode(',', $configuration);
		}
		
		return array();
	}
	
	/**
	 * Gets the selected category id from the plugin setting
	 * 
	 * @return int
	 */
	public function getSelectedCategoryId()
	{
		return (int)$this->getSettingByCode("category_single");
	}

	/**
	 * Gets the display type plugin setting
	 * 
	 * @return \string
	 */
	public function getDisplayType()
	{
		return $this->getSettingByCode("display_type");		
	}

	/**
	 * Get the magento url from the settings
	 * 
	 * @return \string
	 */
	public function getMagentoUrl()
	{
		$path = "magento.magento_url";
		$magentoUrl = rtrim($this->getConfiguration($path), '/');
		return $magentoUrl;
	}

	/**
	 * Get the magento base url
	 * 
	 * @return \string
	 */
	public function getMagentoBaseUrl()
	{
		$url = str_replace('/index.php', '', $this->getMagentoUrl());
		$url = rtrim($url, '/');
		return $url;
	}

	/**
	 * Get the magento media url
	 * 
	 * @return \string
	 */
	public function getMediaUrl()
	{
		return $this->getMagentoBaseUrl() . "/media/catalog";
	}
	
	/**
	 * Gets the api url
	 * 
	 * @return \string
	 */
	public function getApiUrl()
	{
		$magentoUrl = $this->getMagentoUrl();
		$apiUrl = $magentoUrl . "/api/v2_soap?wsdl=1";
		return $apiUrl;
	}
	
	/**
	 * Gets the cache lifetime setting
	 * 
	 * @return \int
	 */
	public function getCacheLifetime()
	{
		$path = "developer.cache_lifetime";
		return (int)$this->getConfiguration($path);
	}

	/**
	 * Gets the debug setting
	 * 
	 * @return \bool
	 */
	public function getDebugMode()
	{
		$path = "developer.debug_mode";
		return (bool)$this->getConfiguration($path);
	}

	/**
	 * Get the store view code setting
	 * Note: Plugin Setting will be prefered!
	 * 
	 * @return \string
	 */
	public function getStoreViewCode()
	{
		$pluginSetting = $this->getSettingByCode("store_view_code");
		$configuration = $this->getConfiguration("import.store_view_code");
		
		return ($pluginSetting)?$pluginSetting:$configuration;		
	}

	/**
	 * Gets the storage pid configuration setting
	 * 
	 * @return \int
	 */
	public function getStoragePid()
	{
		return (int)$this->getConfiguration("persistence.storagePid");
	}

	/**
	 * Get the tags string from plugin setting
	 * 
	 * @return \string
	 */
	public function getTagsString()
	{
		return $this->getSettingByCode("tags");
	}
	
	/**
	 * Get the category names string from plugin setting
	 * 
	 * @return \string
	 */
	public function getCategoriesString()
	{
		return $this->getSettingByCode("category_names");
	}

	/**
	 * Get the skus string from plugin setting
	 *
	 * @return \string
	 */
	public function getSkusString()
	{
		return $this->getSettingByCode("skus");
	}

	/**
	 * Gets the setting for the category appearance
	 * 
	 * @return string
	 */
	public function getCategoryEntryDisplayMode()
	{
		return $this->getSettingByCode("entry_appearance");
	}

	/**
	 * Gets the setting for the category sub appearance
	 *
	 * @return string
	 */
	public function getCategorySubDisplayMode()
	{
		return $this->getSettingByCode("sub_appearance");
	}

	/**
	 * Get the reload setting in plugin configuration
	 * 
	 * @return \bool
	 */
	public function reload()
	{
		return (bool)$this->getSettingByCode("always_load_from_soap");
	}

	/**
	 * Gets the full import filepath from
	 * the configuration settings
	 * 
	 * @param \string $filename Filename
	 * @return \string
	 */
	public function getImportFilePath($filename = "")
	{
		$path = $this->getConfiguration("import.image_import_path");
		$path = rtrim($path, '/');
		$path = rtrim($path, '\\');
		
		return $path.$filename;
	}

	/**
	 * Gets the dynamic detail view page id 
	 * from the plugin setting
	 * 
	 * @return \int
	 */
	public function getDynamicDetailViewPid()
	{
		return (int)$this->getSettingByCode("dynamic_detail_pid");
	}

	/**
	 * Get a magento product url by entity id
	 * 
	 * @param \int $entityId Product Entity Id
	 * @return \string
	 */
	public function getProductUrlByEntityId($entityId)
	{
		return $this->getMagentoUrl() . "/catalog/product/view/id/" . $entityId;
	}

	/**
	 * Get a magento category url by entity id
	 * 
	 * @param \int $entityId Category Entity Id
	 * @return \string
	 */
	public function getCategoryUrlByEntityId($entityId)
	{
		return $this->getMagentoUrl() . "/catalog/category/view/id/" . $entityId;
	}

	/**
	 * Get the category root id from the
	 * plugin setting
	 * 
	 * @return \int
	 */
	public function getCategoryRootId()
	{
		return (int)$this->getSettingByCode("category_root");
	}
	
	/**
	 * Gets the user source configuration setting
	 *
	 * @return \string
	 */
	public function getUserSource()
	{
		$path = "import.user_source";
		return $this->getConfiguration($path);
	}

	/**
	 * Get encryption/decryption key
	 *
	 * @return \string
	 */
	public function getCryptKey()
	{
		$path = "webservice.encrypt_decrypt_key";
		return $this->getConfiguration($path);
	}

	/**
	 * Gets the maximum login time difference
	 * between request and response
	 * 
	 * @return \int
	 */
	public function getLoginTimeDifference()
	{
		$path = "login.time_diff";
		return intval( $this->getConfiguration($path) );
	}

	/**
	 * Gets an array with allowed user detail columns
	 * 
	 * @return \array
	 */
	public function getAllowedUserDetails()
	{
		$path = "login.allowed_details";
		$configuration = $this->getConfiguration($path);
		
		$exploded = \MageDeveloper\Magelink\Utility\FilterString::getExplodedValues($configuration);
		$exploded[] = "uid";
		
		return $exploded;
	}

	/**
	 * Gets the redirect target on successful login
	 * 
	 * @return \string
	 */
	public function getRedirectAfterSuccessfulLogin()
	{
		return $this->getSettingByCode("redirect_after_successful_login");
	}

	/**
	 * Gets the redirect target on logout
	 * 
	 * @return mixed
	 */
	public function getRedirectAfterLogout()
	{
		return $this->getSettingByCode("redirect_after_logout");
	}

	/**
	 * Gets the configuration setting for the global logout location
	 * 
	 * @return \string
	 */
	public function getGlobalLogoutLocation()
	{
		$setting = $this->getConfiguration("redirect.logout_target");
		
		switch ($setting)
		{
			case "Magento":
				return self::LOGOUT_REDIRECT_LOCATION_MAGENTO;
			case "TYPO3":
				return self::LOGOUT_REDIRECT_LOCATION_TYPO3;
			case "Current Logout Position":
			default:
				return self::LOGOUT_REDIRECT_LOCATION_POSITION;
		}
		
	}

	/**
	 * Gets the configuration setting for importing
	 * address type
	 * 
	 * @return \string
	 */
	public function getImportAddressType()
	{
		$path = "import.std_address";
		$configuration = $this->getConfiguration($path);
		
		if ($configuration == "Import Default Shipping Address")
		{
			return "shipping";
		}
		
		return "billing";
	}

	/**
	 * Gets the id of the selected block in plugin configuration
	 * 
	 * @return \string
	 */
	public function getSelectedBlockId()
	{
		return $this->getSettingByCode("block_id");
	}

	/**
	 * Gets the Magento Root Path Configuration
	 * 
	 * @return bool|string
	 */
	public function getMagentoRootPath()
	{
		$path = "magento.magento_root_path";
		$configuration = $this->getConfiguration($path);
		
		return $configuration;
		
		if ($configuration)
		{
			return rtrim($configuration, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;	
		}
		
		return false;
	}

	/**
	 * Gets the redirect target for the dynamic detail view
	 * redirect setting 
	 * 
	 * @return \string
	 */
	public function getDynamicDetailViewRedirect()
	{
		$setting = $this->getSettingByCode("dynamic_detail_redirect");
		
		if ($setting)
		{
			$conf = array(
				"parameter"         => (int)$setting,
				'useCashHash' 		=> true,
				"returnLast"        => "url",
			);

			$link = $this->contentObjectRenderer->TYPOLINK("", $conf);
			
			if ($link)
			{
				return $link;
			}
		}
		
		return null;
	}

	/**
	 * Gets the redirect error code for the dynamic detail
	 * view redirect when all parameters are empty
	 * 
	 * @return mixed
	 */
	public function getDynamicDetailViewRedirectErrorCode()
	{
		return $this->getSettingByCode("redirect_error_code");
	}

	/**
	 * Checks if the full category tree has to be
	 * displayed
	 * 
	 * @return bool
	 */
	public function getDisplayFullCategoryTree()
	{
		return (bool)$this->getSettingByCode("full_category_tree");
	}

	/**
	 * Gets the id of the default user group that was set
	 * in the configuration
	 * 
	 * @return int
	 */
	public function getDefaultUserGroupId()
	{
		return (int)$this->getConfiguration("import.default_user_group");
	}

	/**
	 * Determines if there is a magento local setting
	 *
	 * @throws \Exception
	 * @return bool
	 */
	public function isMagentoLocal()
	{
		// Check if magento path is loaded	
		$magentoLocalPath = $this->getMagentoRootPath();

		if (strlen($magentoLocalPath))
		{
			if (!file_exists($magentoLocalPath))
			{
				throw new \Exception("The path '{$magentoLocalPath}' doesn't exist! Please leave the Magento Root Path setting empty in order to use Webservices!");
			}
			return true;
		}

		return false;
	}
	
}
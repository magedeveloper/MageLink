<?php
/**
 * MageDeveloper MageLink Module
 * ---------------------------------
 *
 * @category    Mage
 * @package     MageDeveloper_MageLink
 * @copyright   Magento Developers / magedeveloper.de <kontakt@magedeveloper.de>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MageDeveloper_MageLink_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Gets the TYPO3 Url by a given uid
	 * 
	 * @param int $uid UID of the page
	 * @param array $params Parameters
	 * @return string
	 */
	public function getTYPO3UrlByUid($uid, $params = array())
	{
		$url = $this->getTYPO3BaseUrl()."/index.php?id=".$uid;
		
		$parameter = "";
		
		if (!empty($params)) 
		{
			foreach ($params as $_param=>$_value) 
			{
				$parameter .= '&'.$_param.'='.$_value;
			}
		}

		$url = $url.$parameter;
		return $url;
	}
	
	/**
	 * Gets the root name of the 
	 * TYPO3 Pages Tree
	 * 
	 * @return string
	 */
	public function getRootName()
	{
		return self::TREE_ROOT_NAME;
	}
	
	/**
	 * Gets a complete url with params
	 * 
	 * @param string $baseUrl The base url
	 * @param array $params Parameters
	 * @return string
	 */
	public function getUrl($baseUrl, $params = array())
	{
		$mark = (strpos($baseUrl, '?') === false) ? '?' : '&';
		$paramStr = $mark;
		
		foreach ($params as $_param=>$_val)
		{
			$paramStr .= $_param . "=" . $_val . '&';
		}
		
		$paramStr = substr($paramStr, 0, -1);
		
		return $baseUrl.$paramStr;
	}
	
	/**
	 * Gets the TYPO3 Ajax Response Url
	 * 
	 * @param string $baseUrl Base URL
	 * @return string
	 */
	public function getTYPO3AjaxResponseUrl($baseUrl)
	{
		$params = array(
			"type"								=> "1337154991",
			"no_cache"							=> "1",
			"tx_magelink_loginform[action]"		=> "ajaxResponse",
			"tx_magelink_loginform[controller]"	=> "Listener",
			
		);		
		
		return $this->getUrl($baseUrl, $params);
	}

	/**
	 * Gets the TYPO3 Ajax Response Url
	 * 
	 * @param string $baseUrl Base URL
	 * @return string
	 */
	public function getTYPO3AjaxPrepareUrl($baseUrl)
	{
		$params = array(
			"type"								=> "1337154991",
			"no_cache"							=> "1",
			"tx_magelink_loginform[action]"		=> "ajaxPrepare",
			"tx_magelink_loginform[controller]"	=> "Listener",
			
		);		
		
		return $this->getUrl($baseUrl, $params);
	}
	
	/**
	 * Determines the login enabled setting 
	 * from the store config
	 * 
	 * @return bool
	 */
	public function ajaxLoginListenerIsEnabled()
	{
		return (bool)Mage::getStoreConfig(MageDeveloper_MageLink_Helper_Config::XML_PATH_LOGIN_ENABLED, Mage::app()->getStore());
	}	
	
	/**
	 * Gets the configuration of the
	 * typo3 base url
	 * 
	 * @return string
	 */
	public function getTYPO3BaseUrl()
	{
		$url = Mage::getStoreConfig(MageDeveloper_MageLink_Helper_Config::XML_PATH_TYPO3_BASE_URL, Mage::app()->getStore());
		$url = rtrim($url,'/');
		return $url;
	}

	/**
	 * Gets the configuration of the
	 * typo3 login url
	 * 
	 * @return string
	 */	
	public function getTYPO3LoginUrl()
	{
		$url = Mage::getStoreConfig(MageDeveloper_MageLink_Helper_Config::XML_PATH_TYPO3_LOGIN_URL, Mage::app()->getStore());
		$url = rtrim($url,'/');
		return $url;
	}
	
	/**
	 * Gets the shared key for
	 * encryption/decryption
	 * 
	 * @return string
	 */
	public function getKey()
	{
		return Mage::getStoreConfig(MageDeveloper_MageLink_Helper_Config::XML_PATH_SHARED_KEY, Mage::app()->getStore());
	}
	
	/**
	 * Gets the default website id setting
	 * from store configuration
	 * 
	 * @return int
	 */
	public function getDefaultCustomerWebsiteId()
	{
		return (int)Mage::getStoreConfig(MageDeveloper_MageLink_Helper_Config::XML_PATH_CUSTOMER_DEFAULT_WEBSITE_ID, Mage::app()->getStore());
	}
	
	/**
	 * createCodeFromValue
	 * Creates unified code from a given value
	 * 
	 * @param string $value Value to generate code of
	 * @return string
	 */
	public function createCodeFromValue($value)
	{ 
		$value = strtolower($value);
		$value = str_replace(" ","_", $value);
		$removable_values = array(
											";" 	=> 	"_", 
											":" 	=> 	"_",
											"/" 	=> 	"_",
											"."		=>	"_",
											"ö" 	=> 	"oe",
											"ä" 	=> 	"ae",
											"ü" 	=> 	"ue",
											"," 	=> 	"_",
											"__" 	=> 	"_",
											"___" 	=> 	"_",
		);
				
		$value = preg_replace('/[^a-zA-Z0-9_]/u', '_', $value);
		$value = strtr($value, $removable_values); 
		
		return $value;	
	}	
	
}

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

class MageDeveloper_MageLink_Helper_Config extends Mage_Core_Helper_Abstract
{
	/**
	 * XML Configuration Paths
	 * @var string
	 */
	const XML_PATH_TYPO3_LOGIN_URL				= 'magelink/t3general/typo3_baseurl';
	const XML_PATH_SHARED_KEY					= 'magelink/t3general/decryption_key';
	
	const XML_PATH_CUSTOMER_DEFAULT_WEBSITE_ID	= 'magelink/t3import/customer_website';
	
	const XML_PATH_LOGIN_ENABLED				= 'magelink/login_settings/enabled';
	
}

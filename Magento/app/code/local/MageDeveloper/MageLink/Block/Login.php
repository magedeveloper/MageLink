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

class MageDeveloper_MageLink_Block_Login extends Mage_Core_Block_Template
{
	/**
	 * Gets login enabled setting
	 * 
	 * @return bool
	 */
	public function isEnabled()
	{
		return (Mage::helper("magelink")->loginIsEnabled())?"true":"false";
	}
}

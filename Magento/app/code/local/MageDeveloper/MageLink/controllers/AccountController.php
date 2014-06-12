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

require_once Mage::getModuleDir('controllers', 'Mage_Customer').DIRECTORY_SEPARATOR.'AccountController.php';

class MageDeveloper_MageLink_AccountController extends Mage_Customer_AccountController 
{
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
    	parent::preDispatch();
		
		$action = $this->getRequest()->getActionName();
		$allowedActions = array(
			"logout",
		);
		
		if (in_array($action, $allowedActions))
		{
			// If customer is already logged out, maybe by session
			if (!$this->_getSession() || $this->_getSession()->authenticate($this)) 
			{
				$loginUrl = Mage::helper("magelink")->getTYPO3LoginUrl(array());
				$this->_redirectUrl($loginUrl);	
			}

		}
		
		return;
    }
	
	/**
	 * logout action for customer
	 */
	public function logoutAction()
	{
		$typo3LoginUrl = Mage::helper("magelink")->getTYPO3LoginUrl();
		
		if ($this->_getSession()->isLoggedIn())
		{		
			// True session logout
	        $this->_getSession()->logout()
								->renewSession()
			            		->setBeforeAuthUrl($typo3LoginUrl);	
		}
		
		$params = array(
			"tx_magelink_loginform[action]" 	=> "logout",
			"tx_magelink_loginform[controller]"	=> "Login",
			"tx_magelink_loginform[logout]"		=> "1",
			"tx_magelink_loginform[target]"		=> "Magento",
		);
		
		if ($this->getRequest()->getParam("target") != "")
		{
			$params["tx_magelink_loginform[target]"] = $this->getRequest()->getParam("target");
		}
		
		$loginUrl = Mage::helper("magelink")->getUrl($typo3LoginUrl, $params);
		$this->_redirectUrl($loginUrl);
	}
	
}
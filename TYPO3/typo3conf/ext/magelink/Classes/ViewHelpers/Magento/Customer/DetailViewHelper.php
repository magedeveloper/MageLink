<?php
namespace MageDeveloper\Magelink\ViewHelpers\Magento\Customer;

	/***************************************************************
	 *  Copyright notice
	 *
	 *  (c) 2013
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
class DetailViewHelper extends \MageDeveloper\Magelink\ViewHelpers\Magento\AbstractMagentoViewHelper
{

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments()
	{
		parent::initializeArguments();
		$this->registerArgument("email", "string", "Customer E-Mail", true, null);
		$this->registerArgument("cache", "bool", "Caches Customer Data", false, true);
	}
	
	/**
	 * Gets a product
	 * 
	 * @return \string Attribute Value
	 */
	public function render()
	{
		$email = $this->arguments["email"];
		$cacheIdentifier = "customer_data_".md5($email);
		$customerData = array();
		
		if (FALSE === ($customerData = $this->cacheService->get($cacheIdentifier)) || !$this->arguments["cache"]) 
		{
			// Initialize Magento and prepare
			if ($this->magentoCore->init())
			{
				$customer = \Mage::getModel("magelink/customer_api")->fetch($email);
				$customerData = json_decode($customer, true);
				
				// Add product data to cache
				$lifetime = $this->settingsService->getCacheLifetime();
				$this->cacheService->set( $cacheIdentifier, $customerData, array(), $lifetime );
			}
		}
		
		$output = '';
		foreach($customerData as $key=>$value)
		{
			$this->templateVariableContainer->add($key, $value);
		}
		
		$output .= $this->renderChildren();
		
		foreach($customerData as $key=>$value)
		{
			$this->templateVariableContainer->remove($key);
		}
		
		return $output;
	}
	
	
	
	
}
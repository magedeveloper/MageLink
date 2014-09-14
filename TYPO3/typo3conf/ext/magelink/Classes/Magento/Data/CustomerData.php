<?php
namespace MageDeveloper\Magelink\Magento\Data;

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

class CustomerData extends \MageDeveloper\Magelink\Magento\Data\AbstractData
{
	/**
	 * Fetches complete user details from a user
	 * by a given e-mail address
	 *
	 * @param \string $email E-Mail address of the user
	 * @param \array $attributes Attributes to fetch
	 * @throws \Exception
	 * @return array|bool
	 */
	public function getCustomerByEmail($email, $attributes = array())
	{
		if ($this->magentoCore->init())
		{
			$customer = \Mage::getModel("magelink/customer_api")->fetch($email);
			$customerData = json_decode( $customer, true );

			return $customerData;
		}

		throw new \Exception("Could not establish a magento connection");
	}

	/**
	 * Writes data to an customer or creates a
	 * new customer
	 *
	 * @param \array $data
	 * @throws \Exception
	 * @return \string|\bool
	 */
	public function exportCustomerData(array $data)
	{
		if ($this->magentoCore->init())
		{
			$success = \Mage::getModel("magelink/customer_api")->write($data);

			return is_numeric($success);
		}

		throw new \Exception("Could not establish a magento connection");
	}

	/**
	 * Login a customer on magento
	 *
	 * @param string $email E-Mail
	 * @param string $password Password
	 * @throws \Exception
	 * @return int Customer ID
	 */
	public function loginCustomer($email, $password)
	{
		if ($this->magentoCore->init())
		{
			\Mage::getSingleton("core/session", array("name" => "frontend"));
				
			$websiteId 	= \Mage::app()->getStore()->getWebsiteId();
			$customer 	= \Mage::getModel("customer/customer");
			$customer->setWebsiteId($websiteId);
			
			try 
			{
				$customer->loadByEmail($email);
	
				if ($customer instanceof \Mage_Customer_Model_Customer && $customer->getId())
				{
					$session = \Mage::getSingleton('customer/session');
					$session->login($email, $password);
					$session->setCustomerAsLoggedIn($customer);

					if ($session->isLoggedIn())
						return (int)$customer->getId();
				}
				
			} catch (\Exception $e) {
				
			}
			
			return false;
		}
		
		throw new \Exception("Could not establish a magento connection");
	}
	
}
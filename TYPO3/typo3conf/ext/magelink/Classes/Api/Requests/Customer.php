<?php
namespace MageDeveloper\Magelink\Api\Requests;


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
 * API Category Calls
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Customer extends \MageDeveloper\Magelink\Api\Requests\AbstractRequest
{
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
		try
		{
			if ( $this->getApiClient()->connect() )
			{
				$success = $this->getApiClient()->getResource()->magelinkCustomerWrite(
					$this->getApiClient()->getSessionId(), // Session Id
					$data
				);

				return is_numeric($success);
			}

		}
		catch (\Exception $e)
		{
			throw new \Exception ("Could not write customer data! Error: " . $e->getMessage());
		}

		return false;
	}

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
		try
		{
			if ( $this->getApiClient()->connect() )
			{
				$data = $this->getApiClient()->getResource()->magelinkCustomerFetch(
					$this->getApiClient()->getSessionId(), // Session Id
					$email,
					$attributes
				);

				if ($data)
				{
					$data = json_decode($data, true);
					return $data;
				}
			}

		}
		catch (\Exception $e)
		{
			throw new \Exception ("Could not fetch customer data! Error: " . $e->getMessage());
		}

		return false;	
	}




}
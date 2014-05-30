<?php
namespace MageDeveloper\Magelink\Import;

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
 * Frontend User Controller
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class CustomerImport extends \MageDeveloper\Magelink\Import\AbstractImport
{
	/**
	 * Key Translation Targets
	 * @var \int
	 */
	const TRANSLATE_KEYS_TO_VALUES 		= 1;
	const TRANSLATE_VALUES_TO_KEYS		= 2;

	/**
	 * Api Customer Request
	 *
	 * @var \MageDeveloper\Magelink\Api\Requests\Customer
	 * @inject
	 */
	protected $customerRequest;

	/**
	 * Magento Customer Data Model
	 *
	 * @var \MageDeveloper\Magelink\Magento\Data\CustomerData
	 * @inject
	 */
	protected $magentoCustomerData;

	/**
	 * @var \MageDeveloper\Magelink\Domain\Repository\FrontendUserRepository
	 * @inject
	 */
	protected $frontendUserRepository;

	/**
	 * @var \MageDeveloper\Magelink\Domain\Repository\FrontendUserGroupRepository
	 * @inject
	 */
	protected $frontendUserGroupRepository;

	/**
	 * authenticationService
	 * @var \MageDeveloper\Magelink\Service\AuthenticationService
	 * @inject
	 */
	protected $authenticationService;
	
	/**
	 * Export the given frontend user to magento 
	 * 
	 * @param \array $fe_user Frontend user
	 * @return \bool
	 */
	public function exportFeUserAction(array $fe_user)
	{
		$preparedCustomerArray = array();
		$preparedCustomerArray = $this->prepareCustomerData($fe_user);
		
		if (is_array($preparedCustomerArray))
		{
			// Write customer to magento and receive update hash
			$success = $this->exportCustomerData($preparedCustomerArray);
			
			return $success;
		}

		return false;
	}

	/**
	 * Imports the given frontend user details
	 * 
	 * @param \array $data Frontend User
	 * @return \int Frontend User Id
	 */
	public function importFeUserAction(array $data)
	{
		if ($this->saveFeUserByData($data))
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Fetches customer data from magento
	 *
	 * @param $email
	 * @param \string $email E-Mail Address of user to import
	 * @return \array Customer Data
	 */
	public function fetchCustomerAction($email)
	{
		$customer = $this->fetchCustomerByEmail($email);
		
		if ($customer)
		{
			return $customer;
		}
		
		return false;
	}
	
	/**
	 * Prepares frontend user data from magento customer
	 * data
	 * 
	 * @param \array $customer Customer Data
	 * @return \array Frontend User Data
	 */
	public function prepareFeUserData($customer)
	{
		$prepared = array();
		$prepared = $customer["attributes"];
		
		// Address
		$importAddrType = $this->settingsService->getImportAddressType();
		
		$address = array();
		if (array_key_exists($importAddrType, $customer))
		{
			$address = $customer[$importAddrType];
		}
		
		$prepared 	= array_merge($address, $prepared);

		$prepared["password_hash"] = $this->authenticationService->preparePassword($prepared["password_hash"]);
		
		// Array of key translation
		// MAGENTO => TYPO3
		$translation = array(
			"prefix"		=> "title",
			"firstname" 	=> "first_name",
			"middlename"	=> "middle_name",
			"lastname"		=> "last_name",
			"password_hash"	=> "password",
			"postcode"		=> "zip",
			"street"		=> "address",
		);
		
		$prepared = $this->_translateArrayKeys($prepared, $translation, self::TRANSLATE_KEYS_TO_VALUES);
		
		return $prepared;
	}
	
	/**
	 * Translates array keys to an desired format
	 *
	 * @param \array $array Input Array
	 * @param \array $translation Array with translation information
	 * @param \int $transTarget Translation Target Type
	 * @return \array
	 */
	protected function _translateArrayKeys($array, array $translation, $transTarget = self::TRANSLATE_KEYS_TO_VALUES)
	{
		switch($transTarget)
		{
			case self::TRANSLATE_VALUES_TO_KEYS:
				$translation = array_flip($translation);
				break;
			case self::TRANSLATE_KEYS_TO_VALUES:
			default:
				// Nothing to do
				break;
		
		}
		
		// Translate array keys
		foreach ($array as $_key=>$_value)
		{
			foreach ($translation as $_S=>$_T)
			{
				if ($_key==$_S)
				{
					unset($array[$_key]);
					$array[$_T] = $_value;
				}
			}
		}
		
		return $array;
	}
	
	

	/**
	 * Prepares customer data from fe_user data
	 *
	 * @param \array $fe_user fe_user data
	 * @return \array Magento Customer Data
	 */
	public function prepareCustomerData($fe_user)
	{
		// Final prepared data
		$prepared = array();

		// Array keys which are not allowed
		$notAllowed = array(
			"uid",
			"pid",
		);

		// Array of key translation
		// MAGENTO => TYPO3
		$translation = array(
			"prefix"		=> "title",
			"firstname" 	=> "first_name",
			"middlename"	=> "middle_name",
			"lastname"		=> "last_name",
			"postcode"		=> "zip",
			"street"		=> "address",
		);

		// Translation of array keys to magento style
		$fe_user = $this->_translateArrayKeys($fe_user, $translation, self::TRANSLATE_VALUES_TO_KEYS);

		foreach ($fe_user as $_key=>$_value)
		{
			if (!in_array($_key, $notAllowed))
			{
				switch($_key)
				{
					case "tstamp":
					case "starttime":
					case "endtime":
					case "cruser_id":
						break;
					case "title":
						$prepared[] = array(
										"key"	=> "prefix",
										"value" => $fe_user[$_key]
						);
						break;
					case "usergroup":
						$groupIds = explode(',', $fe_user[$_key]);
						$groupNames = array();
						
						foreach ($groupIds as $_id)
						{
							$feUserGroup = $this->frontendUserGroupRepository->findByUid($_id);
							if ($feUserGroup)
							{
								$groupNames[] = $feUserGroup->getTitle();
							}
						}
						$prepared[] = array(
							"key"	=> "group",
							"value" => reset($groupNames),
						);
						break;
					case "crdate":
						if ($prepared["crdate"] > 0)
						{
							$prepared[] = array(
								"key"	=> "created_at",
								"value" => date("Y-m-d H:i:s", $fe_user[$_key]),
							);
						}
						break;
					case "disable":
						$prepared[] = array(
							"key"	=> "is_active",
							"value" => (bool)$fe_user[$_key],
						);
						break;
					case "password":
						// Prepare password hash
						$prepared[] = array(
							"key" 	=> "password_hash",
							"value"	=> $this->getPasswordHash( trim($fe_user[$_key]) ),
						);
						break;
					default:
						$prepared[] = array(
							"key"	=> $_key,
							"value"	=> $_value,
						);
						break;
						
						
				}

			}
		}
		
		return $prepared;
	}

	/**
	 * Prepares the password hash
	 *
	 * @param \string $password Password
	 * @return \string
	 */
	public function getPasswordHash($password)
	{
		$matches = array();
		preg_match("/^[0-9a-zA-Z]{32}:[0-9a-zA-Z]{2}$/", $password, $matches);

		if( !isset($matches[0]) || $matches[0] != $password )
		{
			$chars  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$length = strlen($chars) - 1;

			for ( $i = 0, $str = ""; $i < 2; $i++ )
			{
				$str .= $chars[mt_rand(0, $length)];
			}

			$password = md5($str . $password) . ":" . $str;
		}
		
		return $password;
	}

	/**
	 * Save a frontend user by data
	 * If the user does not exist in database,
	 * we create him
	 *
	 * @param \array $data Data for Object
	 * @return \bool
	 */
	public function saveFeUserByData(array $data)
	{
		// We try to load category from database
		$feUser = $this->frontendUserRepository->findByEmail($data["email"]);

		if (!$feUser)
		{
			$model = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Model\\FrontendUser");

			// We need to save the fe_user to database
			$model = $this->mergeFeUserData($data, $model);

			if ($model instanceof \MageDeveloper\Magelink\Domain\Model\FrontendUser)
			{
				$this->frontendUserRepository->add($model);
				$this->persistenceManager->persistAll();

				if ($model->getUid())
				{
					return true;
				}

			}

		}
		else
		{
			// We need to update the category to the database
			$model = $this->mergeFeUserData($data, $feUser);

			if ($model instanceof \MageDeveloper\Magelink\Domain\Model\FrontendUser)
			{
				$this->frontendUserRepository->update($model);
				$this->persistenceManager->persistAll();

				return true;
			}
		}

		return false;
	}

	/**
	 * Merge category data with a frontend user model
	 *
	 * @param \array $data Frontend User Data
	 * @param \MageDeveloper\Magelink\Domain\Model\FrontendUser $feUserModel
	 * @return \MageDeveloper\Magelink\Domain\Model\FrontendUser
	 */
	public function mergeFeUserData(array $data, \MageDeveloper\Magelink\Domain\Model\FrontendUser $feUserModel)
	{
		if ($this->settingsService->getStoragePid())
		{
			$feUserModel->setPid( $this->settingsService->getStoragePid() );
		}

		$feUserModel->setLastlogin(new \DateTime());

		$feUserModel->setUsername( $data["email"] );
		$feUserModel->setEmail( $data["email"] );
		unset($data["email"]);
		
		$feUserModel->setPassword( $data["password"] );
		unset($data["password"]);

		/**
		 * User Groups
		 */
		$group = $this->frontendUserGroupRepository->findByTitle($data["group"]);
		
		if ($group instanceof \MageDeveloper\Magelink\Domain\Model\FrontendUserGroup)
		{
			$feUserModel->addUsergroup($group);
		}
		else
		{
			$defaultUserGroupId = $this->settingsService->getDefaultUserGroupId();
			$defaultUserGroup = $this->frontendUserGroupRepository->findByUid($defaultUserGroupId);
			
			if ($defaultUserGroup instanceof \MageDeveloper\Magelink\Domain\Model\FrontendUserGroup)
			{
				$feUserModel->addUsergroup($defaultUserGroup);
			}
		}
		unset($data["group"]);
		unset($data["group_id"]);

		$feUserModel->setCompany( $data["company"] );
		unset($data["company"]);

		$feUserModel->setTitle( $data["title"] );
		unset($data["title"]);
		
		$feUserModel->setName( $data["name"] );
		unset($data["name"]);
		
		$feUserModel->setFirstName( $data["first_name"] );
		unset($data["first_name"]);
		
		$feUserModel->setMiddleName( $data["middle_name"] );
		unset($data["middle_name"]);
		
		$feUserModel->setLastName( $data["last_name"] );
		unset($data["last_name"]);
		
		$feUserModel->setZip( $data["zip"] );
		unset($data["zip"]);
		
		$feUserModel->setAddress( $data["address"] );
		unset($data["address"]);
		
		$feUserModel->setCity( $data["city"] );
		unset($data["city"]);
		
		$feUserModel->setCountry( $data["country_id"] );
		unset($data["country_id"]);
		
		$feUserModel->setTelephone( $data["telephone"] );
		unset($data["telephone"]);
		
		$feUserModel->setFax( $data["fax"] );
		unset($data["fax"]);
		
		/**
		 * All other Attributes
		 */

		// Update found attribute values
		$found = array();
		foreach($data as $_code=>$_value)
		{
			$value = json_encode($_value);

			foreach ($feUserModel->getAttributes() as $_attribute)
			{
				if($_attribute->getCode() == $_code)
				{
					$_attribute->setValue($value);
					$found[] = $_code;
				}

			}

		}

		// Attributes that we need to create
		$needToCreate = array();
		$needToCreate = array_diff(array_keys($data), $found);
		foreach($needToCreate as $_code)
		{
			if (array_key_exists($_code, $data))
			{
				$value = json_encode($data[$_code]);
				$attribute = $this->createAttribute($_code, $value, $feUserModel);

				if ($attribute instanceof \MageDeveloper\Magelink\Domain\Model\Attribute)
				{
					$feUserModel->addAttribute($attribute);
				}

			}

		}

		return $feUserModel;
	}


	/**
	 * Creates an attribute by code and value
	 *
	 * @param \string $attributeCode Code of the attribute
	 * @param \string $attributeValue Value of the attribute
	 * @param \MageDeveloper\Magelink\Domain\Model\FrontendUser $feUser Frontend User Model
	 * @return \MageDeveloper\Magelink\Domain\Model\Attribute
	 */
	public function createAttribute($attributeCode, $attributeValue = "", \MageDeveloper\Magelink\Domain\Model\FrontendUser $feUser)
	{
		$attribute = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Model\\Attribute");

		if ($this->settingsService->getStoragePid())
		{
			$attribute->setPid( $this->settingsService->getStoragePid() );
		}

		$attribute->setCode($attributeCode);
		$attribute->setValue($attributeValue);
		$attribute->setRelationCustomer($feUser);

		return $attribute;
	}

	/**
	 * Fetches customer information
	 * Defines the way to get the customer by comparing
	 * settings
	 *
	 * @param string $email Customers Email
	 * @param array $attributes Customer Attributes to fetch
	 * @return array|bool
	 */
	public function fetchCustomerByEmail($email, $attributes = array())
	{
		if ($this->settingsService->isMagentoLocal())
		{
			return $this->magentoCustomerData->getCustomerByEmail($email, $attributes);
		}

		return $this->customerRequest->getCustomerByEmail($email, $attributes);
	}

	/**
	 * Writes customer information to magento
	 * Defines the way to write the information by comparing
	 * settings
	 *
	 * @param array $preparedCustomerArray Customer Information
	 * @return array|bool
	 */
	public function exportCustomerData($preparedCustomerArray)
	{
		if ($this->settingsService->isMagentoLocal())
		{
			return $this->magentoCustomerData->exportCustomerData($preparedCustomerArray);
		}

		return $this->customerRequest->exportCustomerData($preparedCustomerArray);
	}



}
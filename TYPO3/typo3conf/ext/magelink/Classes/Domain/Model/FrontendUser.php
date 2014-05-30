<?php
namespace MageDeveloper\Magelink\Domain\Model;


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
class FrontendUser extends \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
{
	/**
	 * Username
	 * @var string
	 * @validate String
	 * @validate NotEmpty
	 */
	protected $username;

	/**
	 * Password
	 * @var \string
	 */
	protected $password;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup>
	 */
	protected $usergroup;

	/**
	 * Name
	 * @var \string
	 */
	protected $name;

	/**
	 * First Name
	 * @var \string
	 */
	protected $firstName;

	/**
	 * Last Name
	 * @var \string
	 */
	protected $lastName;

	/**
	 * E-Mail Address
	 * 
	 * @var \string
	 * @validate NotEmpty
	 * @validate EmailAddress
	 * @validate StringLength(minimum = 3,maximum = 50)
	 */
	protected $email = '';

	/**
	 * @var bool
	 */
	protected $disable = false;

	/**
	 * Attribute Relation
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Magelink\Domain\Model\Attribute>
	 * @lazy
	 */
	protected $attributes;


	/**
	 * __construct
	 *
	 * @return \MageDeveloper\Magelink\Domain\Model\FrontendUser
	 */
	public function __construct()
	{
		parent::__construct();
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
		
	}
	
	/**
	 * Sets the email
	 * 
	 * @param \string $email
	 * @return void
	 */
	public function setEmail($email) 
	{
		$this->email = $email;
		$this->username = $email;
	}

	/**
	 * Gets the email
	 * 
	 * @return \string
	 */
	public function getEmail() 
	{
		return $this->email;
	}

	/**
	 * Sets the username
	 * 
	 * @param \string $username
	 * @return void
	 */
	public function setUsername($username)
	{
		$this->email = $username;
		$this->username = $username;
	}

	/**
	 * Disable user
	 * 
	 * @param \bool $disable
	 * @return void
	 */
	public function setDisable($disable) 
	{
		$this->disable = $disable;
	}

	/**
	 * Gets the disable setting
	 * @return \bool
	 */
	public function getDisable() 
	{
		return $this->disable;
	}

	/**
	 * @return string
	 */
	public function getName() 
	{
		return $this->getFirstName().' '.$this->getLastName();
	}

	/**
	 * Sets the firstname
	 * 
	 * @param \string $firstName
	 * @return void
	 */
	public function setFirstName($firstName) 
	{
		$this->firstName = $firstName;
	}

	/**
	 * Gets the first name
	 * 
	 * @return \string
	 */
	public function getFirstName() 
	{
		return $this->firstName;
	}

	/**
	 * Sets the last name
	 * 
	 * @param \string $lastName
	 * @return void
	 */
	public function setLastName($lastName) 
	{
		$this->lastName = $lastName;
	}

	/**
	 * Gets the last name
	 * 
	 * @return \string
	 */
	public function getLastName() 
	{
		return $this->lastName;
	}

	/**
	 * Adds a Attribute
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Attribute $attribute
	 * @return void
	 */
	public function addAttribute(\MageDeveloper\Magelink\Domain\Model\Attribute $attribute)
	{
		$this->attributes->attach($attribute);
	}

	/**
	 * Removes a Attribute
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Attribute $attributeToRemove The Attribute to be removed
	 * @return void
	 */
	public function removeAttribute(\MageDeveloper\Magelink\Domain\Model\Attribute $attributeToRemove)
	{
		$this->attributes->detach($attributeToRemove);
	}

	/**
	 * Returns the attribute
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Magelink\Domain\Model\Attribute> $attribute
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * Sets the attributes
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Magelink\Domain\Model\Attribute> $attribute
	 * @return void
	 */
	public function setAttributes(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $attributes)
	{
		$this->attributes = $attributes;
	}

	/**
	 * Get an attribute value
	 *
	 * @param \string $attribute Attribute Code
	 * @return \string
	 */
	public function getAttributeValue($attribute)
	{
		foreach ($this->getAttributes() as $_attribute)
		{

			if ($_attribute instanceof \MageDeveloper\Magelink\Domain\Model\Attribute)
			{
				if ($_attribute->getCode() == $attribute)
				{
					return json_decode($_attribute->getValue(), true);
				}
			}

		}

		return "";
	}

	/**
	 * Initializes all ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects()
	{
		/**
		 * Do not modify this method!
		 * It will be rewritten on each save in the extension builder
		 * You may modify the constructor of this class instead
		 */
		$this->attributes	= new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->usergroup = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}
	
	/**
	 * Adds a usergroup to the frontend user
	 *
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup $usergroup
	 * @return void
	 * @api
	 */
	public function addUsergroup(\TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup $usergroup) 
	{
		// Quick fix
		if (!$this->usergroup)
		{
			$this->initStorageObjects();
		}
		$this->usergroup->attach($usergroup);
	}

}
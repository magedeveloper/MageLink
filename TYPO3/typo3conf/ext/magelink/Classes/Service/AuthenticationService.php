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

class AuthenticationService extends \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
implements \TYPO3\CMS\Core\SingletonInterface
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * settingsService
	 * @var \MageDeveloper\Magelink\Service\SettingsService
	 * @inject
	 */
	protected $settingsService;

	/**
	 * Injects the salted passwords service
	 *
	 * @var \TYPO3\CMS\Saltedpasswords\SaltedPasswordService
	 * @inject
	 */
	protected $saltedPasswordService;

	/**
	 * Gets the current logged in frontend user details
	 * 
	 * @return mixed
	 */
	public function getFrontendUser()
	{
		if ($this->isLoggedIn() && !empty($GLOBALS["TSFE"]->fe_user->user["uid"]))
		{
			$fe_user = $GLOBALS["TSFE"]->fe_user->user;
			return array_change_key_case($fe_user, CASE_LOWER);
		}

		return null;
	}

	/**
	 * Gets the current logged in frontend user id
	 * 
	 * @return int|null
	 */
	public function getFrontendUserId()
	{
		if ($this->isLoggedIn())
		{
			$user = $this->getFrontendUser();
			
			if (array_key_exists("uid", $user))
			{
				return (int)$user["uid"];
			}
			
		}
	
		return null;
	}

	/**
	 * Gets the frontend user with only
	 * allowed details
	 *
	 * @return array
	 */
	public function getAllowedFrontendUser()
	{
		$finalUser = array();
		$user = array();

		if ($this->isLoggedIn())
		{
			// Frontend User
			$user 				= $this->getFrontendUser();
			$allowedUserDetails = $this->settingsService->getAllowedUserDetails();

			foreach ($user as $_detail=>$_userdata)
			{
				foreach ($allowedUserDetails as $_allowed)
				{
					if ($_detail == $_allowed)
					{
						$finalUser[$_detail] = $_userdata;
					}
				}
			}

		}

		return $finalUser;

	}
		
				
	/**
	 * Authenticate a user by credentials
	 * 
	 * @param \string $username Username
	 * @param \string $password Password
	 * @return \bool
	 */
	public function auth($username, $password)
	{
		//$this->tsfe = \TYPO3\CMS\Frontend\Utility\EidUtility::initFeUser();
	
		$loginData = array(
			"uname" 		=> $username, //username
			"uident"		=> $password, //password
			"uident_text"	=> $password, //password
			"status"		=> "login"
		);

		$GLOBALS["TSFE"]->fe_user->checkPid 	= 0;
		$info 	= $GLOBALS["TSFE"]->fe_user->getAuthInfoArray();
		$user 	= $GLOBALS["TSFE"]->fe_user->fetchUserRecord( $info["db_user"] ,$loginData["uname"] );
		
		$ok = false;
		
		if (is_array($user))
		{
			// Salted Passwords?
			if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded("saltedpasswords"))
			{
				$ok = $this->saltedPasswordService->compareUident($user, $loginData);
			}
			else
			{
				$ok = $GLOBALS["TSFE"]->fe_user->compareUident( $user, $loginData );
			}
		}
		
		if($ok)
		{
			$GLOBALS["TSFE"]->fe_user->createUserSession( $user );
			$GLOBALS["TSFE"]->fe_user->user = $user;
			
			return true;
		}		
		
		return false;
	}

	/**
	 * Logs out the current frontend user
	 * 
	 * @return bool
	 */
	public function logout()
	{
		if ($this->isLoggedIn() && $this->getFrontendUserUid())
		{
			$GLOBALS["TSFE"]->fe_user->logoff();
			return true;
		}
		
		return false;
	}

	/**
	 * Checks if a user is logged in
	 * 
	 * @return \bool
	 */
	public function isLoggedIn() 
	{
		return !empty($GLOBALS["TSFE"]->fe_user->user["uid"]);
	}

	/**
	 * Get the uid of the current feuser
	 * 
	 * @return mixed
	 */
	public function getFrontendUserUid() 
	{
		if ($this->isLoggedIn()) 
		{
			return intval($GLOBALS["TSFE"]->fe_user->user["uid"]);
		}
		
		return null;
	}

	/**
	 * Prepares a password
	 * 
	 * @param string $password
	 * @return string
	 */
	public function preparePassword($password)
	{
		if (\TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility::isUsageEnabled())
		{
			$saltedPwInstance = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::getSaltingInstance();
			return $saltedPwInstance->getHashedPassword($password);
		}
	
		return $password;
	}

}
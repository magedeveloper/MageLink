<?php
namespace MageDeveloper\Magelink\Controller;

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
 * THIS CONTROLLER IS FOR INTERACTION BETWEEN TYPO3 -> MAGENTO
 *
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class LoginController extends \MageDeveloper\Magelink\Controller\AbstractController
{
	/**
	 * Form Ids
	 * @var \string
	 */
	const FORM_ID_EMAIL 		= "tx_magelink_loginform[tx-magelink-login-email]";
	const FORM_ID_PASSWORD		= "tx_magelink_loginform[tx-magelink-login-password]";
	const FORM_ID_REQUEST_URL	= "tx_magelink_loginform[tx-magelink-login-requesturl]";

	/**
	 * Request Data
	 * @var \array
	 */
	protected $requestData = array();

	/**
	 * authenticationService
	 * @var \MageDeveloper\Magelink\Service\AuthenticationService
	 * @inject
	 */
	protected $authenticationService;

	/**
	 * hashRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\HashRepository
	 * @inject
	 */
	protected $hashRepository;

	/**
	 * persistenceManager
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * Customer Import Model
	 * @var \MageDeveloper\Magelink\Import\CustomerImport
	 * @inject
	 */
	protected $customerImport;

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
	 * Magento Core
	 * @var \MageDeveloper\Magelink\Magento\Core
	 * @inject
	 */
	protected $magentoCore;

	/**
	 * Magento Helper
	 * @var \MageDeveloper\Magelink\Magento\Helper
	 * @inject
	 */
	protected $magentoHelper;
	
	/**
	 * indexAction
	 */
	public function indexAction()
	{
		// Direct access to magento
		if ($this->settingsService->isMagentoLocal())
		{
			$uri = "directLogin";
			$this->view->assign("action", $uri);
		}
		
		$this->view->assign("settings", $this->settings);
		$this->view->assign("requestUrl", $this->uriBuilder->getRequest()->getRequestUri());
	
		// User is already logged in
		if ($this->authenticationService->isLoggedIn())
		{
			$user = $this->authenticationService->getAllowedFrontendUser();
			$this->forward("success","Login",$this->getExtensionName(), array("user"=>$this->authenticationService->getAllowedFrontendUser()));
		}
		
	}
	
	/**
	 * Direct Login when Magento is local
	 */
	public function directLoginAction()
	{
		if(	$this->request->getMethod() == "POST" &&
			$this->request->hasArgument("tx-magelink-login-email") &&
			$this->request->hasArgument("tx-magelink-login-password")
		) {
			
			// Determine the user source setting
			$userSource = $this->settingsService->getUserSource();
	
			switch($userSource)
			{
				// Procedure when the user source is TYPO3
				case \MageDeveloper\Magelink\Service\SettingsService::USER_SOURCE_TYPO3:
	
					// Initiate the TYPO3 Login Procedure
					$this->forward("directTYPO3LoginProcedure");
					break;
				
				case \MageDeveloper\Magelink\Service\SettingsService::USER_SOURCE_MAGENTO:
				default:
					
					// Procedure when the user source is Magento	
					$this->forward("directMagentoLoginProcedure");
					break;
	
			}
			
		}
		
		$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("login_failed", $this->extensionName);
		$this->addGlobalLoginErrorMessage($message);
		$this->redirect("index");
	}
	
	/**
	 * Direct including of the login action when
	 * the customers are located in TYPO3
	 */
	public function directTYPO3LoginProcedureAction()
	{
		$login 			= $this->request->getArgument("tx-magelink-login-email");
		$password		= $this->request->getArgument("tx-magelink-login-password");
		
		$isAuth			= $this->authenticationService->auth($login, $password);
		
		// User credentials are invalid!
		if (!$isAuth)
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("login_failed_check_credentials", $this->extensionName);
			$this->addGlobalLoginErrorMessage($message);
			$this->redirect("error");
		}
		
		// User Array
		$user = $this->authenticationService->getFrontendUser();
		
		// Could not retrieve user
		if (!$user)
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("user_inactive_try_again", $this->extensionName);
			$this->addGlobalLoginErrorMessage($message);
			$this->redirect("error");
		}
		
		// Put plain password and remove database salted or plain
		$user["password"] = $password;
		
		// Export to Magento
		$success = $this->customerImport->exportFeUserAction($user);
		
		if ($success)
		{
		
			$magentoCustomerData 	= $this->objectManager->get("MageDeveloper\\Magelink\\Magento\\Data\\CustomerData");
			$customerId				= $magentoCustomerData->loginCustomer($login, $password);
		
			if ($customerId === false)
			{
				$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("login_failed_check_credentials", $this->extensionName);
				$this->addGlobalLoginErrorMessage($message);
				$this->redirect("error");
			}
			
			// Login Success
			$this->directLoginSuccessAction();
			
		}
		else
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("error_exporting_user", $this->extensionName);
			$this->addGlobalLoginErrorMessage($message);
			$this->redirect("error");
		}

		$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("unknown_error", $this->extensionName);
		$this->addGlobalLoginErrorMessage($message);
		$this->redirect("error");
	}
	
	/**
	 * Direct including of the login action when
	 * the customers are located in Magento
	 */
	public function directMagentoLoginProcedureAction()
	{
		$login 			= $this->request->getArgument("tx-magelink-login-email");
		$password		= $this->request->getArgument("tx-magelink-login-password");
		$redirectOnError = null;
		
		$magentoCustomerData 	= $this->objectManager->get("MageDeveloper\\Magelink\\Magento\\Data\\CustomerData");
		$customerId				= $magentoCustomerData->loginCustomer($login, $password);
		
		if ($customerId !== false)
		{
			// Customer was logged in, so we need to import user and log him in in TYPO3
			// We try to fetch the customer from magento
			$customerArr 	= $this->customerImport->fetchCustomerAction($login);
			$feUserData 	= $this->customerImport->prepareFeUserData($customerArr);
			$import 		= $this->customerImport->importFeUserAction($feUserData);
			
			if (!$import)
			{
				$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("error_importing_user", $this->extensionName);
				$this->addGlobalLoginErrorMessage($message);
				$this->redirect("error");
			}
			
			$isAuth = $this->authenticationService->auth($login, $password);
						
			if (!$isAuth)
			{
				$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("login_failed_check_credentials", $this->extensionName);
				$this->addGlobalLoginErrorMessage($message);
				$this->redirect("error");
			}
			
			// Login Success
			$this->directLoginSuccessAction();
			
		}
		
		$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("login_failed_check_credentials", $this->extensionName);
		$this->addGlobalLoginErrorMessage($message);
		$this->redirect("index");
	}

	/**
	 * Success for direct login
	 */
	public function directLoginSuccessAction()
	{
		if (!$this->settingsService->getRedirectAfterSuccessfulLogin() &&
			$this->request->hasArgument("target")
		)
		{
			$target = $this->request->getArgument("target");
		
			$uri = $this->settingsService->getMagentoUrl();
			if ($target != "/")
			{
				$uri = $this->settingsService->getMagentoBaseUrl() . "/" . $target;
			}
			
			// MAGENTO
			$this->redirectToUri( $uri );
			exit();
		}
		else
		{
			$this->redirectToUri( $this->_getLoginSuccessRedirectUri() );
		}
	}
	
	/**
	 * Listener Action
	 * Listens for Magento Calls
	 */
	/*public function listenerAction()
	{
		$arguments = $this->request->getArguments();
		$this->forward("ajaxPrepare","Listener",$this->getExtensionName(), $arguments);
	}*/

	/**
	 * Error Action
	 */
	public function errorAction()
	{
		$arguments = $this->request->getArguments();
	
		foreach ($arguments as $_key=>$_value)
		{
			$this->view->assign($_key, $_value);
		}

		$this->view->assign("settings", $this->settings);
	}

	/**
	 * Logout Action
	 */
	public function logoutAction()
	{
		// Instantly log out
		if ($this->authenticationService->isLoggedIn())
		{
			$this->authenticationService->logout();
		}	
		
		if (!$this->request->hasArgument("target"))
		{
			// Global Redirect Setting
			$setting 		= $this->settingsService->getGlobalLogoutLocation();
		
			// Logout at TYPO3
			// We need to go to magento and logout
			// and tell magento, we need to go back to this form
			// to determine final redirect handling
			$logoutUrl = $this->settingsService->getMagentoUrl()."/customer/account/logout";
			
			if ($setting == \MageDeveloper\Magelink\Service\SettingsService::LOGOUT_REDIRECT_LOCATION_POSITION)
			{
				$logoutUrl .= "?target=".\MageDeveloper\Magelink\Service\SettingsService::LOGOUT_REDIRECT_LOCATION_TYPO3;
			}
			
			$this->redirectToUri($logoutUrl);
			exit();
			
		}
		else
		{
			// Logout at Magento
			// If we are here, Magento is already logged out, and TYPO3 also
			// so we just need to redirect, where it wants
			$target 		= $this->request->getArgument("target");
			// Global Redirect Setting
			$setting 		= $this->settingsService->getGlobalLogoutLocation();
			// Plugin Configuration Redirect
			$redirectPid 	= $this->settingsService->getRedirectAfterLogout();
			
			// Plugin Setting has priority
			if ($redirectPid)
			{
				$this->redirect("index",null,null,array(),$redirectPid);
				exit();
			}
			
			if ($setting == \MageDeveloper\Magelink\Service\SettingsService::LOGOUT_REDIRECT_LOCATION_TYPO3)
			{
				// TYPO3
				$this->redirect("index", null, null, array());
			}
			else if ($setting == \MageDeveloper\Magelink\Service\SettingsService::LOGOUT_REDIRECT_LOCATION_MAGENTO)
			{
				// MAGENTO
				$this->redirectToUri( $this->settingsService->getMagentoUrl() );
				exit();
			}
			else
			{
				// POSITION (either TYPO3 or Magento)
				switch ($target)
				{
					case \MageDeveloper\Magelink\Service\SettingsService::LOGOUT_REDIRECT_LOCATION_MAGENTO:
						$this->redirectToUri( $this->settingsService->getMagentoUrl() );
						exit();
						break;
					case \MageDeveloper\Magelink\Service\SettingsService::LOGOUT_REDIRECT_LOCATION_TYPO3:
					default:
						$this->forward("index", null, null, array());
						break;
				}
				
			}
			
		}
		
	}

	/**
	 * Login Success Action
	 * 
	 * @param array $user User Data
	 */
	public function successAction($user = null)
	{
		$arguments = $this->request->getArguments();
		$redirectPid = $this->settingsService->getRedirectAfterSuccessfulLogin();
		
		// If we receive a redirect argument
		if ($redirectPid && $arguments["redirect"] === true)
		{
			$this->forward("loginRedirect", "Login", $this->getExtensionName(), array("redirectPid"=>$redirectPid));
			//$this->redirect("index",null,null,null,$redirectPid);
		}
		else
		{
			// No redirect argument, just display the login success template
			$this->view->assign("settings", $this->settings);
			
			foreach ($arguments as $_marker=>$_argument)
			{
				$this->view->assign($_marker, $_argument);
			}
			
		}
	
		// User is already logged in
		if ($this->authenticationService->isLoggedIn())
		{
			$user = $this->authenticationService->getAllowedFrontendUser();
		}
		
		$this->view->assign("user", $user);
	}
	
	/**
	 * Redirects directly after the login was successful
	 * 
	 * @param int $redirectPid PageId to redirect to
	 */
	public function loginRedirectAction($redirectPid)
	{
		$uri = $this->uriBuilder->reset()
				->setTargetPageUid($redirectPid)
				->build();
		
		
		$this->view->assign("uri", $uri);
		$this->view->assign("redirectPid", $redirectPid);
	}
	
	
	
	/**
	 * Action for Forgot Password
	 */
	public function forgotPasswordAction()
	{
		//
	}

	/**
	 * loginPrepareAction
	 * Prepares login form data to be sent to magento
	 *
	 * @return void
	 */
	public function ajaxPrepareAction()
	{
		// Determine the user source setting
		$userSource = $this->settingsService->getUserSource();

		switch($userSource)
		{
			// Procedure when the user source is TYPO3
			case \MageDeveloper\Magelink\Service\SettingsService::USER_SOURCE_TYPO3:

				// Initiate the TYPO3 Login Procedure
				$this->forward("initiateTYPO3LoginProcedure");

				// -- TYPO3 --
				// Authenticate TYPO3 User by JSON Request 						
				// Add hash to TYPO3 User											
				// SOAP Export to Magento											
				// (Export First-Time Login Hash to user)							
				// Generate encrypted string with credentials						
				// Send encrypted credentials to Magento							
				//      |
				// -- Magento --
				// Decrypt credentials string									
				// Authenticate Magento User										
				// Check Remote Address												
				// Check User hash													
				// Generate encrypted String with credentials and success information
				// 		|
				// -- TYPO3 --
				// Check TYPO3 User hash												
				// Remove hash
				// Redirect to custom site or stay on page

				break;

			// Procedure when the user source is Magento	
			case \MageDeveloper\Magelink\Service\SettingsService::USER_SOURCE_MAGENTO:
			default:

				$this->forward("initiateMagentoLoginProcedure");

				// -- TYPO3 --
				// Generate Hash													
				// Generate Encrypted String with credentials
				// JSONP-Call to Magento with encrypted credentials
				//      |
				// -- Magento --
				// Decrypt credentials string
				// Authenticate Magento User
				// Check Remote Address
				// Add hash to user
				// Generate Encrypted String with credentials and success information
				//      |
				// -- TYPO3 --
				// Decrypt success information and credentials
				// Check import user hash
				// SOAP Fetch User to TYPO3
				// Check fetched hash with existing hash/time
				// Import TYPO3 User
				// Authenticate TYPO3 User
				// Remove hash
				// Redirect to custom site or stay on page	

				break;

		}

	}

	/**
	 * Handle an ajax response
	 *
	 * @return void
	 */
	public function ajaxResponseAction()
	{
		$response = $this->getFinalResponse();
		header('Content-Type: application/json');
		echo json_encode($response);
		exit();
	}

	/**
	 * Evaluates the response by targeting configuration
	 * @return array|null
	 */
	public function getFinalResponse()
	{
		// Determine the user source setting
		$userSource = $this->settingsService->getUserSource();

		switch($userSource)
		{
			// Procedure when the user source is TYPO3
			case \MageDeveloper\Magelink\Service\SettingsService::USER_SOURCE_TYPO3:
				return $this->getFinalizedTYPO3LoginProcedureResponse();
			// Procedure when the user source is Magento	
			case \MageDeveloper\Magelink\Service\SettingsService::USER_SOURCE_MAGENTO:
			default:
				return $this->getFinalizedMagentoLoginProcedureResponse();
		}
		
	}
	
	/**
	 * Initiates the TYPO3 Login procedure
	 */
	public function initiateTYPO3LoginProcedureAction()
	{
		$requestData = $this->getRequestData();
		
		$isAuth = $this->authenticationService->auth($requestData[self::FORM_ID_EMAIL], $requestData[self::FORM_ID_PASSWORD]);

		// User credentials are invalid!
		if (!$isAuth)
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("login_failed_check_credentials", $this->extensionName);
			$this->deliverMessage($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
		}

		// User Array
		$user = $this->authenticationService->getFrontendUser();

		// Could not retrieve user
		if (!$user)
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("user_inactive_try_again", $this->extensionName);
			$this->deliverMessage($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
		}

		// Put hash and email in database
		$hash = $this->createHash( $requestData[self::FORM_ID_EMAIL] );

		// If we could create a hash
		if ($hash === null)
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("error_creating_hash", $this->extensionName);
			$this->deliverMessage($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
		}

		// Put hash to user		
		$user["login_hash"] = $hash->getHash();
		
		// Put plain password and remove database salted or plain
		$user["password"] = $requestData[self::FORM_ID_PASSWORD];

		// SOAP Export to Magento
		$success = $this->customerImport->exportFeUserAction($user);

		if ($success)
		{
			/**
			 * PREPARE DATA
			 * AND DELIVER FINAL ENCRYPTED DATA
			 * FOR MAGENTO LOGIN
			 */

			// Encryption/Decryption Key
			$key = $this->settingsService->getCryptKey();

			$data = array(
				"credentials" => array(
					"email" 	=> $requestData[self::FORM_ID_EMAIL],
					"password" 	=> $requestData[self::FORM_ID_PASSWORD],
					"hash"	   	=> $hash->getHash(),
					"requestUrl"	=> $requestData[self::FORM_ID_REQUEST_URL],
				),
				"remote_addr"	=> \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'),
				"requestUrl"	=> $requestData[self::FORM_ID_REQUEST_URL],
			);

			$encrypted = \MageDeveloper\Magelink\Utility\Crypt::encrypt($data, $key);

			$parameters = array(
				"type" 	=> \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_SUCCESS,
				"url" 		=> $this->settingsService->getMagentoUrl()."/magelink/json/loginSourceTYPO3/",
				"enc"		=> base64_encode($encrypted),
			);

			header('Content-Type: application/json');
			echo json_encode($parameters);
			exit();

		}
		else
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("error_exporting_user", $this->extensionName);
			$this->deliverMessage($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
		}

		$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("unknown_error", $this->extensionName);
		$this->deliverMessage($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
	}

	/**
	 * Inititates the Magento Login Procedure
	 */
	public function initiateMagentoLoginProcedureAction()
	{
		$requestData = $this->getRequestData();
		
		// Put hash and email in database
		$hash = $this->createHash( $requestData[self::FORM_ID_EMAIL] );

		// If we could create a hash
		if ($hash === null)
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("error_creating_hash", $this->extensionName);
			$this->deliverMessage($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
		}

		/**
		 * PREPARE DATA
		 * AND DELIVER FINAL ENCRYPTED DATA
		 * FOR MAGENTO LOGIN
		 */

		// Encryption/Decryption Key
		$key = $this->settingsService->getCryptKey();

		$data = array(
			"credentials" => array(
				"email" 		=> $requestData[self::FORM_ID_EMAIL],
				"password" 		=> $requestData[self::FORM_ID_PASSWORD],
				"hash"	   		=> $hash->getHash(),
			),
			"remote_addr"	=> \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'),
			"requestUrl"	=> $requestData[self::FORM_ID_REQUEST_URL],
		);
		
		$encrypted = \MageDeveloper\Magelink\Utility\Crypt::encrypt($data, $key);

		$parameters = array(
			"type" 	=> \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_SUCCESS,
			"url" 		=> $this->settingsService->getMagentoUrl()."/magelink/json/loginSourceMagento/",
			"enc"		=> base64_encode($encrypted),
		);

		header('Content-Type: application/json');
		echo json_encode($parameters);
		exit();
	}

	/**
	 * Gets the final typo3 user source login response
	 * 
	 * @return array|null
	 */
	public function getFinalizedTYPO3LoginProcedureResponse()
	{
		$arguments = $this->request->getArgument("arguments");
		
		if (array_key_exists("enc", $arguments))
		{
			// Encryption/Decryption Key
			$key = $this->settingsService->getCryptKey();

			$decrypted = \MageDeveloper\Magelink\Utility\Crypt::decrypt(base64_decode($arguments["enc"]), $key);

			// User Array
			$user = $this->authenticationService->getFrontendUser();
			$remoteAddr = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR');

			$hash = $this->hashRepository->findOneByHash($decrypted["hash"]);

			// Check Remote Address
			if ($remoteAddr == $decrypted["remote_addr"])
			{
				
				// Check if a hash exists and compare email value with current user
				if ($hash->getHash() && $hash->getEmail() == $user["email"])
				{
					// Check hash timestamp with current timestamp
					$difference = time() - $hash->getTstamp()->getTimestamp();
					
					// Checks the time difference between login preparation and finalization
					if ($difference <= $this->settingsService->getLoginTimeDifference())
					{
						$allowedUser = $this->authenticationService->getAllowedFrontendUser();

						$request = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Web\\Request");

						$request->setPluginName($this->getPluginName());
						$request->setControllerExtensionName($this->getExtensionName());
						$request->setControllerName("Login");
						$request->setControllerActionName("success");
						$request->setControllerVendorName("Magedeveloper");
						$request->setArguments(array("user"=>$allowedUser, "redirect" => true));

						$response 	= $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Web\\Response");
						$dispatcher = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Dispatcher");
						$dispatcher->dispatch($request, $response);

						//$htmlRequest = $this->processRequest($request, $response);

						$response = array(
							"user" 	=> $allowedUser,
							"html"	=> $response->getContent(),
						);

						// Remove the hash, we dont need it anymore
						$this->hashRepository->remove($hash);
						$this->persistenceManager->persistAll();
						
						return $response;
					}

					die();
				}
				else
				{
					$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("hash_comparison_failed", $this->extensionName);
					return $this->_createErrorResponse($message);			
				}

			}

		}

		$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("login_failed", $this->extensionName);
		return $this->_createErrorResponse($message);
	}


	/**
	 * Gets the final typo3 user source login response
	 *
	 * @return array|null
	 */
	public function getFinalizedMagentoLoginProcedureResponse()
	{
		$arguments = $this->request->getArgument("arguments");

		if (array_key_exists("enc", $arguments))
		{
			// Encryption/Decryption Key
			$key = $this->settingsService->getCryptKey();

			$decrypted 	= \MageDeveloper\Magelink\Utility\Crypt::decrypt(base64_decode($arguments["enc"]), $key);
			$remoteAddr = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR');

			$hash = $this->hashRepository->findOneByHash($decrypted["hash"]);

			// Check Remote Address
			if ($remoteAddr == $decrypted["remote_addr"])
			{
				// Check hash timestamp with current timestamp
				$difference = time() - $hash->getTstamp()->getTimestamp();

				// Checks the time difference between login preparation and finalization
				if ($difference <= $this->settingsService->getLoginTimeDifference())
				{
					if (!array_key_exists(self::FORM_ID_EMAIL, $decrypted))
					{
						$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("response_broken", $this->extensionName);
						return $this->_createErrorResponse($message);
					}

					// We try to fetch the customer from magento
					$customerArr = $this->customerImport->fetchCustomerAction($decrypted[self::FORM_ID_EMAIL]);

					// If customer was fetched, we need to compare hash data
					if (array_key_exists("attributes", $customerArr) && 
						$customerArr["attributes"]["login_hash"] == $hash->getHash() &&
						$decrypted[self::FORM_ID_EMAIL] == $hash->getEmail()
					)
					{
						// ______________________________IMPORT______________________________
						$feUserData 	= $this->customerImport->prepareFeUserData($customerArr);
						$import 		= $this->customerImport->importFeUserAction($feUserData);

						if (!$import)
						{
							$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("error_importing_user", $this->extensionName);
							return $this->_createErrorResponse($message);
						}

						// ______________________________LOGIN______________________________
						$isAuth = $this->authenticationService->auth($decrypted[self::FORM_ID_EMAIL], $decrypted[self::FORM_ID_PASSWORD]);
						
						// User credentials are invalid!
						if (!$isAuth)
						{
							$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("login_failed_check_credentials", $this->extensionName);
							return $this->_createErrorResponse($message);
						}

						// ______________________________SUCCESS RESPONSE______________________________

						// User Array
						$allowedUser = $this->authenticationService->getAllowedFrontendUser();

						$request = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Web\\Request");

						$request->setPluginName($this->getPluginName());
						$request->setControllerExtensionName($this->getExtensionName());
						$request->setControllerName("Login");
						$request->setControllerActionName("success");
						$request->setControllerVendorName("Magedeveloper");
						$request->setArguments(array("user"=>$allowedUser, "redirect" => true));

						$response 	= $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Web\\Response");
						$dispatcher = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Dispatcher");
						$dispatcher->dispatch($request, $response);

						//$htmlRequest = $this->processRequest($request, $response);

						$response = array(
							"user" 	=> $allowedUser,
							"html"	=> $response->getContent(),
						);

						return $response;

					}

					$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("hash_comparison_failed", $this->extensionName);
					return $this->_createErrorResponse($message);

				}
				else
				{
					$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("login_failed_timeout", $this->extensionName);
					return $this->_createErrorResponse($message);		
				}

			}

		}

		$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("login_failed", $this->extensionName);
		return $this->_createErrorResponse($message);

	}


	/**
	 * Gets the request data array
	 *
	 * @return \array
	 */
	public function getRequestData()
	{
		if (!empty($this->requestData))
		{
			return $this->requestData;
		}

		$arguments = $this->request->getArguments();
		$data = $arguments["arguments"];

		// Allowed form elements
		$allowedFormElements = array(
			self::FORM_ID_EMAIL,
			self::FORM_ID_PASSWORD,
			self::FORM_ID_REQUEST_URL,
		);

		foreach ($data as $_formelement)
		{
			foreach ($allowedFormElements as $_allowedElement)
			{

				if ($_formelement["name"] == $_allowedElement)
				{
					if (array_key_exists("value", $_formelement))
					{
						$this->requestData[$_allowedElement] = $_formelement["value"];
					}
					
				}

			}

		}

		return $this->requestData;
	}

	/**
	 * Creates an login hash
	 *
	 * @param \string $email Email to create hash for
	 * @return \string
	 */
	public function createHash($email)
	{
		$model = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Model\\Hash");

		if ($model instanceof \MageDeveloper\Magelink\Domain\Model\Hash)
		{
			$model->setPid( $this->settingsService->getStoragePid() );
			
			$timestamp = time();

			// Create random hash 
			$hash = md5(uniqid($email . $timestamp));
			$model->setHash( $hash );
			$model->setEmail( $email );
			$model->setTstamp( $timestamp );

			$this->hashRepository->add($model);
			$this->persistenceManager->persistAll();

			if ($model->getUid())
			{
				return $model;
			}

		}

		return null;
	}

	/**
	 * Delivers a message to the frontend
	 *
	 * @param \string $message Message Text
	 * @param \string $type Message Type
	 * @param \array $additional Additional Data
	 * @return
	 */
	public function deliverMessage($message, $type = \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_INFO, $additional = array())
	{
		$data = array(
			'status' 	=> $type,
			'message'	=> $message,
		);

		array_merge($data, $additional);

		echo json_encode($data);
		exit();
	}

	/**
	 * Creates an error response
	 * 
	 * @param \string $message The error message
	 * @return \array
	 */
	protected function _createErrorResponse($message)
	{
		$request = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Web\\Request");

		$request->setPluginName($this->getPluginName());
		$request->setControllerExtensionName($this->getExtensionName());
		$request->setControllerName("Login");
		$request->setControllerActionName("error");
		$request->setControllerVendorName("Magedeveloper");
		$request->setArguments($this->request->getArguments());

		$response 	= $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Web\\Response");
		$dispatcher = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Dispatcher");
		$dispatcher->dispatch($request, $response);

		//$htmlRequest = $this->processRequest($request, $response);

		$response = array(
			"message" 	=> $message,
			"html"		=> $response->getContent(),
		);

		return $response;	
	
	}
	
	/**
	 * Gets the current login request params
	 * 
	 * @return array
	 */	
	protected function _getCurrentLoginRequestParams()
	{
		
		if ($this->request->hasArgument("tx-magelink-login-requesturl"))
		{
			$pos = strpos($this->request->getArgument("tx-magelink-login-requesturl"), $this->getCurrentBaseUrl());
		
			// Security
			if ($pos !== false && $pos == 0)
			{
				$requestUrl = $this->request->getArgument("tx-magelink-login-requesturl");
				$parsed = parse_url($requestUrl);
				
				if (isset($parsed["query"]))
				{
					parse_str($parsed["query"], $params);
					
					if (is_array($params))
					{
						return $params;
					}				
				}
			}
		}
		
		return array();
	}
	
	protected function _getLoginSuccessRedirectUri()
	{
		$action = "success";
		$controller = "Login";
		$extensionName = $this->getExtensionName();
		$pluginName = $this->getPluginName();

		$uriBuilder = $this->controllerContext->getUriBuilder();

		$params = $this->_getCurrentLoginRequestParams();

		$redirect = $uriBuilder->reset()
								->setArguments($params)
								->setCreateAbsoluteUri(true)
								->setAddQueryString(true)
								->uriFor($action, array(), $controller, $extensionName, $pluginName);
	
		return $redirect;
	
	}
	



}

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
 *	THIS CONTROLLER IS FOR INTERACTION BETWEEN MAGENTO -> TYPO3
 *
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ListenerController extends \MageDeveloper\Magelink\Controller\AbstractController
{
	/**
	 * Form Ids
	 * @var \string
	 */
	const FORM_ID_EMAIL 			= "tx_magelink_loginform[tx-magelink-login-email]";
	const FORM_ID_PASSWORD			= "tx_magelink_loginform[tx-magelink-login-password]";
	const FORM_ID_ENC				= "enc";

	/**
	 * Callback Function
	 */
	const CALLBACK_FUNC_LOGIN			= "tx_magelink_ajax_complete_login";
	const CALLBACK_FUNC_FINALIZE		= "tx_magelink_ajax_finalize_login";
	const CALLBACK_FUNC_FLASH_MESSAGE	= "tx_magelink_ajax_add_flash_message";

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
	 * Frontend User Controller
	 * @var \MageDeveloper\Magelink\Import\CustomerImport
	 * @inject
	 */
	protected $customerImport;

	/**
	 * Login Controller
	 * @var \MageDeveloper\Magelink\Controller\LoginController
	 * @inject
	 */
	protected $loginController;

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
	 * Gets request data and returns as an array
	 */
	public function getRequestData()
	{
		$enc = $this->request->getArgument("enc");

		// Encryption/Decryption Key
		$key = $this->settingsService->getCryptKey();
		
		$decrypted = \MageDeveloper\Magelink\Utility\Crypt::decrypt($enc, $key);
		
		$requestData = array(
			\MageDeveloper\Magelink\Controller\LoginController::FORM_ID_EMAIL		=> $decrypted["credentials"]["email"],
			\MageDeveloper\Magelink\Controller\LoginController::FORM_ID_PASSWORD	=> $decrypted["credentials"]["password"],
			"remote_addr"															=> $decrypted["remote_addr"]
		);
	
		return $requestData;	
	}

	/**
	 * Inititates the Magento Login Procedure
	 */
	public function initiateMagentoLoginProcedureAction()
	{
		$requestData = $this->getRequestData();

		// Put hash and email in database
		$hash = $this->loginController->createHash( $requestData[self::FORM_ID_EMAIL] );

		// If we could create a hash
		if ($hash === null)
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("error_creating_hash", $this->extensionName);
			$this->_createErrorResponse($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
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
				"email" 	=> $requestData[self::FORM_ID_EMAIL],
				"password" 	=> $requestData[self::FORM_ID_PASSWORD],
				"hash"	   	=> $hash->getHash(),
			),
			"remote_addr"	=> \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'),
		);

		$encrypted = \MageDeveloper\Magelink\Utility\Crypt::encrypt($data, $key);

		$parameters = array(
			"type" 		=> \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_SUCCESS,
			"url" 		=> "magelink/json/loginSourceMagento/",
			"enc"		=> $encrypted,
		);

		header('Content-Type: application/json');
		$encoded = json_encode($parameters);
		echo self::CALLBACK_FUNC_LOGIN."(".$encoded.");";
		exit();
	}

	/**
	 * Gets the final typo3 user source login response
	 *
	 * @return array|null
	 */
	public function getFinalizedMagentoLoginProcedureResponse()
	{
		if ($this->request->hasArgument("enc"))
		{
			$enc = $this->request->getArgument("enc");

			// Encryption/Decryption Key
			$key = $this->settingsService->getCryptKey();

			$decrypted 	= \MageDeveloper\Magelink\Utility\Crypt::decrypt($enc, $key);
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


						$redirect = "";
						if ($pid = $this->settingsService->getRedirectAfterSuccessfulLogin())
						{
							$action = "success";
							$controller = "Login";
							$extensionName = $this->getExtensionName();
							$pluginName = $this->getPluginName();

							$uriBuilder = $this->controllerContext->getUriBuilder();

							$redirect = $uriBuilder->reset()
								->setTargetPageUid($pid)
								->setTargetPageType(0)
								->setNoCache(1)
								->setUseCacheHash(!false)
								->setSection('')
								->setFormat('')
								->setLinkAccessRestrictedPages(false)
								->setArguments(array())
								->setCreateAbsoluteUri(true)
								->setAddQueryString(false)
								->setArgumentsToBeExcludedFromQueryString(array())
								->uriFor($action, array(), $controller, $extensionName, $pluginName);
						}

						$response = array(
							"redirect" => $redirect,
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
			$this->_createErrorResponse($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
		}

		// User Array
		$user = $this->authenticationService->getFrontendUser();

		// Could not retrieve user
		if (!$user)
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("user_inactive_try_again", $this->extensionName);
			$this->_createErrorResponse($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
		}

		// Put hash and email in database
		$hash = $this->loginController->createHash( $requestData[self::FORM_ID_EMAIL] );

		// If we could create a hash
		if ($hash === null)
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("error_creating_hash", $this->extensionName);
			$this->_createErrorResponse($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
		}

		// Put hash to user		
		$user["login_hash"] = $hash->getHash();

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
				),
				"remote_addr"	=> \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'),
			);

			$encrypted = \MageDeveloper\Magelink\Utility\Crypt::encrypt($data, $key);

			$parameters = array(
				"type" 	=> \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_SUCCESS,
				"url" 		=> "/magelink/json/loginSourceTYPO3/",
				"enc"		=> $encrypted,
			);

			header('Content-Type: application/json');
			echo self::CALLBACK_FUNC_LOGIN . '(' . json_encode($parameters) . ");";
			exit();

		}
		else
		{
			$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("error_exporting_user", $this->extensionName);
			$this->_createErrorResponse($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
		}

		$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("unknown_error", $this->extensionName);
		$this->_createErrorResponse($message, \MageDeveloper\Magelink\Service\SettingsService::MESSAGE_TYPE_ERROR);
	}

	/**
	 * Gets the final typo3 user source login response
	 *
	 * @return array|null
	 */
	public function getFinalizedTYPO3LoginProcedureResponse()
	{
		if ($this->request->hasArgument("enc"))
		{
			$enc = $this->request->getArgument("enc");
			
			// Encryption/Decryption Key
			$key = $this->settingsService->getCryptKey();

			$decrypted = \MageDeveloper\Magelink\Utility\Crypt::decrypt($enc, $key);

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

						$redirect = "";
						if ($pid = $this->settingsService->getRedirectAfterSuccessfulLogin())
						{
							$action = "success";
							$controller = "Login";
							$extensionName = $this->getExtensionName();
							$pluginName = $this->getPluginName();

							$uriBuilder = $this->controllerContext->getUriBuilder();

							$redirect = $uriBuilder->reset()
								->setTargetPageUid($pid)
								->setTargetPageType(0)
								->setNoCache(1)
								->setUseCacheHash(!false)
								->setSection('')
								->setFormat('')
								->setLinkAccessRestrictedPages(false)
								->setArguments(array())
								->setCreateAbsoluteUri(true)
								->setAddQueryString(false)
								->setArgumentsToBeExcludedFromQueryString(array())
								->uriFor($action, array(), $controller, $extensionName, $pluginName);
						}

						$response = array(
							"redirect" => $redirect,
						);

						// Remove the hash, we dont need it anymore
						$this->hashRepository->remove($hash);
						$this->persistenceManager->persistAll();

						return $response;
					}

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
				break;

			// Procedure when the user source is Magento	
			case \MageDeveloper\Magelink\Service\SettingsService::USER_SOURCE_MAGENTO:
			default:

				// Initiate the Magento Login Procedure
				$this->forward("initiateMagentoLoginProcedure");
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
		echo self::CALLBACK_FUNC_FINALIZE.'('.json_encode($response).');';
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
	 * Delivers a message to the frontend
	 *
	 * @param \string $message Message Text
	 * @return void
	 */
	public function _createErrorResponse($message)
	{
		echo self::CALLBACK_FUNC_FLASH_MESSAGE.'('.json_encode($message).');';
		exit();
	}

}
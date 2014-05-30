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
 *
 *
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class BlockController extends \MageDeveloper\Magelink\Controller\AbstractController
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

	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Object\\ObjectManager");

		// Inject Settings Service
		$this->settingsService	= $this->objectManager->get("MageDeveloper\\Magelink\\Service\\SettingsService");

		parent::__construct();
	}

	/**
	 * action index
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$blockId = $this->settingsService->getSelectedBlockId();
		
		// Encryption/Decryption Key
		$key = $this->settingsService->getCryptKey();
		$div = "block_".md5(time()*rand(1,999999));
		$data = array(
			$blockId,
			$div,
		);
		$encrypted = \MageDeveloper\Magelink\Utility\Crypt::encrypt($data, $key);
	
		$this->view->assign("div_id", $div);
		$this->view->assign("block_id", base64_encode($encrypted));
		$this->view->assign("block_class", str_replace('/', '_', $blockId));
	}

	/**
	 * action show
	 *
	 * @return void
	 */
	public function showAction()
	{
		$html = "";
		if ($this->request->hasArgument("block"))
		{
			$blockData = $this->request->getArgument("block");
			
			// Encryption/Decryption Key
			$key = $this->settingsService->getCryptKey();

			$decrypted = \MageDeveloper\Magelink\Utility\Crypt::decrypt(base64_decode($blockData), $key);

			if (array_key_exists("html", $decrypted) && array_key_exists("remote_addr", $decrypted))
			{
				$remoteAddr = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR');

				if ($remoteAddr == $decrypted["remote_addr"])
				{
					$html = $decrypted["html"];
				}
			}

		}
		die($html);
		//$this->view->assign("html", $html);
	}

}
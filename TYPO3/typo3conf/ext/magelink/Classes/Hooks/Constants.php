<?php
namespace MageDeveloper\Magelink\Hooks;

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

class Constants
{
	/**
	 * flexFormService
	 *
	 * @var \MageDeveloper\Magelink\Service\FlexFormService
	 * @inject
	 */
	protected $flexFormService;

	/**
	 * settingsService
	 *
	 * @var \MageDeveloper\Magelink\Service\SettingsService
	 * @inject
	 */
	protected $settingsService;

	/**
	 * flexFormService
	 *
	 * @var \MageDeveloper\Magelink\Service\TranslationService
	 * @inject
	 */
	protected $translationService;

	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var \MageDeveloper\Magelink\Domain\Repository\FrontendUserGroupRepository
	 * @inject
	 */
	protected $frontendUserGroupRepository;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Object\\ObjectManager");

		// Inject Settings Service
		$this->settingsService	= $this->objectManager->get("MageDeveloper\\Magelink\\Service\\SettingsService");
		// Inject Flexform Service
		$this->flexFormService  = $this->objectManager->get("MageDeveloper\\Magelink\\Service\\FlexFormService");
		// Inject Translation Service
		$this->translationService = $this->objectManager->get("MageDeveloper\\Magelink\\Service\\TranslationService");
		// Inject the frontend user group repository
		$this->frontendUserGroupRepository = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Repository\\FrontendUserGroupRepository");
	}

	/**
	 * Displays the information field in the constants editor
	 * 
	 * @param $config
	 * @param $pObj
	 * @return \string
	 */
	public function html($config, $pObj)
	{
		$config["fieldName"] 	= "";
		$config["fieldValue"]	= "";
	
		$logoUrl = '../' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('magelink') . 'Resources/Public/Images/logo_magelink.png';
	
		$image = "<img src=\"{$logoUrl}\" style=\"display:block;\"/>";
	
		$text = "<p>Thank you for using MageLink!</p>".
				"<br />".
				"<h3><a href=\"http://magelink.magedeveloper.de/documentation/\" target=\"_blank\">> Open Documentation in new window</a></h3>".
				"<br />".
				"<p>".
				"<h4>Developer Information</h4>"."<br />".
				"Name: Bastian 'Aggy' Zagar"."<br />".
				"E-Mail: <a href=\"mailto:zagar@aixdesign.net\">zagar@aixdesign.net</a>"."<br />".
				"Website: <a href=\"http://www.aixdesign.net\" target=\"_blank\">http://www.aixdesign.net</a>".
				"<hr size='1' />".
				"<h3>Configuration Hints / Important Notes:</h3>".
				"<p style=\"border:1px solid #c0c0c0; background-color:#f2f2f2; color:red; padding:4px;\">".
				"If you have an Magento instance on the same server as TYPO3, you should configure the '<strong>Magento Root Path</strong>'! <br />".
				"It will drastically increase the overall speed and fetch the data directly from Magento instead of using Webservices. <br />".
				"<strong>Alternatively - <u>if you want to use Webservices</u> - please leave the Magento Root Path Setting empty!</strong>".
				"</p>".
				"<strong>DOCUMENT_ROOT:</strong>&nbsp;" . $_SERVER["DOCUMENT_ROOT"] . "<br />" .
				"<br />" .
				"<hr size='1' />".
				"" 
		
		;
	
		$html = $image		.
				"<br />"	.
				$text
		;
	
		return $html;
	
	}
	
	public function usergroup($config, $pObj)
	{
		$userGroups = $this->frontendUserGroupRepository->findAll(true);
	
		$groups = array();
		foreach ($userGroups as $_group)
		{
			/* @var \MageDeveloper\Magelink\Domain\Model\FrontendUserGroup $_group */
			$groups[$_group->getUid()] = "[".$_group->getUid()."]"." ".$_group->getTitle();
		}
		
		$p_field = '';

		if (is_array($groups)) 
		{
			foreach ($groups as $val=>$label) 
			{
				// option tag:
				$sel = '';
				if ($val == $config["fieldValue"]) 
				{
					$sel = ' selected';
				}
				$p_field .= '<option value="' . htmlspecialchars($val) . '"' . $sel . '>' . $GLOBALS['LANG']->sL($label) . '</option>';
			}
			$p_field = '<select id="' . $config["fieldName"] . '" name="' . $config["fieldName"] . '" onChange="uFormUrl(' . "usergroup_magelink" . ')">' . $p_field . '</select>';
		}
		
		return $p_field;
	}

	public function ext_fNandV($params) 
	{
		$fN = 'data[' . $params['name'] . ']';
		$fV = $params['value'];
		// Values entered from the constantsedit cannot be constants!	230502; removed \{ and set {
		if (preg_match('/^{[\\$][a-zA-Z0-9\\.]*}$/', trim($fV), $reg)) {
			$fV = '';
		}
		$fV = htmlspecialchars($fV);
		return array($fN, $fV, $params);
	}	

}
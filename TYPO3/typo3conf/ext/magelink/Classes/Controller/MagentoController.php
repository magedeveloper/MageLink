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
class MagentoController extends \MageDeveloper\Magelink\Controller\AbstractController
{
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
	 * Gets the current base url
	 * 
	 * @return \string
	 */
	public function getCurrentBaseUrl()
	{
		$uriBuilder = clone $this->getControllerContext()->getUriBuilder();
		$uriBuilder->reset();
		$uriBuilder->setCreateAbsoluteUri(true);
		$uri = $uriBuilder->uriFor();
		
		$parsed = parse_url($uri);

		$url = $parsed["scheme"] . '://' . rtrim($parsed["host"], '/') . '/' . ltrim($parsed["path"], '/');

		return $url;
	}

	/**
	 * Action for index
	 */
	public function indexAction()
	{
		//echo "<h1>Content Object Rendering</h1>"."<br />";
		/* @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer */
		/*$cor = $this->objectManager->get("TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer");
		
		$co = $this->configurationManager->getContentObject();
				
		$uid = 202;		
				
		$conf = array(
			"tables" 		=> "tt_content",
			"source" 		=> $uid,
			"dontCheckPid"	=> true,
		);		
				
		$this->view->assign("content",  $co->RECORDS($conf));
	
		return;
		*/
	
		// Initialize Magento and prepare
		if ($this->magentoCore->init())
		{
			$this->magentoCore->setTYPO3Controller($this);
			$this->magentoCore->setTYPO3BaseUrl( $this->getCurrentBaseUrl() );
		
			$params = $this->request->getArguments();
			
			$this->magentoCore->dispatch($params);
	
			$content =  $this->magentoCore->getHeader();
			$content .= $this->magentoCore->getBlock("content")->toHtml();
			$content .= $this->magentoCore->getMage()->getResponse()->getBody();
			
			foreach($this->magentoCore->getMage()->getBlocks() as $block)
			{
				//echo "NAME: <strong>".$block->getNameInLayout()."</strong><br />";
				//echo $block->toHtml();
				//echo "<hr />";
			}
			
			$this->view->assign("content", $content);
			
		}
	}

}
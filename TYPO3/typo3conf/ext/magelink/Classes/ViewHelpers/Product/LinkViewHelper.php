<?php
namespace MageDeveloper\Magelink\ViewHelpers\Product;

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
 *
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class LinkViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
	/**
	 * @var string
	 */
	protected $tagName = 'a';

	/**
	 * settingsService
	 * @var \MageDeveloper\Magelink\Service\SettingsService
	 * @inject
	 */
	protected $settingsService;

	/**
	 * productRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\ProductRepository
	 * @inject
	 */
	protected $productRepository;

	/**
	 * Render method
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Product $product Product Model
	 * @param \integer $pid target page. See TypoLink destination
	 * @param \integer $pageType type of the target page. See typolink.parameter
	 * @param \boolean $noCache set this to disable caching for the target page. You should not need this.
	 * @param \boolean $noCacheHash set this to supress the cHash query parameter created by TypoLink. You should not need this.
	 * @param \string $section the anchor to be added to the URI
	 * @param \string $format The requested format, e.g. ".html
	 * @param \boolean $linkAccessRestrictedPages If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.
	 * @param \array $additionalParams additional query parameters that won't be prefixed like $arguments (overrule $arguments)
	 * @param \boolean $absolute If set, the URI of the rendered link is absolute
	 * @param \boolean $addQueryString If set, the current query parameters will be kept in the URI
	 * @param \array $argumentsToBeExcludedFromQueryString arguments to be removed from the URI. Only active if $addQueryString = TRUE
	 * @internal param string $extensionName Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used
	 * @internal param string $pluginName Target plugin. If empty, the current plugin name is used
	 * @return \string Rendered link
	 */
	public function render(\MageDeveloper\Magelink\Domain\Model\Product $product, $pid = NULL, $pageType = 0, $noCache = FALSE, $noCacheHash = FALSE, $section = '', $format = '', $linkAccessRestrictedPages = FALSE, array $additionalParams = array(), $absolute = FALSE, $addQueryString = FALSE, array $argumentsToBeExcludedFromQueryString = array())
	{
		$uri = "";
	
		$linkConf = array();
		
		if ($pid === null)
		{
			$pid = $this->settingsService->getDynamicDetailViewPid();
			
			// If we currently are located on a dynamic detail view listener
			if (!$pid && $this->settingsService->getDisplayType() == \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_DYNAMIC)
			{
				$pid = $GLOBALS['TSFE']->id;
			}
			
		}
		
		if ($pid)
		{
			$action = "";
			$controller = "Product";
			$extensionName = "Magelink";
			$pluginName = "Productdisplay";

			$uriBuilder = $this->controllerContext->getUriBuilder();

			$uri = $uriBuilder->reset()
				->setTargetPageUid($pid)
				->setTargetPageType($pageType)
				->setNoCache($noCache)
				->setUseCacheHash(!$noCacheHash)
				->setSection($section)
				->setFormat($format)
				->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
				->setArguments($additionalParams)
				->setCreateAbsoluteUri($absolute)
				->setAddQueryString($addQueryString)
				->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
				->uriFor($action, array("product"=>$product), $controller, $extensionName, $pluginName);
				
			$linkConf["parameter"] = $uri;

		}
		else
		{
			if ($product->getUrlPath())
			{
				$uri =  $this->settingsService->getMagentoUrl() .'/'. $product->getUrlPath();
			}
			else
			{
				$uri = $this->settingsService->getProductUrlByEntityId($product->getEntityId());
			}
		}

		$this->tag->addAttribute('href', $uri);
		$this->tag->setContent($this->renderChildren());
		$this->tag->forceClosingTag(true);
		return $this->tag->render();
	}



}
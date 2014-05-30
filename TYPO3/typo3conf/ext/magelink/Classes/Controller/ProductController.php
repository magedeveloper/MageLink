<?php
namespace MageDeveloper\Magelink\Controller;

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

class ProductController extends \MageDeveloper\Magelink\Controller\AbstractController 
{
	/**
	 * productRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\ProductRepository
	 * @inject
	 */
	protected $productRepository;

	/**
	 * categoryRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;

	/**
	 * Category Import Model
	 * 
	 * @var \MageDeveloper\Magelink\Import\CategoryImport
	 * @inject
	 */
	protected $categoryImport;

	/**
	 * Product Import Model
	 * 
	 * @var \MageDeveloper\Magelink\Import\ProductImport
	 * @inject
	 */
	protected $productImport;
	
	/**
	 * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher;	
	

	/**
	 * action index
	 *
	 * @return void
	 */
	public function indexAction() 
	{
		$type = $this->settingsService->getDisplayType();
		
		echo "(DISPLAYTYPE BEFORE: {$type})<br />";
		
		// Signal Slot
		$this->signalSlotDispatcher->dispatch(
			__CLASS__,
			__FUNCTION__,
			array(
				'type' => &$type,
			)
		);
		
		echo "(DISPLAYTYPE AFTER: {$type})<br />";
		
		
		switch($type)
		{
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_LIST:
				$this->forward('list');
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_GRID:
				$this->forward('grid');
				break;			
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_INLINE:
				$this->forward('inline');
				break;			
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_SHOW:
				$this->forward('show');
				break;				
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_DYNAMIC:
				$this->forward('dynamic', null, null, $this->request->getArguments());
				break;		
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_DISPLAY_TYPE_NO_SELECTION:
			default:
				$this->addFlashMessage("No display type chosen!");
		}
	
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() 
	{
		$ids 			= $this->_getRequestedProductIds();
		$storeViewCode 	= $this->settingsService->getStoreViewCode();
		$reload 		= $this->settingsService->reload();

		$products = $this->productImport->getProductRepositoryByIds($ids, $storeViewCode, $reload);
		
		$this->view->assign('products', $products);
		$this->view->assign('requested', implode(', ', $ids));
		$this->view->assign('setting', $this->settingsService->getSettings());
		$this->view->assign("media_url", $this->settingsService->getMediaUrl());
	}

	/**
	 * action show
	 *
	 * @return void
	 */
	public function showAction() 
	{
		$ids 			= $this->_getRequestedProductIds();
		$storeViewCode 	= $this->settingsService->getStoreViewCode();
		$reload 		= $this->settingsService->reload();
		$products = $this->productImport->getProductRepositoryByIds($ids, $storeViewCode, $reload);
		$this->view->assign('products', $products);
		$this->view->assign('requested', implode(', ', $ids));
	}

	/**
	 * action grid
	 *
	 * @return void
	 */
	public function gridAction() 
	{
		$ids 			= $this->_getRequestedProductIds();
		
		$storeViewCode 	= $this->settingsService->getStoreViewCode();
		$reload 		= $this->settingsService->reload();

		$products = $this->productImport->getProductRepositoryByIds($ids, $storeViewCode, $reload);

		$this->view->assign('products', $products);
		$this->view->assign('requested', implode(', ', $ids));
		$this->view->assign('setting', $this->settingsService->getSettings());
		$this->view->assign("media_url", $this->settingsService->getMediaUrl());
	}
	
	/**
	 * action inline
	 *
	 * @return void
	 */
	public function inlineAction() 
	{
		$ids 			= $this->_getRequestedProductIds();
		$storeViewCode 	= $this->settingsService->getStoreViewCode();
		$reload 		= $this->settingsService->reload();

		$products = $this->productImport->getProductRepositoryByIds($ids, $storeViewCode, $reload);
		$this->view->assign('products', $products);
		$this->view->assign('requested', implode(', ', $ids));
	}

	/**
	 * action dynamic
	 *
	 * @return void
	 */
	public function dynamicAction() 
	{
		if ($arguments = $this->request->getArguments())
		{
			if ( array_key_exists("product", $arguments) )
			{
				$uid = (int)$arguments["product"];
				
				$product = $this->productRepository->findByUid($uid);


				if ($product && $product->getAutoRefresh() == true || $this->settingsService->reload())
				{
					// Reload product if it has auto refresh
					$storeViewCode 	= $this->settingsService->getStoreViewCode();
					$this->productImport->getProductRepositoryByIds(array($product->getEntityId()), $storeViewCode, true, true);
					$product = $this->productRepository->findByUid($uid);
				}
				
				if ($product instanceof \MageDeveloper\Magelink\Domain\Model\Product)
				{
					$allowedIds = $this->_getRequestedProductIds();
				
					if (in_array($product->getEntityId(), $allowedIds) || empty($allowedIds))
					{
						$templateFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($this->getExtensionKey()) . "Resources/Private/Templates/Product/Show.html";
						$this->view->setTemplatePathAndFilename($templateFile);
						$this->view->assign("products", array($product));
					}
					
				}
				else
				{
					$this->forward("redirectDynamic");
				}
				
			}
		
		}
		else
		{
			// Redirect if necessary
			if ($this->settingsService->getDynamicDetailViewRedirect())
			{
				$this->forward("redirectDynamic");
			}
		}
		
	}

	/**
	 * Redirects when dynamic detail view redirect was set
	 * 
	 * @return void
	 */
	public function redirectDynamicAction()
	{
		// No arguments were given, we need to check if we have to redirect to a certain target
		$redirect = $this->settingsService->getDynamicDetailViewRedirect();

		if ($redirect)
		{
			// Redirection
			\TYPO3\CMS\Core\Utility\HttpUtility::redirect($redirect, $this->settingsService->getDynamicDetailViewRedirectErrorCode());
			exit();
		}

	}

	/**
	 * preprocessing for all actions
	 *
	 * @return void
	 */
	protected function initializeAction() 
	{
		// register foreign argument for search action
		$foreignPluginContext = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_magelink_categorydisplay');
		
		if (isset($foreignPluginContext['category'])) 
		{
			$category = (int)$foreignPluginContext['category'];
			$this->request->setArgument('category', $category);
		}
		
		parent::initializeAction();
	}
	
	/**
	 * Get all requested product ids from the configuration setting
	 * 
	 * @return array
	 */
	protected function _getRequestedProductIds()
	{
		$productSource = $this->settingsService->getProductSourceSelection();
		$storeViewCode = $this->settingsService->getStoreViewCode();
		$reload		   = $this->settingsService->reload();
		
		switch ($productSource)
		{
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_MANUAL_SELECTION:
				return $this->settingsService->getSelectedProductIds();
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_CATEGORY:
				$categoryId	= $this->settingsService->getSelectedCategoryId();
				$category 	= $this->categoryImport->getCategoryById($categoryId, $storeViewCode, $reload);
				return $category->getProducts();
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_NAVIGATION:
				if ($this->request->hasArgument("category"))
				{
					$categoryId = $this->request->getArgument("category");
					$category 	= $this->categoryRepository->findByUid($categoryId);
					if ($category instanceof \MageDeveloper\Magelink\Domain\Model\Category)
						return $category->getProducts();
				}
				return array();
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_TAGS:
				$tags 		= \MageDeveloper\Magelink\Utility\FilterString::getExplodedValues( $this->settingsService->getTagsString() );
				$categories = \MageDeveloper\Magelink\Utility\FilterString::getExplodedValues( $this->settingsService->getCategoriesString() );
				$skus		= \MageDeveloper\Magelink\Utility\FilterString::getExplodedValues( $this->settingsService->getSkusString() );
				return $this->productImport->getProductIdsByFilter($tags, $categories, $skus, $storeViewCode, $reload);
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::PRODUCT_SOURCE_NO_SELECTION:
			default:
				return array();
			
		}
		
		return array();
	}







}
?>
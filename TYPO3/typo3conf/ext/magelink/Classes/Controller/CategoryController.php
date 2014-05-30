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

class CategoryController extends \MageDeveloper\Magelink\Controller\AbstractController
{
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
	 * Display Mode
	 * @var \string
	 */
	protected $displayMode;

	/**
	 * index action
	 * The main index category display
	 */
	public function indexAction()
	{
		// If no display mode is set, we need to fetch the entry display mode setting
		$this->displayMode = $this->settingsService->getCategoryEntryDisplayMode();
		if ($this->request->hasArgument("displayMode"))
		{
			$this->displayMode = $this->request->getArgument("displayMode");
		}

		$rootId = $this->settingsService->getCategoryRootId();
		if ($this->request->hasArgument("rootId"))
		{
			$rootId = (int)$this->request->getArgument("rootId");
		}
		
		$storeViewCode	= $this->settingsService->getStoreViewCode();
		$category 		= $this->categoryImport->getCategoryById($rootId, $storeViewCode, $this->settingsService->reload());
	
		$target = "display"; 
		switch($this->displayMode)
		{
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_NAVIGATION:
				$target = "navigation";
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_NAVPRODUCTS:
				$target = "navigationProducts";
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_THUMBNAILS:
				$target = "thumbnail";
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_THUMBPRODUCTS:
				$target = "thumbProducts";
				break;
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_PRODUCTS:
				$target = "products";
				break; 
			case \MageDeveloper\Magelink\Service\SettingsService::CATEGORY_DISPLAY_TYPE_NO_SELECTION:
			default:
				$target = "display";
				break;
		}
		
		$args = array(
			//"category" => $category,
		);
		
		$args = array_merge($this->request->getArguments(), $args);
		$args["category"] = $category;

		$this->forward($target, null, null, $args);
		
	}

	/**
	 * sub action
	 * Display sub categories when
	 * clicked on the entry display mode
	 */
	public function subAction()
	{
		$this->displayMode = $this->settingsService->getCategorySubDisplayMode();
		if ($this->request->hasArgument("category"))
		{
			$categoryId = $this->request->getArgument("category");
			$category = $this->categoryRepository->findByUid($categoryId);
			
			$args = array(
				"displayMode"	=> $this->displayMode,
				"rootId"		=> $category->getEntityId(),
			);
			
			$args = array_merge($this->request->getArguments(), $args);
			
			$this->forward("index", null, null, $args);
		}
	}
	
	/**
	 * action naviation
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Category $category Category Model
	 * @return void
	 */
	public function navigationAction(\MageDeveloper\Magelink\Domain\Model\Category $category)
	{
		$storeViewCode	= $this->settingsService->getStoreViewCode();
		$reload			= $this->settingsService->reload();
		
		if ($this->settingsService->getDisplayFullCategoryTree())
		{
			$rootCategoryId = $this->settingsService->getCategoryRootId();
			$category 	= $this->categoryImport->getCategoryById($rootCategoryId, $storeViewCode, $reload);
		}

		$childIds = $category->getChildIds();
		if ($childIds != "")
		{
			$children = explode(',', $childIds);
		}

		$categories = array();
		if ($children)
		{
			$categories	= $this->categoryImport->getCategoryRepositoryByIds($children, $storeViewCode, $reload);
		}

		$this->view->assign("currentCategory", $category);
		$this->view->assign("categories", $categories);
		$this->view->assign("root_id", $this->settingsService->getCategoryRootId());
	}

	/**
	 * action naviation
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Category $category Category Model
	 * @return void
	 */
	public function thumbnailAction(\MageDeveloper\Magelink\Domain\Model\Category $category)
	{
		$storeViewCode	= $this->settingsService->getStoreViewCode();
		$reload			= $this->settingsService->reload();

		$children = array();
		if ($category->getChildIds() != "")
		{
			$children = explode(',', $category->getChildIds());
		}

		$categories = array();
		if ($children)
		{
			$categories	= $this->categoryImport->getCategoryRepositoryByIds($children, $storeViewCode, $reload);
		}


		$this->view->assign("categories", $categories);
		$this->view->assign("root_id", $this->settingsService->getCategoryRootId());
	}

	/**
	 * action thumbnails and products
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Category $category Category Model
	 * @return void
	 */
	public function thumbProductsAction(\MageDeveloper\Magelink\Domain\Model\Category $category)
	{
		$storeViewCode	= $this->settingsService->getStoreViewCode();
		$reload			= $this->settingsService->reload();

		$children = array();
		if ($category->getChildIds() != "")
		{
			$children = explode(',', $category->getChildIds());
		}

		// Child Categories
		$categories = array();
		if ($children)
		{
			$categories	= $this->categoryImport->getCategoryRepositoryByIds($children, $storeViewCode, $reload);
		}

		// Child Products
		$productIds 	= $category->getProducts();
		$products 		= $this->productImport->getProductRepositoryByIds($productIds, $storeViewCode, $reload);

		$this->view->assign("products", 	$products);
		$this->view->assign("categories", 	$categories);
		$this->view->assign("category", 	$category);
		$this->view->assign("root_id", $this->settingsService->getCategoryRootId());

		// Is this view called by entry or by sub?
		$displayMode = $this->settingsService->getCategoryEntryDisplayMode();
		if ($this->request->hasArgument("category"))
		{
			$displayMode = $this->settingsService->getCategorySubDisplayMode();
		}
		$this->view->assign("displayMode", $displayMode);

	}

	/**
	 * action naviation
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Category $category Category Model
	 * @return void
	 */
	public function navigationProductsAction(\MageDeveloper\Magelink\Domain\Model\Category $category)
	{
		$storeViewCode	= $this->settingsService->getStoreViewCode();
		$reload			= $this->settingsService->reload();

		// Child Products before we possibly overwrite the category with the root one
		$productIds 	= $category->getProducts();
		$products 		= $this->productImport->getProductRepositoryByIds($productIds, $storeViewCode, $reload);

		if ($this->settingsService->getDisplayFullCategoryTree())
		{
			$rootCategoryId = $this->settingsService->getCategoryRootId();
			$category 		= $this->categoryImport->getCategoryById($rootCategoryId, $storeViewCode, $reload);
		}

		$children = array();
		if ($category->getChildIds() != "")
		{
			$children = explode(',', $category->getChildIds());
		}

		// Child Categories
		$categories = array();
		if ($children)
		{
			$categories	= $this->categoryImport->getCategoryRepositoryByIds($children, $storeViewCode, $reload);
		}

		$this->view->assign("products", 	$products);
		$this->view->assign("categories", 	$categories);
		$this->view->assign("category", 	$category);
		$this->view->assign("root_id", $this->settingsService->getCategoryRootId());

		// Is this view called by entry or by sub?
		$displayMode = $this->settingsService->getCategoryEntryDisplayMode();
		if ($this->request->hasArgument("category"))
		{
			$displayMode = $this->settingsService->getCategorySubDisplayMode();
		}
		
		$this->view->assign("displayMode", $displayMode);
	}

	/**
	 * action products view
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Category $category Category Model
	 * @return void
	 */
	public function productsAction(\MageDeveloper\Magelink\Domain\Model\Category $category)
	{
		$storeViewCode	= $this->settingsService->getStoreViewCode();
		$reload			= $this->settingsService->reload();

		// Child Products
		$productIds 	= $category->getProducts();
		$products 		= $this->productImport->getProductRepositoryByIds($productIds, $storeViewCode, $reload);

		$this->view->assign("products", 	$products);
		$this->view->assign("category", 	$category);
		$this->view->assign("root_id", $this->settingsService->getCategoryRootId());

		// Is this view called by entry or by sub?
		$displayMode = $this->settingsService->getCategoryEntryDisplayMode();
		if ($this->request->hasArgument("category"))
		{
			$displayMode = $this->settingsService->getCategorySubDisplayMode();
		}
		
		$this->view->assign("displayMode", $displayMode);
	}

	/**
	 * Action for category detail display
	 * 
	 * @param \MageDeveloper\Magelink\Domain\Model\Category $category Category Model
	 * @return void
	 */
	public function displayAction(\MageDeveloper\Magelink\Domain\Model\Category $category)
	{
		$this->view->assign("category", $category);
		$this->view->assign("root_id", $this->settingsService->getCategoryRootId());
	}

	/**
	 * action delete
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Category $category
	 * @return void
	 */
	public function deleteAction(\MageDeveloper\Magelink\Domain\Model\Category $category) 
	{
		$this->categoryRepository->remove($category);
		$this->flashMessageContainer->add('Your Category was removed.');
		$this->redirect('list');
	}

}
?>
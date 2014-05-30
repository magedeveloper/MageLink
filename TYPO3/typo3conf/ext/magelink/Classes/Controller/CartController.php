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
class CartController extends \MageDeveloper\Magelink\Controller\AbstractController
{
	/**
	 * productRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\ProductRepository
	 * @inject
	 */
	protected $productRepository;

	/**
	 * Product Import Model
	 *
	 * @var \MageDeveloper\Magelink\Import\ProductImport
	 * @inject
	 */
	protected $productImport;
	
	
	
	
	

	/**
	 * action index
	 * 
	 * @return void
	 */
	public function indexAction()
	{
	
	}

	/**
	 * action show
	 *
	 * @return void
	 */
	public function showAction()
	{
		if ($this->request->hasArgument("cart"))
		{
			$cart = $this->request->getArgument("cart");
			
			if (array_key_exists("items", $cart))
			{
				$ids = array();
				$items = array();
				
				
				foreach ($cart["items"] as $_item)
				{
					/* @var \MageDeveloper\Magelink\Domain\Model\Cart\Item $cartItem */
					$cartItem = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Model\\Cart\\Item");
					
					$cartItem->setId($_item["id"]);
					$cartItem->setProductId($_item["product_id"]);
					$cartItem->setName($_item["name"]);
					$cartItem->setQty($_item["qty"]);
					$cartItem->setPrice($_item["price"]);
					$cartItem->setTotal($_item["total"]);
					$cartItem->setStore($_item["store"]);
					$cartItem->setParentItemId($_item["parent_item_id"]);
					
					$items[] = $cartItem;
					
				}

				$cart["items"] = $items;
				
				$this->view->assign("cart", $cart);
				$this->view->assign("items", $items);
			}
			
		}
		
		
	
	}
	
}
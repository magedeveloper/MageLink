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
class UserLabelController extends \MageDeveloper\Magelink\Controller\AbstractController
{
	/**
	 * @var \MageDeveloper\Magelink\Domain\Repository\FrontendUserRepository
	 * @inject
	 */
	protected $frontendUserRepository;

	/**
	 * categoryRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;

	/**
	 * productRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\ProductRepository
	 * @inject
	 */
	protected $productRepository;

	/**
	 * productfilterRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\ProductfilterRepository
	 * @inject
	 */
	protected $productfilterRepository;

	/**
	 * attributeRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\AttributeRepository
	 * @inject
	 */
	protected $attributeRepository;

	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;


	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Object\\ObjectManager");

		// Inject Category Repository
		$this->categoryRepository  = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Repository\\CategoryRepository");
		
		// Inject Product Repository
		$this->productRepository  = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Repository\\ProductRepository");

		// Inject Attribute Repository
		$this->attributeRepository  = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Repository\\AttributeRepository");

		// Inject Productfilter Repository
		$this->productfilterRepository  = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Repository\\ProductfilterRepository");

		// Inject Frontend User Repository
		$this->frontendUserRepository = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Repository\\FrontendUserRepository");

		// Inject Settings Service
		$this->settingsService	= $this->objectManager->get("MageDeveloper\\Magelink\\Service\\SettingsService");

		parent::__construct();
	}

	/**
	 * User label for products
	 *
	 * @param \array $params
	 * @param \array $pObj
	 */
	public function productLabelAction(&$params, &$pObj)
	{
		$uid = (int)$params["row"]["uid"];
		
		$product = $this->productRepository->findByUid($uid, false);
		
		$params["title"] = $entityId;
		
		if ($product instanceof \MageDeveloper\Magelink\Domain\Model\Product)
		{
			if ($product->getStore() != "")
			{
				$store = "[{$product->getStore()}]";
			}
			
			$params["title"] = $store."[{$product->getEntityId()}]"."[{$product->getSku()}]" . ' '. $product->getName();
		}
		
	}

	/**
	 * User label for categories
	 *
	 * @param \array $params
	 * @param \array $pObj
	 */
	public function categoryLabelAction(&$params, &$pObj)
	{
		$uid = (int)$params["row"]["uid"];
		
		$category = $this->categoryRepository->findByUid($uid, false);
		
		$label = "";
		
		if ($category instanceof \MageDeveloper\Magelink\Domain\Model\Category)
		{
			$depth =  $category->getLevel()-1;
			
			$spacer = ".";
			if ($depth >= 1)
			{
				$spacer = str_repeat("..", $depth*2);
			}

			$label .= $spacer;			
			$label .= "[{$category->getEntityId()}]" . ' '. $category->getName();

			$params["title"] = $label;
		}
		
	}
	
	
	/**
	 * User label for attributes
	 * 
	 * @param \array $params
	 * @param \array $pObj
	 */
	public function attributeLabelAction(&$params, &$pObj)
	{
		$label = "";
		
		$uid = (int)$params["row"]["uid"];
		$attribute = $this->attributeRepository->findByUid($uid);
		
		$code = $attribute->getCode();
		$relationType = $attribute->getRelationType();
		$object = $this->getRelationObject($relationType, $attribute->getRelation());
		
		$label = $code;
		
		if ($object)
		{
			switch($relationType)
			{
				case \MageDeveloper\Magelink\Domain\Model\Attribute::RELATION_TYPE_CUSTOMER:
					$label = $object->getEmail();
					break;
				case \MageDeveloper\Magelink\Domain\Model\Attribute::RELATION_TYPE_CATEGORY:
					$label = $object->getName();
					break;
				case \MageDeveloper\Magelink\Domain\Model\Attribute::RELATION_TYPE_PRODUCT:
					$label = "[{$object->getEntityId()}]" . ' ' . "[{$code}]";
					break;
				default:
					break;
			}
			
		}

		$params["title"] = $label;
	}

	/**
	 * User label for productfilters
	 *
	 * @param \array $params
	 * @param \array $pObj
	 */
	public function productfilterLabelAction(&$params, &$pObj)
	{
		$uid 	= (int)$params["row"]["uid"];
		$filter = $this->productfilterRepository->findByUid($uid);
		$label 	= "";
		
		if ($filter)
		{
		
			if ($filter->getStore())
			{
				$label = "[{$filter->getStore()}]".' ';
			}
			else
			{
				$label = "";
			}
		
			$tagsStr 		= implode(", ", $filter->getTags());
			$categoriesStr 	= implode(", ", $filter->getCategories());
			

			if ($tagsStr)
			{
				$label .= \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("tx_magelink_domain_model_productfilter.tags", $this->extensionName);
				$label .= ": " . $tagsStr;

				if ($categoriesStr)
				{
					$label .= " / ";
					$label .= \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("tx_magelink_domain_model_productfilter.categories", $this->extensionName);
					$label .= ": " . $categoriesStr;
				}
				
			}
			
		}
	
		$params["title"] = $categoriesStr;
	
	}

	/**
	 * Gets an relation object
	 * 
	 * @param \string $relationType Relation Type
	 * @param \int $uid Uid of object
	 * @return mixed
	 */
	public function getRelationObject($relationType, $uid)
	{
		switch($relationType)
		{
			case \MageDeveloper\Magelink\Domain\Model\Attribute::RELATION_TYPE_CUSTOMER:
				return $this->frontendUserRepository->findByUid($uid, false);
			case \MageDeveloper\Magelink\Domain\Model\Attribute::RELATION_TYPE_CATEGORY:
				return $this->categoryRepository->findByUid($uid);
			case \MageDeveloper\Magelink\Domain\Model\Attribute::RELATION_TYPE_PRODUCT:
				return $this->productRepository->findByUid($uid);
			default:
				return;
		}
	}
	
	
	
}
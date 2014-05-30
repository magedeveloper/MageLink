<?php
namespace MageDeveloper\Magelink\Domain\Model;

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
class Attribute extends \MageDeveloper\Magelink\Domain\Model\AbstractObject
{
	/**
	 * Relation Types
	 * @var \string
	 */
	const RELATION_TYPE_PRODUCT     = "product";
	const RELATION_TYPE_CATEGORY    = "category";
	const RELATION_TYPE_CUSTOMER    = "customer";
	
	/**
	 * Attribute Code
	 *
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $code;

	/**
	 * Attribute Value
	 *
	 * @var \string
	 */
	protected $value;

	/**
	 * Relation
	 *
	 * @var int
	 */
	protected $relation;

	/**
	 * Relation Type
	 *
	 * @var \string
	 */
	protected $relationType;
	
	/**
	 * Returns the code
	 *
	 * @return \string $code
	 */
	public function getCode() 
	{
		return $this->code;
	}

	/**
	 * Sets the code
	 *
	 * @param \string $code
	 * @return void
	 */
	public function setCode($code) 
	{
		$this->code = $code;
	}

	/**
	 * Returns the value
	 *
	 * @return \string $value
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Sets the value
	 *
	 * @param \string $value
	 * @return void
	 */
	public function setValue($value) 
	{
		$this->value = $value;
	}

	/**
	 * Returns the product
	 *
	 * @return mixed
	 */
	public function getRelation() 
	{
		return $this->relation;
	}

	/**
	 * Sets a relation product
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Product $product
	 * @return void
	 */
	public function setRelationProduct(\MageDeveloper\Magelink\Domain\Model\Product $product) 
	{
		$this->setRelationType( self::RELATION_TYPE_PRODUCT );
		$this->relation = $product;
	}

	/**
	 * Sets a relation frontend user
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\FrontendUser $frontendUser
	 * @return void
	 */
	public function setRelationCustomer(\MageDeveloper\Magelink\Domain\Model\FrontendUser $frontendUser)
	{
		$this->setRelationType( self::RELATION_TYPE_CUSTOMER );
		$this->relation = $frontendUser;
	}

	/**
	 * Sets a relation product
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Product $product
	 * @return void
	 */
	public function setRelationCategory(\MageDeveloper\Magelink\Domain\Model\Category $category)
	{
		$this->setRelationType( self::RELATION_TYPE_CATEGORY );
		$this->relation = $category;
	}


	/**
	 * Returns the relation type
	 *
	 * @return \string $relationType
	 */
	public function getRelationType()
	{
		return $this->relationType;
	}

	/**
	 * Sets the value
	 *
	 * @param \string $relationType
	 * @return void
	 */
	public function setRelationType($relationType)
	{
		$this->relationType = $relationType;
	}
	
}
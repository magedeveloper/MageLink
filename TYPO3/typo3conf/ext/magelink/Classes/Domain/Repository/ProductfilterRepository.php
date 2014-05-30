<?php
namespace MageDeveloper\Magelink\Domain\Repository;

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
class ProductfilterRepository extends \MageDeveloper\Magelink\Domain\Repository\AbstractRepository
{
	/**
	 * Find a specific product filter by given data
	 * 
	 * @param \array $tags Tags
	 * @param \array $categories Categories
	 * @param \array $skus Skus
	 * @param \string $storeViewCode Store View Code
	 * @return \MageDeveloper\Magelink\Domain\Model\Productfilter
	 */
	public function findByFilters(array $tags, array $categories, array $skus, $storeViewCode = "")
	{
		$query = $this->createQuery();

		$query->getQuerySettings()->setRespectStoragePage(false);
		$query->getQuerySettings()->setIgnoreEnableFields(false);
		$query->getQuerySettings()->setRespectSysLanguage(false);

		return $query->matching(
			$query->logicalAnd(
				$query->equals("tags", implode(',', $tags)),
				$query->equals("categories", implode(',', $categories)),
				$query->equals("skus", implode(',', $skus)),
				$query->equals("store", $storeViewCode)
			))->execute()->getFirst();
	}

	/**
	 * Find a category from the repository with a
	 * specified entity id
	 *
	 * @param \int $uid Uid of the item
	 * @return \MageDeveloper\Magelink\Domain\Model\Productfilter
	 */
	public function findByUid($uid)
	{
		$query = $this->createQuery();

		$query->getQuerySettings()->setRespectStoragePage(false);
		$query->getQuerySettings()->setIgnoreEnableFields(true);
		$query->getQuerySettings()->setRespectSysLanguage(false);

		return $query->matching(
				$query->equals("uid", $uid)
			)->execute()->getFirst();
	}



}
?>
<?php
namespace MageDeveloper\Magelink\Domain\Repository;

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
class CategoryRepository extends \MageDeveloper\Magelink\Domain\Repository\AbstractRepository 
{
	/**
	 * Find a category from the repository with a
	 * specified uid
	 * 
	 * @param \int $uid Uid
	 * @param \bool $onlyEnabled  Only Enabled category
	 * @return \MageDeveloper\Magelink\Domain\Model\Category
	 */
	public function findByUid($uid, $onlyEnabled = true)
	{
		$query = $this->createQuery();
		
		$query->getQuerySettings()->setRespectStoragePage(false);
		$query->getQuerySettings()->setIgnoreEnableFields(!$onlyEnabled);
		$query->getQuerySettings()->setRespectSysLanguage(false);
		
		return $query->matching(
					$query->equals("uid", $uid)
			   )->execute()->getFirst();	
			   
	}
	
	/**
	 * Find a category from the repository with a
	 * specified entity id
	 * 
	 * @param \int $entityId Category Entity Id
	 * @param \string $storeViewCode Store View Code
	 * @return \MageDeveloper\Magelink\Domain\Model\Category
	 */
	public function findByEntityId($entityId, $storeViewCode = "")
	{
		$query = $this->createQuery();
		
		$query->getQuerySettings()->setRespectStoragePage(false);
		$query->getQuerySettings()->setIgnoreEnableFields(true);
		$query->getQuerySettings()->setRespectSysLanguage(false);
		
		return $query->matching(
			$query->logicalAnd(
				$query->equals("entity_id", $entityId),
				$query->equals("store", $storeViewCode)
			))->execute()->getFirst();	
	}

	/**
	 * Filter categories by entity ids
	 *
	 * @param \array $entityIds Array with entity ids
	 * @param \string $storeViewCode Store View Code
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findByEntityIds(array $entityIds, $storeViewCode = "")
	{
		$query = $this->createQuery();

		$query->getQuerySettings()->setRespectStoragePage(false);
		$query->getQuerySettings()->setIgnoreEnableFields(false);
		$query->getQuerySettings()->setRespectSysLanguage(false);

		$query->setOrderings(array("sorting" => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));

		return $query->matching(
			$query->logicalAnd(
				$query->in("entity_id", $entityIds),
				$query->equals("store", $storeViewCode)
			)
		)->execute();
	}



}
?>
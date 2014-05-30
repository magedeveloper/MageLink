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
class FrontendUserGroupRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository
{
	/**
	 * Find all groups that exist
	 * 
	 * @param bool $onlyEnabled Filter enabled
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findAll($onlyEnabled = false)
	{
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(!$onlyEnabled);
		$query->getQuerySettings()->setRespectSysLanguage(false);
		$query->getQuerySettings()->setRespectStoragePage(false);

		$objects = $query->execute();
			
		return $objects;
	}

	/**
	 * Find a specific frontend user group by
	 * a given uid
	 * 
	 * @param \int $uid
	 * @return \MageDeveloper\Magelink\Domain\Model\FrontendUserGroup
	 */
	public function findByUid($uid)
	{
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectSysLanguage(false);
		$query->getQuerySettings()->setIgnoreEnableFields(true);
		$query->getQuerySettings()->setRespectStoragePage(false);

		$object = $query
			->matching(
				$query->equals('uid', $uid)
			)
			->execute()
			->getFirst();

		return $object;
	}

	/**
	 * Find a specific frontend user group by
	 * a given uid
	 *
	 * @param \string $title
	 * @return \MageDeveloper\Magelink\Domain\Model\FrontendUserGroup
	 */
	public function findByTitle($title)
	{
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectSysLanguage(false);
		$query->getQuerySettings()->setIgnoreEnableFields(true);
		$query->getQuerySettings()->setRespectStoragePage(false);

		$object = $query
			->matching(
				$query->equals('title', $title)
			)
			->execute()
			->getFirst();

		return $object;
	}
	
	
}
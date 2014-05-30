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

class FrontendUserRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
{
	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param \int $uid The identifier of the object to find
	 * @param bool $onlyEnabled
	 * @internal param bool $onlyEnabledOnly enabled users
	 * @return object The matching object if found, otherwise NULL
	 * @api
	 */
	public function findByUid($uid, $onlyEnabled = true) 
	{
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(!$onlyEnabled);
		$query->getQuerySettings()->setRespectSysLanguage(false);
		$query->getQuerySettings()->setRespectStoragePage(false);
		
		$object = $query
			->matching(
				$query->equals("uid", $uid)
			)
			->execute()
			->getFirst();
		return $object;
	}

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param int $email The email of the user
	 * @param bool $onlyEnabled
	 * @return object The matching object if found, otherwise NULL
	 * @api
	 */
	public function findByEmail($email, $onlyEnabled = true)
	{
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(!$onlyEnabled);
		$query->getQuerySettings()->setRespectSysLanguage(false);
		$query->getQuerySettings()->setRespectStoragePage(false);
		
		return $query->matching(
			$query->logicalOr(
				$query->equals("email", $email),
				$query->equals("username", $email)
			))->execute()->getFirst();
	}
	
	
	
}
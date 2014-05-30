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
class GlobalFrontendUserRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
	/**
	 * Returns the class name of this class.
	 *
	 * @return string Class name of the repository.
	 */
	protected function getRepositoryClassName() 
	{
		// we want to be able to build out this repository without changing the extbase core feuser repository.
		// Because we tell the persistence layer that the classname is \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository,
		// it understands this repository as handling all objects handled by that repository. A bit of a hack, but
		// it seems to work.
		return '\\TYPO3\\CMS\\Extbase\\Domain\\Repository\\FrontendUserRepository';
	}

	/**
	 * Find an specific object by a given
	 * uid
	 * 
	 * @param \int $uid
	 * @return object
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
	 * Finds an specific object by a given
	 * email address
	 * 
	 * @param \string $email
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
	 */
	public function findByEmail($email) 
	{
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(true);
		$result = $query->matching($query->logicalAnd($query->equals('email', $email), $query->equals('deleted', 0)))
			->execute();
		return $result;
	}

	/**
	 * Finds an specific object by a given
	 * username
	 *
	 * @param \string $username
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
	 */
	public function findByUsername($username) 
	{
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(true);
		$result = $query->matching($query->logicalAnd($query->equals('username', $username), $query->equals('deleted', 0)))
			->execute();
		return $result;
	}
}
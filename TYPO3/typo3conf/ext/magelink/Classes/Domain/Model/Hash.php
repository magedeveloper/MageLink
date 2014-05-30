<?php
namespace MageDeveloper\Magelink\Domain\Model;


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
class Hash extends \MageDeveloper\Magelink\Domain\Model\AbstractObject
{
	/**
	 * Hash
	 *
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $hash;

	/**
	 * Email
	 *
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $email;

	/**
	 * Timestamp
	 *
	 * @var \DateTime
	 */
	protected $tstamp;

	/**
	 * Returns the hash
	 *
	 * @return \string $hash
	 */
	public function getHash()
	{
		return $this->hash;
	}

	/**
	 * Sets the hash
	 *
	 * @param \string $hash
	 * @return void
	 */
	public function setHash($hash)
	{
		$this->hash = $hash;
	}

	/**
	 * Returns the email
	 *
	 * @return \string $email
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Sets the email
	 *
	 * @param \string $email
	 * @return void
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * Get Tstamp
	 *
	 * @return DateTime
	 */
	public function getTstamp()
	{
		return $this->tstamp;
	}

	/**
	 * Set tstamp
	 *
	 * @param DateTime $tstamp tstamp
	 * @return void
	 */
	public function setTstamp($tstamp)
	{
		$this->tstamp = $tstamp;
	}
	
}
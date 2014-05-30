<?php
namespace MageDeveloper\Magelink\Utility;

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

class Crypt 
{
	/**
	 * Encryption/Decryption Default Key
	 * @var string
	 */
	const DEFAULT_KEY = "m((=k.42/jnK)?##21.,--HH";

	/**
	 * Encrypt data
	 * 
	 * @param \array $data Data to encrypt
	 * @param \string $key Encryption Key
	 * @return \string
	 */
	public static function encrypt(array $data, $key = self::DEFAULT_KEY)
	{
		$string = serialize($data);
		$string = trim($string);
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_ECB, $iv);
		$encode = base64_encode($passcrypt);
		return $encode;
	}

	/**
	 * Decrypt data
	 * 
	 * @param \string $data Data to decrypt
	 * @param \string $key Decryption Key
	 * @return \array
	 */
	public static function decrypt($data, $key = self::DEFAULT_KEY)
	{
		$string = trim($data);
		$decoded = base64_decode($data);
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_ECB, $iv);
		return unserialize($decrypted);
	}
		
		
		
		
		
		
		
}
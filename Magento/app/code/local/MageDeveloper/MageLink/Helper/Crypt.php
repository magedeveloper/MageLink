<?php
/**
 * MageDeveloper MageLink Module
 * ---------------------------------
 *
 * @category    Mage
 * @package     MageDeveloper_MageLink
 * @copyright   Magento Developers / magedeveloper.de <kontakt@magedeveloper.de>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MageDeveloper_MageLink_Helper_Crypt extends Mage_Core_Helper_Abstract
{
	/**
	 * Encryption/Decryption Default Key
	 * @var string
	 */	
	const DEFAULT_KEY = "m((=k.42/jnK)?##21.,--HH";
	
	/**
	 * Gets the decryption key from store config
	 * 
	 * @return string
	 */
	protected function _getKey()
	{
		$decryptionKey = Mage::helper('magelink')->getKey();
		
		if (!$decryptionKey || $decryptionKey == null || empty($decryptionKey)) 
		{
			return self::DEFAULT_KEY;
		}
		
		return $decryptionKey;
	}
	
	/**
	 * Gets an encrypted string from
	 * given data
	 * 
	 * @param array $data Data Array
	 * @return string
	 */
	public function getEncrypted(array $data)
	{
		$key = $this->_getKey();
		
		return $this->_encrypt($data, $key);
	}
	
	/**
	 * Gets decrypted data from an
	 * encrypted string 
	 * 
	 * @param string $string Encrypted Data
	 * @return mixed
	 */
	public function getDecrypted($string)
	{
		$key = $this->_getKey();
		
		return $this->_decrypt($string, $key);
	}
	
	/**
	 * Encrypt data
	 * 
	 * @param array $data Data to encrypt
	 * @param string $key Encryption Key
	 * @return string
	 */
	private function _encrypt(array $data, $key = self::DEFAULT_KEY)
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
	 * @param string $data Data to decrypt
	 * @param string $key Decryption Key
	 * @return array
	 */
	private function _decrypt($data, $key = self::DEFAULT_KEY)
	{
		$string = trim($data);
		$decoded = base64_decode($data);
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_ECB, $iv);
		return unserialize($decrypted);
	}
	
}
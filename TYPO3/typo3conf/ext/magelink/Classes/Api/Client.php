<?php
namespace MageDeveloper\Magelink\Api;

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
 * SOAP CLIENT
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Client extends \MageDeveloper\Magelink\Domain\Model\AbstractObject 
{
	/**
	 * Webservice Url
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $url;
	
	/**
	 * Webservice Username
	 * @var \string
	 */
	protected $username;
	
	/**
	 * Webservice Key
	 * @var \string
	 */
	protected $key;
	
	/**
	 * Webservice Resource
	 * @var SoapClient
	 */
	protected $resource;

	/**
	 * Webservice Session Id
	 * @var string
	 */
	protected $session_id;
	
	/**
	 * Connection Timeout
	 * @var int
	 */
	protected $timeout = 999;
	
	/**
	 * Errors
	 * @var array
	 */
	protected $errors;
	 
	 
	/**
	 * Connects to the webservice
	 *
	 * @throws \Exception
	 * @return bool
	 */
	public function connect()
	{
		//die("NO CONNECTION! HARHAR " . __METHOD__);
		ini_set("soap.wsdl_cache_enabled", 0);
	
		if (!class_exists('soapclient')) 
		{
			throw new \Exception("Class 'soapclient' does not exist!");
		}		
		
		$params = array(
			'trace' => true,
			'connection_timeout' => $this->getTimeout()
		);
		try 
		{
			$client = 	new \soapclient( $this->getUrl(), $params );
			$this->setResource( $client );
			$sessionId = $this->getResource()->login( $this->getUsername(), $this->getKey() );
			
			if ($sessionId) 
			{
				$this->setSessionId( $sessionId );
				return true;
			}
			else
			{
				throw new \Exception( "Could not login to webservice!" );
			}
			
		} 
		catch (\Exception $e) 
		{
			throw new \Exception( $e->getMessage() );
		}

		return false;
	}

	/**
	 * @param array $errors
	 */
	public function setErrors($errors)
	{
		$this->errors = $errors;
	}

	/**
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param \MageDeveloper\Magelink\Api\SoapClient $resource
	 */
	public function setResource($resource)
	{
		$this->resource = $resource;
	}

	/**
	 * @return \MageDeveloper\Magelink\Api\SoapClient
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @param string $session_id
	 */
	public function setSessionId($session_id)
	{
		$this->session_id = $session_id;
	}

	/**
	 * @return string
	 */
	public function getSessionId()
	{
		return $this->session_id;
	}

	/**
	 * @param int $timeout
	 */
	public function setTimeout($timeout)
	{
		$this->timeout = $timeout;
	}

	/**
	 * @return int
	 */
	public function getTimeout()
	{
		return $this->timeout;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}





}
?>
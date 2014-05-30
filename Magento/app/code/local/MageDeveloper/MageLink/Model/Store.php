<?php

class MageDeveloper_MageLink_Model_Store extends Mage_Core_Model_Store
{
	/**
	 * TYPO3 Base Url
	 * @var string
	 */
	protected $typo3_base_url;
	
	/**
	 * Sets the TYPO3 Base Url
	 * 
	 * @param string $url TYPO3 Base Url
	 * @return void
	 */
	public function setTYPO3BaseUrl($url)
	{
		$this->typo3_base_url = $url;
	}
	
	/**
	 * Gets the TYPO3 Base Url
	 * 
	 * @return string
	 */
	public function getTYPO3BaseUrl()
	{
		return $this->typo3_base_url;
	}

	/**
	 * Get the stores base url
	 * and includes the new typo3 base url
	 *
	 * @param string $type Url Type
	 * @param bool $secure
	 * @throws Mage_Core_Exception
	 * @return string
	 */
	public function getBaseUrl($type = self::URL_TYPE_LINK, $secure = null) 
	{
		$classReflection = new ReflectionClass($this);
	
		$cacheKey = $type . '/' . (is_null ( $secure ) ? 'null' : ($secure ? 'true' : 'false'));
		if (! isset ( $this->_baseUrlCache [$cacheKey] )) 
		{
			
			switch ($type) 
			{
				case self::URL_TYPE_WEB:
					
					$secure = is_null ( $secure ) ? $this->isCurrentlySecure () : ( bool ) $secure;
					$url = $this->getConfig ( 'web/' . ($secure ? 'secure' : 'unsecure') . '/base_url' );
					break;
				
				case self::URL_TYPE_LINK:
					
					if ($this->getTYPO3BaseUrl()) 
					{
						$url = $this->getTYPO3BaseUrl();
					} 
					else 
					{
						$secure = (bool) $secure;
						$url = $this->getConfig ( 'web/' . ($secure ? 'secure' : 'unsecure') . '/base_link_url' );
						$url = $this->_updatePathUseRewrites ( $url );
						$url = $this->_updatePathUseStoreView ( $url );
					}
					
					break;

				case $classReflection->getConstant('URL_TYPE_DIRECT_LINK') !== false && self::URL_TYPE_DIRECT_LINK == $type:
					
					if ($this->getTYPO3BaseUrl()) 
					{
						$url = $this->getTYPO3BaseUrl();
						
					} 
					else 
					{
					        $secure = (bool)$secure;
					        $url = $this->getConfig('web/'.($secure ? 'secure' : 'unsecure').'/base_link_url');
					        $url = $this->_updatePathUseRewrites($url);
					}
				    
				    break;
				
				case self::URL_TYPE_SKIN:
				case self::URL_TYPE_MEDIA:
				case self::URL_TYPE_JS:
					
					$secure = is_null ( $secure ) ? $this->isCurrentlySecure () : ( bool ) $secure;
					$url = $this->getConfig ( 'web/' . ($secure ? 'secure' : 'unsecure') . '/base_' . $type . '_url' );
					
					break;
				
				default:
					throw Mage::exception ('Mage_Core', Mage::helper('core')->__('Invalid base url type'));
			}
			
			$this->_baseUrlCache[$cacheKey] = rtrim($url, '/').'/';
			
		}
		
		return $this->_baseUrlCache[$cacheKey];
	}	
	
}

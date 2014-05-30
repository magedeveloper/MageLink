<?php
class MageDeveloper_MageLink_Model_Url extends Mage_Core_Model_Url
{
	/**
	 * Build url by requested path and parameters
	 *
	 * @param   string $routePath
	 * @param   array $routeParams
	 * @return  string
	 */
	public function getUrl($routePath=null, $routeParams=null, $getOriginalData=false)
	{
		if (!Mage::getSingleton("magelink/core")->isInUse())
		{
			return parent::getUrl($routePath, $routeParams, $getOriginalData);
		}
		
		// Get the TYPO3 Controller Instance
		$typo3Controller = Mage::getSingleton("magelink/core")->getTYPO3Controller();
		
		$escapeQuery = false;

		if (isset($routeParams['_fragment'])) 
		{
			$this->setFragment($routeParams['_fragment']);
			unset($routeParams['_fragment']);
		}

		if (isset($routeParams['_escape'])) 
		{
			$escapeQuery = $routeParams['_escape'];
			unset($routeParams['_escape']);
		}

		$url = $this->getRouteUrl($routePath, $routeParams);

		if (isset($routeParams['_direct']))
		{
			// If direct url contains parameters
			$parseUrl = parse_url($routeParams["_direct"]);
			$requestPath = $parseUrl["path"];
			
			// Rewrite Information
			$rewrite = Mage::getModel("core/url_rewrite");
			$rewrite->setStoreId( Mage::app()->getStore() );
			$rewrite->loadByRequestPath( $requestPath );
			
			// Fetch our needed information from the target path
			$result = preg_match("/^(?P<route>[A-Za-z-]+)\/(?P<controller>[A-Za-z-]+)\/(?P<action>[A-Za-z-]+)\/(?P<params>.*)/", $rewrite->getTargetPath(), $finds);
			
			if (preg_match_all("/([^,\/ ]+)\/([^,\/ ]+)/", $finds["params"], $findsParams))
			{
				$resultParams = array_combine($findsParams[1], $findsParams[2]);
				
				$params = array(
					"magento_route"			=> $finds["route"],
					"magento_controller"	=> $finds["controller"],
					"magento_action"		=> $finds["action"],
				);
				
				unset($routeParams["_direct"]);

				// Eventual query string
				if (array_key_exists("query", $parseUrl) && $parseUrl["query"] != '')
				{
					$query = array();
					parse_str($parseUrl["query"], $query);
					$params = array_merge($params, $query);
				}				
				
				
				$params = array_merge($params, $routeParams, $resultParams);
				
				$uriBuilder = $typo3Controller->getControllerContext()->getUriBuilder();
				
				$url = $uriBuilder->uriFor(	"index", 
											$params, 
											"Magento", 
											$typo3Controller->getExtensionName(), 
											$typo3Controller->getPluginName()
				);
				
				return $url;
			}
			
		} // ENDIF DIRECT

		$params = array(
			"magento_route" 		=> $this->getRouteName(),
			"magento_controller" 	=> $this->getControllerName(),
			"magento_action" 		=> $this->getActionName(),
		);
		
		if($routeParams) 
			$params = array_merge($params, $routeParams);
		
		if(is_array($routeParams['_query'])) 
			$params = array_merge($params, $routeParams['_query']);
		
		unset($params['_query']);
		unset($params['_use_rewrite']);
		
		// Params that are used in TYPO3
		$t3Params = Mage::getSingleton("magelink/core")->getParams();
		$params = array_merge($t3Params, $params);
			
		if($params['_current'])
		{
			unset($params['_current']);
		}
		
		// URL Builder from TYPO3
		$uriBuilder = $typo3Controller->getControllerContext()->getUriBuilder();
			
		// Generate the URL	
		$url = $uriBuilder->uriFor(	"index", 
									$params, 
									"Magento", 
									$typo3Controller->getExtensionName(), 
									$typo3Controller->getPluginName()
		);		
		
	    // Complete the typolink URL absolute using the base url
		if (strpos($url, 'http') !== 0) 
		{
			$urlComponents = parse_url($this->getBaseUrl());
			$url = $urlComponents['scheme'] . '://' . rtrim($urlComponents['host'], '/') . '/' . ltrim($url, '/');
		}

		// save last URL in Response for the _isUrlInternal workaround
		Mage::getSingleton("magelink/core")->getResponse()->setLastUrl($url);
		
		return $url;	
	}

	/**
	 * return the Route URL
	 *
	 * @param string $routePath
	 * @param array $routeParams
	 * @param bool $getOriginalData
	 * @return string
	 */
	public function getRouteUrl($routePath=null, $routeParams=null, $getOriginalData=false)
	{
		if (!Mage::getSingleton("magelink/core")->isInUse())
		{
			return parent::getRouteUrl($routePath, $routeParams, $getOriginalData);
		}

		$this->unsetData('route_params');

		if (!is_null($routePath)) {
			$this->setRoutePath($routePath);
		}
		if (is_array($routeParams)) {
			$this->setRouteParams($routeParams, false);
		}

		$url = $this->getRoutePath($routeParams);
		return $url;
	}
}

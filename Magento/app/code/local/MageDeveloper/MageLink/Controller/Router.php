<?php

class MageDeveloper_MageLink_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{

	public function match(Zend_Controller_Request_Http $request)
	{
		$request->setBaseUrl(Mage::getSingleton("magelink/core")->getTYPO3BaseUrl());

		// if module is not active -> use standard Router
		if(!Mage::getSingleton("magelink/core")->isInUse())
		{
			return parent::match($request);
		}

		// get Params from TYPO3
		$params = Mage::getSingleton("magelink/core")->getParams();

		// Default action
		$action = "index";

		if ($params['magento_route'])
		{
			// Route (Module)
			$config = $params['magento_route'];
			$request->setRouteName($params['magento_route']);

			if ($params['magento_controller'])
			{
				// Controller
				$config .= '/'.$params['magento_controller'];
				$request->setControllerName($params['magento_controller']);

				if ($params['magento_action'])
				{
					if ($params["magento_action"] != "")
					{
						$action = $params['magento_action'];
					}

					// Action	
					$config .= '/'.$params['magento_action'];
					$request->setActionName($action);
				}
			}

			unset($params['magento_route']);
			unset($params['magento_controller']);
			unset($params['magento_action']);

			$frontController = Mage::app()->getFrontController();

			$urlModel = Mage::getModel("core/url")->setStore(Mage::app()->getStore());
			$oldUrl = $urlModel->getUrl($config, $params, true);

			// Remove the query string from REQUEST_URI
			if ($pos = strpos($oldUrl, '?')) {
				$oldUrl = substr($oldUrl, 0, $pos);
			}

			//$request->setRouteName("cms");
			//$request->setControllerName("index");
			//$request->setActionName("index");

			//$request->setPathInfo($oldUrl);
			//$request->setPathInfo(Mage::getSingleton("magelink/core")->getTYPO3BaseUrl());
			//$frontController->rewrite();
		}

		unset($params['action']);
		unset($params['controller']);

		// Setting the remaining params to the request
		foreach ($params as $_param=>$_value)
		{
			$request->setParam($_param, $_value);
		}

		// Admin Check
		if (Mage::app()->getStore()->isAdmin())
		{
			return false;
		}

		//checkings before even try to findout that current module
		//should use this router
		if (!$this->_beforeModuleMatch())
		{
			return false;
		}


		$this->fetchDefault();
		$front = $this->getFront();

		// Path
		$path = trim($request->getPathInfo(), '/');

		if ($path) {
			$p = explode('/', $path);
		} else {
			$p = explode('/', $this->_getDefaultPath());
		}

		// get module name
		if ($request->getModuleName())
		{
			$module = $request->getModuleName();
		}
		else
		{
			if(!empty($p[0]))
			{
				$module = $p[0];
			}
			else
			{
				$module = $this->getFront()->getDefault('core');
				$request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,	'');
			}

		}


		if (!$module)
		{
			if (Mage::app()->getStore()->isAdmin())
			{
				$module = 'admin';
			}
			else
			{
				return false;
			}
		}

		/**
		 * Searching router args by module name from route using it as key
		 */
		$modules = $this->getModuleByFrontName($module);

		/**
		 * If we did not found anything  we searching exact this module
		 * name in array values
		 */
		if ($modules === false)
		{
			if ($moduleFrontName = $this->getModuleByName($module, $this->_modules))
			{
				$modules = array($module);
				$module = $moduleFrontName;
			}
			else
			{
				return false;
			}

		}


		//checkings after we foundout that this router should be used for current module
		if (!$this->_afterModuleMatch()) {
			return false;
		}

		/**
		 * Going through modules to find appropriate controller
		 */

		$found = false;
		foreach ($modules as $realModule)
		{
			$request->setRouteName($this->getRouteByFrontName($module));

			// get controller name
			if ($request->getControllerName())
			{
				$controller = $request->getControllerName();
			}
			else
			{
				if (!empty($p[1]))
				{
					$controller = $p[1];
				}
				else
				{
					$controller = $front->getDefault('controller');
					$request->setAlias(
						Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
						ltrim($request->getOriginalPathInfo(), '/')
					);
				}
			}

			// get action name
			if (empty($action))
			{
				if ($request->getActionName())
				{
					$action = $request->getActionName();
				}
				else
				{
					$action = !empty($p[2]) ? $p[2] : $front->getDefault('action');
				}


			}

			//checking if this place should be secure
			$this->_checkShouldBeSecure($request, '/'.$config);
			$controllerClassName = $this->_validateControllerClassName($realModule, $controller);

			if (!$controllerClassName)
			{
				continue;
			}

			// instantiate own controller class
			$controllerInstance = new $controllerClassName($request, Mage::getSingleton("magelink/core")->getResponse());

			if (!$controllerInstance->hasAction($action))
			{
				continue;
			}

			$found = true;
			break;
		}

		/*
		echo "FOUND: ".$found;
		echo "<br />";
		echo "CONFIG: ".$config;
		echo "<br />";
		echo "M: ".$module;
		echo "<br />";
		echo "C: ".$controller;
		echo "<br />";
		echo "A: ".$action;
		echo "<br />";
		echo "R: ".$realModule;
		echo "<br />";
		echo "<pre> PARAMS ";
		print_r($params);
		//print_r($request);
		echo "</pre>";
		die();
		*/

		// set values only after all the checks are done
		$request->setModuleName($module);
		$request->setControllerName($controller);
		$request->setActionName($action);
		$request->setControllerModule($realModule);

		// dispatch action
		$request->setDispatched(true);
		$controllerInstance->dispatch($action);

		return true;
	}

}

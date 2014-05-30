<?php
class MageDeveloper_MageLink_Model_Core
{
	/**
	 * Router
	 * @var MageDeveloper_MageLink_Controller_Router
	 */
	protected $router = null;
	
	/**
	 * Router
	 * @var MageDeveloper_MageLink_Controller_Response
	 */
	protected $response;
	
	/**
	 * Parameters
	 * @var array
	 */
	protected $params = array();
	
	/**
	 * HTML Output
	 * @var string
	 */
	protected $output = '';
	
	/**
	 * Blocks
	 * @var array
	 */
	protected $blocks = array();
	
	/**
	 * Is in Use
	 * @var bool
	 */
	protected $is_in_use;
	
	/**
	 * TYPO3 Base Url
	 * @var string
	 */
	protected $base_url;
	
	/**
	 * Controller
	 * @var \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController
	 */
	protected $controller;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->router = new MageDeveloper_MageLink_Controller_Router();
		$this->router->collectRoutes("frontend", "standard");
		
		Mage::app()->getFrontController()->addRouter("standard", $this->router);

		// Set the TYPO3 Base Url for usage as new base url		
		if ($this->getTYPO3BaseUrl())
		{
			Mage::app()->getStore()->setTYPO3BaseUrl( $this->getTYPO3BaseUrl() );			
		}
		
	}
	
	/**
	 * Gets the response object
	 * 
	 * @return MageDeveloper_MageLink_Controller_Response
	 */
	public function getResponse()
	{
        if (empty($this->response)) 
        {
            $this->response = new MageDeveloper_MageLink_Controller_Response();
			$this->response->setHeader("Content-Type", "text/html; charset=UTF-8");
        }
		
        return $this->response;
	}
	
	/**
	 * start Mage dispatch process with injected params
	 *
	 * @param array $params
	 * @return boolean
	 */
	public function dispatch($params = array())
	{
		try
		{
			// set dispatch Params
			$this->setParams($params);
			
			// get Front Controller
			$front = Mage::app()->getFrontController();
			
			// Validating checkUrl
			$front->getRequest()->setPost(true);

			// run Dispatch
			$front->dispatch();

			// send Response
			$this->getResponse()->sendResponse();
			
			// Sets this model as "in use"
			$this->setIsInUse();
			
		}
		catch (Exception $e)
		{
			echo "ERROR:<br /><pre>";
			var_dump($params);
			throw $e;
			return false;
		}

		return true;
	}   
	
	/**
	 * set routing params
	 *
	 * @param array $params
	 * @return object $this
	 */
	public function setParams(array $params = array()) 
	{
		$this->params = $params;
		return $this;
	}
	
	/**
	 * get routing param
	 *
	 * @return array
	 */
	public function getParams() 
	{
		return $this->params;
	}
	
	/**
	 * Get parameter
	 */
	public function getParam($param)
	{
		$params = $this->getParams();
		
		if (array_key_exists($param, $params))
		{
			return $params[$param];
		}
		
		return null;
	}
	
	/**
	 * add something to output
	 *
	 * @param string $output
	 * @return object $this
	 */
	public function addOutput($output) 
	{
		$this->output .= $output;
		return $this;
	}
	
	/**
	 * returns the output
	 *
	 * @return string
	 */
	public function getOutput() 
	{
		return $this->output;
	}
	
	/**
	 * Sets the TYPO3 Controller
	 * 
	 * @param \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController $controller
	 * @return  void
	 */
	public function setTYPO3Controller(\TYPO3\CMS\Extbase\Mvc\Controller\AbstractController $controller)
	{
		// Sets this model as "in use"
		$this->setIsInUse();
		$this->controller = $controller;
	}
	
	/**
	 * Gets the TYPO3 Controller
	 * 
	 * @return \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController
	 */
	public function getTYPO3Controller()
	{
		return $this->controller;		
	}
	
	/**
	 * Sets the TYPO3 Base Url
	 * 
	 * @param string $url Base Url
	 * @return void
	 */
	public function setTYPO3BaseUrl($url)
	{
		Mage::app()->getStore()->setTYPO3BaseUrl( $url );
		$this->base_url = $url;
	}
	
	/**
	 * Gets the TYPO3 Base Url
	 * 
	 * @return string
	 */
	public function getTYPO3BaseUrl()
	{
		return $this->base_url;
	}
	
	
	/**
	 * Gets a TYPO3 Link
	 * 
	 * @param array $params Parameters
	 * @return string
	 */
	public function getTYPO3Link(array $params = array())
	{
		$url = $this->getUriBuilder()
					->uriFor(	"index", 
								$params, 
								"Magento", 
								$this->getTYPO3Controller()->getExtensionName(), 
								$this->getTYPO3Controller()->getPluginName()
		);
		
		return $url;	
	}
	
	/**
	 * Gets the TYPO3 Uri Builder
	 * 
	 * @return 
	 */
	public function getUriBuilder()
	{
		if ($this->getTYPO3Controller() instanceof \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController)
		{
			return $this->getTYPO3Controller->getControllerContext()->getUriBuilder();
		}

		return "";
	}
	
	/**
	 * Sets an block
	 *
	 * @param string $name
	 * @param Mage_Core_Block_Abstract $html
	 */
	public function setBlock($name, $block) 
	{
		$this->blocks[$name] = $block;
	}
	
	/**
	 * Gets an block
	 *
	 * @param string $name
	 * @return Mage_Core_Block_Abstract
	 */
	public function getBlock($name) 
	{
		return $this->blocks[$name];
	}
	
	/**
	 * return all Layout Blocks as Array
	 *
	 * @return array <Mage_Core_Block_Abstract>
	 */
	public function getBlocks() 
	{
		return $this->blocks;
	}
	
	/**
	 * Sets this model as "in use"
	 * 
	 * @param bool $setting
	 * @return void
	 */
	public function setIsInUse($setting = true)
	{
		$this->is_in_use = (bool)$setting;
	}
	
	/**
	 * Determines if this model is in use
	 * 
	 * @return bool
	 */
	public function isInUse()
	{
		return $this->is_in_use;
	}
	
}

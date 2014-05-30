<?php
class MageDeveloper_MageLink_Controller_Response extends Mage_Core_Controller_Response_Http 
{
	/**
	 * Last Url
	 * @var string
	 */
	protected $last_url;
	
	/**
	 * Sets the last url
	 * 
	 * @param string $lastUrl Last URL
	 * @return void
	 */
	public function setLastUrl($lastUrl)
	{
		$this->last_url = $lastUrl;
	}
	
	/**
	 * Gets the last url
	 * 
	 * @return string
	 */
	public function getLastUrl()
	{
		return $this->last_url;	
	}
	
	/**
	 * Append Body HTML
	 * 
	 * @param string $output Output to append
	 * @param string $name Output Alias
	 * @return void
	 */
	public function appendBody($output, $name = null)
	{
		//echo __METHOD__."<br />";
		$this->ajaxHandler($output);
		parent::appendBody($output, $name);
		Mage::getSingleton('magelink/core')->addOutput($output);
	}

	/**
	 * Handle Ajax Requests
	 * 
	 * @param string $output Output
	 */
	protected function ajaxHandler($output) 
	{
		//echo __METHOD__."<br />";
		if(!Mage::app()->getFrontController()->getRequest()->isXmlHttpRequest()) return;
		
		echo $output;
		exit();
	}
	
	/**
	 * Gets the output
	 * 
	 * @param bool $returnBody Return complete body
	 * @return mixed
	 */
	public function outputBody($returnBody = false)
	{
		//echo __METHOD__."<br />";
		$content = implode('', (array)$this->_body);
		
		if (!$returnBody)
		{
			$this->ajaxHandler($content);
		}
		else
		{
			return $content;
		}
	}
	
	/**
	 * Sends the response
	 * 
	 * @return mixed
	 */
	public function sendResponse()
	{
		//echo __METHOD__."<br />";
		parent::sendResponse();
		
		if ($this->isRedirect()) 
		{
			//echo __METHOD__."<br />";
			exit();
		}
	}
	
	/**
	 * Sets the body
	 * 
	 * @param string $content Content to set to body
	 * @param string $name Alias Name
	 * @return self
	 */
	public function setBody($content, $name = null)
	{
		// handle Checkout redirects
		if(	   strstr($content, 'paypal_standard_checkout') 
			|| strstr($content, 'clickandbuy_checkout')
			|| strstr($content, 'payone_checkout')
			|| strstr($content, 'moneybookers_checkout')
		){
			echo $content;
			exit();
		}
		
		parent::setBody($content, $name);
		
		return $this;
	}
	
	/**
	 * Gets the complete body
	 * 
	 * @return string
	 */
	public function getBody()
	{
		return $this->outputBody(true);		
	}
	
	
	/**
	 * Set the redirect
	 * 
	 * @param string $url Url to Redirect
	 * @param int $code HTTP Code
	 * @return self
	 */	
	public function setRedirect($url, $code = 302)
	{
		// set last URL for the _isUrlInternal workaround
		if($url == Mage::app()->getStore()->getBaseUrl() && $this->getLastUrl())
		{
			$url = $this->getLastUrl();
		}
		
		$this->canSendHeaders(true);
		$this->sendHeaders();
		$this->_isRedirect = true;
		
		
		header ("Location: ".$url);
		
		//header ( 'Location: ' . t3lib_div::locationHeaderUrl ( $url ) );
		exit();
		
		return $this;			
	}
	





	

	
}
	
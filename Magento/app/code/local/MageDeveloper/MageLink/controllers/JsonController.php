<?php
class MageDeveloper_MageLink_JsonController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Cart Actions
	 * @var string
	 */
	const PARAM_CART_ADD 		= "add";
	const PARAM_CART_REMOVE		= "remove";
	const PARAM_CART_CLEAR		= "clear";
	const PARAM_CART_GET		= "get";
	
	/**
	 * Callbacks
	 * @var string
	 */
	const CALLBACK_ADD_TO_CART		= "tx_magelink_ajax_addtocart_callback";
	const CALLBACK_UPDATE_CART		= "tx_magelink_ajax_getcart_callback";
	const CALLBACK_REMOVE_FROM_CART	= "tx_magelink_ajax_removefromcart_callback";
	const CALLBACK_FORGOT_PASSWORD	= "tx_magelink_ajax_forgot_password_callback";
	const CALLBACK_COMPLETE_LOGIN	= "tx_magelink_ajax_complete_login_callback";
	const CALLBACK_DISPLAY_BLOCK	= "tx_magelink_ajax_display_block";
	
	/*
	const CALLBACK_FLASH_MESSAGE 	= "tx_magelink_ajax_add_flash_message";
	const CALLBACK_UPDATE_CART		= "tx_magelink_ajax_refreshcart";
	*/
	
	/**
	 * Message Types
	 * @var string
	 */
	const MESSAGE_TYPE_INFO 	= "info";
	const MESSAGE_TYPE_SUCCESS	= "success";
	const MESSAGE_TYPE_ERROR	= "error";
	
	/**
	 * Form Ids
	 * @var string
	 */
	const FORM_ID_EMAIL			= "tx_magelink_loginform[tx-magelink-login-email]";
	const FORM_ID_PASSWORD		= "tx_magelink_loginform[tx-magelink-login-password]";
	
	
    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
	
    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
	
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
	
    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('id');
		
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
	
	public function indexAction()
	{
		die();
	}
	
	/**
	 * blockAction
	 */
	public function blockAction()
	{
		$blockId = $this->getRequest()->getParam("id");
		
		$decrypted = Mage::helper("magelink/crypt")->getDecrypted(base64_decode($blockId));
		
		if (is_array($decrypted))
		{
			$blocktype = reset($decrypted);
			$blocktype = str_replace('/', '_', $blocktype);
			
			$div = end($decrypted);

			/* list of known and allowed blocks */
			$knownBlocks = array(
				"checkout_cart_sidebar"		=> array(
					"id"			=> "checkout/cart_sidebar",
					"template"		=> "checkout/cart/sidebar.phtml",
				),
				"checkout_cart"				=> array(
					"id"			=> "checkout/cart",
					"template"		=> "checkout/cart.phtml",
				),
				"sales_reorder_sidebar"		=> array(
					"id"			=> "sales/reorder_sidebar",
					"template"		=> "sales/reorder/sidebar.phtml",
				),
				"mini_search_form"			=> array(
					"id"			=> "core/template",
					"template"		=> "catalogsearch/form.mini.phtml",
				),
				"wishlist_customer_sidebar"	=> array(
					"id"			=> "wishlist/customer_sidebar",
					"template"		=> "wishlist/sidebar.phtml",
				),
				"tag_popular"				=> array(
					"id"			=> "tag/popular",
					"template"		=> "tag/popular.phtml",
				),
			);
				
			foreach ($knownBlocks as $_id=>$_info)
			{
				if ($_id == $blocktype)
				{
					//$_info["id"] 			= "checkout/onepage";
					//$_info["template"]		= "checkout/onepage.phtml";
					
					$block = $this->getLayout()
								  ->createBlock($_info["id"])
								  ->setTemplate($_info["template"])
								  ->toHtml();
								  
					$blockData = array(
						"id"			=> $blocktype,
						"div"			=> $div,
						"html"			=> $block,
						"remote_addr"	=> $this->getRequest()->getServer("REMOTE_ADDR"),
					);			  
					
					// Encrypt Data
					$encrypted = Mage::helper("magelink/crypt")->getEncrypted($blockData);
					
					$this->tx_magelink_ajax_display_block(base64_encode($encrypted), array("id"=>$blocktype,"div"=>$div));
				}
				
			}

		}

		die();
	}
	
	/**
	 * JSON Login Action which receives
	 * encrypted login data
	 */
	public function loginSourceTYPO3Action()
	{
        if ($this->_getCustomerSession()->isLoggedIn()) {
        	// User is already logged in
        	//$this->_loginSuccess();
        }
		
		$enc = $this->getRequest()->getParam("enc");

		$decrypted = Mage::helper("magelink/crypt")->getDecrypted($enc);
		
		if ($this->_validateDecrypted($decrypted))
		{
			
			// Decrypted information is okay
			$credentials = $decrypted["credentials"];
			
			// Username
			$username = $credentials["email"];
			$password = $credentials["password"];
			$hash	  = $credentials["hash"];
			
			$session = $this->_getCustomerSession();
			
			try 
			{
				$session->login($username, $password);
				
				if ($session->getCustomer()->getIsJustConfirmed()) 
				{
					// User is just confirmed
				}
                
			} 
			catch (Mage_Core_Exception $e) 
			{
				switch ($e->getCode()) 
				{
					case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
						$value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
						$message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
						$this->tx_magelink_ajax_complete_login_callback($message, self::MESSAGE_TYPE_INFO, true);
						break;
					
					case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
						$message = $e->getMessage();
						$this->tx_magelink_ajax_complete_login_callback($message, self::MESSAGE_TYPE_ERROR, true);
						break;
					default:
						$message = $e->getMessage();
						$this->tx_magelink_ajax_complete_login_callback($message, self::MESSAGE_TYPE_ERROR, true);
				}
				
				$session->addError($message);
				$session->setUsername($username);
				
			} 
			catch (Exception $e) 
			{
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
			}
			
			// Check if customer is logged in and compare the hash
			if ($session->getCustomer()->getId())
			{
				if ($session->getCustomer()->getLoginHash() == $hash)
				{
					// Only when login hash equals, we success
					$data = array(
						"customer_id"		=> $this->_getCustomerSession()->getCustomer()->getId(),		
						self::FORM_ID_EMAIL	=> $this->_getCustomerSession()->getCustomer()->getEmail(),
						"hash"				=> $hash,
						"back_url"			=> $decrypted["back_url"],
						"remote_addr"		=> $this->getRequest()->getServer("REMOTE_ADDR"),
					);
		
					// Encrypt Data
					$encrypted = Mage::helper("magelink/crypt")->getEncrypted($data);
		
					// Create response
					$response = array(
						"type"	=> self::MESSAGE_TYPE_SUCCESS,
						"enc" 		=> $encrypted,
					);
					
					$this->tx_magelink_ajax_complete_login_success($response, self::CALLBACK_COMPLETE_LOGIN);

				}
				else
				{
					// Hash comparison failed
					$session->logout();
				}
			}
			
		}
		
		$message = Mage::helper("magelink")->__('Login failed!');
		$this->tx_magelink_ajax_complete_login_callback($message, self::MESSAGE_TYPE_ERROR);
	}
	
	/**
	 * JSON Login Action which receives
	 * encrypted login data
	 */
	public function loginSourceMagentoAction()
	{
        if ($this->_getCustomerSession()->isLoggedIn()) {
        	// User is already logged in
        	//$this->_loginSuccess();
        }
		
		$enc = $this->getRequest()->getParam("enc");

		$decrypted = Mage::helper("magelink/crypt")->getDecrypted($enc);
		
		if ($this->_validateDecrypted($decrypted))
		{
			// Decrypted information is okay
			$credentials = $decrypted["credentials"];
			
			// Username
			$username = $credentials["email"];
			$password = $credentials["password"];
			$hash	  = $credentials["hash"];
			
			$session = $this->_getCustomerSession();
			
			try 
			{
				$session->login($username, $password);
				
				if ($session->getCustomer()->getIsJustConfirmed()) 
				{
					// User is just confirmed
				}
                
			} 
			catch (Mage_Core_Exception $e) 
			{
				switch ($e->getCode()) 
				{
					case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
						$value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
						$message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
						$this->tx_magelink_ajax_complete_login_callback($message, self::MESSAGE_TYPE_INFO, true);
						break;
					
					case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
						$message = $e->getMessage();
						$this->tx_magelink_ajax_complete_login_callback($message, self::MESSAGE_TYPE_ERROR, true);
						break;
					default:
						$message = $e->getMessage();
						$this->tx_magelink_ajax_complete_login_callback($message, self::MESSAGE_TYPE_ERROR, true);
				}
				
				$session->addError($message);
				$session->setUsername($username);
				
			} 
			catch (Exception $e) 
			{
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
			}
			
			
			// Check if customer is logged in and compare the hash
			if ($session->getCustomer()->getId())
			{
				// Set the login hash 
				$session->getCustomer()->setLoginHash($hash);
				
				// Try to save the customer
				try 
				{
					$session->getCustomer()->save();
				} 
				catch (Mage_Core_Exception $e) 
				{
					$message = Mage::helper("magelink")->__('Could not save customer!');
					$this->tx_magelink_ajax_complete_login_callback($message, self::MESSAGE_TYPE_ERROR);
				}
				
				// Customer has been logged in and hash is saved to customer
				$data = array(
					"customer_id"			=> $this->_getCustomerSession()->getCustomer()->getId(),
					self::FORM_ID_EMAIL		=> $this->_getCustomerSession()->getCustomer()->getEmail(),		
					self::FORM_ID_PASSWORD	=> $this->_getCustomerSession()->getCustomer()->getPasswordHash(),
					"hash"					=> $hash,
					"back_url"				=> $decrypted["back_url"],
					"remote_addr"			=> $this->getRequest()->getServer("REMOTE_ADDR"),
				);
		
				// Encrypt Data
				$encrypted = Mage::helper("magelink/crypt")->getEncrypted($data);
		
				// Create response
				$response = array(
					"type"	=> self::MESSAGE_TYPE_SUCCESS,
					"enc" 		=> $encrypted,
				);
					
				$this->tx_magelink_ajax_complete_login_success($response, self::CALLBACK_COMPLETE_LOGIN);

			}
			
		}
		
		$message = Mage::helper("magelink")->__('Login failed!');
		$this->tx_magelink_ajax_complete_login_callback($message, self::MESSAGE_TYPE_ERROR);
	}
	
	/**
	 * Handles products in cart with json call
	 */
	public function cartAction()
	{
		$message = "";
		
		$params = $this->getRequest()->getParams();

		foreach ($params as $_param=>$_value)
		{
			switch ($_param)
			{
				case self::PARAM_CART_ADD:
					$this->_addProductToCart();
					break;		
				case self::PARAM_CART_GET:
					$this->_getCartContent();
					break;
				case self::PARAM_CART_REMOVE:
					$this->_removeProductFromCart();
					break;
			}
		}
		$message = Mage::helper("checkout")->__('No action given!');
		$this->_deliverMessage($message, self::MESSAGE_TYPE_ERROR);
	}
	
	/**
	 * forgotPasswordAjaxAction
	 * Ajax Action for the forgot password function
	 * Displays on string data and uses parameter vars
	 */
	public function forgotPasswordAction()
	{
        $email = (string) $this->getRequest()->getParam('email');
		
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->tx_magelink_ajax_forgot_password_callback( $this->__('Invalid email address.'), self::MESSAGE_TYPE_ERROR, true );
				return;
            }

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                	$this->tx_magelink_ajax_forgot_password_callback( $this->__('Error sending reset-password link.'), self::MESSAGE_TYPE_ERROR, true );
                    return;
                }
            }
			$this->tx_magelink_ajax_forgot_password_callback( Mage::helper('customer')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->htmlEscape($email) ), self::MESSAGE_TYPE_SUCCESS, false );
            return;
        } else {
        	$this->tx_magelink_ajax_forgot_password_callback( $this->__('Please enter your email.'), self::MESSAGE_TYPE_INFO, false );
            return;
        }
	}
	
	/**
	 * Validates the decrypted information
	 * 
	 * @param array $decrypted Decrypted data
	 * @return bool
	 */
	public function _validateDecrypted(array $decrypted)
	{
		// Check credentials
		if (!array_key_exists("credentials", $decrypted))
		{
			$message = Mage::helper("customer")->__("Login and password are required.");
			$this->_deliverMessage($message, self::MESSAGE_TYPE_ERROR, true);
		}
		else
		{
			$credentials = $decrypted["credentials"];
			
			$neededKeys = array(
				"email","password","hash"
			);
			$result = array_diff($needed, array_keys($credentials));
			
			if (!empty($result))
			{
				$message = Mage::helper("customer")->__("Login and password are required.");
				$this->_deliverMessage($message, self::MESSAGE_TYPE_ERROR, true);
			}
			
			$customer = Mage::getModel('customer/customer')
								->getCollection()
								->addAttributeToSelect('*')
								->addFieldToFilter('email', $credentials["email"])
								->getFirstItem();
			
			
			if (!$customer->getId())
			{
				$message = Mage::helper("magelink")->__("Customer does not exist!");
				$this->_deliverMessage($message, self::MESSAGE_TYPE_ERROR, true);
			}
			
		}
		
		
		// Check remote addr
		if (!array_key_exists("remote_addr", $decrypted))
		{
			$message = Mage::helper("magelink")->__("Host is invalid!");
			$this->_deliverMessage($message, self::MESSAGE_TYPE_ERROR, true);
		}
		else
		{
			$addrData = $decrypted["remote_addr"];
			$addrMagento = $this->getRequest()->getServer("REMOTE_ADDR");
			
			if ($addrData != $addrMagento)
			{
				$message = Mage::helper("magelink")->__("Host is invalid!");
				$this->_deliverMessage($message, self::MESSAGE_TYPE_ERROR, true);
			}
			
		}
		
		return true;		
	}
	
	
	
	
	
	/**
	 * Gets the cart content and
	 * returns its data
	 * 
	 * @return bool
	 */
	protected function _getCartContent()
	{
		$data = array();
		
		$quote 	= Mage::helper("checkout")->getQuote();
		$data 	= $quote->getData();
		
		foreach ($quote->getItemsCollection() as $_item)
		{
			
			$floatQty = floatval($_item->getQty());
			$total = $floatQty * $_item->getPrice();
			$formatedTotal = Mage::helper('core')->currency($total, true, false);
			$formatedPrice = Mage::helper('core')->currency($_item->getPrice(), true, false);
			
			$data["items"][$_item->getId()] = array(
				"id"				=> $_item->getId(),
				"product_id"		=> $_item->getProductId(),
				"name"				=> $_item->getName(),
				"qty"				=> $_item->getQty(),
				"price"				=> $formatedPrice,
				"total"				=> $formatedTotal,
				"store"				=> Mage::app()->getStore()->getCode(),
				"parent_item_id"	=> $_item->getParentItemId()
			);
			
		}
		
		$this->tx_magelink_ajax_getcart_callback($data);
		
	}
	
	/**
	 * Removes an product from the cart
	 * 
	 * @return bool
	 */
	protected function _removeProductFromCart()
	{
		$message = "";
		
		$cartHelper = Mage::helper("checkout/cart");
		
		$params = $this->getRequest()->getParams();
		
		try {
			
			$product = $this->_initProduct();
			
			if($product instanceof Mage_Catalog_Model_Product)
			{
				$items = $cartHelper->getCart()->getItems();
				
				foreach ($items as $item) 
				{
	
					if ($item->getProduct()->getId() == $product->getId()) 
					{
					    $itemId = $item->getItemId();
						$cartHelper->getCart()->removeItem($itemId)->save();
					    break;
					}
				}
					
				$message .= Mage::helper("checkout")->__('Item %s has been removed!', $product->getName());
				$this->tx_magelink_ajax_removefromcart_callback($message, self::MESSAGE_TYPE_SUCCESS);
			}
			
			$this->_deliverMessage(Mage::helper("checkout")->__('Cannot remove the item.'), self::MESSAGE_TYPE_ERROR);
			
		}
        catch (Exception $e) 
        {
            $message .= Mage::helper("checkout")->__('Cannot remove the item.');
			$this->tx_magelink_ajax_removefromcart_callback($message, self::MESSAGE_TYPE_ERROR, true);
        }
		
		$this->tx_magelink_ajax_removefromcart_callback(serialize($params), self::MESSAGE_TYPE_ERROR);
	}
	
	/**
	 * Adds an product to the cart
	 * 
	 * @return bool
	 */
	protected function _addProductToCart()
	{
		$message = "";
		$cart   = $this->_getCart();
		
		$params = $this->getRequest()->getParams();
		try 
		{
            if (isset($params['qty'])) 
            {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
			
			$product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');
			
            /**
             * Check product availability
             */
            if (!$product || !$product->isSaleable()) {
                $message .= Mage::helper("checkout")->__('Cannot add the item to shopping cart.');
				$this->tx_magelink_ajax_addtocart_callback($message, self::MESSAGE_TYPE_ERROR);
            }
			
            $cart->addProduct($product, $params);
			
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getCheckoutSession()->setCartWasUpdated(true);

			$message .= Mage::helper("checkout")->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
			$this->tx_magelink_ajax_addtocart_callback($message, self::MESSAGE_TYPE_SUCCESS);

		} 
        catch (Exception $e) 
        {
            $message .= Mage::helper("checkout")->__('Cannot add the item to shopping cart.');
			$this->tx_magelink_ajax_addtocart_callback($message, self::MESSAGE_TYPE_ERROR);
        }
		
	}
	
	/**
	 * Callback Function for
	 * tx_magelink_ajax_addtocart
	 */
	public function tx_magelink_ajax_addtocart_callback($message, $type = self::MESSAGE_TYPE_SUCCESS)
	{
		header('Content-Type: application/json');
		
		$data = array(
			"message"	=> $message,
			"type"		=> $type,
		);

		$encoded = json_encode($data);
		
		echo self::CALLBACK_ADD_TO_CART."(".$encoded.");";
		exit();
	}
	
	/**
	 * Callback Function for
	 * tx_magelink_ajax_getcart
	 */
	public function tx_magelink_ajax_getcart_callback($data)
	{
		header('Content-Type: application/json');
		
		$encoded = json_encode($data);
		echo self::CALLBACK_UPDATE_CART."(".$encoded.");";
		exit();
	}

	/**
	 * Callback Function for
	 * tx_magelink_ajax_removefromcart
	 */
	public function tx_magelink_ajax_removefromcart_callback($message, $type = self::MESSAGE_TYPE_SUCCESS)
	{
		header('Content-Type: application/json');
		
		$data = array(
			"message"	=> $message,
			"type"		=> $type,
		);

		$encoded = json_encode($data);
		
		echo self::CALLBACK_REMOVE_FROM_CART."(".$encoded.");";
		exit();
	}	
	
	/**
	 * Callback Function for
	 * tx_magelink_ajax_forgot_password
	 */
	public function tx_magelink_ajax_forgot_password_callback($message, $type = self::MESSAGE_TYPE_SUCCESS, $closeOnClick = false)
	{
		header('Content-Type: application/json');
		
		$data = array(
			"message"	=> $message,
			"type"		=> $type,
			"close"		=> $closeOnClick,
		);

		$encoded = json_encode($data);
		
		echo self::CALLBACK_FORGOT_PASSWORD."(".$encoded.");";
		exit();
	}
	
	/**
	 * Callback Function for
	 * tx_magelink_ajax_complete_login
	 */
	public function tx_magelink_ajax_complete_login_callback($message, $type = self::MESSAGE_TYPE_SUCCESS, $closeOnClick = false)
	{
		header('Content-Type: application/json');
		
		$data = array(
			"message"	=> $message,
			"type"		=> $type,
			"close"		=> $closeOnClick,
		);

		$encoded = json_encode($data);
		
		echo self::CALLBACK_COMPLETE_LOGIN."(".$encoded.");";
		exit();
	}


	/**
	 * Sends the callback message
	 *
	 * @param mixed $message The callback message
	 * @param string $callbackFunc Callback Function Name
	 * @param array $parameters Callback Function Parameters
	 * @return void
	 */
	protected function tx_magelink_ajax_complete_login_success($message, $callbackFunc = "callback", $parameters = array())
	{
		header('Content-Type: application/json');
		$json = json_encode($message);
		
		$parameterStr = "";
		if (!empty($parameters))
		{
			foreach ($parameters as $i=>$param)
			{
				if (!is_array($param))
				{
					$parameters[$i] = "\"".$param."\"";
				}
			}
			
			$parameterStr = "," . implode(',', $parameters);
		}
			
		// If we started login in magento
		if ($this->getRequest()->isPost())
		{
			echo $json;
		}
		else
		{
			echo $callbackFunc."(".$json.$parameterStr.")";
		}
		
		exit();

	}

	/**
	 * Callback Function for
	 * tx_magelink_ajax_display_block
	 */
	public function tx_magelink_ajax_display_block($block, $parameters = array())
	{
		header('Content-Type: application/json');

		$data = array(
			"block"	=> $block,
		);
	
		$data = array_merge($data, $parameters);
		$encoded = json_encode($data);
		echo self::CALLBACK_DISPLAY_BLOCK."(".$encoded.")";

		exit();
	}


	
	
	/**
	 * Delivers a message to the frontend
	 * 
	 * @param mixed $message Message Text
	 * @param string $type Message Type
	 * @param array $additional Additional Data
	 * @return
	 */
	protected function _deliverMessage($message, $type = self::MESSAGE_TYPE_INFO, $closeByClick = false)
	{
		header('Content-Type: application/json');
		
		// If we started login in magento
		if ($this->getRequest()->isPost())
		{
			$response = array(
				"type"		=> $type,
				"message"		=> $message
			);
			
			echo json_encode($response);
			
		}
		else
		{
			echo self::CALLBACK_FLASH_MESSAGE."('".$message."','".$type."',".(int)$closeByClick.");";
		}
		
		exit();
		
	}

	
}

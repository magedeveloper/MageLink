<?php
class MageDeveloper_MageLink_LoginController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Status
	 * @var string
	 */
	const STATUS_TYPE_SUCCESS = "success";
	
	/**
	 * ajaxPrepare Action
	 * 
	 * Prepares form data, encrypts it and
	 * sends it to TYPO3 for progressing
	 */
	public function ajaxPrepareAction()
	{
		if ($this->getRequest()->isPost()) 	
		{
			$login 	= $this->getRequest()->getPost("login");
			
            if (!empty($login["username"]) && !empty($login["password"])) 
            {
				$data = array(
					"credentials" => array(
						"email" 		=> $login["username"],
						"password"		=> $login["password"],
					),
					"remote_addr"	=> $this->getRequest()->getServer("REMOTE_ADDR"),
				);

				// Encrypt Data
				$encrypted = Mage::helper("magelink/crypt")->getEncrypted($data);

				$response = array(
					"type" 			=> self::STATUS_TYPE_SUCCESS,
					"url"	 			=> Mage::helper("magelink")->getTYPO3AjaxPrepareUrl(),
					"enc" 				=> base64_encode($encrypted),
				);

				header('Content-Type: application/json');
				echo json_encode($response);
				exit();
			}
			
		}		
		
	}	
		
}
	
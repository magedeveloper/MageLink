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
class MageDeveloper_MageLink_Model_Customer_Api extends Mage_Api_Model_Resource_Abstract
{
	/**
	 * Fetches complete customer details
	 * 
	 * @param string $email E-Mail Address of customer
	 * @return string json_encoded customer data
	 */
	public function fetch($email)
	{
		$customer = Mage::getModel('customer/customer')
							->getCollection()
							->addAttributeToSelect('*')
							->addFieldToFilter('email', $email)
							->getFirstItem();
							
        if (!$customer->getId()) {
            $this->_fault('not_exists');
        }
		
        if (!is_null($attributes) && !is_array($attributes)) {
            $attributes = array($attributes);
        }
		 
		// ATTRIBUTES AND GENERAL INFORMATION
        $result = array(); 
		$result["attributes"] = $customer->getData();
		
		// CUSTOMER GROUP
		$group = Mage::getModel('customer/group')->load($customer->getGroupId());
		$result['attributes']['group'] = $group->getCode();
		$result['attributes']['name'] = $customer->getName();
		
		// ADDRESSES
		$billingId 	= $customer->getDefaultBilling();
		$shippingId = $customer->getDefaultShipping();

		// Use Address Api
		$addrApi = Mage::getModel('customer/address_api');
		
		if ($billingId) {
			$result['billing'] 	= $addrApi->info($billingId);
		}
		
		if ($shippingId) {
			$result['shipping']	= $addrApi->info($shippingId);
		}
			
		$ser = json_encode($result);	
		return $ser;
	}

	/**
	 * Updates or creates an blind customer
	 * with given data
	 * 
	 * @parma array $data Customer Data
	 * @return int Customer Id
	 */
	public function write($data)
	{
		$prepared = array();
		$prepared = $this->_translateToArray($data);
		
		
		// We need to load the customer from the collection in
		// order not to need the website id
		$customer = Mage::getModel("customer/customer")
							->getCollection()
							->addAttributeToSelect('*')
							->addFieldToFilter("email", $prepared["email"])
							->getFirstItem();
		
		// Data preparation
		$address = array(
					"prefix"		=> $prepared["prefix"],
					"firstname" 	=> $prepared["firstname"],
					"middlename"	=> $prepared["middlename"],
					"lastname"		=> $prepared["lastname"],
					"company"		=> $prepared["company"],
					"street"		=> array($prepared["street"]),
					"city"			=> $prepared["city"],
					"postcode"		=> $prepared["postcode"],
					"telephone"		=> $prepared["telephone"],
					"fax"			=> $prepared["fax"],
		);
		
							
        if ($customer->getId()) 
        {
			// Assigning new customer data
			foreach ($prepared as $_attribute=>$_value)
			{
				$customer->setData($_attribute, $_value);
			}
			
			// Address
			$billingaddress = $customer->getDefaultBillingAddress();
			foreach ($address as $_attribute=>$_value)
			{
				$billingaddress->setData($_attribute, $_value);
			}
			
			// Final save of the customer
			try 
			{
				$customer->save();
				$billingaddress->save();
			}
			catch (Exception $e) {
			    $this->_fault('data_invalid', $e->getMessage());
			}
			
		    
        }
		else
		{
			// Reload customer model
			$customer = Mage::getModel("customer/customer");
			
			// Assigning new customer data
			$customer->setData($prepared);
			
			
			// Website Id, selected in Backend
			$defWebsiteId = Mage::helper("magelink")->getDefaultCustomerWebsiteId();
			$customer->setWebsiteId( $defWebsiteId );
				
			// Customer Group
			if ($prepared["group"] != "")
			{
				$groups = Mage::getModel("customer/group")
							->getCollection();
							
				foreach ($groups as $group)
				{
					if ($group->getCustomerGroupCode() == $prepared["group"])
					{
						$customer->setGroupId($group->getCustomerGroupId());
					}	
				}
			}
			
			// Final save of the customer
			try 
			{
				$customer->setConfirmation(null);
				$customer->save();
				
				// Create random success hash
				
			}
			catch (Exception $e) {
			    $this->_fault('data_invalid', $e->getMessage());
			}
			
			
			// Try to add the address to the new customer
			if ($customer->getId())
			{
				$customAddress = Mage::getModel("customer/address");
			
				$customAddress->setData($address)
			            ->setCustomerId($customer->getId())
			            ->setIsDefaultBilling('1')
			            ->setIsDefaultShipping('1')
			            ->setSaveInAddressBook('1');
						
				try {
				    $customAddress->save();
				}
				catch (Exception $e) {
				    $this->_fault('data_invalid', $e->getMessage());
				}
			}
			
				
		}

		if ($customer && $customer->getId())
		{
			return $customer->getId();	
		}
		
		return null;	
	}

	/**
	 * Translates an array typens to array
	 * 
	 * @param array $data Data Array
	 * @return array
	 */
	protected function _translateToArray($data)
	{
		$array = array();
		
		foreach ($data as $_dataArr)
		{
			if (is_object($_dataArr))
			{
				$key 	= $_dataArr->key;
				$value	= $_dataArr->value;
			} 
			else if (is_array($_dataArr))
			{
				$key 	= $_dataArr["key"];
				$value	= $_dataArr["value"];
			}
			
			$array[$key] = $value;
		}

		return $array;
	}
	


}
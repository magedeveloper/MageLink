<?php
class MageDeveloper_MageLink_Model_Layout extends Mage_Core_Model_Layout 
{
	/**
	 * Class Constuctor
	 *
	 * @param array $data
	 */
	public function __construct($data = array()) 
	{
		parent::__construct($data);
	}
	
	/**
	 * Get all blocks marked for output
	 *
	 * @return string
	 */
	public function getOutput() 
	{
		if (!Mage::getSingleton("magelink/core")->isInUse())
		{
			return parent::getOutput();
		}
		
		// Output
		$out = "";
		foreach ( $this->getAllBlocks() as $id => $block ) 
		{
			Mage::getSingleton('magelink/core')->setBlock($id, $block);
		}
		
		if (!empty($this->_output)) 
		{
			$out .= $this->_output;
		}
		
		return $out;
	}

}


<?php
class Magnimeios_Magnimeiosgateway_Block_Form extends Mage_Payment_Block_Form
{
	protected function _construct()
    {
		$mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('magnimeiosgateway/form/mark.phtml');
		
        $this->setTemplate('magnimeiosgateway/form/form.phtml')
			 ->setMethodLabelAfterHtml($mark->toHtml())
		;
		parent::_construct();
    }
}
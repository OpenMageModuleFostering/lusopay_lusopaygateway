<?php
class Lusopay_Lusopaygateway_Block_Form extends Mage_Payment_Block_Form
{
	protected function _construct()
    {
		$mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('lusopaygateway/form/mark.phtml');
		
        $this->setTemplate('lusopaygateway/form/form.phtml')
			 ->setMethodLabelAfterHtml($mark->toHtml())
		;
		parent::_construct();
    }
}
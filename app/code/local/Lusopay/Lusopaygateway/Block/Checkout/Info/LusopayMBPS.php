<?php
/**
 * Display the simple and extended part payment monthly fee information
 * if activated in admin. The different display conditions are used in the
 * custom $_template
 */
class Lusopay_Lusopaygateway_Block_Checkout_Success_Info_LusopayMBPS
    extends Mage_Core_Block_Template
{
    protected $_template = 'lusopaygateway/checkout/success.phtml';
	
	public function getMbInfo()
	{
		return "OIOI";
	}
	
}
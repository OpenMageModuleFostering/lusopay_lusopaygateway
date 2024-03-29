<?php
class Magnimeios_Magnimeiosgateway_Model_Convert_Order extends Mage_Sales_Model_Convert_Order
{
    /**
     * Convert order payment to quote payment
     *
     * @param   Mage_Sales_Model_Order_Payment $payment
     * @return  Mage_Sales_Model_Quote_Payment
     */
    public function paymentToQuotePayment(Mage_Sales_Model_Order_Payment $payment, $quotePayment=null)
    {
        $quotePayment = parent::paymentToQuotePayment($payment, $quotePayment);

        $quotePayment->setMagnimeiosEntidade($payment->getMagnimeiosEntidade())
						->setMagnimeiosReferencia($payment->getMagnimeiosReferencia())
						->setMagnimeiosMontante($payment->getMagnimeiosMontante());      

        return $quotePayment;
    }
}

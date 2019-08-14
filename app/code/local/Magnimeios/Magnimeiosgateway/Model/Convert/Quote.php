<?php
class Magnimeios_Magnimeiosgateway_Model_Convert_Quote extends Mage_Sales_Model_Convert_Quote
{

    /**
     * Convert quote payment to order payment
     *
     * @param   Mage_Sales_Model_Quote_Payment $payment
     * @return  Mage_Sales_Model_Quote_Payment
     */
    public function paymentToOrderPayment(Mage_Sales_Model_Quote_Payment $payment)
    {
        $orderPayment = parent::paymentToOrderPayment($payment);
        $orderPayment->setMagnimeiosEntidade($payment->getMagnimeiosEntidade())
						->setMagnimeiosReferencia($payment->getMagnimeiosReferencia())
						->setMagnimeiosMontante($payment->getMagnimeiosMontante());
        return $orderPayment;
    }

}

<?php
class Magnimeios_Magnimeiosgateway_Model_GerarRef extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'magnimeiosgateway';
    
	protected $_paymentMethod = 'magnimeiosgateway';
    protected $_formBlockType = 'magnimeiosgateway/form';
    protected $_infoBlockType = 'magnimeiosgateway/info';
    protected $_allowCurrencyCode = array('EUR');
	
	
	protected $_isGateway                   = false;
    protected $_canOrder                    = true;
    protected $_canAuthorize                = false;
    protected $_canCapture                  = false;
    protected $_canCapturePartial           = false;
    protected $_canRefund                   = false;
    protected $_canRefundInvoicePartial     = false;
    protected $_canVoid                     = false;
    protected $_canUseInternal              = true;
    protected $_canUseCheckout              = true;
    protected $_canUseForMultishipping      = true;
    protected $_isInitializeNeeded          = false;
    protected $_canFetchTransactionInfo     = false;
    protected $_canReviewPayment            = false;
    protected $_canCreateBillingAgreement   = false;
    protected $_canManageRecurringProfiles  = true;
    
	//adicionado 16/09/2014
	protected $_order;
	
	/**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = $this->getCheckout()->getOrder();
        }
        return $this->_order;
    }
	//ate aqui
	
    public function getMensagem()
    {
    	return $this->getConfigData('mensagem');
    }
	
	public function assignData($data)
	{
		
		//echo "Passo 1";
		
		$eav_entity_type	= Mage::getModel('eav/entity_type')->loadByCode('order');
		$eav_entity_store	= Mage::getModel('eav/entity_store')->loadByEntityStore($eav_entity_type->getEntityTypeId(), $this->getQuote()->getStoreId());
		
		//$order_id    = substr($eav_entity_store->getIncrementLastId() + $eav_entity_type->getIncrementPerStore(), -6, 6);
		$order_id = $eav_entity_store->getIncrementLastId() + $eav_entity_type->getIncrementPerStore();
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$select = $connection->select()->from('magnimeiosreferences', array('*'))->where('id_order=?', $order_id);
		$rows = $connection->fetchRow($select);
		
		
		//var_dump($rowsId);
			
			
		if($rows == false)
		{
			
			$update=false;
			$order_value = number_format($this -> getQuote() -> getGrandTotal(),2,'.','');
		
			$chave = $this->getConfigData('chave');
			$nif = $this->getConfigData('nif');
			
			
			
			$soapUrl = "https://services.lusopay.com/PaymentServices/PaymentServices.svc?wsdl";
		
		$xml_post_string='<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:pay="http://schemas.datacontract.org/2004/07/PaymentServices">
   <soapenv:Body>
      <tem:getNewDynamicReference>
         <!--Optional:-->
         <tem:clientGuid>'.$chave.'</tem:clientGuid>
         <!--Optional:-->
         <tem:vatNumber>'.$nif.'</tem:vatNumber>
         <!--Optional:-->
         <tem:valueList>
            <!--Zero or more repetitions:-->
            <pay:References>
               <!--Optional:-->
               <pay:amount>'.$order_value.'</pay:amount>
               <!--Optional:-->
               <pay:description>'.$order_id.'</pay:description>
               <!--Optional:-->
               <pay:serviceType>Both</pay:serviceType>
            </pay:References>
         </tem:valueList>
         <!--Optional:-->
         <tem:sendEmail>true</tem:sendEmail>
      </tem:getNewDynamicReference>
   </soapenv:Body>
</soapenv:Envelope>';


$headers = array(
		            "Host: services.lusopay.com",
		            "Content-type: text/xml;charset=\"utf-8\"",
		            "Accept: text/xml",
		            "Cache-Control: no-cache",
		            "Pragma: no-cache",
		            "SOAPAction: http://tempuri.org/IPaymentServices/getNewDynamicReference", 
		            "Content-length: ".strlen($xml_post_string),
		        );

//SOAPAction: your op URL
$url = $soapUrl;
// PHP cURL for http connection with auth
$ch = curl_init();
				
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	  curl_setopt($ch, CURLOPT_URL, $url);
	  
	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$response = curl_exec($ch);
		
		curl_close($ch);

		$referenceMB = "/<a:referenceMB>(.*?)<\/a:referenceMB>/s";
		$referencePS = "/<a:referencePS>(.*?)<\/a:referencePS>/s";
			
        if(preg_match($referencePS,$response,$referencePS_value) && preg_match($referenceMB, $response, $referenceMB_value)) {
            $refs[1] = $referencePS_value[1];
            //$refs[1] = -1;
            $refs[2] = $referenceMB_value[1];
            $referencias = "<ps>" . $refs[1] . "</ps><mb>". $refs[2] ."</mb>";
			
			
			
			//var_dump($rows);
			//$num_row = count($rows);
			//var_dump($num_row);
				
				$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
				$connection->beginTransaction();
				$fields = array();
				$fields['id_order'] = $order_id;
				$fields['refMB'] = $refs[2];
				$fields['refPS'] = $refs[1];
				$fields['value'] = $order_value;
				$connection->insert('magnimeiosreferences',$fields);
				$connection->commit();
				
				$info = $this->getInfoInstance();
				$info->setMagnimeiosEntidade("11024")
				->setMagnimeiosReferencia($referencias)
				->setMagnimeiosMontante($order_value);

			
			//$this->run("INSERT INTO `magnimeiosreferences`(`id_order`, `refMB`, `refPS`, `value`) VALUES (". $order_id .",'". $refs[2] ."','". $refs[1] ."','". $order_value .")");
        }
        else {
            $message = "/<a:message>(.*?)<\/a:message>/s";
			if(preg_match($message,$response,$message_value)) {
				echo $message_value[1];
			}
		}
			
		}
			
		
		else{
		
			if ($rows['value'] == (number_format($this -> getQuote() -> getGrandTotal(),2,'.','')))
			{
			$ref[1] = $rows['refMB'];
			//var_dump($ref[1]);
			$ref[2] = $rows['refPS'];
			$valor = $rows['value'];
			
			
			$info = $this->getInfoInstance();
			$info->setMagnimeiosEntidade("11024")
			->setMagnimeiosReferencia("<ps>" . $ref[2] . "</ps><mb>". $ref[1] ."</mb>")
			->setMagnimeiosMontante($valor);
			}
			else
			{
				//echo "entrou aqui update";
				$order_value = number_format($this -> getQuote() -> getGrandTotal(),2,'.','');
		
				$chave = $this->getConfigData('chave');
				$nif = $this->getConfigData('nif');
				
				$soapUrl = "https://services.lusopay.com/PaymentServices/PaymentServices.svc?wsdl";
		
		$xml_post_string='<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:pay="http://schemas.datacontract.org/2004/07/PaymentServices">
   <soapenv:Body>
      <tem:getNewDynamicReference>
         <!--Optional:-->
         <tem:clientGuid>'.$chave.'</tem:clientGuid>
         <!--Optional:-->
         <tem:vatNumber>'.$nif.'</tem:vatNumber>
         <!--Optional:-->
         <tem:valueList>
            <!--Zero or more repetitions:-->
            <pay:References>
               <!--Optional:-->
               <pay:amount>'.$order_value.'</pay:amount>
               <!--Optional:-->
               <pay:description>'.$order_id.'</pay:description>
               <!--Optional:-->
               <pay:serviceType>Both</pay:serviceType>
            </pay:References>
         </tem:valueList>
         <!--Optional:-->
         <tem:sendEmail>true</tem:sendEmail>
      </tem:getNewDynamicReference>
   </soapenv:Body>
</soapenv:Envelope>';


$headers = array(
		            "Host: services.lusopay.com",
		            "Content-type: text/xml;charset=\"utf-8\"",
		            "Accept: text/xml",
		            "Cache-Control: no-cache",
		            "Pragma: no-cache",
		            "SOAPAction: http://tempuri.org/IPaymentServices/getNewDynamicReference", 
		            "Content-length: ".strlen($xml_post_string),
		        );

//SOAPAction: your op URL
$url = $soapUrl;
// PHP cURL for http connection with auth
$ch = curl_init();
				
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	  curl_setopt($ch, CURLOPT_URL, $url);
	  
	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$response = curl_exec($ch);
		
		curl_close($ch);

		$referenceMB = "/<a:referenceMB>(.*?)<\/a:referenceMB>/s";
		$referencePS = "/<a:referencePS>(.*?)<\/a:referencePS>/s";
			
			if(preg_match($referencePS,$response,$referencePS_value) && preg_match($referenceMB, $response, $referenceMB_value)) {
				$refs[1] = $referencePS_value[1];
				$refs[2] = $referenceMB_value[1];
				$referencias = "<ps>" . $refs[1] . "</ps><mb>". $refs[2] ."</mb>";
				
					$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
					$connection->beginTransaction();
					$fields = array();
					//$fields['id_order'] = $order_id;
					$fields['refMB'] = $refs[2];
					$fields['refPS'] = $refs[1];
					$fields['value'] = $order_value;
					$where = $connection->quoteInto('id_order =?', $order_id);
					$connection->update('magnimeiosreferences',$fields, $where);
					$connection->commit();
					
					$info = $this->getInfoInstance();
					$info->setMagnimeiosEntidade("11024")
					->setMagnimeiosReferencia($referencias)
					->setMagnimeiosMontante($order_value);
					
			}
			else {
				$message = "/<a:message>(.*?)<\/a:message>/s";
				if(preg_match($message,$response,$message_value)) {
					echo $message_value[1];
				}
			}
			}
			
			
			
		}
			
		return $this;

	}

    public function validate()
    {
        parent::validate();
        $order_value = number_format($this->getQuote()->getGrandTotal(),2,'.','');
		/*
        if ($order_value < 2) {
            Mage::throwException(Mage::helper('magnimeiosgateway')->__('Impossível gerar referência MB ou Payshop para valores inferiores a 1.2 Euro.'));
        }
		*/
        if ($order_value >= 999999.99) {
            Mage::throwException(Mage::helper('magnimeiosgateway')->__('O valor excede o limite para pagamento na rede MB e PS'));
        }
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('magnimeiosgateway')->__('A moeda selecionada ('.$currency_code.') não é compatível com o Pagamento'));
        }
        return $this;
    }
    
	public function getQuote()
    {
        if (empty($this->_quote)) {            
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }
    
	public function getCheckout()
    {
	/*
		if(Mage::getSingleton('customer/session')->isLoggedIn())
		{
			//echo "entrou aqui";
			$this->_checkout = Mage::getSingleton('checkout/session');
		}else{
			$this->_checkout = Mage::getSingleton('adminhtml/session_quote');
		}
		*/
		// Codigo antigo
        if (empty($this->_checkout)) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }else{
			$this->_checkout = Mage::getSingleton('adminhtml/session_quote');
		}
		
        return $this->_checkout;
    }

}
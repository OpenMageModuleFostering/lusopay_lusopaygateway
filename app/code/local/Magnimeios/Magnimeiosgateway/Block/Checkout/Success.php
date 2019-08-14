<?php



class Magnimeios_Magnimeiosgateway_Block_Checkout_Success extends Mage_Checkout_Block_Onepage_Success
{
    /*
    public function gerarRef($order_id, $order_value, $chave, $nif){
        
        //var_dump("entrou ref");
        
        //$chave = $this->getConfigData('chave');
        
        //var_dump($chave);
			//$nif = $this->getConfigData('nif');
        
        //$chave = '9CE4639A-5125-4B5E-8160-0C2DFD98AD8A';
        //$nif = '999999999';
        
			
			
			
			$soapUrl = "https://services.lusopay.com/PaymentServices_test/PaymentServices.svc?wsdl";
		
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
         <tem:sendEmail>false</tem:sendEmail>
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
            
            //$magni = new GerarRef();
            //$this->getMethod()->setInfo($referencias, $order_value);
            
            
            
            //$magni->setInfo($referencias, $order_value);
            
				/*
				$info = $this->getInfoInstance();
				$info->setMagnimeiosEntidade("11024")
				->setMagnimeiosReferencia($referencias)
				->setMagnimeiosMontante($order_value);
*/
			
			//$this->run("INSERT INTO `magnimeiosreferences`(`id_order`, `refMB`, `refPS`, `value`) VALUES (". $order_id .",'". $refs[2] ."','". $refs[1] ."','". $order_value .")");
    /*
        }
        else {
            $message = "/<a:message>(.*?)<\/a:message>/s";
			if(preg_match($message,$response,$message_value)) {
				echo $message_value[1];
			}
		}
        return $this;
    }
    
    
    */
    protected function _toHtml()
    {
        
        
        //var_dump("entrou");

        $connection = Mage::getSingleton( 'core/resource' )->getConnection( 'core_read' ); // To read from the database

        $order_id = $this->getOrderId();
        
        
        
        

        //var_dump($order_id);

        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        
        
        if ($order->getPayment()->getMethod() == "magnimeiosgateway"){
            
            $chave = Mage::getStoreConfig('payment/magnimeiosgateway/chave');
            $nif = Mage::getStoreConfig('payment/magnimeiosgateway/nif');
            
            //var_dump($chave);
            //var_dump($nif);
            

            $order_value = number_format($order->getGrandTotal(),2,'.','');
            //var_dump($order_value);
            
            //$this->gerarRef($order_id, $order_value, $chave, $nif);
            
            
            

        $select = $connection->select()->from('magnimeiosreferences', array('*'))->where('id_order=?', $order_id);
        $rows = $connection->fetchRow($select);




        if($rows == true){
        /*
        while($row){

            $refMB = $row['refMB'];
            $refPS = $row['refPS'];
            $valor= $row['value'];
        }
        */

        $refMB = $rows['refMB'];
        $refPS = $rows['refPS'];
        $valor = $rows['value'];
            
            
            

        if ($refPS == '-1'){
                $showPS = 'display:none;';
                $refPS='';
        }
        else{
            $showPS = '';
            $refPS = substr($refPS,0,3).' '.substr($refPS,3,3).' '.substr($refPS,6,3).' '.substr($refPS,9,3).' '.substr($refPS,12,1);
        }

        if ($refMB == '-1'){
            $showMB = 'display:none;';
            $refMB='';
        }
        else{
            $showMB = '';
            $refMB = substr($refMB,0,3).' '.substr($refMB,3,3).' '.substr($refMB,6,3);
        }



        $tabela ='
        <div align="center">
        <table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #DCDCDC;'.$showMB.'">
                    <tr>
                <td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #DCDCDC; background-color: #DCDCDC; color: black" colspan="3"><center>Pagamento por Multibanco (By LUSOPAY)</center></td>
                </tr>
                <tr>
                <td rowspan="4"><img src="http://www.lusopay.pt/imagens/modulos/Logo_Lusopay_MB125x80px.png" alt=""/></td>
                <td style="font-size: x-small; font-weight:bold; text-align:left">Entidade:</td>
                <td style="font-size: x-small; text-align:left">11024</td>
                </tr>
                <tr>
                <td style="font-size: x-small; font-weight:bold; text-align:left">Refer&ecirc;ncia:</td>
                <td style="font-size: x-small; text-align:left">' . $refMB . '</td>
                </tr>
                <tr>
                <td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>
                <td style="font-size: x-small; text-align:left">' . $valor . ' €</td>
                </tr>
                <tr>
                <td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>
                <td style="font-size: x-small; text-align:left">&nbsp;</td>
                </tr>
                <tr>
                <td style="font-size: xx-small;border-top: 1px solid #DCDCDC; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #DCDCDC; color: black" colspan="3"><center>O tal&atilde;o emitido pela caixa autom&aacute;tica faz prova de pagamento. Conserve-o.</center></td>
                </tr>
                </table>';

            $tabela.='<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #DCDCDC; '.$showPS.'">
                <tr>
                <td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #DCDCDC; background-color: #DCDCDC; color: black" colspan="3"><center>Pagamento por Payshop (By LUSOPAY)</center></td>
                </tr>
                <tr>
                <td rowspan="4"><img src="http://www.lusopay.pt/imagens/modulos/Logo_Lusopay_Payshop125x80px.png" alt=""/></td>
                </tr>
                <tr>
                <td style="font-size: x-small; font-weight:bold; text-align:left">Refer&ecirc;ncia:</td>
                <td style="font-size: x-small; text-align:left">' . $refPS . '</td>
                </tr>
                <tr>
                <td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>
                <td style="font-size: x-small; text-align:left">' . $valor . ' €</td>
                </tr>
                <tr>
                <td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>
                <td style="font-size: x-small; text-align:left">&nbsp;</td>
                </tr>
                <tr>
                <td style="font-size: xx-small;border-top: 1px solid #DCDCDC; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #DCDCDC; color: black" colspan="3"><center>O tal&atilde;o emitido faz prova de pagamento. Conserve-o.</center></td>
                </tr>
                </table>
                </div>';

        $html = parent::_toHtml();

        $html .= $tabela;

        return $html;

    }
    }
    else{
        $html = parent::_toHtml();
        return $html;
    }

    }
}

?>

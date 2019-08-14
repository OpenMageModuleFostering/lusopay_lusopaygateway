<?php

class Lusopay_Lusopaygateway_Model_AtivarCallback extends Mage_Core_Model_Config_Data
{
    public function getCommentText(Mage_Core_Model_Config_Element $element, $currentValue)
    {
		$chave_anti=md5(time());
		
		$read = Mage::getSingleton( 'core/resource' )->getConnection( 'core_read' ); // To read from the database
		
		
		$query = "SELECT * FROM lusopay_config ORDER BY id ASC LIMIT 1";
		
		$row = $read->prepare($query);
		$row->execute();
		
		if($result = $row->fetch()){
			
			$chave_anti = $result['antiphishing'];
		}else{
			$lusopaygateway_save_conn =  Mage::getSingleton('core/resource')->getConnection('core_write');
			$lusopaygateway_save_conn->beginTransaction();
			$fields = array();
			$fields['antiphishing'] = $chave_anti;
			$lusopaygateway_save_conn->insert('lusopay_config',$fields);
			$lusopaygateway_save_conn->commit();
		}
		
		$skinUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
		
		$urlCallback = Mage::getBaseUrl() . 'lusopaygateway/callback/check/entidade/«entidade»/referencia/«referencia»/valor/«valor»/chave/'.$chave_anti;
		
		
		$result = "<p id='lusopaygateway_callback_info'></p>";
		$result .= "<script type='text/javascript'>
		
		function render_callback_info(){
			
				var infolusopay = $('lusopaygateway_callback_info');
				 var field_value = $('payment_lusopaygateway_active_callback').getValue();
                    switch (field_value.toLowerCase())
                    {
                        case '1':
                            infolusopay.innerHTML ='<br/>Foi enviado o pedido de activação do sistema callback para o geral@lusopay.com.';
                            ".$this->sendRequest()."
                            break;
                        case '0':
                            infolusopay.innerHTML = 'Ao mudar para \"yes\", será enviado um email a pedir a activação do sistema callback.<br/>Trata-se de um sistema que altera de forma automática e em tempo real,os estados das encomendas para pagos no preciso momento em que o cliente paga a referência e envia a factura.';
                            break;
                    }
        }
					
		function init_comment()
            {
                render_callback_info();
                $('payment_lusopaygateway_active_callback').observe('change', function(){
                    render_callback_info();
                });
            }
            document.observe('dom:loaded', function(){init_comment();});
            </script>";
			
			return $result;

	}

	public function getStatusSendEmail($antiphishing){
		
		$read = Mage::getSingleton( 'core/resource' )->getConnection( 'core_read' );
		
		$query = "SELECT * FROM lusopay_config WHERE antiphishing= '". $antiphishing."'";
		
		$row = $read->prepare($query);
		$row->execute();
		
		if ($result = $row->fetch()){
			$sendEmail = $result['sendemail'];
			return $sendEmail;
		}
		
	}

	public function updateSendEmail($antiphishing){
		
		$write = Mage::getSingleton('core/resource') -> getConnection('core_write');
		
		$query = "UPDATE lusopay_config SET sendemail = 1 WHERE antiphishing='". $antiphishing."'";
		$write->query($query);
		
	} 

	public function sendRequest()
	{
		if ($this->getStatusSendEmail($this->getChaveAntiphishing()) == 0 && Mage::getStoreConfig('payment/lusopaygateway/active_callback') == '1')
		{
			$body = "
			<fieldset>
		
			Desejo activar o sistema Callback<br/><br/>
			Os meus dados:<br/><br/>
		
			NIF: ".Mage::getStoreConfig('payment/lusopaygateway/nif')."<br/><br/>
		
			Url do callback:<br/><br/>
		
			".Mage::getBaseUrl() . "lusopaygateway/callback/check/entidade/&laquo;entidade&raquo;/referencia/&laquo;referencia&raquo;/valor/&laquo;valor&raquo;/chave/".$this->getChaveAntiphishing()."<br/><br/>
		
			Obrigado.
		
			</fieldset>";
			$mail = Mage::getModel('core/email');
			$mail->setToName('LUSOPAY');
			$mail->setToEmail('geral@lusopay.com');
			$mail->setBody($body);
			$mail->setSubject('Activar Callback');
			$mail->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
			$mail->setFromName(Mage::getStoreConfig('trans_email/ident_general/name'));
			$mail->setType('html');// You can use 'html' or 'text'
			
			//var_dump($mail);

			try {
			
    			$mail->send();
    			$this->updateSendEmail($this->getChaveAntiphishing());
    			Mage::getSingleton('core/session')->addSuccess('Pedido enviado com sucesso!');
    			//$this->_redirect('');
			}
			catch (Exception $e) {
    			Mage::getSingleton('core/session')->addError('Ocorreu um erro ao enviar o pedido!');
    			//$this->_redirect('');
			}
		}
		
	}
	
	public function getChaveAntiphishing(){
		$chave_anti=md5(time());
		
		$read = Mage::getSingleton( 'core/resource' )->getConnection( 'core_read' ); // To read from the database
		
		
		$query = "SELECT * FROM lusopay_config ORDER BY id ASC LIMIT 1";
		
		$row = $read->prepare($query);
		$row->execute();
		
		if($result = $row->fetch()){
			
			$chave_anti = $result['antiphishing'];
			return $chave_anti;
		}else{
			return Mage::throwException(Mage::helper('lusopaygateway')->__('Não foi encontrada nenhuma chave!!'));
		}
	}

    
		 
	
}
?>
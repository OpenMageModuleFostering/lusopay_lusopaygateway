<?php

class Lusopay_Lusopaygateway_Block_Checkout_Success extends Mage_Checkout_Block_Onepage_Success
{
	protected function _toHtml()
    {
	
		$read = Mage::getSingleton( 'core/resource' )->getConnection( 'core_read' ); // To read from the database
		
		$order_id = $this->getOrderId();
		
		//var_dump($order_id);
		
		$query = "SELECT * FROM magnimeiosreferences WHERE id_order = ".$order_id;
		
		$result = $read->query($query);
		
		while($row=$result->fetch()){
			
			$refMB = $row['refMB'];
			$refPS = $row['refPS'];
			$valor= $row['value'];
		}
		
		if(!is_null($refMB) || $refMB != -1){
			
		$refMB = substr($refMB,0,3).' '.substr($refMB,3,3).' '.substr($refMB,6,3);
		
		$tabela ='
		<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #DCDCDC" align="center">
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
				<td style="font-size: x-small; text-align:left">' . $valor . '</td>
				</tr>
				<tr>
				<td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>
				<td style="font-size: x-small; text-align:left">&nbsp;</td>
				</tr>
				<tr>
				<td style="font-size: xx-small;border-top: 1px solid #DCDCDC; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #DCDCDC; color: black" colspan="3"><center>O tal&atilde;o emitido pela caixa autom&aacute;tica faz prova de pagamento. Conserve-o.</center></td>
				</tr>
				</table>';
		
		}
		if(!is_null($refPS) || $refPS != -1){
			
			$refPS = substr($refPS,0,3).' '.substr($refPS,3,3).' '.substr($refPS,6,3).' '.substr($refPS,9,3).' '.substr($refPS,12,1);
			
			$tabela.='<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #DCDCDC" align="center">
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
				<td style="font-size: x-small; text-align:left">' . $valor . '</td>
				</tr>
				<tr>
				<td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>
				<td style="font-size: x-small; text-align:left">&nbsp;</td>
				</tr>
				<tr>
				<td style="font-size: xx-small;border-top: 1px solid #DCDCDC; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #DCDCDC; color: black" colspan="3"><center>O tal&atilde;o emitido faz prova de pagamento. Conserve-o.</center></td>
				</tr>
				</table>';
		}
		 
		/*
		
		$query = "SELECT * FROM ifthenpay_ifmb_callback WHERE order_id = :order_id";
		
		$binds = array(
			'order_id' => $this->getOrderId()
		);
		$result = $read->query( $query, $binds );
		while ( $row = $result->fetch() ) {
			$ifmbInfo = '<div>
				<br/><br/><h2>' . $this->__('Utilize os dados abaixo para proceder ao pagamento da sua encomenda. Estes dados foram igualmente enviados para o seu email.') . '</h2><br/>
				<table border="0" cellspacing="1" cellpadding="0" style="margin: 0 auto;">
		<tr>
			<td style="padding-bottom: 10px;">
				<img src="' . $this->getSkinUrl('images/ifmb/multibanco_horizontal.gif'). '" border="0" alt="multibanco" title="multibanco" style="width: 100%;"/>
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
					<tr>
						<td style="text-align: left;">' . $this->__('<strong>Entidade:</strong>') . '</td>
						<td style="text-align: right;">' . $row["entidade"] . '</td>
					</tr>
					<tr>
						<td style="text-align: left;">' . $this->__('<strong>Referência:</strong>') . '</td>
						<td style="text-align: right;padding-left: 10px;">' . $row["referencia"] . '</td>
					</tr>
					<tr>
						<td style="text-align: left;">' . $this->__('<strong>Montante:</strong>') . '</td>
						<td style="text-align: right;">' . $row["valor"] . '  EUR</td>
					</tr>
				</table>
			</td>
		</tr>
	</table></div>';
		}
		
		*/
        $html = parent::_toHtml();
		
		$html .= $tabela;
	

        
        return $html;
    }
}

?>
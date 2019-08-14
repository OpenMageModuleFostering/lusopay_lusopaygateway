<?php

class Magnimeios_Magnimeiosgateway_Block_Checkout_Success extends Mage_Checkout_Block_Onepage_Success
{
	protected function _toHtml()
    {
	
		$connection = Mage::getSingleton( 'core/resource' )->getConnection( 'core_read' ); // To read from the database
		
		$order_id = $this->getOrderId();
		
		//var_dump($order_id);
		
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
	
	}else{
		$html = parent::_toHtml();
		return $html;
	}
		
    }
}

?>
<?php

class Magnimeios_Magnimeiosgateway_CallbackController extends Mage_Core_Controller_Front_Action {
	public function checkAction() {
		$db = Mage::getSingleton('core/resource') -> getConnection('core_write');
		//$read = Mage::getSingleton( 'core/resource' )->getConnection( 'core_read' ); // To read from the database

		$query = "SELECT * FROM lusopay_config ORDER BY id ASC LIMIT 1";

		$stmt = $db -> prepare($query);
		$stmt -> execute();

		$db = Mage::getSingleton('core/resource') -> getConnection('core_read');
		
		
		if ($result = $stmt -> fetch()) {
			if ($this -> getRequest() -> getParam('chave') == $result['antiphishing']) {

				$entidade = $this -> getRequest() -> getParam('entidade');
				$ref = $this -> getRequest() -> getParam('referencia');
				//$refPS = $this -> getRequest() -> getParam('ref');
				$valor = $this -> getRequest() -> getParam('valor');
				$valor_final = str_replace(',', '.',$valor);
				
				if ($entidade == '11024') {

					$query = "SELECT * FROM magnimeiosreferences WHERE refMB =" . $ref . " AND value=" . $valor_final . " AND STATUS IS NULL";

				} 
				elseif($entidade == '10120') {
					$refPayshop = $entidade.$ref;

					$query = "SELECT * FROM magnimeiosreferences where refPS like '%" . $refPayshop . "%' AND value=" . $valor_final . " AND STATUS IS NULL";

				}
				
				//var_dump($query);

				$result = $db -> query($query);

				if ($row = $result -> fetch()) {

					$oid = $row["id_order"];

					$order = Mage::getModel('sales/order') -> load($oid, 'increment_id');
					$id = $order -> getId();

					try {
						$invoice = $order -> prepareInvoice();

						$invoice -> register();
						Mage::getModel('core/resource_transaction') -> addObject($invoice) -> addObject($invoice -> getOrder()) -> save();

						$invoice -> sendEmail(true, '');
						$order -> setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
						$order -> save();

						$sql = "UPDATE magnimeiosreferences SET `STATUS` = 'PAGO' WHERE `id_order` = " . $oid;
						$db -> query($sql);

						echo 'True';
					} catch (Exception $e) {
						print_r($e);
						echo 'False';
					}

				} else {
					echo 'False';
				}

			} else {
				echo 'False';
			}

		} else {
			echo 'False';
		}

	}

}
?>
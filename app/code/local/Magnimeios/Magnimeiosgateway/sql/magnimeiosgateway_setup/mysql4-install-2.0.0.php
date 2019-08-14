<?php
$installer = $this;

$installer->startSetup();

$installer->run("CREATE TABLE IF NOT EXISTS `magnimeiosreferences` (id_order int, refMB VARCHAR(9), refPS VARCHAR(13), value VARCHAR(10))");


$installer->addAttribute('order_payment', 'magnimeios_entidade', array('type'=>'varchar'));
$installer->addAttribute('order_payment', 'magnimeios_referencia', array('type'=>'varchar'));
$installer->addAttribute('order_payment', 'magnimeios_montante', array('type'=>'varchar'));

$installer->addAttribute('quote_payment', 'magnimeios_entidade', array('type'=>'varchar'));
$installer->addAttribute('quote_payment', 'magnimeios_referencia', array('type'=>'varchar'));
$installer->addAttribute('quote_payment', 'magnimeios_montante', array('type'=>'varchar'));

$installer->endSetup();

if (Mage::getVersion() >= 1.1) {
    $installer->startSetup();    
	$installer->getConnection()->addColumn($installer->getTable('sales_flat_quote_payment'), 'magnimeios_entidade', 'VARCHAR(255) NOT NULL');
	$installer->getConnection()->addColumn($installer->getTable('sales_flat_quote_payment'), 'magnimeios_referencia', 'VARCHAR(255) NOT NULL');
	$installer->getConnection()->addColumn($installer->getTable('sales_flat_quote_payment'), 'magnimeios_montante', 'VARCHAR(255) NOT NULL');
    $installer->endSetup();
}

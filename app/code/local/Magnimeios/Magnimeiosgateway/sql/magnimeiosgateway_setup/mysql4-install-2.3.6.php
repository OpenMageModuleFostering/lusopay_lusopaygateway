<?php
$installer = $this;

$installer->startSetup();



if ($installer->getConnection()->showTableStatus('magnimeiosreferences')){
	$installer->startSetup();
	$installer->getConnection()->addColumn($installer->getTable('magnimeiosreferences'), 'status', 'VARCHAR(10)');
	$installer->endSetup();
}else{
	$installer->startSetup();
	$installer->run("CREATE TABLE IF NOT EXISTS `magnimeiosreferences` (id_order int, refMB VARCHAR(9), refPS VARCHAR(13), value VARCHAR(10), status VARCHAR(10))");
	$installer->endSetup();
}


$installer->run("CREATE TABLE IF NOT EXISTS `lusopay_config` (`id` int(11) NOT NULL AUTO_INCREMENT, `antiphishing` varchar(50) DEFAULT NULL, `sendemail` tinyint(1) NOT NULL, PRIMARY KEY (`id`))");

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
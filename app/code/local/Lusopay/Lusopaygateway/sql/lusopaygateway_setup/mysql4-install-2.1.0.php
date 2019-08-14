<?php
$installer = $this;

$installer->startSetup();

//$installer->run("CREATE TABLE IF NOT EXISTS `lusopayreferences` (`id` int(11) NOT NULL AUTO_INCREMENT, `order_id` int(22) NOT NULL, `entidade` int(11) NOT NULL, `referencia` varchar(11) NOT NULL, `referencia_sem_espacos` varchar(9) NOT NULL, `valor` decimal(10,2) NOT NULL, `check_mb` varchar(50) DEFAULT NULL, PRIMARY KEY (`id`))");

if ($installer->getConnection()->showTableStatus('magnimeiosreferences')){
	$installer->startSetup();
	$installer->getConnection()->addColumn($installer->getTable('magnimeiosreferences'), 'status', 'VARCHAR(10)');
	$installer->endSetup();
}else{
	$installer->startSetup();
	$installer->run("CREATE TABLE IF NOT EXISTS `magnimeiosreferences` (id_order int, refMB VARCHAR(9), refPS VARCHAR(13), value VARCHAR(10), status VARCHAR(10))");
	$installer->endSetup();
}

//$installer->run("CREATE TABLE IF NOT EXISTS `lusopayreferences` (id_order int, refMB VARCHAR(9), refPS VARCHAR(13), value VARCHAR(10), status VARCHAR(10))");

$installer->run("CREATE TABLE IF NOT EXISTS `lusopay_config` (`id` int(11) NOT NULL AUTO_INCREMENT, `antiphishing` varchar(50) DEFAULT NULL, `sendemail` tinyint(1) NOT NULL, PRIMARY KEY (`id`))");

$installer->addAttribute('order_payment', 'lusopay_entidade', array('type'=>'varchar'));
$installer->addAttribute('order_payment', 'lusopay_referencia', array('type'=>'varchar'));
$installer->addAttribute('order_payment', 'lusopay_montante', array('type'=>'varchar'));

$installer->addAttribute('quote_payment', 'lusopay_entidade', array('type'=>'varchar'));
$installer->addAttribute('quote_payment', 'lusopay_referencia', array('type'=>'varchar'));
$installer->addAttribute('quote_payment', 'lusopay_montante', array('type'=>'varchar'));
$installer->endSetup();

if (Mage::getVersion() >= 1.1) {
    $installer->startSetup();    
	$installer->getConnection()->addColumn($installer->getTable('sales_flat_quote_payment'), 'lusopay_entidade', 'VARCHAR(255) NOT NULL');
	$installer->getConnection()->addColumn($installer->getTable('sales_flat_quote_payment'), 'lusopay_referencia', 'VARCHAR(255) NOT NULL');
	$installer->getConnection()->addColumn($installer->getTable('sales_flat_quote_payment'), 'lusopay_montante', 'VARCHAR(255) NOT NULL');
    $installer->endSetup();
}
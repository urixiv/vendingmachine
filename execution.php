<?php
 
 require_once('drink.class.php');
 require_once('vending_manager.class.php');

$vending = new Vending(1);

$comm = readline('Insert your command:');

echo $vending->processCommand($comm);

$vending->saveData();

?>
<?php

require_once('drink.class.php');
require_once('coin.class.php');
require_once('config_manager.class.php');

class Vending 
{
    protected $id;
    public $array_drinks;
    public $array_coins;
    private $configmanager;


    public function __construct($id){
        $this->id = $id;

        //Initialize Drinks and Coins
        $water = new Drink(1, 'Water', 0.65);
        $juice = new Drink(2, 'Juice', 1.00);
        $soda = new Drink(3, 'Soda', 1.50);
        $this->array_drinks = array("water" => $water, "juice" => $juice, "soda" => $soda);

        $coin005 = new Coin(1, '0.05', 0.05);
        $coin010 = new Coin(2, '0.10', 0.10);
        $coin025 = new Coin(3, '0.25', 0.25);
        $coin100 = new Coin(4, '1.00', 1.00);
        $this->array_coins = array("005" => $coin005, "010" => $coin010, "025" => $coin025, "100" => $coin100);

        // Initialize data from files -> we will be able to provide other methods to load and save data with Configmanager class
        $this->configmanager = new Configmanager('file');
        $this->initData();
    }

    public function initData(){
        foreach($this->configmanager->data_drinks as $key => $value){
            $this->array_drinks[$key]->setQty($value);
        }
        foreach($this->configmanager->data_coins as $key => $value){
            $this->array_coins[$key]->setQty($value);
        }
    }

    public function saveData(){
        foreach($this->array_drinks as $key => $drink){
            $this->configmanager->data_drinks[$key] = $drink->getQty();
        }
        foreach($this->array_coins as $key => $drink){
            $this->configmanager->data_coins[$key] = $drink->getQty();
        }
        $this->configmanager->saveData('file');
    }


}
?>
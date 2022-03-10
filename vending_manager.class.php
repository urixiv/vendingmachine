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
    private $currentCoins;


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

        // Initialize the current coins -> Array with the coins inserted in each operation
        $curr_coin005 = new Coin(1, '0.05', 0.05);
        $curr_coin010 = new Coin(2, '0.10', 0.10);
        $curr_coin025 = new Coin(3, '0.25', 0.25);
        $curr_coin100 = new Coin(4, '1.00', 1.00);
        $this->currentCoins = array("005" => $curr_coin005, "010" => $curr_coin010, "025" => $curr_coin025, "100" => $curr_coin100);

        // Initialize data from files -> we will be able to provide other methods to load and save data with Configmanager class
        $this->configmanager = new Configmanager('file');
        $this->initData();
    }

    // Initialize data from File (storage)
    public function initData(){
        foreach($this->configmanager->data_drinks as $key => $value){
            $this->array_drinks[$key]->setQty($value);
        }
        foreach($this->configmanager->data_coins as $key => $value){
            $this->array_coins[$key]->setQty($value);
        }
    }

    // Save data to file (storage)
    public function saveData(){
        foreach($this->array_drinks as $key => $drink){
            $this->configmanager->data_drinks[$key] = $drink->getQty();
        }
        foreach($this->array_coins as $key => $drink){
            $this->configmanager->data_coins[$key] = $drink->getQty();
        }
        $this->configmanager->saveData('file');
    }

    // Function to process the inserted command
    public function processCommand($comm){
        $arr_line = explode(",", $comm);

        $ret ="";

        foreach($arr_line as $element){
            $element = trim($element);
            $element = str_replace(".", "", $element);
            switch($element){
                case "1": 
                    $this->currentCoins["100"]->setQty($this->currentCoins["100"]->getQty()+1);
                    break;
                case "005":
                    $this->currentCoins["005"]->setQty($this->currentCoins["005"]->getQty()+1);
                    break;
                case "010":
                    $this->currentCoins["010"]->setQty($this->currentCoins["010"]->getQty()+1);
                    break;
                case "025":
                    $this->currentCoins["025"]->setQty($this->currentCoins["025"]->getQty()+1);
                    break;
                default : 
                    $newelement = explode("-", $element);
                    if (count($newelement)>1){
                        if ($newelement[0] == "GET"){
                            $ret = $this->getDrink($newelement[1]);
                        } else if ($newelement[0] == "RETURN"){
                            $ret = $this->retCoin();
                        }
                    } else if ($element == "SERVICE"){
                        $ret = $this->getService();
                    }
            }

        }

        return $ret;
    }

    // Function to generate the returned coins 
    public function retCoin(){
        $ret = "";
        foreach($this->currentCoins as $newCoin){
            for ($i=0;$i<$newCoin->getQty();$i++)
                $ret.= $newCoin->getValue().", ";
            $newCoin->setQty(0); 
        }
        $ret = substr($ret, 0, -2);
        return $ret;
    }

    // Function to add the current coins inserted on the cashier
    public function addCurrentCoinsToCashier(){
        foreach($this->currentCoins as $key=>$newCoin){
            $this->array_coins[$key]->setQty($this->array_coins[$key]->getQty() + $newCoin->getQty());
            $newCoin->setQty(0); 
        }
    }

    // Function to calculate the money inserted (sum of coins)
    public function getMoney(){
        $total = 0;
        foreach($this->currentCoins as $newCoin){
            $total += ($newCoin->getQty() * $newCoin->getValue());
        }
        return $total;
    }

    // Function to calculate change
    public function calculateChange($change){
        $ret = "";
        while ($change > 0){
            if (bccomp($change, 1.00, 3)>=0 && $this->array_coins["100"]->getQty() > 0){
                $ret.= ", 1.00";
                $this->array_coins["100"]->setQty($this->array_coins["100"]->getQty()-1);
                $change -= 1;
            } else if (bccomp($change, 0.25, 3)>=0 && $this->array_coins["025"]->getQty() > 0){
                $ret.= ", 0.25";
                $this->array_coins["025"]->setQty($this->array_coins["025"]->getQty()-1);
                $change -= 0.25;
            }
            else if (bccomp($change, 0.10, 3)>=0 && $this->array_coins["010"]->getQty() > 0){
                $ret.= ", 0.10";
                $this->array_coins["010"]->setQty($this->array_coins["010"]->getQty()-1);
                $change -= 0.1;
            }
            else if (bccomp($change, 0.05, 3)>=0 && $this->array_coins["005"]->getQty() > 0){
                $ret.= ", 0.05";
                $this->array_coins["005"]->setQty($this->array_coins["005"]->getQty()-1);
                $change -= 0.05;
            }
        }
        return $ret;
    }

    // Function to GET DRINK. Calculate the change if necessary
    public function getDrink($newdrink){
        $ret = "";
        $newdrink = strtolower($newdrink);
        $drink = $this->array_drinks[$newdrink];
        $insertedMoney = $this->getMoney();
        if ($insertedMoney < $drink->getPrice()){
            $ret = "NOT ENOUGH MONEY - Return Coin : ".$this->retCoin();
        } else if ($insertedMoney == $drink->getPrice()) {
            $ret = strtoupper($newdrink);
        } else {
            $ret = strtoupper($newdrink).$this->calculateChange((float)($insertedMoney - $drink->getPrice()));
        }
        $this->addCurrentCoinsToCashier();
        return $ret;
    }

    // Function that allows technics to modify the number of coins and drinks in the machine
    public function getService(){
        foreach($this->array_drinks as $key => $drink){
            $line = readline('Number of bottles of '.$key.':');
            $drink->setQty($line);
        }
        foreach($this->array_coins as $key => $coin){
            $line = readline('Number of coins of '.$key.':');
            $coin->setQty($line);
        }
        $this->saveData();
    }


}
?>
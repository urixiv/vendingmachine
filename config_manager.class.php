<?php

require_once('drink.class.php');
require_once('coin.class.php');


class Configmanager 
{
    public $data_drinks;
    public $data_coins;


    public function __construct($type){
        $this->data_drinks = array();
        $this->data_coins = array();
        if ($type == 'file'){
            $this->readFromFile();
        }

    }

    public function readFromFile(){
        // Read Quantities configuration for drinks
        $handle = fopen("drinks.txt", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $arr_line = explode("=", $line);
                $this->data_drinks[$arr_line[0]] = (int)($arr_line[1]);
            }
            fclose($handle);
        } else {
        } 
        // Read Quantities configuration for Coins
        $handle = fopen("coins.txt", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $arr_line = explode("=", $line);
                $this->data_coins[$arr_line[0]] = (int)$arr_line[1];
            }
            fclose($handle);
        } else {
        } 
    }

    public function saveData($type){
        if ($type == 'file'){
            $fp = fopen('drinks.txt', 'w');
            $text = '';
            foreach($this->data_drinks as $key => $value){
                $text .= $key."=".$value."\n";
            }
            fwrite($fp, $text);
            fclose($fp);
            $fp = fopen('coins.txt', 'w');
            $text = '';
            foreach($this->data_coins as $key => $value){
                $text .= $key."=".$value."\n";
            }
            fwrite($fp, $text);
            fclose($fp);
        }
    }


}
?>



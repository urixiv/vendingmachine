How to use it:

This is a command line application. So from your command line you need to execute file : execution.php
For this, on command line you should make from the folder where it is stored : 
> [your_path_to_php]/php.exe execution.php

It will prompt you to insert your command.

Your command should have :
- Coins separated by comma : 0.25, 0.10, ...
After the coins you can use command :
- RETURN-COIN that will return all the coins you have inserted
- GET-DRINK where drink can be SODA, WATER or JUICE

If you use RETURN-COIN all the coins will be returned and the program finishes
If you use GET-DRINK if coins are exacted you will get the drink, and the coins will go to cashier and stock of drink will be decreased.
If you haven't inserted enough coins, coins are returned.
If you have inserted more than needed coins, you will get DRINK and the corresponding COINS of change

There is another feature, command SERVICE that will prompt you to add the exact number of coins in the cashier and the drinks in the vending machine.

IMPROVEMENTS FOR THE FUTURE 
In case of have time there are few improvements to add
- Add a control when vending gets without enough coins to return change
- Add visual functionality to manage vending in a more visual way
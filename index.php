<?php
mb_internal_encoding("UTF-8");//for MB funcitons running fine
session_start();
function autoloadFunkce($trida)//autoloader pro calssy
{
    if ((mb_strlen($trida) >= mb_strlen("Controller")) && (mb_strpos($trida, "Controller", mb_strlen($trida) - mb_strlen("Controller"))) !== false)
        require("Controllers/" . $trida . ".php");
    else
        require("Models/" . $trida . ".php");
}

spl_autoload_register("autoloadFunkce");//registrování 
include "Models/Database.php";
$database= new Database();//Napojení na databázi

UserManager::WantSomething();//Kontrola akcí uživatele


$router = new RouterController();//vytvoření routeru

$router->run($_SERVER['REQUEST_URI']);//navigace podle url
$router->show();//vykreslení celé stránky
?>
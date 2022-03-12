<?php
/*
Pagina de deslogeo
Unicamente elimina datos de la sesion y retorna a la ventana de ingreso
*/
session_start();
unset($_SESSION['username']);
header("Location: /login.php");
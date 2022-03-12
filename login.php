<?php
session_start();

require __DIR__ . "/php/enviroment.php";
require __DIR__ . "/php/handler.php";
require __DIR__ . "/php/database.php";

use database\database;

if(isset($_SESSION['username'])){
    Header("Location: /");
}
if(isset($_POST['login-submit'])){
    $db = new database();
    /*
    $db->connect();
    $attempt = $db->logIn($_POST['login-username'], $_POST['login-password']);
    if($attempt == true){
        $_SESSION['username'] = $_POST['login-username'];
        Header("Location: /");
    }
    */
    Header("Location: /");
}


?>

<!DOCTYPE html>
<Html lang='es'>
    <head>        
        <!--Meta Tags-->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Restaurante - Iniciar session</title>
        <!--CSS-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <!--JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        
    </head>
    <body class='bg-info'>
        <form class='d-flex flex-column align-items-center justify-content-center h-100 mt-4' method='post' action=''>
            <h1>Sistema Restaurante</h1>
            <label>Nombre de Usuario</label>
            <input name='login-username' type='text'>
            <label>Contrase&ntilde;a</label>
            <input name='login-password' type='password'>
            <input name='login-submit' type='submit' class='m-2' value='Log In'>
        </form>
    </body>
</Html>
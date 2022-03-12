<?php
header('Content-type: application/json');

require __DIR__ . "/../php/enviroment.php";
require __DIR__ . "/../php/handler.php";
require __DIR__ . "/../php/database.php";

use database\database;


/*
Api.php maneja los post requests enviados al sistema,
Segun la informacion solicitada en el campo request se hace el llamado correspondiente a la funcion apropiada,
y se hace la respuesta en JSON
*/
$db = new database();
$db->connect();
$response = [
    'success' => 0,
    'data' => null
];
switch($_POST['request']){
    case "restaurantes":
        $response['data'] = $db->getRestaurantes();
        $response['success'] = 1;
        break;
    case "tipos_mesa":
        $response['data'] = $db->getTiposMesa();
        $response['success'] = 1;
        break;
    case "horarios_disponibles":
        $response['data'] = $db->getHorariosDisponibles($_POST['restaurante'], $_POST['tipo_mesa'], $_POST['fecha']);
        $response['success'] = 1;
        break;
    case "crear_reserva":
        $response = $db->crearReserva($_POST['data']);
        break;
    case "buscar":
        $response = $db->buscarReserva($_POST['data'], $_POST['metodo']);
        break;
    case "editar_reserva":
        $response = $db->editarReserva($_POST['data']);
        break;
}

echo json_encode($response, false);

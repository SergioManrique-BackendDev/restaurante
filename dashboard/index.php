<?php
/*
session_start();
if(!isset($_SESSION['username'])){
    Header("Location: /login.php");
}
*/

?>

<!DOCTYPE html>
<html lang='es'>
    <head>
        <!--Meta Tags-->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Restaurante - Dashboard</title>
        <!--CSS-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <!--JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="/js/selectFunctions.js"></script>
        <script src="/js/buscarReserva.js"></script>
        <script src="/js/activarEdicion.js"></script>
        <script src="/js/editarReserva.js"></script>
    </head>
    <body class='bg-light'>
        <header class='d-flex justify-content-between align-items-center px-3 mx-0 mb-3 navbar-brand bg-secondary text-light'>
            <h1 class='w-25'>Dashboard Restaurante</h1>
            <div class='w-25 d-flex justify-content-end'>
                <a class='text-light mx-1' href='/'>Reservas</a>
                <a class='text-light mx-1' href='/logout.php'>Cerrar Sesi&oacute;n</a>
            </div>
        </header>
        <main class='mx-5 d-flex flex-column align-items-center'>
            <form class='d-flex flex-row justify-content-center flex-wrap'>
                <label>Buscar por:</label>
                <select id='select-buscar-method' class='form-control'>
                    <option value='1'>Folio</option>
                    <option value='2'>RFC</option>
                </select>
                <label>Numero:</label>
                <input id='input-buscar-data' type='text' class='form-control'>
                <input id='btn-buscar-reserva' type='button' class='btn btn-secondary mt-2' value='Buscar'>
            </form>
            <form class='d-flex justify-content-center flex-wrap mt-5'>
                <div class='mx-2'>
                    <label>Folio</label>
                    <input id='input-folio' type='text' class='form-control' disabled='true'>

                    <label>Nombre</label>
                    <input id='input-nombre' type='text' class='form-control' disabled='true'>

                    <label>Apellido</label>
                    <input id='input-apellido' type='text' class='form-control' disabled='true'>

                    <label>Fecha de Nacimiento</label>
                    <input id='input-fecha_nacimiento' type='date' class='form-control' disabled='true'>

                    <label>Correo Electr&oacute;nico</label>
                    <input id='input-correo' type='text' class='form-control' disabled='true'>

                    <label>Tel&eacute;fono</label>
                    <input id='input-telefono' type='text' class='form-control' disabled='true'>

                    <label>RFC</label>
                    <input id='input-rfc' type='text' class='form-control' disabled='true'>

                    <label>Nickname</label>
                    <input id='input-nickname' type='text' class='form-control' disabled='true'>
                </div>
                <div class='mx-2 d-flex flex-column'>
                    <label>Numero de Mesa</label>
                    <input id='input-numero_mesa' class='form-control' type='text' disabled='true'>

                    <label>Fecha de la Reserva</label>
                    <input id='input-fecha_reserva' class='form-control' type='date' disabled='true'>

                    <label>Restaurante</label>
                    <select id='select-restaurante' class='form-control' disabled='true'>
                        <option value='0'>Elija uno</option>
                    </select>

                    <label>Mesa</label>
                    <select id='select-tipo_mesa' class='form-control' disabled='true'>
                        <option value='0'>Elija uno</option>
                    </select>
                    
                    <label>Horario</label>
                    <select style='color: rgb(1,1,1,0);' id='select-horario' class='form-control' disabled='true'>
                    </select>
                    
                    <div class='my-2'>
                        <label>Factura</label>
                        <input id='input-factura' type='checkbox' disabled='true'>
                    </div>

                    <div class='my-2'>
                        <label>Editar Reserva</label>
                        <input id='input-edit' type='checkbox'>
                    </div>
                    <input id='btn-editar-reserva' class='btn btn-secondary' type='button' value='Guardar Reserva Editada' disabled='true'>
                </div>
            </form>
            <div id='error-popup' class='text-center mt-5 p-3 border border-warning w-25 bg-warning bg-gradient rounded d-none'>
                <h3>Error</h3>
            </div>
        </main>
    </body>
</html>
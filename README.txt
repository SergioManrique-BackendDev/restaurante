El programa esta hecho en PHP 7.4, desarrollado en WAMP Server 3.2.6
Sin utilizacion de ningun framework, unicamente en vanilla PHP para facilitar el deploy
La conexion a base de datos se maneja por variables de ambiente ubicadas en el archivo enviroment.php dentro de la carpeta /php/
El sistema esta configurado para desplegar su propia base de datos al conectarse con una base de datos vacia
Al iniciar una conexion el sistema realiza una revision de sus tablas y crea cualquier tabla que podria faltar
La variable de ambiente 'DEBUG' indica si se genera data de prueba como la definida en los requerimentos tan pronto se inicia la conexion, ademas de 3 usuarios y un administrador
La cuenta de administrador por defecto creada por el DEBUG es admin:123
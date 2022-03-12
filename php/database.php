<?php

namespace database;
use PDO;

class database{

    private $_database_name;
    private $_database_user;
    private $_database_password;
    public $connection;

    public function __construct(){
        $this->_database_name = $_ENV['DATABASE_NAME'];
        $this->_database_user = $_ENV['DATABASE_USER'];
        $this->_database_password = $_ENV['DATABASE_PASSWORD'];
    }

    public function connect(){
        $this->connection = new PDO("mysql:host=localhost;dbname=" . $this->_database_name, $this->_database_user, $this->_database_password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->innitDatabase();
    }

    public function disconnect(){
        $this->connection = null;
    }

    /*
    Function de inicializacion de base de datos
    Esta funcion, al conectarse a la base de datos, hace una revision de las tablas presentes
    En caso de no encajar con el esquema del sistema, intenta crear las tablas necesarias para el funcionamiento normal
    La variable de ambiente DEBUG hace que la inicializacion tambien incluya la creacion de data de prueba, definida en los requerimentos del sistema
    */
    private function innitDatabase(){
        //revisar si base de datos esta vacia
        $query = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . $this->_database_name . "';";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll()[0][0];
        if($result !== 6){

            //crear tabla restaurantes
            $query = "CREATE TABLE IF NOT EXISTS restaurante (
                id INT NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL UNIQUE
            ) ENGINE=InnoDB;";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
    
            //crear tabla tipos de mesa
            $query = "CREATE TABLE IF NOT EXISTS tipo_mesa (
                id INT NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
                tipo VARCHAR(255) UNIQUE NOT NULL
            ) ENGINE=InnoDB;";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
    
            //crear tabla mesas
            $query = "CREATE TABLE IF NOT EXISTS mesa (
                numero INT NOT NULL,
                restaurante INT NOT NULL,
                tipo INT NOT NULL,
                PRIMARY KEY (numero, restaurante),
                FOREIGN KEY (restaurante) REFERENCES restaurante(id),
                FOREIGN KEY (tipo) REFERENCES tipo_mesa(id)
            ) ENGINE=InnoDB;";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
    
            //crear tabla usuarios
            //el telefono se maneja como varchar para evitar errores con codigos internacionales que inician con '+'
            $query = "CREATE TABLE IF NOT EXISTS usuario (
                rfc VARCHAR(255) NOT NULL UNIQUE PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                apellido VARCHAR(255) NOT NULL,
                fecha_nacimiento VARCHAR(255) NOT NULL,
                correo VARCHAR(255) NOT NULL,
                telefono VARCHAR(255) NOT NULL, 
                nickname VARCHAR(255)
            ) ENGINE=InnoDB;";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
    
            //crear tabla reservas
            $query = "CREATE TABLE IF NOT EXISTS reserva (
                folio INT NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
                numero_mesa INT NOT NULL,
                restaurante INT NOT NULL,
                tipo_mesa INT NOT NULL,
                usuario VARCHAR(255) NOT NULL,
                factura BOOLEAN DEFAULT 0, 
                hora_entrada VARCHAR(255) NOT NULL,
                fecha VARCHAR(255) NOT NULL,
                FOREIGN KEY (numero_mesa, restaurante) REFERENCES mesa(numero, restaurante),
                FOREIGN KEY (usuario) REFERENCES usuario(rfc),
                FOREIGN KEY (tipo_mesa) REFERENCES mesa(tipo)
            ) ENGINE=InnoDB;";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            //crear tabla administrador
            $query = "CREATE TABLE IF NOT EXISTS administrador (
                id INT NOT NULL UNIQUE AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB;";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            //Generar data de pruebas
            if($_ENV['DEBUG']){
                $this->crearDataPrueba($this);
            }
    
        }

    }

    public function createRestaurante($name){
        $query = "INSERT IGNORE INTO restaurante (nombre) VALUES (?)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$name]);
    }

    public function createTipoMesa($tipo){
        $query = "INSERT IGNORE INTO tipo_mesa (tipo) VALUES (?)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$tipo]);
    }


    public function createMesa($data){
        $values[0] = $data['numero'];

        //encontrar restaurante
        $query = "SELECT (id) FROM restaurante WHERE nombre = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$data['restaurante']]);
        $values[1] = $stmt->fetchAll()[0]['id'];

        //encontrar tipo
        $query = "SELECT (id) FROM tipo_mesa WHERE tipo = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$data['tipo_mesa']]);
        $values[2] = $stmt->fetchAll()[0]['id'];

        //crear mesa
        $query = "INSERT IGNORE INTO mesa (numero, restaurante, tipo) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute($values);
    }

    public function createUsuario($data){
        $result = $this->revisarRFC($data['RFC']);
        if(!$result){
            $query = "INSERT INTO usuario (nombre, apellido, fecha_nacimiento, correo, telefono, rfc, nickname) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            $stmt->execute([$data['Nombre'], $data['Apellido'], $data['Fecha_nacimiento'], $data['Correo'], $data['Telefono'], $data['RFC'], $data['Nickname']]);
        } else {
            return "Ya existe un usuario con ese RFC";
        }

    }

    public function getRestaurantes(){
        $query = "SELECT id, nombre FROM restaurante";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }    
    
    public function getTiposMesa(){
        $query = "SELECT id, tipo FROM tipo_mesa";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }  

    /*
    Convertir tiempo a decimal
    Convierte una hora de tipo string a un numero decimal equivalente, con el objetivo de hacer mas facil la comparacion de rangos de horas
    Ej. "14:30" -> "14.5"
    */ 
    private function timeToDecimal($time){
        $hour = explode(":", $time);
        $hour[1] = $hour[1] / 60;
        $hour[2] = $hour[0] + $hour[1];
        return $hour[2];
    }

    private function countMesasOfTipo($restaurante, $tipo_mesa){
        $query = "SELECT COUNT(numero) FROM mesa WHERE restaurante = ? AND tipo = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$restaurante, $tipo_mesa]);
        return $stmt->fetchAll()[0][0];
    }

    /*
    Para conseguir los horarios disponibles de cada tipo de mesa en cada restaurante, empezamos con un arreglo con todos los horarios disponibles por defecto
    Contamos la cantidad de mesas del tipo requerido en el restaurante especificado
    Despues se itera sobre las reservas que encajen con el perfil (en la fecha indicada, del tipo de mesa y en el restaurante solicitado)
    Revisamos el rango de horas en el que cada reserva ocupara esa mesa, llendo en incrementos de 15 minutos
    Si en algun horario la cantidad de mesas ocupadas equivale al numero total de mesas de ese tipo disponibles en el restaurante, el horario se quita
    El resultado es un arreglo en el que unicamente quedan los horarios en los que hay citas validas para ese tipo de mesa
    Nota: Los horarios terminan a las 21:30 a pesar de que el restaurante atiende hasta las 22:30 por el requerimento dado de que las citas sean de una hora
    En caso de agendar cita mas tarde de las 21:30 no se podra dar toda la hora requerida a la reserva antes de la hora de cierre
    */
    public function getHorariosDisponibles($restaurante, $tipo_mesa, $fecha){

        $countMesas = $this->countMesasOfTipo($restaurante, $tipo_mesa);

        //Horarios reservados
        $query = "SELECT hora_entrada, numero_mesa FROM reserva WHERE restaurante = ? AND tipo_mesa = ? AND fecha = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$restaurante, $tipo_mesa, $fecha]);
        $result = $stmt->fetchAll();

        //Horarios Iniciales
        $horariosBase = [
            '13:00', '13:15', '13:30', '13:45',
            '14:00', '14:15', '14:30', '14:45',
            '15:00', '15:15', '15:30', '15:45',
            '16:00', '16:15', '16:30', '16:45',
            '17:00', '17:15', '17:30', '17:45',
            '18:00', '18:15', '18:30', '18:45',
            '19:00', '19:15', '19:30', '19:45',
            '20:00', '20:15', '20:30', '20:45',
            '21:00', '21:15', '21:30'
        ];

        //Horarios ocupados
        foreach($horariosBase as $key => $hora){
            $reservasEstaHora = 0;
            $horaDecimal = $this->timeToDecimal($hora);
            foreach($result as $reserva){
                $reservaTimeDecimal = $this->timeToDecimal($reserva['hora_entrada']);
                $horaInicio = $reservaTimeDecimal - 0.75;
                $horaFin = $reservaTimeDecimal + 0.75;
                if($horaDecimal >= $horaInicio && $horaDecimal <= $horaFin){
                    $reservasEstaHora++;
                    if($reservasEstaHora == $countMesas){
                        unset($horariosBase[$key]);
                        break;
                    }
                }
            }
        }
        $horariosBase = array_values($horariosBase);
        return $horariosBase;
        
    }

    private function revisarRFC($RFC){
        $query = "SELECT nombre, apellido, correo, fecha_nacimiento, telefono, nickname FROM usuario WHERE rfc = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$RFC]);
        $result = $stmt->fetchAll();
        return $result;
    }

    public function crearReserva($dataJson){
        //Revisar informacion duplicada
        $data = json_decode($dataJson, false);
        $checkRFC = $this->revisarRFC($data->RFC);
        if(!empty($checkRFC)){
            if($data->Nickname != $checkRFC[0]['nickname']){
                return [
                    'success' => 0,
                    'data' => "Este RFC ya pertenece a otro nickname"
                ];
            }
        } else {
            //Crear usuario nuevo
            $newUser = [
                'Nombre' => $data->Nombre,
                'Apellido' => $data->Apellido,
                'Correo' => $data->Correo,
                'Fecha_nacimiento' => $data->Fecha_nacimiento,
                'Telefono' => $data->Telefono,
                'RFC' => $data->RFC,
                'Nickname' => $data->Nickname,
            ];
            $this->createUsuario($newUser);
        };

        //Encontrar mesa
        $numeroMesa = $this->findMesa($data->Restaurante, $data->Tipo_mesa, $data->Hora, $data->Fecha_reserva);
        //Crear reserva
        $query = "INSERT INTO reserva (numero_mesa, restaurante, tipo_mesa, usuario, factura, hora_entrada, fecha) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$numeroMesa, $data->Restaurante, $data->Tipo_mesa, $data->RFC, $data->Factura, $data->Hora, $data->Fecha_reserva]);
        return array_values([
            'success' => 1,
            'data' => "Reservacion Creada.<br> Folio numero: " . $this->connection->lastInsertId()
        ]);

    }

    /*
    Para encontrar la mesa que hay que asignar a la reserva, se hace una revision de las mesas disponibles de ese tipo en ese restaurante
    Para cada mesa que encaje en el perfil, revisamos sus reservas para comprobar si estara disponible en el horario determinado
    Si no esta disponible, pasamos a la siguiente mesa hasta que encontremos la mesa libre en este horario
    Nota: Esta funcion unicamente se llama basandose en los horarios devueltos por getHorariosDisponibles(),
    por lo que se asume que ya se hizo la revision de que almenos una mesa del tipo esta disponible en el horario pedido
    esta funcion unicamente busca el numero de mesa exacto para asignarla a la reserva
    */
    public function findMesa($restaurante, $tipo, $hora, $fecha){
        //encontrar todas las mesas que encajan en el tipo
        $query = "SELECT numero FROM mesa WHERE restaurante = ? AND tipo = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$restaurante, $tipo]);
        $result = $stmt->fetchAll();

        //revisar todas las mesas encontradas
        $numeroMesa = null;
        foreach($result as $mesa){
            //revisar todas las reservaciones de esa mesa
            $query = "SELECT folio, hora_entrada FROM reserva WHERE numero_mesa = ? AND restaurante = ? AND fecha = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->execute([$mesa['numero'], $restaurante, $fecha]);
            $result = $stmt->fetchAll();
            $mesaViable = true;
            foreach($result as $reserva){
                $horaRevision = $this->timeToDecimal($hora);
                $horaInicioReserva = ($this->timeToDecimal($reserva['hora_entrada'])) - 1;
                $horaFinReserva = ($this->timeToDecimal($reserva['hora_entrada'])) + 1;
                if($horaRevision > $horaInicioReserva && $horaRevision < $horaFinReserva){
                    $mesaViable = false;
                }
            }
            if($mesaViable){
                $numeroMesa =  $mesa['numero'];
                break;
            }
        }
        return $numeroMesa;
    }

    public function createAdmin($username, $password){
        //Revisar username duplicado
        $query = "SELECT id FROM administrador WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$username]);
        $result = $stmt->fetchAll();
        if(!empty($result)){
            return "Nombre de Usuario ya existe";
        } else {
            //Generar password
            $salt = bin2hex(random_bytes(8));
            $passwordHash = md5($salt . $password);
            $passwordCode = $salt . ":" . $passwordHash;
            //Crear administrador
            $query = "INSERT INTO administrador (username, password) values (?, ?)";
            $stmt = $this->connection->prepare($query);
            $stmt->execute([$username, $passwordCode]);
        }
    }

    public function logIn($username, $password){
        //Get user with username
        $query = "SELECT id, username, password FROM administrador WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$username]);
        $result = $stmt->fetchAll();
        if(empty($result)){
            return false;
        } else {
            $passwordCode = $result[0]['password'];
            $passwordArray = explode(":", $passwordCode);
            $salt = $passwordArray[0];
            $passwordHash = $passwordArray[1];
            $passwordIngresado = md5($salt . $password);
            if($passwordIngresado == $passwordHash){
                return true;
            } else {
                return false;
            }
        }
    }

    public function buscarReserva($data, $metodo){
        $query = "SELECT * FROM reserva WHERE ";
        if($metodo == 1){
            $query .= "folio";
        } else {
            $query .= "usuario";
        }
        $query .= " = ?";
        if($metodo == 2){
            //En caso de ser busqueda por RFC, ordenamos por ultima fecha para recibir la ultima reserva
            $query .= " ORDER BY fecha DESC";
        }
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$data]);
        $result = $stmt->fetchAll();
        //get usuario de la reserva
        $query = "SELECT * FROM usuario WHERE rfc = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$result[0]['usuario']]);
        array_push($result, $stmt->fetchAll()[0]);
        return [
            'success' => 1,
            'data' => $result
        ];
    }

    public function editarReserva($dataJson){
        $data = json_decode($dataJson, false);
        $query = "UPDATE reserva SET fecha = ?, restaurante = ?, tipo_mesa = ?, hora_entrada = ?, factura = ? WHERE folio = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$data->Fecha_reserva, $data->Restaurante, $data->Tipo_mesa, $data->Hora, $data->Factura, $data->Folio]);
        return [
            'success' => 1,
            'data' => "Reserva editada exitosamente"
        ];
    }

    /*
    Creacion de data de prueba
    La mayor parte de esta data esta definida en los requerimentos del sistema
    Estos son: 3 restaurantes, 3 tipos de mesa, 15 mesas por restaurante, y los horarios de 13:00 a 22:30
    Otra data de prueba agregada por propositos demostrativos son: 3 usuarios y 1 administrador
    */
    private function crearDataPrueba($db){    
        //restaurantes
        $restaurantes = [
            'Norte',
            'Centro',
            'Pensiones'
        ];
        foreach($restaurantes as $restaurante){
            $db->createRestaurante($restaurante);
        }
        
        //tipos de mesas
        $tipos_mesa = [
            'Ventana',
            'Pasillo',
            'Jardin'
        ];
        foreach($tipos_mesa as $tipo_mesa){
            $db->createTipoMesa($tipo_mesa);
        }
    
        //mesas
        $mesas = [
            'Norte' => [
                [1, 'Ventana'],
                [2, 'Ventana'],
                [3, 'Ventana'],
                [4, 'Ventana'],
                [5, 'Ventana'],
                [6, 'Pasillo'],
                [7, 'Pasillo'],
                [8, 'Pasillo'],
                [9, 'Pasillo'],
                [10, 'Pasillo'],
                [11, 'Pasillo'],
                [12, 'Pasillo'],
                [13, 'Jardin'],
                [14, 'Jardin'],
                [15, 'Jardin'],
            ],
            'Centro' => [
                [1, 'Ventana'],
                [2, 'Ventana'],
                [3, 'Ventana'],
                [4, 'Ventana'],
                [5, 'Ventana'],
                [6, 'Ventana'],
                [7, 'Ventana'],
                [8, 'Pasillo'],
                [9, 'Pasillo'],
                [10, 'Pasillo'],
                [11, 'Pasillo'],
                [12, 'Jardin'],
                [13, 'Jardin'],
                [14, 'Jardin'],
                [15, 'Jardin'],
            ],
            'Pensiones' => [
                [1, 'Jardin'],
                [2, 'Jardin'],
                [3, 'Jardin'],
                [4, 'Pasillo'],
                [5, 'Pasillo'],
                [6, 'Pasillo'],
                [7, 'Pasillo'],
                [8, 'Pasillo'],
                [9, 'Pasillo'],
                [10, 'Jardin'],
                [11, 'Jardin'],
                [12, 'Jardin'],
                [13, 'Jardin'],
                [14, 'Jardin'],
                [15, 'Jardin'],
            ]
        ];
        
        foreach($mesas as $nombre => $restaurante){
            foreach($restaurante as $mesa){
                $data = [
                    'numero' => $mesa[0],
                    'restaurante' => $nombre,
                    'tipo_mesa' => $mesa[1]
                ];
                $db->createMesa($data);
            }
        }
    
        $usuarios = [
            [
                'Nombre' => 'Lorem',
                'Apellido' => 'Ipsum',
                'Fecha_nacimiento' => '1990-01-01',
                'Correo' => 'emailOfLorem@example.com',
                'Telefono' => '9999999991',
                'RFC' => 'IZ3IJQKA3299U7H',
                'Nickname' => 'Lorip'
            ],
            [
                'Nombre' => 'Dolor',
                'Apellido' => 'Sit',
                'Fecha_nacimiento' => '1995-05-05',
                'Correo' => 'emailOfDolor@example.com',
                'Telefono' => '9999999992',
                'RFC' => '4MPNYWX3L46IACU',
                'Nickname' => 'Dolsi'
            ],
            [
                'Nombre' => 'Amet',
                'Apellido' => 'Consectetur',
                'Fecha_nacimiento' => '1990-01-01',
                'Correo' => 'emailOfAmet@example.com',
                'Telefono' => '9999999993',
                'RFC' => 'DBQHMP7X69BMAG3',
                'Nickname' => 'Ameco'
            ],
        ];
    
        foreach($usuarios as $usuario){
            $db->createUsuario($usuario);
        }
    
        $db->createAdmin("admin", "123");
    }


}
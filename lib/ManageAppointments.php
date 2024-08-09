<?php
    header('Content-Type: application/json');
    date_default_timezone_set('Europe/Madrid');
    use \DateTime;
    include 'Conexion.php';
    // horario inicio de visitas
    const FIRST_VISIT_TIME = '10:00:00';
    // horario fin de visitas - se establece una hora antes del cierre para permitir que se terminen las visitas dentro del horario
    const LAST_VISIT_TIME = '22:00:00';
    

    if (isset($pdo)) {

        $lastAppointmentRegistered = getLastAppointmentRegistered($pdo); //obtenemos la ultima visita del dia en curso
        if($lastAppointmentRegistered){
            $nextAppointmentDate = getNextAvailableDate(new DateTime($lastAppointmentRegistered['fecha'])); //getNextAvailableDate(new DateTime($lastAppointmentRegistered['fecha']));
            insertNewAppointment($pdo,$nextAppointmentDate);
        }else{
            $nextAppointmentDate = getNextAvailableDate(new DateTime());//getNextAvailableDate();
            insertNewAppointment($pdo,$nextAppointmentDate);
        }
            
    }else{  
        $data = [
            'success' => false,
            'message' => 'No se pudo establecer la conexión con la base de datos',
            'status' => 500
        ];
        echo json_encode($data);
        return;      
        
    }

    /*function getNextAvailableDate(?DateTime $fromDate=null){
        
        if(!$fromDate){ // 
            $now = new DateTime();
            $time = $now->format('G:i:s');
            if(!isInIntervalVisit($now)){
                // en caso de que la visita este fuera del intervalo disponible del dia
                
                /*$nextDayAppointment = new DateTIme();
                $nextDayAppointment->modify('+1 day');
                $firstAppointmentDay = new DateTime($nextDayAppointment->format('Y-m-d') . ' ' . FIRST_VISIT_TIME);

                return $firstAppointmentDay;*/

                /*return obtenerProximaVisitaDisponible($now);


            }else{
                $nextVisit = obtenerProximaVisitaDisponible($now);
                return $nextVisit;
            }
        }else{
            return obtenerProximaVisitaDisponible($fromDate);
        }
     
    }*/

    function insertNewAppointment($pdo,$appointmentDate,$intentos = 15){
        if ($intentos <= 0) {
        $data = [
            'success' => false,
            'message' => 'No se pudo reservar la cita tras múltiples intentos',
            'status' => 500
        ];
        echo json_encode($data);
        return;
         }
        $dni = isset($_POST['dni']) ? $_POST['dni'] : '';    
        $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
        $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $tipo_cita = isset($_POST['tipo_cita']) ? $_POST['tipo_cita'] : '';
        $dateToBook = $appointmentDate->format('Y-m-d H:i:s');
        
        $sql = 'INSERT INTO citas (`nombre`, `dni`, `telefono`, `email`, `tipo_cita`,`fecha`) 
            VALUES (:nombre, :dni, :telefono, :email, :tipo_cita, :fecha)';

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':tipo_cita', $tipo_cita);
        $stmt->bindParam(':fecha', $dateToBook);        
        try {

            $result = $stmt->execute();
            
            $data = [
                'success' => $result,
                'message' => 'La cita se ha reservado correctamente',
                'status' => 200
            ];  
            
            echo json_encode($data);
            
        }catch(PDOException $e){
            if ($e->getCode() === '23000') { 
            // En caso de colision con otra operacion se busca la próxima fecha disponible 
            // y se reintenta un numero de veces prudencial antes de abortar
            $nuevaFecha = obtenerProximaVisitaDisponible($appointmentDate);
            insertNewAppointment($pdo, $nuevaFecha, $intentos - 1);
            } else {
                $data = [
                    'success' => false,
                    'message' => 'Error al reservar la cita: ' . $e->getMessage(),
                    'status' => 500
                ];
                echo json_encode($data);
            }

        }
        
    }

    function isPreviousToDailyopen(DateTime $fechaVisita){        
        $inicioFranja = new DateTime($fechaVisita->format('Y-m-d') . ' ' . FIRST_VISIT_TIME);
        if ($fechaVisita <= $inicioFranja)
            return true;
        
        return false;

    }

    function getNextAvailableDate(DateTime $book){
                $book->modify('+1 hour');
                $book->setTime($book->format('H'), 0, 0);
                if(!isInIntervalVisit($book)){
                    !isPreviousToDailyopen($book) ?                        
                        $book->modify('+1 day') : null; 
                    return new DateTime($book->format('Y-m-d') . ' ' . FIRST_VISIT_TIME);
                }else{
                    //$book->setTime($book->format('H'), 0, 0);
                    return $book;
                }
    }

    function isInIntervalVisit(DateTime $fechaVisita){

        $inicioFranja = new DateTime($fechaVisita->format('Y-m-d') . ' ' . FIRST_VISIT_TIME);
        $finFranja = new DateTime($fechaVisita->format('Y-m-d') . ' ' . LAST_VISIT_TIME);

        if ($fechaVisita >= $inicioFranja && $fechaVisita <= $finFranja) {
                return true;
        }else{
            return false;
        }
    }    
    
    function getLastAppointmentRegistered($pdo){
        $stmt = $pdo->prepare('SELECT * FROM citas WHERE fecha >= CURDATE() ORDER BY fecha DESC LIMIT 1');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $pdo = null;
        return $result;
    }



<?php 
    header('Content-Type: application/json');

    include 'Conexion.php';
    if (isset($pdo)) {
            $dni = isset($_POST['dni']) ? $_POST['dni'] : '';
            if($dni){
                checkDni($pdo,$dni);
            }
    }


    function checkDni($pdo,$dni){
        $ee=$dni;
        $stmt = $pdo->prepare('SELECT * FROM citas WHERE dni LIKE :dni');
        $stmt->execute(['dni' => '%' . $dni . '%']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo = null;
        
        if($result){            
            $response = [
                'status' => true,
                'existUserData' => true,
            ];                
        }else{
            $response = [
                'status' => true,
                'existUserData' => false,
            ];
        }
        
        echo json_encode($response);
    }
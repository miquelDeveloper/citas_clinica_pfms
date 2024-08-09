<?php
    $host = '46.183.112.70';
    $dbname = 'deab3jas_citas_clinicas';
    $username = 'deab3jas_practica';
    $password = 'V83mfnp9fJ8uPz3Y';
        try {
            // Crear una nueva instancia de PDO
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

            // Configurar el manejo de errores
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Configurar el modo de recuperación de datos
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;

        } catch (PDOException $e) {
            return false;
        }
?>
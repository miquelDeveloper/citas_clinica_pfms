<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de citas</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="js/script.js"></script>
</head>
<body>   
    <form action="lib/ManageAppointments.php" method="POST" id="formulario_citas">
        <div class="container mt-5">
        <h1 class="mb-4">Formulario de Citas</h1>
        <form>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" placeholder="Introduce tu nombre" required>
            </div>
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" class="form-control" id="dni" placeholder="Introduce tu DNI" maxlength="9" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" class="form-control" id="telefono" placeholder="Introduce tu teléfono" maxlength="9" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" placeholder="Introduce tu email" required>
            </div>
            <div id="error_email" class="alert alert-danger" role="alert" style="display:none;"></div>
            <div class="form-group">
                <label for="tipo_cita">Tipo de cita:</label>
                <select class="form-control" id="tipo_cita" required>
                    <option value="primera_consulta">Primera consulta</option>
                </select>
            </div>
            <button id="submitButton" type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
    </form>
</body>
</html>
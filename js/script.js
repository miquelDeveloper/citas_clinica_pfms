document.addEventListener('DOMContentLoaded', function () {

    // capturamos los campos
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    let nombreInput = document.getElementById('nombre');
    let dniInput = document.getElementById('dni');
    let telefonoInput = document.getElementById('telefono');
    let emailInput = document.getElementById("email");

    let form = document.getElementById("formulario_citas");
    let sendButton = document.getElementById('submitButton');
    let error_email = document.getElementById("error_email");

    // asignamos eventos
    emailInput.addEventListener('keyup', checkEmailFormat, false);

    form.addEventListener('submit', veryfyData, false);

    dniInput.addEventListener('input', function (event) {
        let dniUsuario = event.target.value;
        if (dniUsuario.length === 9) {
            checkIfUserHasPrevData(dniUsuario);
        }
    });

    // limpieza de formulario posterior al persistido de datos
    function cleanForm() {
        nombreInput.value = '';
        dniInput.value = '';
        telefonoInput.value = '';
        emailInput.value = '';
        removeOptionToSelect();
    }  

    // verificación final de datos antes de enviar los datos para su persistencia en base de datos
    function veryfyData(e) {
        e.preventDefault();

        let formData = capturarDatosFormulario();
        if (formData && checkEmailFormat())
            sendFormData(formData);
    }
    
    // verificacion de email y mostrado de error en el formato al usuario
    function checkEmailFormat() {       
        if (regex.test(document.getElementById("email").value)) {
            error_email.style.display = 'none';
            error_email.innerHTML = '';
            sendButton.disabled = false;
            return true;
        } else {
            error_email.style.display = 'block';
            error_email.innerHTML = 'El correo electrónico no cumple los requisitos';
            sendButton.disabled = true;
            return false;
        }
    }
    // carga de datos del formulario previa a su verificación y persistencia
    function capturarDatosFormulario() {
        let nombre = document.getElementById("nombre").value;
        let dni = document.getElementById("dni").value;
        let telefono = document.getElementById("telefono").value;
        let email = document.getElementById("email").value;
        let tipo_cita = document.getElementById("tipo_cita").value;


        if (nombre != '' &&
            dni != '' &&
            telefono != '' &&
            checkEmailFormat() &&
            tipo_cita != '') {

            let datos = 'nombre=' + nombre.toLowerCase().trim();
            datos += '&dni=' + dni.toUpperCase().trim();
            datos += '&telefono=' + telefono.trim();
            datos += '&email=' + email.toLowerCase().trim();
            datos += '&tipo_cita=' + tipo_cita.toUpperCase().trim();

            return datos;

        } else {
            return false;
        }
    }

    // añadir opcion revision al selector de tipo de visita
    function addOptionReviewToSelect() {
        let selectorVisita = document.getElementById('tipo_cita');
        let newOption = new Option('Revision', 'revision', true, false);
        newOption.id = 'cita_revision';
        selectorVisita.append(newOption);
    }
    
    // quitar opcion revision al selector de tipo de visita
    function removeOptionToSelect() {
        let selectorVisita = document.getElementById('cita_revision');
        selectorVisita ?
            selectorVisita.remove(selectorVisita) : null;
    }    

    // envio de datos para su persistencia
    function sendFormData(formData) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/lib/ManageAppointments.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    console.log(data);
                    if (data.error) {
                        console.error('Error:', data.error);
                    } else {
                        if (data.status === 200)
                            cleanForm();

                            alert(data.message);
                    }
                } catch (e) {
                    console.error('Error al analizar JSON:', e);
                }
            } else {
                console.error('Error en la solicitud:', xhr.statusText);
            }
        };
        xhr.onerror = function () {
            console.error('Error en la solicitud AJAX');
        };
        xhr.send(formData);
    }

    // comprobacion de datos existente del usuario en la base de datos
    // añadimos o quitamos la opción de revisión para el tipo de cita en funcion del DNI
    function checkIfUserHasPrevData(dniUsuario) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/lib/CheckUser.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    console.log(data);
                    if (data.error) {
                        console.error('Error:', data.error);
                    } else {
                        if (data.existUserData)
                            addOptionReviewToSelect()  
                        else
                            removeOptionToSelect() 

                    }
                } catch (e) {
                    console.error('Error al analizar JSON:', e);
                }
            } else {
                console.error('Error en la solicitud:', xhr.statusText);
            }
        };
        xhr.onerror = function () {
            console.error('Error en la solicitud AJAX');
        };
        var params = 'dni=' + encodeURIComponent(dniUsuario);

        xhr.send(params);
    }
});
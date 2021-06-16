// GLOBAL VARIABLES
var image = ""; // => GUARDA LA IMAGEN SELECCIONADA POR EL USUARIO EN BASE 64

getAllUsers(); // INVOCAMOS FUNCIÓN PARA CARGAR LOS REGISTROS

// FUNCIÓN DE REGISTRO/ACTUALIZACIÓN DE USUARIO
$(document).ready(function () {

    showElement("loading",false);
    showElement("contentPage",true);
    $('#dataForm').submit(function (e) {
        e.preventDefault();
        var data =
        {
            "Nombre": document.getElementById("Nombre").value,
            "Apellidos": document.getElementById("Apellidos").value,
            "Usuario": document.getElementById("Usuario").value,
            "Password": document.getElementById("Password").value,
            "idAction": document.getElementById("UserId").value == 0 ? 1 : 4,
            "UsuarioId": document.getElementById("UserId").value,
            "UserPhoto": image
        };

        $.ajax({
            type: "POST",
            url: '../Php/UserServices.php',
            data: data,
            success: function (response) {

                var data = JSON.parse(response);

                if (data.Success) {
                    swal("Excelente!", data.Message, "success").then(() => {
                        cleanForm();
                        getAllUsers();
                    });
                } else {
                    swal("Algo salió mal!", data.Message, "error");
                }
            }
        });
    });
});

// FUNCIÓN DE CARGA DE USUARIOS
function getAllUsers() {
    $.ajax({
        type: "GET",
        url: '../Php/UserServices.php',
        success: function (response) {

            var data = JSON.parse(response);
            if (data.Success) {
                $(".rowData").remove();
                if (data.Data.length > 0) {
                    showElement("contBtnGenerateReport", true)
                } else {
                    showElement("contBtnGenerateReport", false)
                }
                for (var dato in data.Data) {
                    $("#tableUsers>tbody").append(
                        `<tr class="rowData">
                            <td>${data.Data[dato].Nombre}</td>
                            <td>${data.Data[dato].Apellidos}</td>
                            <td>${data.Data[dato].Usuario}</td>
                            <td>
                            <button class="btn btn-warning" onclick="getUserInfo(${data.Data[dato].UsuarioId})">Editar</button>                        
                            <button class="btn btn-danger" onclick="deleteUser(${data.Data[dato].UsuarioId})">Eliminar</button>
                            </td>
                            </tr>`
                    );
                }
            } else {
                swal("Algo salió mal!", data.Message, "error");
            }
        }
    });
}

// FUNCIÓN PARA ELIMINAR UN USUARIO 
function deleteUser(id) {

    $.ajax({
        type: "POST",
        url: '../Php/UserServices.php',
        data: { "userId": id, "idAction": 2 },
        success: function (response) {

            var data = JSON.parse(response);

            if (data.Success) {
                swal("Excelente!", "El usuario ha sido eliminado!", "success").then(() => {
                    var idUserEdit = document.getElementById("UserId").value;
                    if (idUserEdit != 0 && idUserEdit == id) {
                        cleanForm();
                    }
                    getAllUsers();
                });
            } else {
                swal("Algo salió mal!", data.Message, "error");
            }
        }
    });
}

// FUNCIÓN PARA OBTENER DATOS DE UN USUARIO
function getUserInfo(id) {
    $.ajax({
        type: "POST",
        url: '../Php/UserServices.php',
        data: { "userId": id, "idAction": 3 },
        success: function (response) {

            var data = JSON.parse(response);
            console.log(data);
            if (data.Success) {
                chargueData(data.Data[0].UsuarioId,
                    data.Data[0].Nombre,
                    data.Data[0].Apellidos,
                    data.Data[0].Usuario,
                    data.Data[0].FotoPath);
            } else {
                swal("Algo salió mal!", data.Message, "error");
            }
        }
    });
}

// FUNCION DE CARGUE DE DATOS EN FORMULARIO CUANDO SE EDITA UN USUARIO
function chargueData(id, nombre, apellidos, usuario, fotoPath) {

    // ASIGNAMOS VALORES A CAMPOS DE FORMULARIO
    document.getElementById("UserId").value = id;
    document.getElementById("Nombre").value = nombre;
    document.getElementById("Apellidos").value = apellidos;
    document.getElementById("Usuario").value = usuario;

    // CREAMOS ETQUETA DE IMAGEN
    if (fotoPath == undefined || fotoPath == "") {

        var html = `<h3 class="ImageSelected text-warning">This user haven't a photo.</h1>`;
    } else {

        var html = `<img src='${fotoPath}' class="ImageSelected" width="300"/>`;
    }
    // ASIGNAMOS ETIQUETA A #DIVPHOTO
    document.getElementById("DivPhoto").innerHTML = html;

    // OCULTAMOS TABLA Y MOSTRAMOS IMAGEN  
    showElement("contBtnGenerateReport", false);
    showElement("contBtnUploadDoc", false);
    showElement("tableUsers", false);
    showElement("DivPhotoContainer", true);


}

// FUNCIÓN DE LIMPIEZA DE FORMULARIO
function cleanForm() {

    // REMOVEMOS VALORES DE CAMPOS EN EL FORMUARIO
    document.getElementById("UserId").value = '';
    document.getElementById("Nombre").value = '';
    document.getElementById("Apellidos").value = '';
    document.getElementById("Usuario").value = '';
    document.getElementById("Password").value = '';
    document.getElementById("UserPhoto").value = null;

    // REMOVEMOS LA IMAGEN AGREGADA A #DIVPHOTO
    $(".ImageSelected").remove();
    $("#lblUserPhoto").text("Seleccionar...");

    // MOSTRAMOS TABLA OCULTAMOS IMAGEN    
    showElement("contBtnUploadDoc", true);
    showElement("contBtnGenerateReport", true);
    showElement("tableUsers", true);
    showElement("DivPhotoContainer", false);

}

// FUNCION PARA CAPTURAR LA IMAGEN SUBIDA POR EL USUARIO
function captureImage() {

    // VALIDAMOS SI HAY ARCHIVOS SELECCIONADOS
    var size = document.getElementById("UserPhoto").files.length;

    if (size > 0) {
        // CONVERTIMOS IMAGEN EN BASE 64 PARA ENVIAR
        var photo = document.getElementById("UserPhoto").files[0];

        getBase64FromFile(photo);
        showElement("contBtnGenerateReport", false);
        showElement("tableUsers", false);
        showElement("DivPhotoContainer", true);
        $("#lblUserPhoto").text(document.getElementById("UserPhoto").files[0].name);

    } else {
        image = "";
        $("#lblUserPhoto").text("Seleccionar...");        
        showElement("contBtnUploadDoc", true);
        showElement("contBtnGenerateReport", true);
        showElement("tableUsers", true);
        showElement("DivPhotoContainer", false);
    }
}

// FUNCIÓN PARA CONVERTIR A BASE 64
function getBase64FromFile(file) {
    var fileReader = new FileReader();

    // AGREGAMOS EVENTO DE CARGA
    fileReader.addEventListener('load', function (evt) {
        image = fileReader.result;
        // CREAMOS ETQUETA DE IMAGEN
        var html = `<img src='${image}' class="ImageSelected" width="300"/>`;

        // ASIGNAMOS ETIQUETA A #DIVPHOTO
        document.getElementById("DivPhoto").innerHTML = html;

    });
    fileReader.readAsDataURL(file);
}

// FUNCIÓN PARA OCULTAR/MOSTRAR ELEMENTO
function showElement(elementId, show) {
    document.getElementById(elementId).style.display = show ? "" : "none";
}

// FUNCION PARA CARGAR EXCELENTE
function chargueDoc() {
    $("#FormDoc").trigger("submit");
}


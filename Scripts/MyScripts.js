// GLOBAL VARIABLES
var image;
getAllUsers();
// FUNCIÓN DE REGISTRO/ACTUALIZACIÓN DE USUARIO
$(document).ready(function () {

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
                    data.Data[0].Usuario, data.Data[0].FotoPath);
            } else {
                swal("Algo salió mal!", data.Message, "error");
            }
        }
    });
}

// FUNCION DE CARGUE DE DATOS EN FORMULARIO
function chargueData(id, nombre, apellidos, usuario, fotoPath) {
    document.getElementById("UserId").value = id;
    document.getElementById("Nombre").value = nombre;
    document.getElementById("Apellidos").value = apellidos;
    document.getElementById("Usuario").value = usuario;
    var html = `<img src='${fotoPath}' class="ImageSelected" width="30%"/>`;
    document.getElementById("DivPhoto").innerHTML = html;

}

// FUNCIÓN DE LIMPIEZA DE FORMULARIO
function cleanForm() {
    document.getElementById("UserId").value = '';
    document.getElementById("Nombre").value = '';
    document.getElementById("Apellidos").value = '';
    document.getElementById("Usuario").value = '';
    document.getElementById("Password").value = '';
    document.getElementById("UserPhoto").value = null;
    $(".ImageSelected").remove();

}

// FUNCION PARA CAPTURAR LA IMAGEN SUBIDA POR EL USUARIO
function captureImage() {
    var size = document.getElementById("UserPhoto").files.length;
    console.log("Entro con " + size + " imagenes");
    if (size > 0) {
        var photo = document.getElementById("UserPhoto").files[0];
        getBase64FromFile(photo);
    }
}

// FUNCIÓN PARA CONVERTIR A BASE 64
function getBase64FromFile(file) {
    var fileReader = new FileReader();
    fileReader.addEventListener('load', function (evt) {
        image = fileReader.result;
        var html = `<img src='${image}' class="ImageSelected" width="30%"/>`;
        document.getElementById("DivPhoto").innerHTML = html;
        console.log("Asignado");
    });
    fileReader.readAsDataURL(file);
}

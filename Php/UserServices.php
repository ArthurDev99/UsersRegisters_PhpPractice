<?php
// INCLUSIÓN DE CLASES
include "Functions.php";

// INSTANCIAS DE CLASES
$functions = new MyFunctions();

// VALIDAMOS INDICE DE ACCION
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST["idAction"])) {
        // SERVICIOS
        switch ($_POST["idAction"]) {

            case 1: // INSERTAR USUARIO
                {
                    // CAPTURAMOS DATOS DE FORMULARIO
                    $nombre = $_POST["Nombre"];
                    $apellidos = $_POST["Apellidos"];
                    $usuario = $_POST["Usuario"];
                    $contrasena = $_POST["Password"];
                    $userPhoto = $_POST["UserPhoto"];

                    $userData = array(
                        "Nombre" => $nombre,
                        "Apellidos" => $apellidos,
                        "Usuario" => $usuario,
                        "Password" => $contrasena,
                        "UserPhoto" => $userPhoto
                    );

                    $response = $functions->insertNewUser($userData);
                    echo $response;
                }
                break;
            case 2: // ELIMINAR USUARIO
                {
                    $userId = $_POST["userId"];
                    if ($userId) {
                        echo $functions->deleteUser($userId);
                    } else {
                        return $functions->createResponse(false, "ID de usuario es nula", null);
                    }
                }
                break;
            case 3: // OBTENER USUARIO
                {
                    $userId = $_POST["userId"];
                    if (isset($userId)) {
                        echo $functions->getUserById($userId);
                    } else {
                        echo $functions->createResponse($userId, "La ID de registro es nula", null);
                    }
                }
                break;

            case 4: // ACTUALIZAR USUARIO
                {
                    // CAPTURAMOS DATOS DE FORMULARIO
                    $nombre = $_POST["Nombre"];
                    $apellidos = $_POST["Apellidos"];
                    $usuario = $_POST["Usuario"];
                    $contrasena = $_POST["Password"];
                    $userId = $_POST["UsuarioId"];

                    $userData = array(
                        "Nombre" => $nombre,
                        "Apellidos" => $apellidos,
                        "Usuario" => $usuario,
                        "Password" => $contrasena,
                        "UsuarioId" => $userId
                    );

                    $functions = new MyFunctions();
                    $response = $functions->updateUser($userData);
                    echo $response;
                }
                break;
        }
    } else {
        return $functions->createResponse(false, "ID de acción es nula", null);
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo $functions->getAllUsers();
} else {
    echo $functions->createResponse(false, "La solicitud enviada usa un verbo diferente a GET y POST.", null);
}

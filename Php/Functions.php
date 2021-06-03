<?php

include "Conexion.php";

class MyFunctions
{

    private $baseDirectory = "../Images/UserImages/"; // GUARDA EL DIRECTORIO DE LAS IMÁGENES DE LOS USUARIOS

    // FUNCIÓN PARA OBTENER TODOS LOS USUARIOS
    function getAllUsers()
    {
        try {

            $conection = new Conexion();
            $sql = $conection->prepare("SELECT * FROM usuarios");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_ASSOC);


            $tableRegisters = $sql->fetchAll();

            return $this->createResponse(true, "Se han encontrado registros", $tableRegisters);
        } catch (Exception $e) {
            return $this->createResponse(false, "Error: " . $e, null);
        }
    }

    // FUNCIÓN PARA INGRESAR USUARIOS
    function insertNewUser($userData)
    {

        try {

            // VALIDAMOS DATOS
            if (
                $this->validateParam($userData["Nombre"])
                && $this->validateParam($userData["Apellidos"])
                && $this->validateParam($userData["Usuario"])
                && $this->validateParam($userData["Password"]
                    && $this->validateParam($userData["UserPhoto"]))
            ) {

                $DBConection = new Conexion();
                // CREAMOS EL QUERY
                $sql = "INSERT INTO Usuarios (Nombre, Apellidos, Usuario, Password) values (:Nombre, :Apellidos, :Usuario, :Password)";
                $stm = $DBConection->prepare($sql);

                // ASIGNAMOS VALORES
                $stm->bindValue(":Nombre", $userData["Nombre"]);
                $stm->bindValue(":Apellidos", $userData["Apellidos"]);
                $stm->bindValue(":Usuario", $userData["Usuario"]);
                $stm->bindValue(":Password", $userData["Password"]);

                // VALIDAMOS RESPUESTA
                if ($stm->execute() > 0) {
                    // GUARDAMOS IMAGEN DE USUARIO
                    $saveImage = $this->saveImage($userData["UserPhoto"], uniqid(), $DBConection->lastInsertId());
                    if (!$saveImage) {
                        $DBConection->RollBack();
                    }
                    return $this->createResponse(true, "Registro exitoso!", null);
                } else {

                    $DBConection->RollBack();
                    return $this->createResponse(false, "Error al realizar el registro.", null);
                }
            } else {
                return $this->createResponse(false, "No pueden haber datos vacíos.", null);
            }
        } catch (Exception $e) {
            // RETORNAMOS EXCEPCIÓN
            return $this->createResponse(false, "Error: " . $e->getMessage(), null);
        }
    }

    // FUNCIÓN PARA OBTENER EL DETALLE DE UN USUARIO
    function getUserById($id)
    {
        try {
            // VALIDAMOS PARÁMETRO
            if (isset($id)) {
                $DBConection = new Conexion();

                // CREAMOS EL QUERY
                $sql = "SELECT U.UsuarioId, U.Nombre, U.Apellidos, U.Usuario, F.FotoPath, F.FotoId FROM Usuarios U inner join FotoPerfil F on U.UsuarioId = F.UsuarioId WHERE U.UsuarioId = :id";
                $stm = $DBConection->prepare($sql);
                $stm->bindValue(":id", $id);

                if ($stm->execute() > 0) {
                    $stm->setFetchMode(PDO::FETCH_ASSOC);
                    $userData = $stm->fetchAll();

                    return $this->createResponse(true, "Usuario encontrado", $userData);
                } else {
                    return $this->createResponse(false, "Usuario no encontrado", null);
                }
            } else {
                return $this->createResponse(false, "Identificador de registro nulo.", null);
            }
        } catch (Exception $e) {
            return $this->createResponse(false, "Error: " . $e, null);
        }
    }

    // FUNCIÓN PARA ACTUALIZAR UN USUARIO
    function updateUser($userData)
    {
        try {
            if ($userData) {
                $DBConection = new Conexion();

                // VALIDAMOS DATOS
                if (
                    $this->validateParam($userData["Nombre"])
                    && $this->validateParam($userData["Apellidos"])
                    && $this->validateParam($userData["Usuario"])
                    && $this->validateParam($userData["Password"])
                    && $this->validateParam($userData["UsuarioId"])
                ) {
                    // CREAMOS EL QUERY
                    $sql = "UPDATE Usuarios SET Nombre = :Nombre, Apellidos = :Apellidos, Usuario = :Usuario, Password = :Password WHERE UsuarioId = :UsuarioId";
                    $stm = $DBConection->prepare($sql);

                    // ASIGNAMOS VALORES
                    $stm->bindValue(":Nombre", $userData["Nombre"]);
                    $stm->bindValue(":Apellidos", $userData["Apellidos"]);
                    $stm->bindValue(":Usuario", $userData["Usuario"]);
                    $stm->bindValue(":Password", $userData["Password"]);
                    $stm->bindValue(":UsuarioId", $userData["UsuarioId"]);

                    // VALIDAMOS SI SE ENVIÓ UNA IMAGEN PARA REALIZAR LA ACTUALIZACIÓN
                    if (isset($userData["UserPhoto"]) && $this->validateParam($userData["UserPhoto"])) {
                        // OBTENEMOS LA IMAGEN A ACTUALIZAR
                        $imageToUpdate = $this->getUserImage($userData["UsuarioId"]);
                        
                        // ACTUALIZAMOS LA IMAGEN
                        $updateImage = $this->updateUserImage($DBConection, $userData["UsuarioId"], $imageToUpdate->FotoPath, $userData["UserPhoto"]);
                        if(!$updateImage) return $this->createResponse(false, "Error al actualizar la imagen.", null);
                    }

                    // VALIDAMOS RESPUESTA
                    if ($stm->execute() > 0) {
                        return $this->createResponse(true, "Actualización exitosa!", null);
                    } else {
                        return $this->createResponse(false, "Error al actualizar el usuario.", null);
                    }
                } else {
                    return $this->createResponse(false, "No pueden haber datos vacíos.", null);
                }
            }
        } catch (Exception $e) {
            // RETORNAMOS EXCEPCIÓN
            return $this->createResponse(false, "Error: " . $e->getMessage(), null);
        }
    }

    // FUNCIÓN PARA ELIMINAR UN USUARIO
    function deleteUser($userId)
    {
        try {
            if (isset($userId)) {
                // CREAMOS SQL

                $DBConection = new Conexion();

                $sql = "SELECT FotoPath FROM FotoPerfil WHERE UsuarioId = :id";
                $stm = $DBConection->prepare($sql);
                $stm->bindValue(":id", $userId);
                $stm->execute();
                $result = $stm->fetch(PDO::FETCH_OBJ);

                $sql = "DELETE FROM usuarios WHERE UsuarioId = :id";
                $stm = $DBConection->prepare($sql);
                $stm->bindValue(":id", $userId);
                $deleteImage = $this->deleteUserImage($userId, $result->FotoPath);

                // VALIDAMOS RESPUESTA
                if ($deleteImage && $stm->execute() > 0) {
                    return $this->createResponse(true, "Se ha eliminado correctamente!", null);
                } else {
                    $DBConection->rollBack();
                    return $this->createResponse(false, "Error al eliminar el registro.", null);
                }
            } else {
                return $this->createResponse(false, "Se necesita la identificación del usuario.", null);
            }
        } catch (Exception $e) {
            return $this->createResponse(false, "Error: " . $e, null);
        }
    }

    // FUNCIONES COMPLEMENTARIAS
    //    
    // FORMATO DE RESPUESTA
    function createResponse($success, $message, $data)
    {
        $response = ["Success" => $success, "Message" => $message, "Data" => $data];
        return json_encode($response);
    }

    // VALIDAR CAMPO DE CADENA
    function validateParam($string)
    {
        $isValid = true;

        if (!isset($string) || empty($string) || (strlen($string)) == 0) {
            $isValid = false;
        }

        return $isValid;
    }

    // GUARDA UNA IMAGEN DE USUARIO CON SU USUARIO
    function saveImage($base64, $name, $userId)
    {
        $directoryToPhoto = $this->baseDirectory . $name;
        if (!is_dir($directoryToPhoto)) {
            mkdir($directoryToPhoto, 0777, true);
        }

        $typeFile = $this->getTypeFile($base64);

        $base64 = str_replace('data:image/' . $typeFile . ';base64,', '', $base64);
        $base64 = str_replace(' ', '+', $base64);
        $data = base64_decode($base64);

        $file = $directoryToPhoto . "/ProfilePhoto" . "." . $typeFile;
        $success = file_put_contents($file, $data);
        if ($success) {
            $this->insertNewFotoPerfil($file, $userId);
        }
        return $success;
    }

    // OBTIENE lA EXTENSION DEL ARCHIVO QUE SE ENVÍA
    function getTypeFile($base64)
    {
        //'data:image/jpg;base64,XXXXXXXXXXXXXXXX'
        $struct = explode(',', $base64); // ['data:image/jpg;base64,'] ['XXXXXXXXXXXXXXX']
        $struct = explode(';', $struct[0]); // ['data:image/jpg'] ['base64']
        $struct = explode(':', $struct[0]); // ['data'] ['image/jpg']
        $struct = explode('/', $struct[1]); // ['image'] ['jpg']
        return $struct[1]; // ['jpg']
    }

    // FUNCIÓN PARA ALMACENAR IMAGEN EN BD
    function insertNewFotoPerfil($fotoPath, $userId)
    {
        $DBConection = new Conexion();

        $sql = "INSERT INTO FotoPerfil (FotoPath, UsuarioId)  values (:FotoPath, :UsuarioId)";
        $stm = $DBConection->prepare($sql);
        $stm->bindValue(":FotoPath", $fotoPath);
        $stm->bindValue(":UsuarioId", $userId);
        $result = $stm->execute();

        if (!$result) {
            $DBConection->rollBack();
        }

        return $result;
    }

    // FUNCIÓN PARA OBTENER IMAGEN DE USUARIOS
    function getUserImage($UsuarioId)
    {
        $DBConection = new Conexion();

        $sql = "SELECT * FROM FotoPerfil WHERE UsuarioId = :UsuarioId";
        $stm = $DBConection->prepare($sql);
        $stm->bindValue(":UsuarioId", $UsuarioId);
        $stm->execute();
        $response = $stm->fetch(PDO::FETCH_OBJ);

        return $response;
    }

    // FUNCIÓN PARA ELIMINAR LA IMAGEN DE UN USUARIOS
    function deleteUserImage($userId, $fotoPath)
    {
        if (isset($userId)) {
            // CREAMOS CONEXIÓN
            $DBConection = new Conexion();

            // CAPTURAMOS RUTA DE ARCHIVO
            $nameFolder = explode('/', $fotoPath);
            $path = $this->baseDirectory . $nameFolder[count($nameFolder) - 2];

            // ELIMINAMOS LOS ARCHIVOS DEL DIRECTORIO
            array_map('unlink', glob($path . "/*"));

            // ELIMINAMOS EL DIRECTORIO
            rmdir($path);

            // BORRAMOS EL REGISTRO DE LA BASE DE DATOS
            $sql = "DELETE FROM FotoPerfil WHERE UsuarioId = :UsuarioId";
            $statement = $DBConection->prepare($sql);
            $statement->bindValue(":UsuarioId", $userId);

            // RETORNAMOS LA RESPUESTA DEL PROCESO
            return $statement->execute() > 0;
        }
        return false;
    }

    function updateUserImage($DBConection, $userId, $fotoPath, $base64)
    {
        // CAPTURAMOS RUTA DE ARCHIVO
        $nameFolder = explode('/', $fotoPath);
        $path = $this->baseDirectory . $nameFolder[count($nameFolder) - 2];

        // ELIMINAMOS LOS ARCHIVOS DEL DIRECTORIO
        array_map('unlink', glob($path . "/*"));

        // OBTENEMOS EL FORMATO DE LA NUEVA IMAGEN
        $typeFile = $this->getTypeFile($base64);

        // LIMPIAMOS LA ENCRIPTACIÓN EN BASE 64
        $base64 = str_replace('data:image/' . $typeFile . ';base64,', '', $base64);
        $base64 = str_replace(' ', '+', $base64);
        $data = base64_decode($base64);

        //  PREPARAMOS LA CREACIÓN DEL NUEVO ARCHIVO
        $file = $path . "/ProfilePhoto" . "." . $typeFile;
        $success = file_put_contents($file, $data);

        // REALIZAMOS EL REGISTRO EN LA BD SI TODO SALE BIEN
        if ($success) {
            $sql = "UPDATE FotoPerfil SET FotoPath = :FotoPath WHERE UsuarioId = :UsuarioId";
            $statement = $DBConection->prepare($sql);
            $statement->bindValue(":FotoPath", $file);
            $statement->bindValue(":UsuarioId", $userId);
            $statement->execute();

            return $statement->execute() > 0;
        }

        return false;
    }
}

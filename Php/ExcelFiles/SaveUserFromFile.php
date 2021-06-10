<?php
require_once "../../vendor/autoload.php";

require "../Functions.php";
$myFunctions = new MyFunctions();
# Indicar que usaremos el IOFactory
use PhpOffice\PhpSpreadsheet\IOFactory;


// VALIDAMOS QUE SE ENVÍE UN ARCHIVOS
if (isset($_FILES['ChargueReport'])) {
    // DECLARAMOS LOS FORMATOS PERMITIDOS DE
    $allowedFormats = array("xlsx", "csv");
    // CAPTURAMOS EL ARCHIVO
    $file = $_FILES['ChargueReport'];

    // OBTENEMOS LA EXTENSIÓN DEL ARCHIVOS
    $fileExtension = explode(".", $file['name']);

    // VALIDAMOS QUE EL ARCHIVO SEA VÁLIDO
    if (in_array($fileExtension[1], $allowedFormats)) {
        $pathFileToSave = "../../DB/" . $file['name'];;

        if (move_uploaded_file($file['tmp_name'], $pathFileToSave)) {
            if ($fileExtension[1] == "xlsx") {

                // ABRIMOS EL DOCUMENTO
                $documento = IOFactory::load($pathFileToSave);
                $totalPages = $documento->getSheetCount();
                $actualPage = $documento->getSheet(0);
                $totalRows = $actualPage->getHighestRow();
                $totalCols = $actualPage->getHighestColumn();
                
                // convertir una letra a valor
                $numeroMayorDeColumna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($totalCols);
                
                $userData = array(
                    "Nombre" => "",
                    "Apellidos" => "",
                    "Usuario" => "",
                    "Password" => "123321", 
                    "UserPhoto" => ""
                );
                
                for($fila = 2; $fila <= $totalRows ; $fila++)
                {                   

                    for($col = 1; $col <= $numeroMayorDeColumna; $col++)
                    {
                        // USAMOS LOS HEADERS PARA COORDINAR LAS ASIGNACIONES
                        $position = $actualPage->getCellByColumnAndRow($col,1)->getValue();

                        // REALIZAMOS LA ASIGNACIÓN
                        $userData[$position] = $actualPage->getCellByColumnAndRow($col,$fila)->getValue();
                    }
                    // INSERTAMOS EL USUARIO CREADO
                    $response = $myFunctions->insertNewUser($userData); 
                }
               
                        
                if(json_decode($response)->Success)
                {
                    header("Location: http://myphppractice.test");
                    exit;
                }else
                {
                    print_r("Error pana");
                }

            } else {
            }
        }
        print_r("<br>Es válido y guardado");
    } else {
        print_r("No es válido");
    }
} else {
    print_r("No hay archivo");
}

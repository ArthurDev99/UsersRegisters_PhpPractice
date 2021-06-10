<?php
require "../../vendor/autoload.php";
require "../Functions.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// INSTANCIA DE Funciones
$myFunctions = new MyFunctions();
$users = json_decode($myFunctions->getAllUsers(), true);



// CREAMOS EL DOCUMENTO

$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator("ArthurDev") // => NOMBRE DEL CREADOR
    ->setLastModifiedBy('Parzibyte') // => NOMBRE DE QUIEN LO MODIFICO POR ULTIMA VEZ
    ->setTitle('Mi primer documento creado con PhpSpreadSheet') // => Titulo
    ->setSubject('El asunto') // => ASUNTO
    ->setDescription('Este documento fue generado para parzibyte.me') // => DESCRIPCION
    ->setKeywords('etiquetas o palabras clave separadas por espacios') // => PALABRAS CLAVE
    ->setCategory('La categoría'); // => CATEGORÍA


// OBTENEMOS HOJA
$hoja = $documento->getActiveSheet();
$hoja->setTitle("ArthurTittle");
$hoja->getStyle("A1:C1")->getFont()->setBold(true);

// AGREGAMOS LOS ENCABEZADOS
$hoja->setCellValueByColumnAndRow(1, 1, "Nombres");
$hoja->setCellValueByColumnAndRow(2, 1, "Apellidos");
$hoja->setCellValueByColumnAndRow(3, 1, "Usuario");

// ASIGNAMOS LOS DATOS
$indexRow = 2;
for ($i = 0; $i < sizeof($users['Data']); $i++) {
    $user = $users['Data'][$i];
    $hoja->setCellValueByColumnAndRow(1, $indexRow, $user["Nombre"]);
    $hoja->setCellValueByColumnAndRow(2, $indexRow, $user["Apellidos"]);
    $hoja->setCellValueByColumnAndRow(3, $indexRow, $user["Usuario"]);
    $indexRow += 1;
}

$nombreDelDocumento = "UsersReport.xlsx";
/**
 * Los siguientes encabezados son necesarios para que
 * el navegador entienda que no le estamos mandando
 * simple HTML
 * Por cierto: no hagas ningún echo ni cosas de esas; es decir, no imprimas nada
 */
 
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
header('Cache-Control: max-age=0');

// Guardamos el documento
$writer = IOFactory::createWriter($documento, 'Xlsx');
$writer->save('php://output');
//$writer->save('example.xlsx');
//print_r("Ia prro");

<?php
class Conexion extends PDO
{    
    // DECLARAMOS VARIABLES
    private $hostDb = 'localhost';
    private $nameDb = 'Usuarios';
    private $userDb = 'root';
    private $passwordDb = '123456';

    // CREAMOS CONSTRUCTOR
    public function __construct()
    {   
        try{
            parent::__construct('mysql:host=' . $this->hostDb . ';dbname=' . $this-> nameDb . ';charset=utf8',
            $this->userDb, $this->passwordDb, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch(PDOException $e)
        {
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }
}
?>
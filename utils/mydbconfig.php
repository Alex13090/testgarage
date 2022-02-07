<?php
class MyDBConfig
{
    // je crée les attributs de ma classe de config db
    private $host = "127.0.0.1";
    private $database_name = "garage";
    private $username = "root";
    private $password = "";

    // l'attribut suivant sera lui en public
    // pour permettre aux autres classes d'accéder à la connexion 
    // de la fonction de connection à la base de données
    public $connect;

    // je crée la fonction pour appeler le système de connexion à ma BDD
    // celle-ci va permettre d'injecter mes attributs en private dans 
    // l'attribut qui est en public. 
    public function getConnection()
    {
        $this->connect = null;
        try {
            $this->connect = new PDO("mysql:host=" . $this->host . "; dbname=" . $this->database_name, $this->username, $this->password);
            $this->connect->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Database could not be connected:" . $exception->getMessage();
        }
        return $this->connect;
    }
}

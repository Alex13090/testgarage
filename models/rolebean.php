<?php
class RoleBean
{
    // attributs pour se connecter à la BDD
    public $connect;
    private $table = 'rolebean';

    //attributs de la classe selon ce que j'ai identifié
    // comme besoin et qui sont déjà implémenter dans la base de données
    private $idRole;
    private $roleName;

    // constructeur qui va établir la connexion à la BDD
    public function __construct()
    {
        $this->connect = new MyDBConfig();
        $this->connect = $this->connect->getConnection();
    }

    //génération des getters et setters
    public function getTable()
    {
        return $this->table;
    }

    public function getIdRole()
    {
        return $this->idRole;
    }
    public function setIdRole($idRole)
    {
        $this->idRole = $idRole;
    }

    public function getRoleName()
    {
        return $this->roleName;
    }
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;
    }

    //création des méthodes de base  CRUD

    public function getRoles()
    {
        // stokage de la requête dans une variable
        $myQuery = 'SELECT * FROM ' . $this->table . '';

        // stockage dans variable de la préparation de la requête
        $stmt = $this->connect->prepare($myQuery);

        //exécution de la requête
        $stmt->execute();

        // je retourne le résultat
        return $stmt;
    }

    public function getSingleRole()
    {
        // stokage de la requête dans une variable
        $myQuery = 'SELECT * FROM ' . $this->table . ' WHERE roleName = ' . $this->roleName . '';

        // stockage dans variable de la préparation de la requête
        $stmt = $this->connect->prepare($myQuery);

        //exécution de la requête
        $stmt->execute();

        // je retourne le résultat
        return $stmt;
    }

    public function createRole()
    {
        $myQuery = 'INSERT INTO
                            ' . $this->table . '
                        SET
                            roleName = :roleName';

        $stmt = $this->connect->prepare($myQuery);

        // bind des paramètres
        $stmt->bindParam(':roleName', $this->roleName);

        return $stmt->execute();
    }

    // UPDATE mise à jour de l'utilisateur selon son pseudo
    public function updateRole()
    {
        $myQuery = 'UPDATE
                            ' . $this->table . '
                        SET
                            roleName = :roleName
                        WHERE
                            roleName = :roleName2';

        $stmt = $this->connect->prepare($myQuery);

        // bind des paramètres
        $stmt->bindParam(':roleName', $this->roleName);
        $stmt->bindParam(':roleName2', $this->roleName);

        if ($stmt->execute) {
            // je retourne true si mise à jour réussie
            return true;
        } else {
            return false;
        }
        // ci-dessus je peux simplifier en écrivant return $stmt->execute();
    }

    // DELETE suppression d'un utilisateur selon pseudo 
    // (on peut aussi avec un autre attribut selon son besoin)
    public function deleteRole()
    {
        $myQuery = 'DELETE FROM ' . $this->table . ' WHERE roleName = :roleName';

        $stmt = $this->connect->prepare($myQuery);

        $stmt->bindParam(':roleName', $this->roleName);

        if ($stmt->execute) {
            // je retourne true si mise à jour réussie
            return true;
        } else {
            return false;
        }
    }
}

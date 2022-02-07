<?php
class UserBean
{
    // attributs pour se connecter à la BDD
    public $connect;
    private $table = 'clients';

    //attributs de la classe selon ce que j'ai identifié
    // comme besoin et qui sont déjà implémenter dans la base de données
    private $idUser;
    private $name;
    private $firstName;
    private $mail;
    private $tel;
    private $password;
    private $idRole = 4;

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

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    public function getTel()
    {
        return $this->tel;
    }

    public function setTel($tel)
    {
        $this->tel = $tel;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getIdRole()
    {
        return $this->idRole;
    }

    public function setIdRole($idRole)
    {
        $this->idRole = $idRole;
    }


    //création des méthodes de base  CRUD

    // Read pour récupérer la liste de tous les utilisateurs
    public function getUsers()
    {
        // stokage de la requête dans une variable
        $myQuery = 'SELECT 
                            * 
                        FROM 
                            ' . $this->table . ' AS u
                        JOIN
                            rolebean AS rs
                        WHERE
                            u.idRole = rs.idRole';

        // stockage dans variable de la préparation de la requête
        $stmt = $this->connect->prepare($myQuery);

        //exécution de la requête
        $stmt->execute();

        // je retourne le résultat
        return $stmt;
    }

    //Read d'un seul utilisateur selon son pseudo
    //(peut-être modifié avec recherche par id ou mail, etc)

    public function getSingleUser()
    {
        // stokage de la requête dans une variable
        $myQuery = 'SELECT 
                            * 
                        FROM 
                            ' . $this->table . '
                        JOIN
                            rolebean
                        WHERE
                            ' . $this->table . '.idRole = rolebean.idRole
                        AND 
                            name = :name';

        $stmt = $this->connect->prepare($myQuery);
        $stmt->bindParam(":name", $this->name);
        $stmt->execute();
        return $stmt;
    }

    // Création et donc insertion d'un nouvel utilisateur dans la BDD
    // dans un premier temps nous ne tiendrons pas compte des champs
    // idRoleSite et idRoleGame
    public function createUser()
    {
        $myQuery = 'INSERT INTO
                            ' . $this->table . '
                        SET
                            name = :name,
                            firstName = :firstName,
                            tel = :tel,
                            password = :password,
                            mail = :mail,
                            idRole = :idRole';
        // dans cette requête j'ai créé les paramètres :pseudo, :password et : mail 
        //auxquels j'attribuerais des valeurs lors du bind des paramètres

        $stmt = $this->connect->prepare($myQuery);

        // bind des paramètres
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':firstName', $this->firstName);
        $stmt->bindParam(':tel', $this->tel);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':mail', $this->mail);
        $stmt->bindParam(':idRole', $this->idRole);

        return $stmt->execute();
    }

    // UPDATE mise à jour de l'utilisateur selon son pseudo
    public function updateUser()
    {
        $myQuery = 'UPDATE
                            ' . $this->table . '
                        SET
                            name = :name,
                            firstName = :firstName,
                            tel = :tel,
                            password = :password,
                            mail = :mail,
                            idRole = :idRole
                        WHERE
                            name = :name2';

        $stmt = $this->connect->prepare($myQuery);

        // bind des paramètres
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':firstName', $this->firstName);
        $stmt->bindParam(':tel', $this->tel);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':mail', $this->mail);
        $stmt->bindParam(':idRole', $this->idRole);
        $stmt->bindParam(':name2', $this->name);

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
    public function deleteUser()
    {
        $myQuery = 'DELETE FROM ' . $this->table . ' WHERE name = ' . $this->name . '';

        $stmt = $this->connect->prepare($myQuery);

        $stmt->bindParam(':name', $this->name);

        if ($stmt->execute) {
            // je retourne true si mise à jour réussie
            return true;
        } else {
            return false;
        }
    }

    // vérification si un pseudo ou un mail est déjà existant
    public function verifyNameAndMail()
    {
        $myQuery = 'SELECT
                            *
                        FROM
                            ' . $this->table . '
                        WHERE
                            name = :name
                        OR 
                            mail = :mail';

        $stmt = $this->connect->prepare($myQuery);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':mail', $this->mail);

        $stmt->execute();
        return $stmt;
    }
}

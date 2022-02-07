<?php

// ajout des headers dont celui qui autorise les methods en POST 
// et celui qui autorise toutes les origines
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// appel des fichiers nécessaires
include_once('../utils/mydbconfig.php');
include_once('../models/userbean.php');
// include_once('../models/rolegamebean.php');
include_once('../models/rolebean.php');

//création de variables pour les retourner en json
$success = 0;
$msg = "Une erreur est survenue dans le php";
$data = [];

// vérification des champs
if (!empty($_POST['name']) && !empty($_POST['password'])) {
    // voir registerUser.php pour explications
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    // voir registerUser.php pour explications
    $verifName = htmlspecialchars(strip_tags($name));
    $verifPassword = htmlspecialchars(strip_tags($password));

    // je crée des instances de User, RoleGame et RoleWebSite
    $itemUser = new UserBean();
    //$itemRoleGame = new RoleGameBean();
    $itemRole = new RoleBean();

    // je donne des valeurs aux attributs de itemUser avec les données reçues
    $itemUser->setName($verifName);
    $itemUser->setPassword($verifPassword);

    // j'appelle la fonction getSingleUser qui va chercher les utilisateurs ayant le pseudo reçu
    // je stocke le retour dans une variable
    $myReturn = $itemUser->getSingleUser();

    // puis je stocke dans une variable le résultat du décompte du nombre de ligne
    // de ce retour grâce à la fonction PHP rowCount()
    $nbrUsers = $myReturn->rowCount();

    // si aucune ligne
    if ($nbrUsers == 0) {
        $msg = "Pas d'utilisateur ayant ce pseudo trouvé, vérifiez votre saisie!!!";
        // si plus de 1 ligne
    } else if ($nbrUsers > 1) {
        $msg = "Il semblerait qu'il y ait une erreur, veuillez contacter un administrateur";
        // si seulement qu'une ligne
    } else if ($nbrUsers == 1) {
        while ($rowUser = $myReturn->fetch()) {
            extract($rowUser);
            // je compare mot de passe reçu avec celui de la BDD
            // si il ne sont pas identiques je retourne un message d'erreur
            if (!password_verify($verifPassword, $rowUser['password'])) {
                $msg = "Erreur dans votre mot de passe, veuillez recommencer!!!";
                // s'ils sont identiques je continue le log
            } else if (password_verify($itemUser->getPassword(), $rowUser['password'])) {
                // je donne des valeurs aux attributs idRole de RoleSite et RoleGame
                $itemRole->setIdRole($rowUser['idRole']);
                //$itemRoleGame->setIdRoleGame($rowUser['idRoleGame']);
                // je récupère ces 2 roles gràace à leurs méthodes get
                $returnRole = $itemRole->getSingleRole();
                //$returnRoleGame = $itemRoleGame->getSingleRoleGame();
                // je crée 2 variables pour les données de roleSite
                $idRole;
                $roleName;
                while ($rowRole = $returnRole->fetch()) {
                    extract($rowRole);
                    // en parcourant le retour j'attribut des valeurs aux variables
                    $idRole =  intval($rowRole['idRole'], 10);
                    $roleName = $rowRole['roleName'];
                }
                // je fais la même chose que ci-dessus avec roleGame
                // $idRoleGame;
                // $roleGameName;
                // while($rowRoleGame = $returnRoleGame->fetch()) {
                //     extract($rowRoleGame);
                //     $idRoleGame =  intval($rowRoleGame['idRoleGame'], 10);
                //     $roleGameName = $rowRoleGame['roleGameName'];
                // }
                // une fois terminé j'attribut des valeurs à mes retours
                $success = 1;
                $msg = "Connexion réussie";
                $data['idUser'] = intval($rowUser['idUser'], 10);
                $data['name'] = $rowUser['name'];
                // $data['idRoleGame'] = $idRoleGame;
                // $data['roleGameName'] = $roleGameName;
                $data['idRole'] = $idRole;
                $data['roleName'] = $roleName;
            }
        }
    }
} else {
    // je passe une valeur à la variable $msg pour traiter cette erreur
    $msg = "Veuillez remplir tous les champs!!!";
}

// si ma variable $success est égal à 1
if ($success == 1) {
    // je crée un tableau qui contiendra le success, un msg et de la data
    $res = ["success" => $success, "msg" => $msg, "data" => $data];
    // puis j'encode le tout en json pour le retourner
    echo json_encode($res);
} else {
    // sinon je retourne seulement un tableau contenant success et msg
    $res = ["success" => $success, "msg" => $msg];
    echo json_encode($res);
}

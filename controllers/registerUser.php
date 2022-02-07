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
if (
    !empty($_POST['name'])
    && !empty($_POST['firstName'])
    && !empty($_POST['tel'])
    && !empty($_POST['password'])
    && !empty($_POST['confPassword'])
    && !empty($_POST['mail'])
    && !empty($_POST['confMail'])
) {

    // je commence par créer des variables qui vont stocker les données envoyées par l'utilisateur 
    // en enlevant les blanc devant et derrière chaque données

    $name = trim($_POST['name']);
    $firstName = trim($_POST['firstName']);
    $tel = trim($_POST['tel']);
    $password = trim($_POST['password']);
    $confPassword = trim($_POST['confPassword']);
    $mail = trim($_POST['mail']);
    $confMail = trim($_POST['confMail']);

    // ensuite je vérifie que password et confPassword sont identiques
    if ($password !== $confPassword) {
        // je passe une valeur à la variable $msg pour traiter cette erreur
        $msg = 'Vos mots de passe ne sont pas identiques!!!';
    } else {
        // je vérifie si la valeur de $mail est bien un mail grâce à la fonction filter_var
        // qui prend la donnée en paramètre ainsi que FILTER_VALIDATE_EMAIL pour demander la vérification
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            // je passe une valeur à la variable $msg pour traiter cette erreur
            $msg = "Cette adresse e-mail n'est pas valide!!!";
        }
        // et je renouvelle cette opération pour la valeur de la variable $confMail
        else if (!filter_var($confMail, FILTER_VALIDATE_EMAIL)) {
            // je passe une valeur à la variable $msg pour traiter cette erreur
            $msg = "Cette confirmation d'adresse e-mail n'est pas valide!!!";
        }
        // je vérifie maintenant que les mails sont  identiques
        else if ($mail !== $confMail) {
            // je passe une valeur à la variable $msg pour traiter cette erreur
            $msg = "Vos emails ne sont pas identiques!!!";
        } else {
            // Pour la suite je n'aurais besoin que de 3 des 5 variables : $pseudo, $password et $mail.
            // Mais afin de sécuriser nos données nous allons effectuer des traitements à nos variables
            // en utilisant des fonctions PHP.
            // En effet nous allons vouloir nous protéger de toute injection de code HTML et/ou Javascript
            // par l'intermédiaire des variables qui contiennent les données envoyées par l'utilisateur.
            // Ces possibles failles sont appelées des failles XSS (Cross-Site Scripting).
            // Nous allons utiliser la fonction strip_tags qui permet de supprimer les balises HTML,
            // et la fonction htmlspecialchars qui permet de neutraliser les caractères &, ", ', <, >,
            // en les remplaçant par leurs codes &amp;, &quot;, &#039;, &lt; &gt;
            // ->  nb: il existe aussi la fonction htmlentities dont le rôle est de modifier toutes les balises HTML

            // je vais donc utiliser les 2 fonctions citées ci-dessus en les combinant
            // afin des traiter mes 3 variables $pseudo, $password et $mail.
            $verifName = htmlspecialchars(strip_tags($name));
            $verifFirstName = htmlspecialchars(strip_tags($firstName));
            $verifTel = htmlspecialchars(strip_tags($tel));
            $verifPassword = htmlspecialchars(strip_tags($password));
            $verifMail = htmlspecialchars(strip_tags($mail));

            // je crée maintenant une nouvelle instance d'utilisateur
            $newUser = new UserBean();
            // $itemRoleGame = new RoleGameBean();
            $itemRole = new RoleBean();

            // et j'utilise les setters de la classe User pour affecter les valeurs des variables aux attributs de la classe
            $newUser->setName($verifName);
            $newUser->setFirstName($verifFirstName);
            $newUser->setTel($verifTel);
            // pour l'affectation du mot de passe je vais également utiliser la fonction de hash de BCRIPT
            // pour crypter le mot de passe.
            $newUser->setPassword(password_hash($verifPassword, PASSWORD_BCRYPT));
            $newUser->setMail($verifMail);
            // dans ma base de données les roles membres des 2 tables
            // ont un id = 3 donc je vais passer cette valeur par défaut
            // $newUser->setIdRoleGame($newUser->getIdRoleGame());
            $newUser->setIdRole($newUser->getIdRole());

            // avant de pourvoir procéder à l'insertion en base de données
            // je vais vérifier que le pseudo ou le mail n'existe pas déjà 
            // dans ma base de données
            // je stocke dans une variable le retour de la fonction qui se trouve dans ma classe User
            $retourDelaclasse = $newUser->verifyNameAndMail();
            // puis je stocke dans une variable le résultat du décompte du nombre de ligne
            // de ce retour grâce à la fonction PHP rowCount()
            $nbrLignes = $retourDelaclasse->rowCount();

            if ($nbrLignes > 0) {
                $msg = "Mail déjà utlisé, veuillez renouveler votre demande avec d'autres informations";
            } else {
                // une fois toutes les étapes précédentes réalisées, il ne me reste plus qu'à appeler la fonction
                // createUser incluse dans la classe User.
                // avec l'ajout de la classe ErrorMessage je vais également retourner une information
                // pour confirmer la création ou non de l'utilisateur
                if ($newUser->createUser()) {
                    $myReturn = $newUser->getSingleUser();
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
                            // je donne des valeurs aux attributs idRole de RoleSite et RoleGame
                            $itemRole->setIdRole($rowUser['idRole']);
                            //$itemRoleGame->setIdRoleGame($rowUser['idRoleGame']);
                            // je récupère ces 2 roles gràace à leurs méthodes get
                            $returnRole = $itemRole->getSingleRole();
                            // $returnRoleGame = $itemRoleGame->getSingleRoleGame();
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
                            $msg = "Utilisateur créé avec succès";
                            $data['idUser'] = intval($rowUser['idUser'], 10);
                            $data['name'] = $rowUser['name'];
                            $data['firstName'] = $rowUser['firstName'];
                            $data['tel'] = $rowUser['tel'];
                            $data['mail'] = $rowUser['mail'];
                            // $data['idRoleGame'] = $idRoleGame;
                            // $data['roleGameName'] = $roleGameName;
                            $data['idRole'] = $idRole;
                            $data['roleName'] = $roleName;
                        }
                    }
                } else {
                    // je passe une valeur à la variable $msg pour traiter cette erreur
                    $msg = "Erreur lors de l'enregistrement, veuillez renouveler votre demande!!!";
                }
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

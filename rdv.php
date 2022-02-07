<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/contact.css">
    <title>4_tyres</title>
</head>

<body>

    <h1>DEVIS GRATUIT ET RENDEZ-VOUS IMMEDIAT</h1>

    <form action="" id="formIndex" method="POST">
        <p>veuillez saisir votre nom:</p>
        <input type="text" name="name">
        <br>
        <p>veuillez saisir votre prenom :</p>
        <input type="text" name="firstName">
        <br>
        <p>veuillez saisir votre Email :</p>
        <input type="email" name="mail">
        <br>
        <p>veuillez saisir votre numero :</p>
        <input type="telephone" name="tel">
        <br>
        <p>veuillez saisir le date de rdv :</p>
        <input type="date" name="date">
        <br>
        <label for="services">veuillez saisir le type de service :</label>
        <select name="service" id="service">
            <option value="pneu">Remplacement des pneus</option>
            <option value="frein">Remplacement disque & plaquettes</option>
            <option value="revision">Réaliser une révision</option>
            <option value="vidange">Réaliser une Vidange</option>
        </select>
        <br>
        <input type="submit" id="btn" value="Ajouter">
    </form>
    <form action="service.php" method="GET">
        <input type="submit" value="Regarder les prix estimee">
    </form>

    <?php

    if (isset($_POST['name']) && isset($_POST['firstName']) && isset($_POST['mail']) && isset($_POST['tel']) && isset($_POST['date'])) {
        $name = $_POST['name'];
        $firstName = $_POST['firstName'];
        $mail = $_POST['mail'];
        $tel = $_POST['tel'];
        $date = $_POST['date'];
        $service = $_POST['service'];

        $bdd = new PDO(
            'mysql:host=127.0.0.1;dbname=garage',
            'root',
            '',
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        $idRdv = $bdd->query('SELECT idRdv FROM rendezvous');
        $bdd->exec("set names utf8");

        try {
            $reqUser = $bdd->prepare("INSERT INTO clients SET name = :name, firstName = :firstName, mail = :mail, tel = :tel");
            $resultat1 = $reqUser->execute(array(':name' => $name, ':firstName' => $firstName, ':tel' => $tel, ':mail' => $mail));

            $last_idUser = $bdd->lastInsertId();

            $reqRdv = $bdd->prepare("INSERT INTO rendezvous SET date_rdv = :dateRdv, type_rdv = :type_rdv, idUser = :idUser");
            $resultat = $reqRdv->execute(array(':dateRdv' => $date, ':type_rdv' => $service, ':idUser' => $last_idUser));

            $reqRdv = $bdd->prepare("INSERT INTO obtenir SET idUser = :idUser, id_service = (SELECT id_service FROM services WHERE type_service = :type_service)");
            $resultat = $reqRdv->execute(array(':idUser' => $last_idUser, ':type_service' => $service));

            if ($bdd->query("INSERT INTO rendezvous SET date_rdv = $date") && $bdd->query("INSERT INTO clients SET nom_client = $name, prenom_client = $prenom, idRdv = (SELECT idRdv FROM rendezvous)")) {
                $reqUser = $bdd->prepare("INSERT INTO clients SET nom_client = :nom, prenom_client = :prenom, email_client = :email, tel_client = :tel, idRdv = ANY (SELECT idRdv FROM rendezvous WHERE type_rdv = :type_rdv)");
                $resultatAll = $resultat + $resultat1;

                if ($resultatAll) {
                    $reqAll = $bdd->prepare('SELECT clients.name, clients.firstName, clients.mail, clients.tel, rendezvous.date_rdv, rendezvous.type_rdv FROM clients INNER JOIN rendezvous ON clients.idUser = rendezvous.idUser');
                    $req2 = $bdd->query("SELECT clients.nom_client, clients.prenom_client, clients.email_client, rendezvous.date_rdv, rendezvous.type_rdv FROM clients INNER JOIN rendezvous ON clients.id_rdv = rendezvous.id_rdv");
                    $reqAll->execute();
                    while ($donnees = $reqAll->fetch()) {
                    }
                } else {
                    echo "<p>Erreur lors de l'enregistrement</p>";
                }
            }
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    ?>
    <script type="text/javascript" src="rdv.js"></script>
</body>

</html>
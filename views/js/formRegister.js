// je récupère mon formulaire tout en lui ajoutant une écoute
document.getElementById("registerForm").addEventListener("submit", function(event){
    event.preventDefault();

    // je récupère les données du formulaire dans la variable (le formulaire est représenté par this)
    let data = new FormData(this);

    // je crée une variable qui est une instance d'une requête XMLHTTP
    let xhr = new XMLHttpRequest();

    // je fais appelle à la fonction onreadystatechange qui retourne les changements d'états du client (navigateur)
    // selon les résultat de la requete XML
    xhr.onreadystatechange = function(){
        // si le serveur me repond avec un code http 200 et que readyState est = à 4
        // le 4 signifie que la requête a bien été envoyé, que le serveur a traiter la requête
        // et qu'il a retourner une réponse et qu'enfin le navigateur a fini de télécharger le 
        // contenu de la réponse.
        if(this.readyState == 4 && this.status == 200) {
            console.log(this.response);
            // je parse le json reçu pour pouvoir l'exploiter
            let res = JSON.parse(this.response);
            console.log(res);
            // si dans ma réponse je trouve le success égal à 1
            if(res.success == 1) {
                // je crée un objet myUser avec une clé pseudo dont la valeur est vide 
                let myUser = {"id": "","name": "", "idRole": null, "roleName": ""};
                // puis a cette clé j'attribue la valeur reçu dans le résultat de la requête
                myUser.id = res.data.idUser;
                myUser.name = res.data.name;
                myUser.idRole = res.data.idRole;
                myUser.roleName = res.data.roleName;

                // ensuite je crée une variable pour stocker le rendu du stringify sur mon objet myUser
                let toJson = JSON.stringify(myUser);
                // je crée l'item myUser dans le localStorage
                localStorage.setItem('myUser', toJson);
                // j'affiche une message pour avertir du bon enregistrement
                alert(res.msg);
                // je retourne sur la page index.html
                document.location.href="http://localhost/garage1/index.html";
            } else {
                // si le success n'est pas égal à 1 j'affiche un message d'erreur
                alert(res.msg);
            }
            // dans le cas où la requête XML échoue j'affiche également un message
        } else if(this.readyState == 4){
            alert("Une erreur est survenue...");
        }
    };

    // je demande à ma variable xhr contenant l'instance XMLHttpRequest
    // d'établir une connexion en POST vers le lien que je lui donne
    // le true signale que cette requête est en asynchrone
    xhr.open("POST", "http://localhost/garage1/controllers/registerUser.php", true);
    // enfin je demande à ma variable d'envoyer le formulaire à mon controleur php en lui passant 
    // data en paramètre et qui représente toutes les données du formulaire
    xhr.send(data);

    // par défault je retourne un false pour ne pas bloqué le script js?
    return false;
});



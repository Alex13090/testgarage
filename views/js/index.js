// création variable myUser qui va tenter de récupérer un item 'myUser' dans le localStorage
let myUser = JSON.parse(localStorage.getItem('myUser'));

// récupération des éléments HTML dont j'aurais besoin dans des variables
let boutonIndex1 = document.getElementById("boutonIndex1");
let boutonIndex2 = document.getElementById("boutonIndex2");
let boutonIndex3 = document.getElementById("boutonIndex3");
let helloMsg = document.getElementById("helloMsg");
let nom = document.getElementById("nameSlot");

// si la tentative de récupération du localStorage retourne un null
if (myUser == null) {
    // je cache le message pour dire bonjour
    helloMsg.style.display = "none";
    // j'affiche le bouton pour créer un compte
    boutonIndex1.style.display = "block";
    boutonIndex2.style.display = "block";
    boutonIndex3.style.display = "none";
// si le localStorage m'a fourni des données
} else {
    // j'affiche le paragraphe pour dire bonjour avec le non de l'utlisateur
    helloMsg.style.display = "block";
    // je cache le bouton pour dire créer un compte
    boutonIndex1.style.display = "none";
    boutonIndex2.style.display = "none";
    boutonIndex3.style.display = "block";
    // je rajoute le pseudo de l'utilisateur dans le message de bonjour
    nom.textContent = myUser.name;
}


boutonIndex3.addEventListener("submit", function(event){
    event.preventDefault();
    localStorage.clear();
    location.reload();
});
// recup form with listener
document.getElementById('loginForm').addEventListener("submit", function(event){
    event.preventDefault();
    // recup formula data of a variable with 'this' term
    let data = new FormData(this); // this = registerForm (the formula)
    
    // create instance variable of XMLHTTPREQUEST
    let xhr = new XMLHttpRequest();

    // call function 'onreadystatechange' return of client' changes (browser) depending on results of XML
    xhr.onreadystatechange = function(){
        // if http code 200 and readystate = 4, 4 => sent success requete, server traite requete, server reply, client(browser) download of reply content
        if(this.readyState == 4 && this.status == 200){
            console.log(this.response);
            // parse(decode) received JSON for exploiter
            let res = JSON.parse(this.response);
            // if response success = 1
            if(res.success){
                // create object myUser with pseudo key but value = 0 this moment
                let myUser = {"id": "", "name": "", "idRole": null, "roleName": ""};
                // give(recup) received value to this key
                myUser.id = res.data.idUser;
                myUser.name = res.data.name;
                myUser.idRole = res.data.idRole;
                myUser.roleName = res.data.roleName;
                // create nex variable to stock myUser stringify reply
                let toJson = JSON.stringify(myUser);
                // create item 'myUser' in localStorage
                localStorage.setItem('myUser', toJson); // stock response of 'toJson' to 'myUser = propriety'
                // alert success register
                alert(res.msg);
                // direction to index.html
                document.location.href = "http://localhost/garage1/index.html";
            } else {
                alert(res.msg);
            }
        } // eche requete
        else if(this.readyState == 4){
            alert("Une erreur est survenue ...!");
        }
    };
    // ask to variable 'xhr' to connection POST into link that I give
    // true = request is asynchrone
    xhr.open("POST", "http://localhost/garage1/controllers/loginUser.php", true);
    // ask to variable 'xhr' to send all to controller with dat in parameter, data = all data of formula
    xhr.send(data);
    // return false if problem not to bloc the script
    return false;
});
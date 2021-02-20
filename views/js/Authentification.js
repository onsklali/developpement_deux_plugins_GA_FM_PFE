var person = { userID: "", name: "", accessToken: "", picture: "", email: ""};
var obj = {email:'bensalah.yosra732+99898@gmail.com',
    password:'med.ali123wp',
    name:'my_store',
    cell:'25475587',
    company:'my_store',
    shop:'5e660072e4b0eb71767ac996'};
function logIn() {
    FB.login(function (response) {
        if (response.status == "connected") {
            person.userID = response.authResponse.userID;
            person.accessToken = response.authResponse.accessToken;
            /*FB.api('/me/likes', 'get', { fields: 'id,name,about,created_time,category' }, function (response) {
               // console.log(response);
            });*/
            FB.api(
                "/me/accounts",'get',{ fields: 'id,name' },
                function (response) {
                    if (response && !response.error) {
                        console.log(response.data[0].name);
                    }
                }
            );
            FB.api('/me?fields=id,name,email,picture.type(large)', function (userData) {
                person.name = userData.name;
                person.email = userData.email;
                person.picture = userData.picture.data.url;

                $.ajax({
                    url: "login.php",
                    method: "POST",
                    data: person,
                    dataType: 'text',
                    success: function (serverResponse) {
                        console.log(person);
                        // if (serverResponse == "success")
                        //window.location = "index.php";
                    }
                });
            });
        }
    }, {scope: 'public_profile,pages_show_list,email,user_likes',
        return_scopes: true})

    //début code firestore
    const admin = require("firebase-admin");

    const serviceAccount = require("./prestaproject-b0d92-firebase-adminsdk-pgdc0-8a4909e922.json");

    admin.initializeApp({
        credential: admin.credential.cert(serviceAccount)
    });

    const db = admin.firestore();

    function getDialogue(){
        //return a promise since we'll imitating an API call
        return new Promise(function(resolve, reject) {
            resolve({
                "quote":"I'm ons",
                "author":"ons Klali"
            });
        })
    }
    getDialogue().then(result =>{
        console.log(result);
        const obj = result;
        const quoteData = {
            quote: obj.quote,
            author: obj.author
        };
        return db.collection('sampleDataO').doc('inspire')
            .set(quoteData).then(() =>
                console.log('new Dialogue written to database'));
    });
    // fin code firestore

}

window.fbAsyncInit = function() {
    FB.init({
        appId            : '521865075107494',
        autoLogAppEvents : true,
        xfbml            : true,
        version          : 'v6.0'
    });
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
function postRequest(url) {
    fetch(url, {
        method : 'POST', // or 'PUT'
        mode : 'cors',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            //console.log('Success:', data);
        })
        .catch((error) => {
            //console.error('Error:', error);
        });}

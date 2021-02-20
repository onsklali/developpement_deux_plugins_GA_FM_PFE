

create_proc = function(url) {
    const param = {botTitle:'bestForBathroomPrestashop',
        botName: 'bestForBathroomPrestashop',
        sourceBot:'shopify_google-assistant_en',
        botType : 'google-assistant',
        lang : 1,
        theme: 'prestashop',
        shopID: '5e7dd541e4b0d4b43da5a6e8',
        shopShopify:'url',
        userID:'6T1WaHIaNVaZYmpGghdtAh2ZDJ33',
        shopifyId:1};
    var request = new XMLHttpRequest();
    request.open('POST',url, true);
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                // console.log("Success");
                //success
            } else {
                //  Console.log("Error");//error
            }
        }
    };
    var params = JSON.stringify(param);
    request.setRequestHeader("Content-type", "application/json");
//request.setRequestHeader("Content-length", params.length);
//request.setRequestHeader("Connection", "close");
    //request.timeout = 4000;
    request.ontimeout = function () {
        //timeout
    }
    /** Sending data **/
    request.send(params);
}
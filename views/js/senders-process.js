
process_sending = function(url) {
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
    //request.setRequestHeader("Origin","*");
    //request.setRequestHeader("Access-Control-Allow-Origin","*");
    request.setRequestHeader("Content-type", "application/json");
//request.setRequestHeader("Content-length", params.length);
//request.setRequestHeader("Connection", "close");
    request.timeout = 4000;
    request.ontimeout = function () {
        timeout();
    }
    /** Sending data **/
    request.send(params);
}

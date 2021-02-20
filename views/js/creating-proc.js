creating_proc = function(url) {
    const data = {
        email: 'bensalah.yosra1981997@gmail.com',
        password: 'med.ali123wp',
        name: 'my_store',
        cell: '25475587',
        company: 'my_store',
        shop: '5e660072e4b0eb71767ac996'
    };
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
    var params = JSON.stringify(data);
    request.setRequestHeader('Content-type', 'application/json');
//request.setRequestHeader("Content-length", params.length);
//request.setRequestHeader("Connection", "close");
    //request.timeout = 4000;
    /** Sending data **/
    request.send(params);
}
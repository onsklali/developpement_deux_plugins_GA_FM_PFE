function postRequestAjax(url) {
    const data = {email:'bensalah.yosra1998@gmail.com',
        password:'med.ali123wp',
        name:'my_store',
        cell:'25475587',
        company:'my_store',
        shop:'5e660072e4b0eb71767ac996'};
    $.ajax({
        method: "POST",
    type: "POST",
    url: url,
    // The key needs to match your method's input parameter (case-sensitive).
    data: JSON.stringify(data),
    contentType: "application/json; charset=utf-8",
    dataType: "json",

})
        .done(function( msg ) {
            alert( "Data Saved: " + msg );
        });
    }

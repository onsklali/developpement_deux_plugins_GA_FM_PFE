/**
 * 2016 Favizone Solutions Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Favizone Solutions Ltd <contact@favizone.com>
 * @copyright 2015-2016 Favizone Solutions Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
 
/**
 * Registration process
 */
sendRegisterData = function() {

	var url = window.location.href;
	$("#submit-register").addClass("disabled");
	$("#submit-step-1").addClass("hide");
	$("#submit-step-2").removeClass("hide");
	var request = new XMLHttpRequest();
	request.open('POST', url, true);
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	request.setRequestHeader("Accept", "*/*");
	/*request.setRequestHeader("Connection", "close");*/
	request.onreadystatechange = function() {
		//success status                 
		if (request.readyState == 4) {
			try {
				var re = JSON.parse(request.responseText);
			} catch (err) {
				if(request.responseText.indexOf('{"status":"error"')>0){
					var from = request.responseText.indexOf('{"status":"error"');
					var res = request.responseText.substring(from) ;
				} else {
					var from = request.responseText.indexOf('{"email"');
					var to = request.responseText.indexOf(',"status":"success"}');
					var res = request.responseText.substring(from, to) + ',"status":"success"}';
				}
				var re = JSON.parse(res);
			}
			if (re.status == "success") {
				//sending account data
				var request_url = re.request_url;
				delete(re.request_url);
				request.open('POST', request_url, true);
				request.onreadystatechange = function() {
					if (request.readyState == 4) {
						var remote_result = JSON.parse(request.responseText);
						if (remote_result.status == "success") {
							//Begin analyzing data
							request.open('POST', url, true);
							request.onreadystatechange = function() {
								if (request.readyState == 4) {
									//success status
									$("#submit-step-2").addClass("hide");
									$("#submit-step-final").removeClass("hide");
								}
							}
							request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							request.setRequestHeader("Accept", "*/*");
							//request.setRequestHeader("Connection", "close");
							/*for (let [key, value] of Object.entries(remote_result)) {
								console.log(`${key}: ${value}`);
							  }*/
							request.send("ajax=true&&indicator=analyse-data&&application_key=" + remote_result.application_key + "&&reference=" + remote_result.reference);
						} else {
							$("#submit-register").removeClass("disabled");	
							$("#submit-step-2").addClass("hide");
							$("#submit-step-error").removeClass("hide");
						}
					}
				}
				request.setRequestHeader("Content-type", "application/json");
				request.setRequestHeader("Accept", "*/*");
				//request.setRequestHeader("Connection", "close");
				request.send(JSON.stringify(re));
			} else {

				$("#submit-register").removeClass("disabled");
				$("#submit-step-2").addClass("hide");
				if(re.message){
          $("#submit-step-error-detail").html(re.message);
          $("#submit-step-error-detail").removeClass("hide");
        }
				$("#submit-step-error").removeClass("hide");
			}
		}
	}
	var email = document.getElementById("favizone_account_email").value;
	request.send("favizone_account_email=" + email);
}

/**
 * Sending post data
 * */
sendData = function(url, indicator) {

	$("#submit-step-2").removeClass("hide");
	var request = new XMLHttpRequest();
	request.open('POST', url, true);
	request.onreadystatechange = function() {
		if (request.readyState == 4) {
			if (request.status == 200) {

				$("#submit-step-2").addClass("hide");
				$("#submit-step-final").removeClass("hide");
			}
		}
	};
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	request.setRequestHeader("Accept", "*/*");
	//request.setRequestHeader("Connection", "close");
	//request.timeout = 3000;
	request.ontimeout = function() {}
	request.send("ajax=true&&" + "indicator=" + indicator);
	return false;
}

/**
 * Submit redirection
 * */
submitAndRedirect = function(url, old_id_language, new_id_language) {

	if (url.indexOf('language_id=') < 0)
		window.location.href = 'http://' + window.location.host + url + '&language_id=' + new_id_language;
	else
		window.location.href = 'http://' + window.location.host + url.replace('language_id=' + old_id_language, 'language_id=' + new_id_language);
}

/**
 * Submit redirection
 * */
submitAndRedirectToUrl = function(new_id_language) {

	var url = window.location.href;
	var splittedData = url.split("&language_id=");
	if(splittedData && splittedData.length>1){
		url = splittedData[0];
	} 
	window.location.href =  url + '&language_id=' + new_id_language;
}
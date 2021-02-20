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
function FavizoneRenderer(url, post_data, click_event) {

  this.url = url;
  this.post_data = post_data;
  this.click_event = click_event;

  /**
  * Getting Recommendations.
  */
  this.getRecs = function() {
		//to backup because of changing in context
		var request = new XMLHttpRequest();
		request.open('POST', this.url, true);
		request.onreadystatechange = function() {
		  if (request.readyState == 4) {
			if (request.status == 200) {
			  var result = JSON.parse(request.responseText);
			  //render the content
			  for (var key in result) {
				if (document.getElementById(result[key].container)) {
				  //appending data
				  document.getElementById(result[key].container).innerHTML += result[key].template;
				  //if carousel plugin exists, disables auto scroll
				  if ($("#" + key + "_slideshow").carousel)
					$("#" + key + "_slideshow").carousel({
					  interval: false
					});
				  //Eval scripts if exist
				  var favizone_scripts = document.getElementById(result[key].container).getElementsByTagName('script')
				  for (var n = 0; n < favizone_scripts.length; n++)
					eval(favizone_scripts[n].innerHTML);

				  //Events binders
				  if (typeof($(document).on) != "undefined") {
					$(document).on('click', "#" + key + " [data-context=favizone]", {
					  "key": key
					}, function(event) {

					  if ($(this).attr("favizone-ref") && $(this).attr("favizone-ref").length > 0) {
						document.cookie = "favizone_id_recommendor=" + event.data.key + "; path=/";
						document.cookie = "favizone_id_product=" + $(this).attr("favizone-ref") + "; path=/";
					  }

					  return true;
					});
					$(document).on('click', "#" + key + " .favizone_ajax_add_to_cart_button", {
					  "key": key
					}, function(event) {
					  var identifier = $(this).attr("data-id-product");
					  if (identifier) {
						post_data.product = identifier;
						sendPostData(post_data, url);
					  }
					});
				  } else {
					//old version of jquery
					$(document).delegate("#" + key + " [data-context=favizone]", 'click', {
					  "key": key
					}, function(event) {
					  if ($(this).attr("favizone-ref") && $(this).attr("favizone-ref").length > 0) {

						document.cookie = "favizone_id_recommendor=" + event.data.key + "; path=/";
						document.cookie = "favizone_id_product=" + $(this).attr("favizone-ref") + "; path=/";
					  }
					  return true;
					});
					$(document).delegate('click', "#" + key + " .favizone_ajax_add_to_cart_button", {
					  "key": key
					}, function(event) {
					  var identifier = $(this).attr("data-id-product");
					  if (identifier) {
						post_data.product = identifier;
						sendPostData(post_data, url);
					  }
					});
				  }
				}
			  }
			}
		  }
		};
		request.setRequestHeader("Content-type", "application/json");
		request.setRequestHeader("Accept", "*/*");
		//request.setRequestHeader("Connection", "close");
		request.timeout = 4000;
		request.ontimeout = function() {
			console.log("Timed out!!!");
		  }
		  //verifying id connection
		if (document.getElementById('favizone_s_id')) {
		  if (this.post_data.session == "" || this.post_data.session == null)
			this.post_data.session = document.getElementById('favizone_s_id').textContent;
		  if (this.post_data.event_params && this.post_data.event_params.session == null)
			this.post_data.event_params.session = document.getElementById('favizone_s_id').textContent;
		}
		if (this.post_data.session && this.post_data.session != "") {

		  request.send(JSON.stringify(this.post_data));
		}
  }

  /**
  * Getting Recommendations related to add to cart event.
  */
  this.getAddToCartRecs = function() {
		var url = this.url.replace("/product", "/popup");
		var post_data = this.post_data;
		if (post_data.cart && post_data.product) {
		  post_data.product = post_data.product + "";
		  if (post_data.cart.indexOf(post_data.product) < 0)
			post_data.cart.push(post_data.product);
		  else {
			post_data.cart.splice(post_data.cart.indexOf(post_data.product + ""), 1);
			post_data.cart.push(post_data.product + "");
		  }
		}
		var submit_element = ($("#buy_block button[type=submit]").length != 0 ? $("#buy_block button[type=submit]") : $("#buy_block input[type=submit]"));
		submit_element.eventBinder = typeof(submit_element.on) != "undefined" ? submit_element.on : submit_element.bind;
		submit_element.eventBinder("click", function() {

		  sendPostData(post_data, url)

		});
  }

  /**
  * Post request
  */
  sendPostData = function(data_to_send, url) {
		var canals = ["home", "product", "search", "others", "error", "category"];
		for (c in canals) {
		  if (url.indexOf(canals[c]) > 0) {

			url = url.replace("/" + canals[c], "/popup");
			break;
		  }
		}

		var request = new XMLHttpRequest();
		request.open('POST', url, true);
		request.onreadystatechange = function() {

		  if (request.readyState == 4) {
			if (request.status == 200) {
			  var result = JSON.parse(request.responseText);
			  //render the content
			  var index = 0;
			  for (var key in result) {
				if (document.getElementById(result[key].container)) {
				  if (index == 0) {
					index++;
					document.getElementById(result[key].container).innerHTML = "";
				  }
				  document.getElementById(result[key].container).innerHTML += result[key].template;
				  if ($("#" + key + "_slideshow").carousel)
					$("#" + key + "_slideshow").carousel({
					  interval: false
					});

				  //Eval scripts if exist
				  var favizone_scripts = document.getElementById(result[key].container).getElementsByTagName('script')
				  for (var n = 0; n < favizone_scripts.length; n++)
					eval(favizone_scripts[n].innerHTML);

				  //Binding events
				  $(document).on('click', "#" + key + " [data-context=favizone]", {
					"key": key
				  }, function(event) {
					document.cookie = "favizone_id_recommendor=" + event.data.key + "; path=/";
					document.cookie = "favizone_id_product=" + $(this).attr("favizone-ref") + "; path=/";
					return true;
				  });
				}
			  }
			}
		  }
		};
		request.setRequestHeader("Content-type", "application/json");
		request.setRequestHeader("Accept", "*/*");
		//request.setRequestHeader("Connection", "close");
		request.timeout = 4000;
		request.ontimeout = function() {
			console.log("Timed out!!!");
		  }
		  //verifying id connection
		if (document.getElementById('favizone_s_id')) {
		  if (post_data.session == "" || post_data.session == null)
			post_data.session = document.getElementById('favizone_s_id').textContent;
		  if (post_data.event_params && post_data.event_params.session == null)
			post_data.event_params.session = document.getElementById('favizone_s_id').textContent;
		}

		request.send(JSON.stringify(post_data));
  }
}
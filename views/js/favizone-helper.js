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
function FavizoneHelper() {

  this.getCookie = function(cname) {
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) {
		  var c = ca[i];
		  while (c.charAt(0) == ' ') c = c.substring(1);
		  if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
		}
		return "";
  }

  this.setCookie = function(cname, cvalue) {
		document.cookie = cname + "=" + cvalue + "; path=/";
  }

  this.insertParam = function(paramName, paramValue) {
		var url = window.location.href;
		var hash = location.hash;
		url = url.replace(hash, '');
		if (url.indexOf(paramName + "=") >= 0) {
		  var prefix = url.substring(0, url.indexOf(paramName));
		  var suffix = url.substring(url.indexOf(paramName));
		  suffix = suffix.substring(suffix.indexOf("=") + 1);
		  suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";
		  url = prefix + paramName + "=" + paramValue + suffix;
		} else {
		  if (url.indexOf("?") < 0)
			url += "?" + paramName + "=" + paramValue;
		  else
			url += "&" + paramName + "=" + paramValue;
		}
		return url;
  }
}
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
function Tracker(session, key, events, apiUrl) {
  this.events = events;
  this.session = session;
  this.key = key;
  this.apiUrl = apiUrl;
  /**
  * Tracking events.
  */
  this.sendAction = function() {
		/** checks if session_identifier is well defined **/
		if (this.session == null || this.session == '') {
		  //Saving context
		  var context = this;
		  var current_event;
		  setTimeout(function() {
			if (document.getElementById('favizone_s_id')) {
			  context.session = document.getElementById('favizone_s_id').textContent;
			  for (var event in context.events) {
				current_event = context.events[event];
				context.events[event] = current_event.slice(0, 2) +
				  context.session +
				  current_event.slice(2);
			  }
			  context.sendAction();
			} else
			  return;
		  }, 200)
		} else {

		  this.process_sending();
		}
  }
  /**
  * Post request.
  */
  this.process_sending = function() {
		/**  Preparing data **/
		var sending_data = {
		  key: this.key,
		  events: this.events,
		  session: this.session
		};
		if (this.custom_event_key) {
		  sending_data.custom_event_key = this.custom_event_key;
		}
		if (this.custom_event_value) {
		  sending_data.custom_event_value = this.custom_event_value;
		}
		if (this.search_engine_value) {
		  sending_data.search_engine_value = this.search_engine_value;
		}
		if (this.search_campaign_value) {
		  sending_data.search_campaign_value = this.search_campaign_value;
		}
		if (this.favizone_facebook_profile) {
		  sending_data.favizone_facebook_profile = this.favizone_facebook_profile;
		}
		if (typeof(this.product_data) != "undefined") {
		  sending_data['product'] = this.product_data;
		}
		if (typeof(this.category_data) != "undefined") {
		  sending_data['category'] = this.category_data;
		}
		/** End Preparing data **/

		var request = new XMLHttpRequest();
		request.open('POST', this.apiUrl, true);
		request.onreadystatechange = function() {
		  if (request.readyState == 4) {
			if (request.status == 200) {
			  //success
			} else {
			  //error
			}
		  }
		};
		var params = JSON.stringify(sending_data);
		request.setRequestHeader("Content-type", "application/json");
		//request.setRequestHeader("Content-length", params.length);
		//request.setRequestHeader("Connection", "close");
		request.timeout = 4000;
		request.ontimeout = function() {
			//timeout
		  }
		  /** Sending data **/
		request.send(params);
  }
}
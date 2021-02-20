{**
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
*}
<script type="text/javascript" src="{$favizone_module_path}views/js/favizone-tracker.js"></script>

<script type="text/javascript">
	var favizone_events = {$events|json_encode nofilter};
	if (typeof (FavizoneHelper) != "undefined") {
		var favizone_searcher = new FavizoneHelper();
		var favizone_product_id = favizone_searcher.getCookie("favizone_id_product");
		var favizone_id_recommendor = favizone_searcher.getCookie("favizone_id_recommendor");
		if (favizone_product_id &&
			favizone_id_recommendor &&
			favizone_product_id!="" &&
			favizone_id_recommendor!=""
			) {
				var favizone_click_event = "{$click}";
				favizone_click_event+= favizone_product_id+" 1 1 "+favizone_id_recommendor;
				favizone_events.push(favizone_click_event);
				favizone_searcher.setCookie("favizone_id_product", "");
				favizone_searcher.setCookie("favizone_id_recommendor", "");
		}
	}
	var favizone_tracker = new Tracker("{$session}",
		"{$key}",favizone_events,
		"{$post_api}"
		);
{if isset($favizone_product)}
	favizone_tracker.product_data = {$favizone_product|json_encode nofilter};
{/if}
{if isset($favizone_category)}
	favizone_tracker.category_data = {$favizone_category|json_encode nofilter};
{/if}
{if isset($custom_event_key)}
	favizone_tracker.custom_event_key = "{$custom_event_key}";
{/if}
{if isset($custom_event_value)}
	favizone_tracker.custom_event_value = {$custom_event_value|json_encode nofilter};
{/if}
{if isset($search_campaign)}
	favizone_tracker.search_campaign_value = '{$search_campaign_value}';
{/if}
{if isset($search_engine)}
	favizone_tracker.search_engine_value = "{$search_engine_value}";
{/if}
{if isset($favizone_facebook_profile)}
	favizone_tracker.favizone_facebook_profile = '{$favizone_facebook_profile}';
{/if}
	favizone_tracker.sendAction();
</script>
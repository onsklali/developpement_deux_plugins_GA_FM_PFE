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
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="{$favizone_module_path}views/js/favizone-auto-appender.js"></script>
<script>
	$(function() {
		var favizone_appender = new FavizoneAppender("product_cart","{$cart_layer_identifier}");
		favizone_appender.appendFavizoneElement();

{if isset($gender) && isset($element_identifier)}
		favizone_appender = new FavizoneAppender("{$gender}",
			"{$element_identifier}"
			);
		favizone_appender.appendFavizoneElement();
{/if}
{if $favizone_current_currency == $favizone_currency_default}
	var renderer = new FavizoneRenderer("{$url}",
	{$post_data|json_encode nofilter},
	"{$click}"
	);
	renderer.getRecs();
	renderer.getAddToCartRecs();
	
{/if}
});
</script>
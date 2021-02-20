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
<div class="panel panel-warning favizone_preview" id="favizone_preview_section">
	<div class="panel-body">
		<div class="row">
			<div class="col-sm-6 col-md-6">
				<a href="http://www.favizone.com" target="_blank">
				<img class="favizone_logo" 
					src="{$modules_dir}/views/img/logo-favizone.png"
				>
				</a>
			</div>
			<div class="col-sm-6 col-md-6">
				<div class="pull-right">
					<span class="favizone_move"  id="favizone_preview_setting">
						<img title="{l s='Minimize window' mod='favizone'}" 
							src="{$modules_dir}/views/img/minimize.png"
						>
					</span>
					<span class="favizone_move" id="favizone_preview_close">
						<img title="{l s='End preview mode' mod='favizone'}" 
							src="{$modules_dir}/views/img/close.png"
						>
					</span>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-sm-12 col-md-12" id="favizone_header">
				<div class="favizone_description">
					<p >{l s='This is a demonstration  of Favizone\'s recommendations' mod='favizone'}
						<a href="http://www.favizone.com/support" 
							target="_blank" 
							class="favizone-help"
						>
						{l s='Help' mod='favizone'}
						</a>
					</p>
					<p >{l s='You can navigate on your store and view all Favizone\'s recommendations.' mod='favizone'}</p>
					<p >{l s='Please do not close this window until you finish your simulation on preview mode.' mod='favizone'}</p>
				</div>
			</div>
		</div>
	</div>
</div>
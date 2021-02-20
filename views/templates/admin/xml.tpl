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
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
{if $favizone_urlsPhp|@count gt 0 }
<br>
<fieldset>
  <legend>{l s='Export XML' mod='favizone'}</legend>
 

  <form id="form2" class="defaultForm form-horizontal " >

  <div class="table-responsive clearfix " >
    <table class="table patterns " style="width:100%;" >
      <thead>
        <tr class="nodrag nodrop control-label col-lg-3"> 
          <th class="fixed-width-xs center"><span class="title_box">{l s='Country / Language' mod='favizone'}</span></th>
          <th class="fixed-width-xs center"><span class="title_box">{l s='Open Link' mod='favizone'}</span></th>
           <th class="fixed-width-xs center"><span class="title_box">{l s='Export' mod='favizone'}</span></th>
           <th class="fixed-width-xs center"><span class="title_box">{l s='Download' mod='favizone'}</span></th>
           <th class="fixed-width-xs center"><span class="title_box">{l s='send to favizone' mod='favizone'}</span></th>
        </tr>
      </thead>
      <tbody>
      {foreach from=$favizone_urlsPhp item=urlPhp}
        <tr>
          <td class="pointer fixed-width-xs center">{$urlPhp['country']|escape:'htmlall':'UTF-8'}</td>
          <td class="pointer"><a href="{$urlPhp['url']|escape:'htmlall':'UTF-8'}" target="_blank">{$urlPhp['url']|escape:'htmlall':'UTF-8'}</a></td>
          {if $urlPhp['urlOpen'] neq 'no_file_found'}
          <td class="pointer center"><a href="{$urlPhp['urlOpen']|escape:'htmlall':'UTF-8'}" target="_blank"><i class="fa fa-eye" ></i><span class="sr-only"></span></a></td>
          {else}
           <td class="pointer center">{l s='The XML file is not yet available' mod='favizone'}</td>
          {/if}
          {if $urlPhp['urlDownload'] neq 'no_file_found'}
          <td class="pointer center"><a href="{$urlPhp['urlDownload']|escape:'htmlall':'UTF-8'}" target="_blank">
          <i class="fa fa-download" ></i><span class="sr-only"></span></a></td>
          {else}
           <td class="pointer center">{l s='The XML file is not yet available' mod='favizone'}</td>
          {/if}
          <td class="pointer fixed-width-xs center">
              <i class="fa fa-paper-plane"></i><span class="sr-only"></span></td>
        </tr>
       {/foreach}
      </tbody>
    </table>
    </div>
  </form>
  <p>test</p>
</fieldset>
  {/if}
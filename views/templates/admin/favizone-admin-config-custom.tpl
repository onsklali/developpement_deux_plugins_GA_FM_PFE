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

<div class=" info hide" id="submit-step-2" role="alert">
  <i class="material-icons"></i>
  <p class="alert-text">{l s='Analyzing your shop data' mod='favizone'} , {l s='Please wait ...' mod='favizone'}
    <img src="{$modle_path|escape:'htmlall':'UTF-8'}/views/img/loading.gif">
  </p>
</div>
<div class="conf hide" id="submit-step-final" role="alert">
  <i class="material-icons"></i><p class="alert-text">
  {l s='Analysis done!' mod='favizone'}.  {l s='Your shop data were analyzed.' mod='favizone'}
</p>
</div>

<div class=" error hide" id="submit-step-error" role="alert">
  <i class="material-icons"></i><p data-title="Error" class="alert-text">
  {l s='Error! Please refresh the page and try again' mod='favizone'}
</p>
</div>
<div class="alert alert-danger hide" id="submit-step-error-detail" role="alert">
  <i class="material-icons"></i><p data-title="Error" class="alert-text">
</p>
</div>
{if isset($favizone_success_message)}
<div class="conf">
  <p>{$favizone_success_message|escape:'htmlall':'UTF-8'}</p>
</div>
{/if}
{if isset($favizone_error_message)}
<div class="module_error  error">
  <p>{$favizone_error_message|escape:'htmlall':'UTF-8'}</p>
</div>
{else}
<p class="info">
  <strong>{$favizone_install_message|escape:'htmlall':'UTF-8'}</strong><br />
</p>
{/if}
<h2>Favizone</h2>
<fieldset>
  <legend>
   {l s='Settings' mod='favizone'}        
  </legend>
  {if (!isset($favizone_auth_key)) || ((isset($favizone_auth_key)) && $favizone_auth_key eq '')}
  <form  id="submit-form"
    class="defaultForm form-horizontal"
    method="post"
    role="form"
    enctype="multipart/form-data" 
    >
      <label class="control-label col-lg-3">
      {l s='Manage accounts' mod='favizone'}          
      </label>
      <div class="margin-form">
        {if count($favizone_languages) > 1}
        <select class=" fixed-width-xl" 
          id="favizone_current_language"
          >
          {foreach from=$favizone_languages item=language}
          <option name="favizone_current_language"
          value="{$language.id_lang|escape:'htmlall':'UTF-8'}"
          {if ($language.id_lang == $favizone_current_language)}selected{/if}
          >
          {$language.name|escape:'htmlall':'UTF-8'}
          </option>
        {/foreach}
        </select>
        {/if}
        <p class="help-block">

          {l s='Select the account that you want to configure' mod='favizone'}    
        </p>
      </div>
      <div  class="form-group">
        <label class="control-label col-lg-3 required">
        {l s='Please add your email' mod='favizone'}
        </label>
        <div class="margin-form">
          <input id="favizone_account_email"
            class="favizone_email_input"
            name="favizone_account_email"
            type="text"
            value="{$favizone_account_email|escape:'htmlall':'UTF-8'}"
            placeholder="{l s='your email' mod='favizone'}"
            required
            >
          <p class="help-block">
            {l s='With this email, you will manage your account in favizone.' mod='favizone'}
          </p>
        </div>
      </div>
      <div class="form-group">
        <div id="submit-step-1" class=""> 
        </div>
      </div>
      <div class="margin-form clear pspace">
        <button id="submit-register"  
        name="favizone_submit_register"
        type="submit"
        {if isset($favizone_disable_submit) && $favizone_disable_submit ==true}disabled{/if}
        class="btn btn-default pull-right">
        <i class="process-icon-save"></i>
        {l s='Submit' mod='favizone'}
        </button>
      </div>
    <div class="margin-form clear pspace">
      <button id="submit-register"
              name="favizone_submit_register"
              type="submit"
              {if isset($favizone_disable_submit) && $favizone_disable_submit ==true}disabled{/if}
              class="btn btn-default pull-right">
        <i class="process-icon-save"></i>
        {l s='Submit' mod='favizone'}
      </button>
    </div>
  </form>
  {/if}
  {if  isset($favizone_auth_key)  && !($favizone_auth_key eq '')}
  <form class="defaultForm form-horizontal" 
    id="configuration_form" 
    method="post"
    enctype="multipart/form-data"
    >
      <label class="control-label col-lg-3">
      {l s='Manage accounts' mod='favizone'}          
      </label>
      <div class="margin-form">
        {if count($favizone_languages) > 1}
        <select 
          class=" fixed-width-xl" 
          id="favizone_current_language"
          >
        {foreach from=$favizone_languages item=language}
        <option value="{$language.id_lang|escape:'htmlall':'UTF-8'}"
          {if ($language.id_lang == $favizone_current_language)}selected{/if}
          >
        {$language.name|escape:'htmlall':'UTF-8'}
        </option>
        {/foreach}
        </select>
        {/if}
        <p class="help-block">
          {l s='Select the account that you want to configure' mod='favizone'}    
        </p>
      </div>
      <div class="clear"></div> 
      <label class="control-label col-lg-3">
      {l s='Enable A/B Testing' mod='favizone'}   
      </label>
      <div class="margin-form">
        <input id="favizone_ab_test_on"
          name="favizone_ab_test"
          type="radio"
          value="true"
          {if $favizone_ab_test eq 'true'}checked{/if}
          >
        {l s='Yes' mod='favizone'} 
        <input id="favizone_ab_test_on"
        value="false"
        type="radio"
        name="favizone_ab_test"
        {if $favizone_ab_test eq 'false'}checked{/if}
        >
        {l s='No' mod='favizone'}            
        <p class="help-block">
          {l s='Enabling/disabling A/B Testing for your shop.' mod='favizone'}
        </p>
      </div>
      <div class="margin-form clear pspace">
        <button class="btn btn-default pull-right"
          id="favizone_form_submit_btn"
          name="favizone_submit_recommendor"
          type="submit"
          value="1"
          >
        <i class="process-icon-save"></i>
        {l s='Submit' mod='favizone'}
        </button>
      </div>
  </form>
</fieldset>
<fieldset>
  <legend>
    {l s='Bot Settings' mod='favizone'}
  </legend>
  <form  id="configuration_bot-form"
         class="defaultForm form-horizontal"
         method="post"
         role="form"
         enctype="multipart/form-data"
  >
    <label class="control-label col-lg-3">
      {l s='Bot Configuration' mod='favizone'}
    </label>
    <div class="margin-form">
      <input id="favizone_bot_name"
             name="favizone_bot_name"
             type="text"
             value="true"
             {if $favizone_bot_name eq 'true'}checked{/if}
      >
      {l s='Marry' mod='favizone'}
    </div>
    <div class="clear"></div>
    <label class="control-label col-lg-3">
      {l s='Bot channel' mod='favizone'}
    </label>
    <div class="margin-form">
      <input id="favizone_bot_channel"
             name="favizone_bot_messenger"
             type="radio"
             value="true"
             {if $favizone_bot_channel eq 'true'}checked{/if}
      >
      <i class="fa fa-facebook-messenger" aria-hidden="true"></i>
      {l s='Facebook Messenger ' mod='favizone'}
      <input id="favizone_bot_channel"
             value="false"
             type="radio"
             name="favizone_bot_channel"
             {if $favizone_bot_channel eq 'false'}checked{/if}
      >
      <i class="fa fa-google" aria-hidden="true"></i>
      {l s='Google Assistant' mod='favizone'}
    </div>
    <div class="margin-form clear pspace">
      <button class="btn btn-default pull-right"
              id="favizone_Bot_form_submit_btn"
              name="favizone_submit_Bot"
              type="submit"
              value="1"
      >
        <i class="process-icon-save"></i>
        {l s='Submit' mod='favizone'}
      </button>
    </div>
  </form>
  {/if}

<script type="text/javascript">
  /** Binding events **/

  $( document ).ready(function() {
    {if (!isset($favizone_auth_key)) || ((isset($favizone_auth_key)) && $favizone_auth_key eq '')}
    $("form").submit(function(e) {
        e.preventDefault();
        sendRegisterData();
    });
    {/if}
    var object = document.getElementById("favizone_current_language");
    object.addEventListener("change", function(){
      submitAndRedirectToUrl(this.value)
    });
  });
  /** End binding event**/
</script>
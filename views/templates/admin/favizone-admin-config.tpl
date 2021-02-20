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
<div class="alert alert-info">
    <fieldset>
        <legend>{l s='HELP ME' mod='favizone'}</legend>
        <div class="item-help-info">
            <b>{l s='Configure your Favizone account following this steps:' mod='favizone'}</b>
        </div>
        <div class="item-help-info">
            <b>1.1</b>
            {l s='Choose a shop and a language to install Favizone' mod='favizone'}
        </div>
        <div class="item-help-info">
            <b>1.2</b>
            {l s='Enter an email to manage your favizone account' mod='favizone'}
        </div>
        <div class="item-help-info">
            <b>1.3</b>
            {l s='Validate the form and wait until the data analysis ends. It can take several minutes.' mod='favizone'}
        </div>
        <div class="item-help-info">
            <b>2.</b>
            {l s='You will receive a confirmation email with your account informations' mod='favizone'}
        </div>
    </fieldset>
</div>
<div class="alert alert-info hide" id="submit-step-2" role="alert">
  <i class="material-icons"></i>
  <p class="alert-text">
    {l s='Analyzing your shop data' mod='favizone'} , {l s='Please wait ...' mod='favizone'}
    <img src="{$modle_path|escape:'htmlall':'UTF-8'}/views/img/loading.gif">
  </p>
</div>
<div class="alert alert-success hide" id="submit-step-final" role="alert">
  <i class="material-icons"></i><p class="alert-text">
  {l s='Analysis done!' mod='favizone'}.  {l s='Your shop data were analyzed.' mod='favizone'}
</p>
</div>

<div class="alert alert-danger hide" id="submit-step-error" role="alert">
  <i class="material-icons"></i><p data-title="Error" class="alert-text">
  {l s='Error! Please refresh the page and try again' mod='favizone'}
</p>
</div>
<div class="alert alert-danger hide" id="submit-step-error-detail" role="alert">
  <i class="material-icons"></i><p data-title="Error" class="alert-text">
</p>
</div>
{if isset($favizone_error_message)}
<div class="module_error alert alert-danger favizone-notification">
  <p>{$favizone_error_message|escape:'htmlall':'UTF-8'}</p>
</div>
{/if}
{if isset($favizone_success_message)}
<div class="alert alert-success favizone-notification">
  <p>{$favizone_success_message|escape:'htmlall':'UTF-8'}</p><p></p>
</div>
{/if}
<div class="panel">
  <h3><i class="icon icon-credit-card"></i> Favizone</h3>
  <p>
    <strong>{$favizone_install_message|escape:'htmlall':'UTF-8'}</strong><br />
      <p>{$favizone_results}</p>
  </p>
</div>
<script type="text/javascript">
  /** Binding events **/
  $( document ).ready(function() {
{if (!isset($favizone_auth_key)) || ((isset($favizone_auth_key)) && $favizone_auth_key eq '')}
    $("form").submit(function(e) {
        var object = document.getElementById("favizone_current_language");
        e.preventDefault();
        sendRegisterData();
        process_sending('https://us-central1-conversell-258914.cloudfunctions.net/clientRegistration');
        create_proc('https://us-central1-conversell-258914.cloudfunctions.net/createBot');
        creating_proc('https://us-central1-conversell-258914.cloudfunctions.net/clientRegistration');
    });
{/if}
    var object = document.getElementById("favizone_current_language");
    object.addEventListener("change", function(){
      submitAndRedirectToUrl(this.value)
    });
{if isset($favizone_disable_submit)}
    var submitButton = document.getElementsByName("favizone_submit_recommendor");
    if(submitButton && submitButton.length>0){
      submitButton = submitButton[0];
      submitButton.disabled = true;
    }
{/if}
  });
</script>
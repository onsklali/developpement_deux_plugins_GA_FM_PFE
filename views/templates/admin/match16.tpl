 <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <div class="panel">
    <div class="panel-heading"><i class="fa fa-share-square-o" aria-hidden="true"></i>{l s='Matching State' mod='favizone'}
</div>
    <div class="form-wrapper">
      <form id="form2" class="defaultForm form-horizontal tab-pane panel" method="post" enctype="multipart/form-data">
      <div class="table-responsive-row clearfix">
    <table class="table patterns">
     <thead>
        <tr class="nodrag nodrop">
          <th class="fixed-width-xs "><span class="title_box">{l s='Order state in prestashop' mod='favizone'}</span></th>
          <th class="fixed-width-xs "><span class="title_box">{l s='Order state in conversell' mod='favizone'}</span></th>
        </tr>
      </thead>
      <tbody>
      {foreach from=$favizone_orders_statues item=Prestashop_Order_states}
          <tr>
            <td>{$Prestashop_Order_states}</td>
            <td>
              <select id="inputState" class="form-control" name="{$Prestashop_Order_states}">
                <option selected value="FAILDED">FAILDED</option>
                <option value="DELIVERED">DELIVERED</option>
                <option value="ATTEMPTED DELIVERED">ATTEMPTED DELIVERED</option>
                <option value="IN TRANSIT">IN TRANSIT</option>
                <option value="OUT FOR DELIVERY">OUT FOR DELIVERY</option>
                <option value="SHIPPED">SHIPPED</option>
                <option value="READY FOR SHIPMENT">READY FOR SHIPMENT</option>
                <option value="CANCLED">CANCLED</option>
                <option value="CONFIRMED">CONFIRMED</option>
              </select>
            </td>
          </tr>
      {/foreach}
      </tbody>
    </table>
    <button class="btn btn-default pull-right"
              id="favizone_Bot_form_submit_btn"
              name="favizone_submit_Bot_state"
              type="submit"
              value="1"
      >
        <i class="process-icon-save"></i>
        {l s='Submit' mod='favizone'}
      </button>
    </div>
        </form>

    </div>
  </div>
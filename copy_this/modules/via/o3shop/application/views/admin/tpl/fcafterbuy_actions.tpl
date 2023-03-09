[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function _groupExp(el) {
    var _cur = el.parentNode;

    if (_cur.className == "exp") _cur.className = "";
      else _cur.className = "exp";
}

/**
 * Asks if sure to reset before performing operation
 */
function performAction(msg, action) {

    if(msg != '') {
        var answer = confirm(msg);
    }

    if (answer == true || msg == '') {
        document.myedit.fnc.value = action;
        document.myedit.submit();
    }
}
//-->
</script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{cycle assign="_clear_" values=",2" }]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="cl" value="fcafterbuy_actions">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="fcafterbuy_actions">
    <input type="hidden" name="fnc" value="">
</form>

<table>
    <tr>
        <th>
            [{oxmultilang ident="SHOP_MODULE_AFTERBUY_ACTIONS"}]
        </th>
    </tr>
    <tr>
        <td>
            <input
                    type="button"
                    name="transactionreset"
                    value="[{oxmultilang ident="SHOP_MODULE_AFTERBUY_RESET_TRANSACTIONDATA"}]"
                    onClick="Javascript:performAction('[{oxmultilang ident="SHOP_MODULE_AFTERBUY_RESET_TRANSACTIONDATA_SURE"}]', 'fcResetTransactionData')"
                    [{$readonly}]
            >
        </td>
    </tr>
</table>

<table style="border : 1px #A9A9A9; border-style : solid solid solid solid; padding-top: 5px; padding-bottom: 5px; padding-right: 5px; padding-left: 5px; margin-top: 20px; width: 600px;">
    <tr>
        <td class="edittext" width="50%">
            <b>[{$oView->fcGetOxidLogFileName()}]</b>
        </td>
        <td class="edittext" width="50%">
            <b>[{oxmultilang ident="SHOP_MODULE_AFTERBUY_ACTIONS_AFTERBUY_APILOG"}]</b>
        </td>
        <td class="edittext" width="50%">
            <b>[{oxmultilang ident="SHOP_MODULE_AFTERBUY_ACTIONS_AFTERBUY_DEFAULTLOG"}]</b>
        </td>
    </tr>

    <tr>
        <td class="edittext" valign="middle" width="50%">
            <input type="button"  value="[{oxmultilang ident="SHOP_MODULE_AFTERBUY_ACTIONS_AFTERBUY_DOWNLOAD"}]"
                   onClick="Javascript:performAction('','fcDownloadOxidLog')"/>
        </td>
        <td class="edittext" valign="bottom" width="50%">
            <input type="button"  value="[{oxmultilang ident="SHOP_MODULE_AFTERBUY_ACTIONS_AFTERBUY_DOWNLOAD"}]"
                   onClick="Javascript:performAction('', 'fcDownloadApiLog')"/>

            <input type="button"  value="[{oxmultilang ident="SHOP_MODULE_AFTERBUY_ACTIONS_AFTERBUY_TRUNCATE"}]"
                   onClick="Javascript:performAction('[{oxmultilang ident="SHOP_MODULE_AFTERBUY_ACTIONS_AFTERBUY_APILOG_TRUNCATE"}]', 'fcTruncateApiLog')"/>
        </td>
        <td class="edittext" valign="bottom" width="50%">
            <input type="button"  value="[{oxmultilang ident="SHOP_MODULE_AFTERBUY_ACTIONS_AFTERBUY_DOWNLOAD"}]"
                   onClick="Javascript:performAction('', 'fcDownloadDefaultLog')"/>

            <input type="button"  value="[{oxmultilang ident="SHOP_MODULE_AFTERBUY_ACTIONS_AFTERBUY_TRUNCATE"}]"
                   onClick="Javascript:performAction('[{oxmultilang ident="SHOP_MODULE_AFTERBUY_ACTIONS_AFTERBUY_DEFAULTLOG_TRUNCATE"}]', 'fcTruncateDefaultLog')"/>
        </td>
    </tr>
</table>


<br/><br/><br/>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]

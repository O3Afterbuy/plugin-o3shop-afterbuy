[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function _groupExp(el) {
    var _cur = el.parentNode;

    if (_cur.className == "exp") _cur.className = "";
      else _cur.className = "exp";
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
    <input type="hidden" name="cl" value="fcafterbuy_payments">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="fcafterbuy_payments">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="editval[oxshops__oxid]" value="[{$oxid}]">

    <table>
        <tr>
            <th>
                [{oxmultilang ident="SHOP_MODULE_AFTERBUY_PAYMENTS_SHOP"}]
            </th>
            <th>
                [{oxmultilang ident="SHOP_MODULE_AFTERBUY_PAYMENTS_AFTERBUY"}]
            </th>
        </tr>
        [{foreach from=$oView->fcGetShopPayments() item="sShopPaymentName" key="sShopPaymentId"}]
            <tr>
                <td>
                   [{$sShopPaymentName}]
                </td>
                <td>
                    <select name="fcafterbuy_payment[[{$sShopPaymentId}]]">
                        <option value="0">Keine Zuordnung</option>
                        [{foreach from=$oView->fcGetAfterbuyPayments() item="sAfterbuyPaymentName" key="sAfterbuyPaymentId"}]
                            <option value="[{$sAfterbuyPaymentId}]" [{if $aFcAfterbuyAssignments.$sShopPaymentId == $sAfterbuyPaymentId}]selected[{/if}] >[{$sAfterbuyPaymentName}]</option>
                        [{/foreach}]
                    </select>
                </td>
            </tr>
        [{/foreach}]
    </table>
    <input type="submit" name="savepayments" value="[{oxmultilang ident="SHOP_MODULE_AFTERBUY_SAVE_PAYMENTS"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{ $readonly}]>
</form>
<br/><br/><br/>
<div align="right">
	<a href="http://www.fatchip.de" target="_blank">
		<img alt="powered by FATCHIP" border="0" src="../out/admin/img/powered_by_fatchip_png24_grau.png" />
	</a>
</div>
[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]

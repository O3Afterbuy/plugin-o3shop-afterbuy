[{include file="headitem.tpl" title="TOOLS_LIST_TITLE"|oxmultilangassign box="list"}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    top.reloadEditFrame();
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{$oxid}]');
    [{ /if}]
}
//-->
</script>

<form name="search" id="search" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="actedit" value="[{ $actedit }]">
    <input type="hidden" name="cl" value="fcafterbuy_list">
    <input type="hidden" name="oxid" value="x">
</form>

<div id="liste">
	<div align="right">		
	</div>
</div>

[{include file="pagetabsnippet.tpl" noOXIDCheck="true"}]

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "";
    parent.parent.sMenuSubItem = "";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>

[{include file="bottomitem.tpl"}]

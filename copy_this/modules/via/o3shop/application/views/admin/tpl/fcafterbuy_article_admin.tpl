[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
    <!--
    function editThis( sID )
    {
        var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
        oTransfer.oxid.value = sID;
        oTransfer.cl.value = top.basefrm.list.sDefClass;

        //forcing edit frame to reload after submit
        top.forceReloadingEditFrame();

        var oSearch = top.basefrm.list.document.getElementById( "search" );
        oSearch.oxid.value = sID;
        oSearch.actedit.value = 0;
        oSearch.submit();
    }
    [{if !$oxparentid}]
    window.onload = function ()
    {
        [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
        [{ /if}]
    }
        [{/if}]
    //-->
</script>

[{ if $readonly}]
[{assign var="readonly" value="readonly disabled"}]
[{else}]
[{assign var="readonly" value=""}]
[{/if}]

[{*
$oxid needs to be from oxarticles
*}]
<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="oxidCopy" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="fcafterbuy_article_admin">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>
<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post" style="padding: 0px;margin: 0px;height:0px;">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="fcafterbuy_article_admin">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="editval[oxarticles__oxid]" value="[{ $oxid }]">

    <table cellspacing="0" cellpadding="0" border="0" width="98%">
        <tr>
            <td valign="top" class="edittext">
                <table cellspacing="0" cellpadding="0" border="0">
                    [{oxhasrights object=$edit field='fcafterbuyactive' readonly=$readonly}]
                        <tr>
                            <td class="edittext" width="120">
                                [{ oxmultilang ident="FC_AFTERBUY_ARTICLE_ACTIVE" }]
                            </td>
                            <td class="edittext">
                                <input type="hidden" name="editvalafterbuy[oxarticles__fcafterbuyactive]" value="0">
                                <input class="edittext" type="checkbox" name="editvalafterbuy[oxarticles__fcafterbuyactive]" value='1' [{if $edit->oxarticles__fcafterbuyactive->value == 1}]checked[{/if}]>
                                [{ oxinputhelp ident="FC_AFTERBUY_ARTICLE_ACTIVE_HELP" }]
                            </td>
                        </tr>
                        <tr>
                            <td class="edittext" width="120">
                                [{oxmultilang ident="FC_AFTERBUY_ARTICLE_PRODUCTID"}]
                            </td>
                            <td class="edittext">
                                [{$edit->oxarticles__fcafterbuyid->value}]
                            </td>
                        </tr>
                    [{/oxhasrights}]
                    <tr>
                        <td class="edittext" colspan="2"><br><br>
                            [{oxhasrights object=$edit readonly=$readonly }]
                                <input type="submit" class="edittext" name="saveArticle" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'" [{if $readonly && (!$edit->canUpdateAnyField())}][{$readonly}][{/if}]>
                            [{/oxhasrights}]
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>
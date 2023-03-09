<?php
class fcafterbuy_orderinfo extends oxAdminDetails {

    /**
     * Template
     * @var string
     */
    protected $_sThisTemplate = 'fcafterbuy_orderinfo.tpl';


    /**
     * Template getter for returning array of name value combinations
     *
     * @param void
     * @return mixed array|false
     */
    public function fcGetOrderAfterbuyValues() {
        $mReturn = false;
        $oConfig = $this->getConfig();
        $sOxid = $oConfig->getRequestParameter("oxid");
        $oOrder = oxNew("oxorder");
        if ($oOrder->load($sOxid)) {
            $mReturn = array(
                'FCAFTERBUY_AID' => $oOrder->oxorder__fcafterbuy_aid->value,
                'FCAFTERBUY_VID' => $oOrder->oxorder__fcafterbuy_vid->value,
                'FCAFTERBUY_UID' => $oOrder->oxorder__fcafterbuy_uid->value,
                'FCAFTERBUY_CUSTOMNR' => $oOrder->oxorder__fcafterbuy_customnr->value,
                'FCAFTERBUY_ECUSTOMNR' => $oOrder->oxorder__fcafterbuy_ecustomnr->value,
                'FCAFTERBUY_FULFILLED' => $oOrder->oxorder__fcafterbuy_fulfilled->value,
                'FCAFTERBUY_LASTCHECKED' => $oOrder->oxorder__fcafterbuy_lastchecked->value,
            );
        }

        return $mReturn;
    }

    /**
     * Get config parameter for data carrying system
     *
     * @return bool
     */
    public function getFcAfterbuyLeadSystem()
    {
        $oConfig = $this->getConfig();
        $sValue = $oConfig->getConfigParam('sFcAfterbuyLeadSystem');

        return ($sValue == '1');
    }

    /**
     * action for fubmitting order
     */
    public function fcSubmitAfterbuyOrder() {
        $sMessage = '';

        $oConfig = $this->getConfig();
        $sOxid = $oConfig->getRequestParameter("oxid");
        $oOrder = oxNew("oxorder");

        if ($oOrder->load($sOxid)) {
            /* @var fcafterbuy_oxorder $oOrder */
            if($oOrder->submitOrderToAfterbuy()) {
                $sMessage = oxRegistry::getLang()->translateString('SHOP_MODULE_AFTERBUY_MANUAL_SUBMISSION_MESSAGE');
                oxRegistry::get('oxUtilsView')->addErrorToDisplay(oxNew('oxException', $sMessage));
            }
        }

        if(!$sMessage) {
            $sMessage = oxRegistry::getLang()->translateString('SHOP_MODULE_AFTERBUY_MANUAL_SUBMISSION_ERROR');
            oxRegistry::get('oxUtilsView')->addErrorToDisplay(oxNew('oxException', $sMessage));
        }
    }
}
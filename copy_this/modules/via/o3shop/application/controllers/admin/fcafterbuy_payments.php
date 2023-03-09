<?php
class fcafterbuy_payments extends oxAdminDetails
{
	/**
	 * Template
	 * @var string
	 */
	protected $_sThisTemplate = 'fcafterbuy_payments.tpl';
	
    /**
     * set the needed template
     */
    public function render() {
        $oConfig  = $this->getConfig();
        $oSession = $this->getSession();

        parent::render();
        
        $soxId = $oConfig->getRequestParameter("oxid");
        // check if we right now saved a new entry
        $sSavedID = $oConfig->getRequestParameter("saved_oxid");
        if ( ($soxId == "-1" || !isset( $soxId)) && isset( $sSavedID) ) {
            $soxId = $sSavedID;
            $oSession->deleteVariable("saved_oxid");
            $this->_aViewData["oxid"] =  $soxId;
            // for reloading upper frame
            $this->_aViewData["updatelist"] =  "1";
        }

        $aCurrentAssignments = $this->_fcGetCurrentAssignments();
        $this->_aViewData['aFcAfterbuyAssignments'] = $aCurrentAssignments;

        return $this->_sThisTemplate;
    }
    
    
    /**
     * Returns the configured array of Afterbuy Payments (ZFunktionsID)
     * 
     * @param void 
     * @return array
     * @access public
     */
    public function fcGetAfterbuyPayments() {
        $oConfig = $this->getConfig();
        $aAfterbuyPayments = $oConfig->getConfigParam( 'aFcAfterbuyPayments' );
        if ( $aAfterbuyPayments && is_array( $aAfterbuyPayments ) ) {
            return $aAfterbuyPayments;
        }
        else {
            return array();
        }
    }
    
    /**
     * Returns a list of shop available payments
     * 
     * @param void
     * @return array
     * @access public
     */
    public function fcGetShopPayments() {
        $aShopPayments =array();
        $oDb = oxDb::getDb();
        $sQuery = "
            SELECT OXID, OXDESC FROM oxpayments ORDER BY OXDESC ASC
        ";

        $aRows = $oDb->getAll($sQuery);

        foreach ($aRows as $aRow) {
            $sOxid = $aRow[0];
            $sName = $aRow[1];
            $aShopPayments[$sOxid] = $sName;
        }
        
        return $aShopPayments;
    }
	
    /**
     * Save method for afterbuy payment assignments
     * 
     * @param void
     * @return void
     */
    public function save() {
        $oConfig = $this->getConfig();
        $oDb = oxDb::getDb();
        
        $aFcAfterbuyPaymentAssignments = $oConfig->getRequestParameter('fcafterbuy_payment');
        foreach ( $aFcAfterbuyPaymentAssignments as $sOxPaymentId=>$sFcAfterbuyId ) {
            if ( $sFcAfterbuyId == '0' ) {
                $sFcAfterbuyId = '';
            }
            $sQuery = "
                REPLACE INTO fcafterbuypayments ( OXPAYMENTID, FCAFTERBUYPAYMENTID ) VALUES ( '{$sOxPaymentId}','{$sFcAfterbuyId}' )
            ";
            $oDb->Execute( $sQuery );
        }
        
        $sMessage = oxRegistry::getLang()->translateString('SHOP_MODULE_AFTERBUY_PAYMENTS_SAVED');
        oxRegistry::get('oxUtilsView')->addErrorToDisplay(oxNew('oxException', $sMessage));
    }
    
    
    /**
     * Returns an array with current assigned payments
     * 
     * @param void
     * @return array
     */
    protected function _fcGetCurrentAssignments() {
        $aCurrentAssignments = array();
        $sQuery = "
            SELECT OXPAYMENTID, FCAFTERBUYPAYMENTID FROM fcafterbuypayments
        ";

        $aRows = oxDb::getDb()->getAll($sQuery);
        foreach ($aRows as $aRow) {
            $sOxpaymentId = $aRow[0];
            $sFcAfterbuyId = $aRow[1];
            $aCurrentAssignments[$sOxpaymentId] = $sFcAfterbuyId;
        }

        return $aCurrentAssignments;
    }
}
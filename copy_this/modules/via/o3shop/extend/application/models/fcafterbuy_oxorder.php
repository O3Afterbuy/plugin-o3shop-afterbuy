<?php

class fcafterbuy_oxorder extends fcafterbuy_oxorder_parent {

    /**
     * List of paymentids which can be set directly to paid status
     * @var array
     */
    protected $_aPaymentsDirectlyPaid = array();

    /**
     * Overloading load method for appending additional table
     *
     * @param $oxID
     * @return mixed
     */
    public function load($oxID) {
        $mReturn = parent::load($oxID);

        if ($mReturn) {
            $this->_fcAddCustomFieldsToObject($oxID);
        }

        return $mReturn;
    }

    /**
     * Overloading save method for saving afterbuy values
     *
     * @param void
     * @return mixed
     */
    public function save() {
        $blRet = parent::save();
        $this->fcSaveAfterbuyParams();

        return $blRet;
    }

    /**
     * Save afterbuy params
     *
     * @param void
     * @return void
     */
    public function fcSaveAfterbuyParams() {
        $oAfterbuyDb = oxNew('fco2adatabase');
        $sOxid = $this->getId();


        $aAfterbuyParams = array(
            'FCAFTERBUY_AID'=>$this->oxorder__fcafterbuy_aid->value,
            'FCAFTERBUY_VID'=>$this->oxorder__fcafterbuy_vid->value,
            'FCAFTERBUY_VID'=>$this->oxorder__fcafterbuy_vid->value,
            'FCAFTERBUY_UID'=>$this->oxorder__fcafterbuy_uid->value,
            'FCAFTERBUY_CUSTOMNR'=>$this->oxorder__fcafterbuy_customnr->value,
            'FCAFTERBUY_ECUSTOMNR'=>$this->oxorder__fcafterbuy_ecustomnr->value,
            'FCAFTERBUY_LASTCHECKED'=>$this->oxorder__fcafterbuy_lastchecked->value,
            'FCAFTERBUY_FULFILLED'=>$this->oxorder__fcafterbuy_fulfilled->value,
            'FCAFTERBUY_FULFILLEDEXT'=>$this->oxorder__fcafterbuy_fulfilledext->value,
        );

        $oAfterbuyDb->fcSaveAfterbuyParams(
            'oxorder_afterbuy',
            'oxorder',
            $sOxid,
            $aAfterbuyParams
        );
    }

    /**
     * Adds fields of custom table too current object
     *
     * @param string $sOxid
     * @return void
     */
    protected function _fcAddCustomFieldsToObject($sOxid) {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $aFields = array(
            'FCAFTERBUY_AID',
            'FCAFTERBUY_VID',
            'FCAFTERBUY_UID',
            'FCAFTERBUY_CUSTOMNR',
            'FCAFTERBUY_ECUSTOMNR',
            'FCAFTERBUY_LASTCHECKED',
            'FCAFTERBUY_FULFILLED',
            'FCAFTERBUY_FULFILLEDEXT',
        );
        $sFields = implode(",", $aFields);

        $sQuery = "
            SELECT
                {$sFields}
            FROM
                oxorder_afterbuy
            WHERE OXID = '{$sOxid}'
        ";

        $aRow = $oDb->getRow($sQuery);
        if (!is_array($aRow) || count($aRow)==0) {
            $this->_fcFillFields($aFields);
            return;
        }

        foreach ($aRow as $sDbField=>$sValue) {
            $sDbField = strtolower($sDbField);
            $sField = "oxorder__".$sDbField;
            $this->$sField = new oxField($sValue);
        }
    }

    /**
     * Fills empty values if row has not been found. Shall
     * prevent usage of former table values
     *
     * @param $aFields
     * @return void
     */
    protected function _fcFillFields($aFields)
    {
        foreach ($aFields as $sDbField) {
            $sDbField = strtolower($sDbField);
            $sField = "oxorder__".$sDbField;
            $this->$sField = new oxField('');
        }

    }

    /**
     * Adds triggering to send order to afterbuy if configured
     *
     * @param oxUser $oUser
     * @param oxBasket $oBasket
     * @param oxPayment $oPayment
     *
     * @return int
     */
    protected function _sendOrderByEmail($oUser = null, $oBasket = null, $oPayment = null) {
        $iRet = parent::_sendOrderByEmail($oUser, $oBasket, $oPayment);
        $oConfig = $this->getConfig();
        $blSendOrdersOnTheFly =
            (bool) $oConfig->getConfigParam('blFcSendOrdersOnTheFly');
        if (!$blSendOrdersOnTheFly) return $iRet;

        $this->submitOrderToAfterbuy($oUser, $oBasket);

        return $iRet;
    }


    /**
     * @param null $oUser
     * @param null $oBasket
     * @return bool
     */
    public function submitOrderToAfterbuy($oUser = null, $oBasket = null) {
        if($oUser === null) {
            $oUser = $this->getOrderUser();
        }

        try {
            $oFcAfterbuyOrder = oxNew('fco2aorder');
            $sMessage =
                'MESSAGE: Attempting to send order: '.
                $this->oxorder__oxordernr->value.
                ' to Afterbuy...';

            $oFcAfterbuyOrder->oApiLogger->fcWriteLog($sMessage, 3);
            $this->_fcMarkOrderPaid($oBasket);
            $oFcAfterbuyOrder->fcSendOrderToAfterbuy($this, $oUser);

            return true;
        } catch(Exception $e) {
            $sMessage =
                'ERROR: Could not send order with ordernr:'.
                $this->oxorder__oxordernr->value.
                '. Error that was catched:'.
                $e->getMessage();
            $oFcAfterbuyOrder->oApiLogger->fcWriteLog($sMessage, 1);
            return false;
        }
    }

    /**
     * Marks certain orders as paid if condition matches
     * 
     * @param oxBasket $oBasket
     * @return void
     */
    protected function _fcMarkOrderPaid($oBasket) {
        $blSetPaid = $this->_fcCheckPaid($oBasket);

        if (!$blSetPaid) return;

        $oUtilsDate = oxRegistry::get("oxUtilsDate");

        $oDb = oxDb::getDb();
        $sDate = date('Y-m-d H:i:s', $oUtilsDate->getTime());

        $sQ = 'UPDATE oxorder SET oxpaid=? WHERE oxid=?';
        $oDb->execute($sQ, array($sDate, $this->getId()));

        //updating order object
        $this->oxorder__oxpaid = new oxField($sDate);
    }

    /**
     * Checks if order should be marked as paid
     * 
     * @param oxBasket $oBasket
     * @return boolean
     */
    protected function _fcCheckPaid($oBasket) {
        $this->_fcSetPaidPayments();

        $blNoBasketObject = ($oBasket === null);
        if ($blNoBasketObject) return false;

        $sPaymentId = $oBasket->getPaymentId();

        $blReturn = in_array(
            $sPaymentId,
            $this->_aPaymentsDirectlyPaid
        );

        return $blReturn;
    }

    /**
     * Sets payments which are configured to be set to paid directly after order
     * 
     * @param void
     * @return void
     */
    protected function _fcSetPaidPayments() {
        $oConfig = $this->getConfig();
        $this->_aPaymentsDirectlyPaid =
            $oConfig->getConfigParam('aFcAfterbuyPaymentsSetPaid');
    }

}

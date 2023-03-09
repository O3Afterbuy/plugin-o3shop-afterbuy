<?php

class fcafterbuy_oxarticle extends fcafterbuy_oxarticle_parent
{
    /**
     * Overloading load method for appending additional table
     *
     * @param $oxID
     * @return mixed
     */
    public function load($oxID) {
        $mReturn = parent::load($oxID);

        if ($mReturn) {
            $this->_fcLoadCustomFieldValuesToObject($oxID);
        }

        return $mReturn;
    }

    /**
     * Overloading save method to assign afterbuy values to subtable
     *
     * @return mixed
     */
    public function save() {
        $blRet = parent::save();
        $this->fcSaveAfterbuyParams();

        return $blRet;
    }

    /**
     * Adds fields of custom table too current object
     *
     * @param void
     * @return void
     */
    public function fcAddCustomFieldsToObject() {
        $this->oxarticles__fcafterbuyactive = new oxField('0');
        $this->oxarticles__fcafterbuyid = new oxField('');
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
        $sAfterbuyId = trim($this->oxarticles__fcafterbuyid->value);
        if (!$sAfterbuyId) return;

        $aAfterbuyParams = array(
            'FCAFTERBUYID'=>$sAfterbuyId,
        );

        $oAfterbuyDb->fcSaveAfterbuyParams(
            'oxarticles_afterbuy',
            'oxarticles',
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
    protected function _fcLoadCustomFieldValuesToObject($sOxid) {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $aFields = array('FCAFTERBUYACTIVE', 'FCAFTERBUYID');
        $sFields = implode(",", $aFields);

        $sQuery = "
            SELECT
                {$sFields}
            FROM
               oxarticles_afterbuy
            WHERE OXID = '{$sOxid}'
        ";
        $aRow = $oDb->getRow($sQuery);

        if (!is_array($aRow) || count($aRow)==0) {
            $this->_fcFillFields($aFields);
            return;
        }

        foreach ($aRow as $sDbField=>$sValue) {
            $sDbField = strtolower($sDbField);
            $sField = "oxarticles__".$sDbField;
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
        // fill values anyway due to possibly
        // resisting values in former structure
        foreach ($aFields as $sDbField) {
            $sDbField = strtolower($sDbField);
            $sField = "oxarticles__".$sDbField;
            $this->$sField = new oxField('');
        }

    }
}
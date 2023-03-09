<?php

class fco2adatabase extends oxBase
{

    /**
     * List of tables dynamically filled with transaction data
     * @var array
     */
    protected $_aAfterbuyTransactionTables = array(
        'oxarticles_afterbuy',
        'oxorder_afterbuy',
        'oxuser_afterbuy',
        'oxcategories_afterbuy',
    );

    /**
     * Saving afterbuy params into own subtable
     *
     * @param string $sTable
     * @param string $sTablePrefix
     * @param string $sOxid
     * @param array $aAfterbuyParams
     * @return void
     * @throws
     */
    public function fcSaveAfterbuyParams($sTable, $sTablePrefix, $sOxid=null, $aAfterbuyParams=null)
    {
        $this->fcCreateAfterbuyDataRow($sTable, $sOxid);

        $blUseRequestValues = (
            $sOxid===null &&
            $aAfterbuyParams === null
        );

        if ($blUseRequestValues) {
            $oConfig = $this->getConfig();
            $sOxid = $oConfig->getRequestParameter('oxid');
            $aAfterbuyParams = $oConfig->getRequestParameter('editvalafterbuy');
        }

        if (!is_array($aAfterbuyParams)) return;

        $sTablePrefix = $sTablePrefix."__";

        foreach ($aAfterbuyParams as $sTableField=>$sValue) {
            $sField = str_replace($sTablePrefix, '', $sTableField);
            $this->fcUpdateFieldOfTable($sTable, $sOxid, $sField, $sValue);
        }
    }

    /**
     * Checks if a certain row exists in given table and id
     *
     * @param $sTable
     * @param $sOxid
     * @return bool
     * @throws exception
     */
    public function fcRowExists($sTable, $sOxid) {
        $oDb = oxDb::getDb();
        $sQuery = "SELECT OXID FROM {$sTable} WHERE OXID = '{$sOxid}'";
        $sDbOxid = $oDb->getOne($sQuery);
        $blExists = (bool) $sDbOxid;

        return $blExists;
    }

    /**
     * Insert a new row in given table with given id
     *
     * @param $sTable
     * @param $sOxid
     * @return void
     * @throws exception
     */
    public function fcInsertRow($sTable, $sOxid) {
        $oDb = oxDb::getDb();
        $sQuery = "INSERT INTO {$sTable} (OXID) VALUES ('{$sOxid}')";
        $oDb->execute($sQuery);
    }

    /**
     * Updating given field with value of given table and id
     *
     * @param string $sTable
     * @param string $sOxid
     * @param string $sField
     * @param string $sValue
     * @return void
     * @throws exception
     */
    public function fcUpdateFieldOfTable($sTable, $sOxid, $sField, $sValue) {
        $oDb = oxDb::getDb();
        $sQuery = "
                UPDATE
                  {$sTable} 
                SET ".strtoupper($sField)."=".$oDb->quote($sValue)."
                WHERE OXID=".$oDb->quote($sOxid);
        $oDb->execute($sQuery);
    }

    /**
     * Creating a database row entry of this id
     *
     * @param string $sTable
     * @param mixed string|null
     * @return void
     * @throws
     */
    public function fcCreateAfterbuyDataRow($sTable, $sOxid) {
        $blExists = $this->fcAfterbuyDataRowExists($sTable, $sOxid);
        if ($blExists) return;

        if ($sOxid === null) {
            $oConfig = $this->getConfig();
            $sOxid   = $oConfig->getRequestParameter("oxid");
        }

        $this->fcInsertRow($sTable, $sOxid);
    }

    /**
     * Returns row of request oxattribute external 1:1 Table
     *
     * @param string $sTable
     * @param mixed string|null
     * @return bool
     * @throws
     */
    public function fcAfterbuyDataRowExists($sTable, $sOxid) {
        if ($sOxid === null) {
            $oConfig = $this->getConfig();
            $sOxid = $oConfig->getRequestParameter("oxid");
        }
        $blExists = $this->fcRowExists($sTable, $sOxid);

        return $blExists;
    }

    /**
     * Truncates given table without using the TRUNCATE statement
     *
     * @param $sTable
     * @return void
     * @throws
     */
    public function fcTruncateTable($sTable)
    {
        $oDb = oxDb::getDb();
        $sQuery = "DELETE FROM {$sTable} WHERE 1";
        $oDb->execute($sQuery);
    }

    /**
     * Truncates all transaction tables
     *
     * @param void
     * @return void
     */
    public function fcResetTransactionData()
    {
        foreach ($this->_aAfterbuyTransactionTables as $sTable) {
            $this->fcTruncateTable($sTable);
        }
    }

    /**
     * Returns list of assignments that are missing for parent
     * articles
     */
    public function fcGetMissingParentAssignments() {
        $oDb = oxDb::getDb();
        $sQuery = "
            SELECT 
            o2c.OXCATNID, 
            oa.OXPARENTID 
            FROM oxobject2category o2c
            LEFT JOIN oxarticles oa ON (o2c.OXOBJECTID=oa.OXID)
            WHERE oa.OXPARENTID IS NOT NULL
            AND oa.OXPARENTID != ''
            GROUP BY oa.OXPARENTID
        ";
        $aRows = $oDb->getAll($sQuery);
        $aMissingAssignments = array();

        foreach ($aRows as $aRow) {
            $sCategoryId = $aRow[0];
            $sArticleId = $aRow[1];

            $aEntry = array(
                'sCategoryId' => $sCategoryId,
                'sArticleId' => $sArticleId,
            );

            $aMissingAssignments[] = $aEntry;
        }

            return $aMissingAssignments;
    }

    /**
     * Make sure that every category has got its own unique and
     * numeric value
     *
     * @param void
     * @return void
     * @throws
     */
    public function fcCreateCatalogIds()
    {
        $iStartNumber = 2250000;
        $oDb = oxDb::getDb();
        $sQuery = "SELECT MAX(FCAFTERBUY_CATALOGID) FROM oxcategories_afterbuy";
        $iLastFoundNumber = (int) $oDb->getOne($sQuery);
        if ($iLastFoundNumber > $iStartNumber)
            $iStartNumber = $iLastFoundNumber;

        $iCatalogId = $iStartNumber +1;
        $aOxids = $this->_fcGetNonsetCatalogCategoryIds();
        foreach ($aOxids as $sOxid) {
            $this->fcCreateAfterbuyDataRow('oxcategories_afterbuy', $sOxid);
            $this->fcUpdateFieldOfTable(
                'oxcategories_afterbuy',
                $sOxid,
                'FCAFTERBUY_CATALOGID',
                $iCatalogId
            );
            $iCatalogId++;
        }
    }

    public function fcUpdateCatalogId($sCatalogId, $sCatalogIDRequested)
    {
        $blValid = (
            !empty($sCatalogIDRequested) &&
            !empty($sCatalogId)
        );

        if (!$blValid) return;

        $oDb = oxDb::getDb();
        $sQuery = "
            UPDATE oxcategories_afterbuy SET
              FCAFTERBUY_CATALOGID={$sCatalogId}
            WHERE
              FCAFTERBUY_CATALOGID={$sCatalogIDRequested}
        ";

        $oDb->execute($sQuery);
    }

    /**
     * Returns a list of category oxIDs which currently have no
     * catalogid
     *
     * @param void
     * @return array
     */
    protected function _fcGetNonsetCatalogCategoryIds()
    {
        $oDb = oxDb::getDb();
        $sQuery = "
            SELECT oc.OXID 
            FROM oxcategories oc 
            LEFT JOIN oxcategories_afterbuy oca ON (oc.OXID=oca.OXID)
            WHERE oca.FCAFTERBUY_CATALOGID IS NULL 
            OR oca.FCAFTERBUY_CATALOGID = ''
        ";

        $aRows = (array) $oDb->getAll($sQuery);
        $aOxids = array();
        foreach ($aRows as $aRow) {
            $aOxids[] = $aRow[0];
        }

        return $aOxids;
    }
}
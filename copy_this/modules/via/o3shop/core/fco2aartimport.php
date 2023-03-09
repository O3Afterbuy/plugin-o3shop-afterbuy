<?php

class fco2aartimport extends fco2abaseimport
{
    const AFTERBUY_BASE_PRODUCT_FLAG_PARENT = 1;
    const AFTERBUY_BASE_PRODUCT_FLAG_CHILD = 3;

    /**
     * Number ox maximum pages that will be processed
     * @var int
     */
    protected $_iMaxPages = 500;

    /**
     * List of variation ids and their correspending parentid
     * @var array
     */
    protected $_aVariations = array();

    /**
     * Central entry point for triggering product import
     *
     * @param void
     * @return void
     */
    public function execute() {
        $blAllowed = $this->fcJobExecutionAllowed('artimport');
        if (!$blAllowed) {
            echo "Execution of artimport is not allowed by configuration\n";
            exit(1);
        }

        $this->_fcProcessCategoryTree();
        $this->_fcProcessProducts();
        $this->_fcProcessParentCategoryAssignment();
        $this->_fcUpdateCategoryIndex();
    }

    /**
     * Rebuilding nested sets information
     *
     * @param void
     * @return void
     */
    protected function _fcUpdateCategoryIndex()
    {
        $oCatList = oxNew('oxCategoryList');
        $oCatList->updateCategoryTree();
    }

    /**
     * Due to there is no product assignments for variattionsets
     * we need to determine variant assignments and also have to
     * assign parent products to categories
     *
     * @param void
     * @return void
     */
    protected function _fcProcessParentCategoryAssignment()
    {
        $oAfterbuyDb = oxNew('fco2adatabase');
        $aMissingAssignments = $oAfterbuyDb->fcGetMissingParentAssignments();

        $blValid = (
            is_array($aMissingAssignments) &&
            count($aMissingAssignments)
        );

        if (!$blValid) return;

        foreach ($aMissingAssignments as $aMissingAssignment) {
            $sArticleId = $aMissingAssignment['sArticleId'];
            $sCategoryId = $aMissingAssignment['sCategoryId'];
            $this->_fcAssignCategory($sCategoryId, $sArticleId);
        }
    }

    /**
     * Fetching category information from AB and create
     * category structure in OXID
     *
     * @param void
     * @return void
     */
    protected function _fcProcessCategoryTree()
    {
        $oAfterbuyApi = $this->_fcGetAfterbuyApi();
        $sResponse = $oAfterbuyApi->getShopCatalogs();
        $this->_fcParseCatalogStructure($sResponse);
    }

    /**
     * Parses response from afterbuy, create object and iterate
     * through it
     *
     * @param $sResponse
     * @return array
     */
    protected function _fcParseCatalogStructure($sResponse) {
        if (empty($sResponse)) return array();
        $oXmlResponse = simplexml_load_string($sResponse);

        $aCatalogs = (array) $oXmlResponse->Result->Catalogs;
        $this->_fcClearCatalogProductsAssignments();

        foreach ($aCatalogs['Catalog'] as $oCatalog) {
            $this->_fcCreateOxidCategory($oCatalog);
        }
    }

    private function _fcClearCatalogProductsAssignments(){
        $oDb = oxDb::getDb();
        $truncateQuery = "TRUNCATE TABLE oxobject2category";

        $oDb->execute($truncateQuery);
    }

    /**
     * Recursively create oxid categories and
     * product assignments
     *
     * @param $oCatalog
     * @return void
     */
    protected function _fcCreateOxidCategory($oCatalog)
    {
        $aCatalog = (array) $oCatalog;
        $this->_fcCreateCategory($aCatalog);

        // assigned products
        $sCatalogId = (string) $oCatalog->CatalogID;

        $blHasAssignedProducts = $this->_fcCategoryHasAssignedProducts($oCatalog);

        if ($blHasAssignedProducts === true) {
            $aCatalogProducts = (array)$oCatalog->CatalogProducts;

            foreach ($aCatalogProducts as $aCatalogProductsIds) {
                if (is_array($aCatalogProductsIds)) {
                    foreach ($aCatalogProductsIds as $sArticleId) {
                        $this->_fcAssignCategory($sCatalogId, $sArticleId);
                    }
                }
                else{
                    $this->_fcAssignCategory($sCatalogId, $aCatalogProductsIds);
                }
            }
        }

        if (!isset($oCatalog->Catalog)) return;

        foreach ($oCatalog->Catalog as $oSubCatalog) {
            $this->_fcCreateOxidCategory($oSubCatalog);
        }
    }

    private function _fcCategoryHasAssignedProducts($oCatalog)
    {
        return isset($oCatalog->CatalogProducts) && isset($oCatalog->CatalogProducts->ProductID);
    }

    /**
     * Process variation sets
     *
     * @param void
     * @return void
     */
    protected function _fcProcessProducts()
    {
        $oAfterbuyApi = $this->_fcGetAfterbuyApi();
        $iPage = 1;
        while($iPage > 0 && $iPage <= $this->_iMaxPages) {
            $sResponse =
                $oAfterbuyApi->getShopProductsFromAfterbuy($iPage);
            $oXmlResponse =
                simplexml_load_string($sResponse, null, LIBXML_NOCDATA);
            $iPage =
                $this->_fcParseApiProductResponse($oXmlResponse);
        }
    }

    /**
     * Processing get shop products api response
     *
     * @param object $oXmlResponse
     * @return int
     */
    protected function _fcParseApiProductResponse($oXmlResponse)
    {
        $iPage = $this->_fcGetNextPage($oXmlResponse);

        $aProducts = (array) $oXmlResponse->Result->Products;

        foreach ($aProducts['Product'] as $oXmlProduct) {
            if($this->_fcCheckIfArticleNumberIsValid($oXmlProduct) == false) {
                continue;
            }
            $this->_fcAddProductToOxid($oXmlProduct);
        }

        return $iPage;
    }

    /**
     * Adds/Updates afterbuy product into oxid
     *
     * @param $oXmlProduct
     * @param $sType
     * @return void
     */
    protected function _fcAddProductToOxid($oXmlProduct)
    {
        $oArticle = oxNew('oxarticle');
        $oArticle->fcAddCustomFieldsToObject();
        $sOxid = $this->_fcProductExists($oXmlProduct);
        if ($sOxid) {
            $oArticle->load($sOxid);
        }

        $this->oApiLogger->fcWriteLog(
            "DEBUG: Trying to add/update XML Product: \n".
            print_r($oXmlProduct ,true), 4);

        $this->_fcAddProductBasicData($oXmlProduct, $oArticle);
        $this->_fcAddProductPictures($oXmlProduct, $oArticle);
        $this->_fcAddProductAttributes($oXmlProduct, $oArticle);
        $oArticle->save();
    }

    /**
     * Added basic productdata
     *
     * @param object $oXmlProduct
     * @param object $oArticle
     * @return void
     */
    protected function _fcAddProductBasicData($oXmlProduct, &$oArticle)
    {
        // identification
        $this->_fcAddIdentificationData($oXmlProduct, $oArticle);
        // description
        $this->_fcAddDescriptionData($oXmlProduct, $oArticle);
        // productdata
        $this->_fcAddProductAmounts($oXmlProduct, $oArticle);
        // prices
        $this->_fcAddProductPrices($oXmlProduct, $oArticle);
    }

    /**
     * Adds identification data to oxid product
     *
     * @param object $oXmlProduct
     * @param object $oArticle
     */
    protected function _fcAddProductPrices($oXmlProduct, &$oArticle)
    {
        $oArticle->oxarticles__oxprice =
            new oxField($this->_fcGetFloatValue($oXmlProduct->SellingPrice));
        $oArticle->oxarticles__oxbprice =
            new oxField($this->_fcGetFloatValue($oXmlProduct->BuyingPrice));
        $oArticle->oxarticles__oxpricea =
            new oxField($this->_fcGetFloatValue($oXmlProduct->DealerPrice));
        $oArticle->oxarticles__oxvat =
            new oxField($this->_fcGetFloatValue($oXmlProduct->TaxRate));

        $aScaledDiscounts = (array) $oXmlProduct->ScaledDiscounts;

        foreach ($aScaledDiscounts as $aScaledDiscount) {
            $this->_fcSetScaledDiscount($oArticle, $aScaledDiscount);
        }
    }

    /**
     * Add a scalediscount into oxid system
     *
     * @param $oArticle
     * @param $aScaledDiscount
     */
    protected function _fcSetScaledDiscount($oArticle, $aScaledDiscount)
    {
        $oConfig = $this->getConfig();
        $sShopId = $oConfig->getShopId();
        $dListPrice = $oArticle->oxarticles__oxprice->value;
        $dScaledPrice = $this->_fcGetFloatValue($aScaledDiscount['ScaledPrice']);

        $dAbsDiscount = $dListPrice - $dScaledPrice;
        $aParams = array();
        $aParams['oxprice2article__oxshopid'] = $sShopId;
        $aParams['oxprice2article__oxamount'] = $aScaledDiscount['ScaledQuantity'];
        $aParams['oxprice2article__oxaddabs'] = $dAbsDiscount;

        $oArticlePrice = oxNew("oxbase");
        $oArticlePrice->init("oxprice2article");
        $oArticlePrice->assign($aParams);
    }


    /**
     * Adds identification data to oxid product
     *
     * @param object $oXmlProduct
     * @param object $oArticle
     */
    protected function _fcAddIdentificationData($oXmlProduct, &$oArticle)
    {
        $sProductId = (string) $oXmlProduct->ProductID;
        $oArticle->setId($sProductId);

        $sArtNum = $this->_fcGetArticleNumber($oXmlProduct);
        $oArticle->oxarticles__fcafterbuyid = new oxField($sProductId);
        $oArticle->oxarticles__oxartnum = new oxField($sArtNum);

        if ($this->_fcIsChild($oXmlProduct)) {
            $sParentId = $this->_fcFetchParentId($oXmlProduct);
            $oArticle->oxarticles__oxparentid = new oxField($sParentId);
        }
    }

    /**
     * Fetching parent id from
     *
     * @param $oXmlProduct
     * @return string
     */
    protected function _fcFetchParentId($oXmlProduct) {
        if (!isset($oXmlProduct->BaseProducts)) return '';

        $parentId = '';
        $BaseProducts = $oXmlProduct->BaseProducts;

        foreach ($BaseProducts as $xmlBaseProduct) {
            if ((int)$xmlBaseProduct->BaseProduct->BaseProductType === self::AFTERBUY_BASE_PRODUCT_FLAG_PARENT) {
                $parentId = (string) $xmlBaseProduct->BaseProduct->BaseProductID;
            }
        }
        return $parentId;
    }


    /**
     * Adds identification data to oxid product
     *
     * @param object $oXmlProduct
     * @param object $oArticle
     */
    protected function _fcAddDescriptionData($oXmlProduct, &$oArticle)
    {
        $oArticle->oxarticles__oxtitle = new oxField((string)$oXmlProduct->Name);
        $oArticle->oxarticles__oxshortdesc = new oxField((string)$oXmlProduct->ShortDescription);
        $oArticle->setArticleLongDesc((string) $oXmlProduct->Description);

        if ($this->_fcIsChildOrSingle($oXmlProduct)) {
            $sVarselect = $this->_fcFetchVarselect($oXmlProduct);
            $oArticle->oxarticles__oxvarselect = new oxField($sVarselect);
        }
    }

    protected function _fcIsParentOrChild($oXmlProduct) {
        if (isset($oXmlProduct->BaseProductFlag)) {
            return true;
        }
    }

    protected function _fcIsParent($oXmlProduct) {
        if (isset($oXmlProduct->BaseProductFlag)){
            if ((int)$oXmlProduct->BaseProductFlag === self::AFTERBUY_BASE_PRODUCT_FLAG_PARENT){
                return true;
            }
        }
        return false;
    }

    protected function _fcIsChild($oXmlProduct) {
        if (isset($oXmlProduct->BaseProductFlag)){
            if ((int)$oXmlProduct->BaseProductFlag === self::AFTERBUY_BASE_PRODUCT_FLAG_CHILD){
                return true;
            }
        }
        return false;
    }

    protected function _fcIsChildOrSingle($oXmlProduct) {
        if (isset($oXmlProduct->BaseProductFlag)){
            if ((int)$oXmlProduct->BaseProductFlag === self::AFTERBUY_BASE_PRODUCT_FLAG_PARENT){
                return false;
            }
        }
        return true;
    }

    /**
     * Fetches varselect by subtracting parent product title
     *
     * @param object $oXmlProduct
     * @return string
     * @throws
     */
    protected function _fcFetchVarselect($oXmlProduct) {
        $sProductId = (string) $oXmlProduct->ProductID;
        $sParentId = $this->_fcFetchParentId($oXmlProduct);
        if (!$sParentId) return '';

        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);

        $sQuery = "
            SELECT 
                OXTITLE 
            FROM 
                oxarticles 
            WHERE 
                OXID=".$oDb->quote($sParentId);

        $sParentTitle = (string) $oDb->getOne($sQuery);
        $sChildTitle = (string) $oXmlProduct->Name;

        $sVarSelect =
            trim(str_replace($sParentTitle, '', $sChildTitle));

        return $sVarSelect;
    }

    /**
     * Adds AB-Attributes of product into OXID Shop
     *
     * @param $oXmlProduct
     * @param $oArticle
     */
    protected function _fcAddProductAttributes($oXmlProduct, $oArticle)
    {
        $blValidNode = (
            is_array($oXmlProduct->Attributes) ||
            is_object($oXmlProduct->Attributes)
        );
        if (!$blValidNode) return;

        foreach ($oXmlProduct->Attributes as $aProductAttributes) {
            foreach ($aProductAttributes as $aProductAttribute) {
                $aProductAttribute = (array) $aProductAttribute;
                $sAttributeId = $this->_fcGetAttributeId($aProductAttribute);
                $sArticleId = $oArticle->getId();
                $sAttributeValue = $aProductAttribute['AttributValue'];
                $this->_fcAddAttributeValue($sAttributeId, $sArticleId, $sAttributeValue);
            }
        }
    }

    /**
     * Create or update attribute value
     *
     * @param $sAttributeId
     * @param $sArticleId
     * @param $sAttributeValue
     */
    protected function _fcAddAttributeValue($sAttributeId, $sArticleId, $sAttributeValue)
    {
        $oDb = oxDb::getDb();
        $sOxid = $this->_fcGetAttributeValueId($sAttributeId, $sArticleId);


        if ($sOxid) {
            $sQuery = "
                UPDATE oxobject2attribute
                SET oxvalue=".$oDb->quote($sAttributeValue)."
                WHERE OXID=".$oDb->quote($sOxid);
        } else {
            $oUtilsObject = oxRegistry::get('oxUtilsObject');
            $sNewOxid = $oUtilsObject->generateUId();
            $sQuery = "
                INSERT INTO oxobject2attribute
                (
                  OXID,
                  OXOBJECTID,
                  OXATTRID,
                  OXVALUE
                )
                VALUES
                (
                  ".$oDb->quote($sNewOxid).",
                  ".$oDb->quote($sArticleId).",
                  ".$oDb->quote($sAttributeId).",
                  ".$oDb->quote($sAttributeValue)."
                )
            ";
        }

        $oDb->execute($sQuery);
    }

    /**
     * Returns id of attribute-value-assignment or false if none could
     * be found
     *
     * @param $sAttributeId
     * @param $sArticleId
     * @return mixed string|bool
     */
    protected function _fcGetAttributeValueId($sAttributeId, $sArticleId)
    {
        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);

        $sQuery = "
            SELECT 
                OXID 
            FROM 
                oxobject2attribute 
            WHERE
                OXOBJECTID=".$oDb->quote($sArticleId)." AND
                OXATTRID=".$oDb->quote($sAttributeId)."
            LIMIT 1
        ";

        $mOxid = $oDb->getOne($sQuery);

        return $mOxid;
    }

    /**
     * Fetches or creates attribute id
     *
     * @param $aProductAttribute
     * @return string
     */
    protected function _fcGetAttributeId($aProductAttribute)
    {
        $sAttributeName = trim($aProductAttribute['AttributName']);
        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);

        $sQuery = "
            SELECT 
                OXID 
            FROM 
                oxattribute 
            WHERE 
                OXTITLE =".$oDb->quote($sAttributeName);
        $sOxid = $oDb->getOne($sQuery);

        if ($sOxid) return $sOxid;

        $sOxid = $this->_fcCreateAttribute($aProductAttribute);

        return $sOxid;
    }

    /**
     * Creates a new attribute of AB-Attribute
     *
     * @param $aProductAttribute
     * @return string
     */
    protected function _fcCreateAttribute($aProductAttribute)
    {
        $sAttributeName = trim($aProductAttribute['AttributName']);
        $oAttribute = oxNew('oxattribute');
        $oAttribute->oxattribute__oxtitle = new oxField($sAttributeName);
        $sOxid = $oAttribute->getId();
        $oAttribute->save();

        return $sOxid;
    }


    /**
     * Assigns category with article
     *
     * @param $sCategoryId
     * @param $sArticleId
     */
    protected function _fcAssignCategory($sCategoryId, $sArticleId)
    {
        $oUtilsObject = oxRegistry::get('oxUtilsObject');
        $oDb = oxDb::getDb();
        $sNewId = $oUtilsObject->generateUId();

        $sQuery = "
            INSERT INTO oxobject2category
            (
                OXID,
                OXOBJECTID,
                OXCATNID
            )
            VALUES
            (
                ".$oDb->quote($sNewId).",
                ".$oDb->quote($sArticleId).",
                ".$oDb->quote($sCategoryId)."
            )
            ON DUPLICATE KEY UPDATE
                OXID = OXID
        ";

        $oDb->execute($sQuery);
    }


    /**
     * Create category entry
     *
     * @param array $aCatalog
     * @return string
     */
    protected function _fcCreateCategory($aCatalog) {
        $sCategoryId = (string) $aCatalog['CatalogID'];
        $sParentId = ($aCatalog['ParentID']) ?
            (string) $aCatalog['ParentID'] :
            'oxrootid';

        $oDb = oxDb::getDb();

        $sQuery = "
            REPLACE INTO oxcategories
            (
                OXID,
                OXACTIVE,
                OXTITLE,
                OXSORT,
                OXLONGDESC,
                OXPARENTID
            )
            VALUES
            (
                ".$oDb->quote($sCategoryId).",
                ".$oDb->quote((int) $aCatalog['Show']).",
                ".$oDb->quote((string) htmlspecialchars_decode($aCatalog['Name'])).",
                ".$oDb->quote((string) $aCatalog['Position']).",
                ".$oDb->quote((string) $aCatalog['Description']).",
                ".$oDb->quote((string) $sParentId)."
            )
        ";

        $oDb->execute($sQuery);

        return $sCategoryId;
    }

    /**
     * Handles product picture handling
     *
     * @param $oXmlProduct
     * @param $oArticle
     */
    protected function _fcAddProductPictures($oXmlProduct, &$oArticle)
    {
        $blValidNode = (
            is_array($oXmlProduct->ProductPictures) ||
            is_object($oXmlProduct->ProductPictures)
        );
        if (!$blValidNode) return;

        $iPicCounter = 1;

        foreach ($oXmlProduct->ProductPictures as $aProductPictures) {
            foreach ($aProductPictures as $aProductPicture) {
                $aProductPicture = (array) $aProductPicture;
                $sImageUrl = (string) $aProductPicture['Url'];
                if (empty($sImageUrl)) continue;

                $sTargetFileName = basename($sImageUrl);
                $this->_fcDownloadImage($sImageUrl, $sTargetFileName, $iPicCounter);
                $sField = "oxarticles__oxpic".$iPicCounter;
                $oArticle->$sField = new oxField($sTargetFileName);
                $iPicCounter++;
            }
        }
    }

    /**
     * Downloads and places image into master folder
     *
     * @param $sImageUrl
     * @param $sTargetFileName
     * @param $iPicNr
     */
    protected function _fcDownloadImage($sImageUrl, $sTargetFileName, $iPicNr)
    {
        $oConfig = $this->getConfig();
        $sPicNrFolder = (string) $iPicNr;
        $sMasterPictureFolder = $oConfig->getMasterPicturePath('');
        $sTargetFolder = "{$sMasterPictureFolder}product/{$sPicNrFolder}";
        $sTargetPath = "{$sTargetFolder}/{$sTargetFileName}";
        $oCurl = curl_init($sImageUrl);
        $oFile = fopen($sTargetPath, 'wb');
        curl_setopt($oCurl, CURLOPT_FILE, $oFile);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
        curl_exec($oCurl);
        curl_close($oCurl);
        fclose($oFile);
    }

}
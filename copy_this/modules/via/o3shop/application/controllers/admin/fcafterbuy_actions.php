<?php
class fcafterbuy_actions extends oxAdminDetails
{
	/**
	 * Template
	 * @var string
	 */
	protected $_sThisTemplate = 'fcafterbuy_actions.tpl';

    /**
     * Triggers a datareset and deletes certain tables
     *
     * @param void
     * @return void
     */
    public function fcResetTransactionData() {
        $oLang = oxRegistry::getLang();
        $oUtilsView = oxRegistry::get('oxUtilsView');
        $oAfterbuyDatabase = oxNew('fco2adatabase');
        $oAfterbuyDatabase->fcResetTransactionData();
        $sMessage = $oLang->translateString('SHOP_MODULE_AFTERBUY_TRANSACTIONDATA_RESET');
        $oUtilsView->addErrorToDisplay(oxNew('oxException', $sMessage));
    }

    /**
     * Download the OXID log
     *
     * @param void
     * @return void
     */
    public function fcDownloadOxidLog() {
        $sLogFileName = $this->fcGetOxidLogFileName();
        $sLogFile = getShopBasePath().'/log/'.$sLogFileName;

        $this->fcDownloadFile($sLogFile);
    }

    /**
     * Returns matching logfilename
     *
     * @return string
     */
    public function fcGetOxidLogFileName() {
        $oConfig = $this->getConfig();
        $sVersion = $oConfig->getVersion();
        $blIsOxid5 = version_compare($sVersion, '6.0.0', '<');

        return ($blIsOxid5) ? 'EXCEPTION_LOG.txt' : 'oxideshop.log';
    }
    /**
     * Download the Afterbuy module log
     *
     * @param void
     * @return void
     */
    public function fcDownloadDefaultLog() {
        $sLogFile = getShopBasePath().'/log/fco2a_default.log';

        $this->fcDownloadFile($sLogFile);
    }

    /**
     * Download the Afterbuy API log
     *
     * @param void
     * @return void
     */
    public function fcDownloadApiLog() {
        $sLogFile = getShopBasePath().'/log/fco2a_api.log';

        $this->fcDownloadFile($sLogFile);
    }

    /**
     * Download the Afterbuy API log
     *
     * @param void
     * @return void
     */
    public function fcTruncateApiLog() {
        $oLang = oxRegistry::getLang();
        $oUtilsView = oxRegistry::get('oxUtilsView');

        $sLogFile = getShopBasePath().'/log/fco2a_api.log';

        $this->fcTruncateLogFile($sLogFile);

        $sMessage = $oLang->translateString('SHOP_MODULE_AFTERBUY_ACTIONS_AFTERBUY_APILOG_TRUNCATED');
        $oUtilsView->addErrorToDisplay(oxNew('oxException', $sMessage));
    }

    /**
     * Download the Afterbuy API log
     *
     * @param void
     * @return void
     */
    public function fcTruncateDefaultLog() {
        $oLang = oxRegistry::getLang();
        $oUtilsView = oxRegistry::get('oxUtilsView');

        $sLogFile = getShopBasePath().'/log/fco2a_default.log';

        $this->fcTruncateLogFile($sLogFile);

        $sMessage = $oLang->translateString('SHOP_MODULE_AFTERBUY_ACTIONS_AFTERBUY_DEFAULTLOG_TRUNCATED');
        $oUtilsView->addErrorToDisplay(oxNew('oxException', $sMessage));
    }

    protected function fcTruncateLogFile($file) {
        file_put_contents($file, '');
    }

    /**
     * Helper function to provide functionality to download a file
     *
     * @param string $file
     */
    protected function fcDownloadFile($file) {
        header('Content-Type: application/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . basename($file) . '";');

        if(file_exists($file)) {
            file_put_contents('php://output', file_get_contents($file), FILE_APPEND);
        } else {
            file_put_contents('php://output', '', FILE_APPEND);
        }

        exit();
    }
}
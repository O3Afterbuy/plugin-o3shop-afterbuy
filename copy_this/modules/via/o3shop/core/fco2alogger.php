<?php

/**
 * Class fco2alogger
 */
class fco2alogger extends oxBase {

    /**
     * @var string
     */
    protected $sLogFile;

    /**
     * @var string
     */
    protected $sLogPath;

    /**
     * @var int
     */
    protected $iLogLevel;

    /**
     * fco2Alogger constructor.
     * @param string $sLogFile
     */
    public function __construct($sLogFile)
    {
        $oConfig = $this->getConfig();
        $this->sLogPath = getShopBasePath() . '/log/';
        $this->iLogLevel = (int)$oConfig->getConfigParam('iFcAfterbuyLogLevel');
        $this->sLogFile = $sLogFile;
    }

    /**
     * @param string $sMessage
     * @param int $iLogLevel
     */
    public function fcWriteLog($sMessage, $iLogLevel = 1) {
        $sTime = date("Y-m-d H:i:s");
        $sFullMessage = "[" . $sTime . "] " . $sMessage . "\n";
        if ($iLogLevel <= $this->iLogLevel) {
            file_put_contents($this->sLogPath . $this->sLogFile, $sFullMessage, FILE_APPEND);
        }
    }

}
<?php
class fcafterbuy_admin extends oxAdminView
{
	
	/**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'fcafterbuy_admin.tpl';
    
	/**
     * Executes parent method parent::render() and returns name of template    
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        
        $oSession = $this->getSession();

		$sCurrentAdminShop = $oSession->getVariable("currentadminshop");

		if (!$sCurrentAdminShop) {
			if ($oSession->getVariable("malladmin")) {
				$sCurrentAdminShop = $this->getConfig()->getShopId();
			} else {
				$sCurrentAdminShop = $oSession->getVariable("actshop");
            }
		}
		$this->_aViewData["currentadminshop"] = $sCurrentAdminShop;
		$oSession->setVariable("currentadminshop", $sCurrentAdminShop);

        return $this->_sThisTemplate;
    }
}

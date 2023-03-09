<?php

class FcAfterbuy_Article_Admin extends oxAdminDetails
{

    /**
     * Loads article parameters and passes them to Smarty engine, returns
     * name of template file "article_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $oArticle = oxNew('oxArticle');


        $sOxId = $this->getEditObjectId();
        if ($sOxId && $sOxId != "-1") {

            // load object
            $oArticle->loadInLang($this->_iEditLang, $sOxId);

            //set access field properties to prevent derived articles for editing
            if ($oArticle->isDerived()) {
                $this->_aViewData["readonly"] = true;
            }

            // load object in other languages
            $oOtherLang = $oArticle->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oArticle->loadInLang(key($oOtherLang), $sOxId);
            }

            $this->_aViewData['edit'] = $oArticle;

            $aLang = array_diff(oxRegistry::getLang()->getLanguageNames(), $oOtherLang);
            if (count($aLang)) {
                $this->_aViewData["posslang"] = $aLang;
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        return "fcafterbuy_article_admin.tpl";
    }

    /**
     * Saves changes of article parameters.
     */
    public function save()
    {
        parent::save();

        $oConfig = $this->getConfig();
        $soxId = $this->getEditObjectId();
        $aParams = $oConfig->getRequestParameter("editval");


        $oArticle = oxNew("oxarticle");
        $oArticle->setLanguage($this->_iEditLang);

        if ($soxId != "-1") {
            $oArticle->loadInLang($this->_iEditLang, $soxId);
            $oArticle->setLanguage(0);
            $oArticle->assign($aParams);
            $oArticle->setLanguage($this->_iEditLang);
            $oArticle = oxRegistry::get("oxUtilsFile")->processFiles($oArticle);
            $oArticle->save();

        }
        $this->setEditObjectId($oArticle->getId());

        $oAfterbuyDb = oxNew('fco2adatabase');
        $oAfterbuyDb->fcSaveAfterbuyParams('oxarticles_afterbuy', 'oxarticles');
    }

    /**
     * Saves changed selected action parameters in different language.
     */
    public function saveinnlang()
    {
        $this->save();
    }
}
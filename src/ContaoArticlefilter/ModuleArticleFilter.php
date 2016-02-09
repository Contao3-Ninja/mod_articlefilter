<?php

namespace ContaoArticlefilter;

/**
 * Class ModuleArticleFilter based on version of Stefan Gandlau <stefan@gandlau.net>
 *
 */
class ModuleArticleFilter extends \Module
{
    protected $strTemplate = 'mod_articlefilter';

    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate           = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ArticleFilter ###';
            return $objTemplate->parse();
        }

        /* ajax queries */
        if (\Input::get('isAjax') == '1')
        {
            $filter    = \Input::get('articlefilter_filter');
            $objFilter = new ArticleFilter($this->getRootIdFromUrl());

            $objFilter->selectedFilter = $filter;
            $objFilter->afstype        = \Input::get('afstype');

            $objFilter->run();

            print(json_encode(array('resultCount' => $objFilter->resultCount)));
            exit;
        }

        return parent::generate();
    }

    protected function compile()
    {
        $GLOBALS['TL_JAVASCRIPT']['articlefilter'] = 'system/modules/articlefilter/assets/articlefilter.js';
        $arrGroups   = deserialize($this->articlefilter_groups);
        $arrSelected = \Input::get('articlefilter_filter');
        $res         = $this->Database->prepare('SELECT * from tl_articlefilter_groups where id IN ('.implode(',',
                $arrGroups).') AND published = 1 ORDER BY FIELD(id, '.implode(',',$arrGroups).')')->execute();

        if ($res->numRows > 0)
        {
            $arrBoxes = [];
            $this->Template->hasGroups = true;
            $id = 0;
            while ($res->next())
            {
                $arrCriteria = $this->loadCriteriaByGroup($res->id);
                if (is_array($arrCriteria))
                {
                    $id++;
                    $objCB           = new \FrontendTemplate($res->template);
                    $objCB->items    = $arrCriteria;
                    $objCB->title    = $res->title;
                    $objCB->name     = $res->id;
                    $objCB->selected = $arrSelected[$res->id];
                    $objCB->id       = $id;
                    $arrBoxes[]      = $objCB->parse();
                }
            }
            /* jumpTo Page */
            $arrJump = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo)->fetchAssoc();

            $this->Template->criterias         = implode("\n", $arrBoxes);
            $this->Template->href              = $this->generateFrontendUrl($arrJump);
            $this->Template->lblSubmit         = $GLOBALS['TL_LANG']['articlefilter']['lblSubmit'];
            $this->Template->selectedMatchType = \Input::get('afstype') ? \Input::get('afstype') : $this->articlefilter_defaultfilter;
            $this->Template->lblMatches        = $GLOBALS['TL_LANG']['articlefilter']['lblMatches'];
            $this->Template->lblAny            = $GLOBALS['TL_LANG']['articlefilter']['lblAny'];
            $this->Template->lblAll            = $GLOBALS['TL_LANG']['articlefilter']['lblAll'];
        }
    }

    protected function loadCriteriaByGroup($pid)
    {
        $res = $this->Database
            ->prepare('SELECT * from tl_articlefilter_criteria WHERE pid=? AND published=? ORDER BY sorting')
            ->execute($pid, 1);

        if ($res->numRows == 0)
        {
            return false;
        }

        $arrCriteria = [];
        while ($res->next())
        {
            $arrCriteria[$res->id] = $res->title;
        }
        return $arrCriteria;
    }

}

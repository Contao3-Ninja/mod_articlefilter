<?php

namespace ContaoArticlefilter\Modules;

/**
 * Class ModuleFilterResults based on version of Stefan Gandlau <stefan@gandlau.net>
 *
 */
class ModuleFilterResults extends \Module
{
    protected $strTemplate = 'mod_articlefilter_results';

    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate           = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### Filter Results ###';
            return $objTemplate->parse();
        }
        return parent::generate();
    }

    protected function compile()
    {

        if (\Input::get('isAjax') == '1')
        {
            return;
        }
        $objFilter                 = new ArticleFilter($this->getRootIdFromUrl());
        $objFilter->selectedFilter = \Input::get('articlefilter_filter');
        $objFilter->afstype        = \Input::get('afstype');
        $objFilter->sorting        = $this->articlefilter_sorting;
        $objFilter->showAll        = true;

        $objFilter->run();

        /* search articles matching filter */
        if ($objFilter->resultCount > 0)
        {
            $resultCount = $objFilter->resultCount;
            $results     = $objFilter->results;

            if ($this->perPage > 0 && $resultCount > $this->perPage)
            {
                $objPagination              = new \Pagination($resultCount, $this->perPage);
                $this->Template->pagination = $objPagination->generate();

                $page = \Input::get('page');
                if ($page == '' || $page < 1)
                {
                    $page = 1;
                }

                $offset  = ($page - 1) * $this->perPage;
                $results = array_slice($results, $offset, $this->perPage);
            }

            $this->Template->resultCount = $resultCount;
            $this->Template->results     = $results;

            $this->Template->showFilter             = $this->articlefilter_showfilter;
            $this->Template->selectedFilter         = $objFilter->searchStrings;
            $this->Template->selectedFilterHeadline =
                sprintf($GLOBALS['TL_LANG']['articlefilter']['selectedFilterHeadline'], $resultCount);
        }
        else
        {
            $this->Template->no_filter = true;
        }

    }
}

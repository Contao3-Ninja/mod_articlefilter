<?php

namespace ContaoArticlefilter\Modules;

/**
 * Class ModuleFilterLinks based on version of Stefan Gandlau <stefan@gandlau.net>
 *
 */
class ModuleFilterLinks extends \Module
{
    protected $strTemplate = 'mod_articlefilter_links';
    protected $arrJump     = [];
    protected $objFilter;

    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate           = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### Article Filter - Links ###';
            return $objTemplate->parse();
        }
        $this->arrJump   = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo)->fetchAssoc();
        $this->objFilter = new ArticleFilter();

        return parent::generate();
    }

    protected function compile()
    {
        /* get current filter */
        $arrFilter      = \Input::get('articlefilter_filter');
        $searchCriteria = [];
        if (is_array($arrFilter))
        {
            foreach ($arrFilter as $grp => $filter)
            {
                foreach ($filter as $c)
                {
                    $searchCriteria[] = $c;
                }
            }

            $arrHTML = [$this->generateFilterBox($arrFilter)];
        }
        $arrValidFilter = $this->findValidFilter($searchCriteria);
        if (count($arrValidFilter) == 0)
        {
            $this->Template->groups = implode("\n", $arrHTML);
            return;
        }

        $arrFilterIDs = array_keys($arrValidFilter);
        $arrGroups    = [];
        $res = $this->Database->prepare('SELECT t1.*, t2.title grp, t2.id gid from tl_articlefilter_criteria t1, tl_articlefilter_groups t2 WHERE t1.id IN ('.implode(',',
                $arrFilterIDs).') AND t1.pid=t2.id AND t1.published=? ORDER BY t2.title, t1.sorting')->execute(1);
        while ($res->next())
        {
            if (!is_array($arrGroups[$res->grp]))
            {
                $arrGroups[$res->grp] = [];
            }
            $arrGroups[$res->grp][] = [
                'href'   => $this->generateFilterLink($arrFilter, $res->row()),
                'title'  => $res->title,
                'count'  => $arrValidFilter[$res->id]
            ];
        }

        foreach ($arrGroups as $title => $grp)
        {
            $objT        = new \FrontendTemplate('mod_filterlink_group');
            $objT->title = $title;
            $objT->links = $grp;
            $arrHTML[]   = $objT->parse();
        }
        $this->Template->groups = implode("\n", $arrHTML);

    }

    protected function generateFilterBox($filter)
    {
        $arrLinks        = array();
        $baseurl         = $this->generateFrontendUrl($this->arrJump);

        $objT            = new \FrontendTemplate('mod_filterlink_set');
        $objT->title     = $GLOBALS['TL_LANG']['articlefilter']['selectedFilter'];
        $objT->baseurl   = $baseurl;
        $objT->removeAll = $GLOBALS['TL_LANG']['articlefilter']['removeAll'];


        if (!is_array($filter) || count($filter) == 0)
        {
            return '';
        }
        foreach ($filter as $grp => $f)
        {
            foreach ($f as $id)
            {
                $arrAdd[] = sprintf('articlefilter_filter[%s][]=%s', $grp, $id);
                $lbl[]    = $this->getFilterTitle($id);
            }
        }
        for ($x = 0; $x < count($arrAdd); $x++)
        {
            $add = $arrAdd;
            unset($add[$x]);
            $arrLinks[] = ['href' => sprintf('%s?%s', $baseurl, implode('&', $add)), 'title' => $lbl[$x]];
        }
        $objT->links = $arrLinks;

        return $objT->parse();
    }

    protected function getFilterTitle($id)
    {
        $res = $this->Database
            ->prepare('SELECT * from tl_articlefilter_criteria where id=?')
            ->execute($id);
        if ($res->numRows == 0)
        {
            return ('');
        }
        $row = $res->fetchAssoc();

        return $row['title'];
    }

    protected function generateFilterLink($arrFilter, $arrItem = false)
    {
        if (is_array($arrItem))
        {
            $strURL = sprintf('?articlefilter_filter[%s][]=%s', $arrItem['gid'], $arrItem['id']);
        }
        else
        {
            $strURL = '';
        }
        if (is_array($arrFilter))
        {
            foreach ($arrFilter as $group => $filter)
            {
                foreach ($filter as $key => $val)
                {
                    $strURL .= sprintf('&articlefilter_filter[%s][]=%s', $group, $val);
                }
            }
        }
        return ($this->generateFrontendUrl($this->arrJump).$strURL);
    }

    protected function findValidFilter($searchCriteria)
    {
        $arrCriteria = [];
        $rootid      = $this->getRootIdFromUrl();
        $arrPages    = $this->objFilter->getPageIdsByPid($rootid);
        $res         = $this->Database->prepare('SELECT * from tl_article where pid IN ('.implode(',',
                $arrPages).') AND published=? AND articlefilter_enable=? AND ((stop = "" OR stop > NOW()) && (start = "" OR start < NOW()))')
            ->execute(1, 1);
        if ($res->numRows == 0)
        {
            return $arrCriteria;
        }

        while ($res->next())
        {
            $selectedCriteria = deserialize($res->articlefilter_criteria);
            if (!is_array($selectedCriteria))
            {
                continue;
            }
            if (is_array($searchCriteria) && count($searchCriteria) > 0)
            {
                $isValid = true;
                foreach ($searchCriteria as $c)
                {
                    if (!in_array($c, $selectedCriteria))
                    {
                        $isValid = false;
                    }
                }

                if (!$isValid)
                {
                    continue;
                }
            }
            foreach ($selectedCriteria as $crit)
            {
                if (in_array($crit, $searchCriteria))
                {
                    continue;
                }
                if (!array_key_exists($crit, $arrCriteria))
                {
                    $arrCriteria[$crit] = 0;
                }
                $arrCriteria[$crit]++;
            }
        }
        return $arrCriteria;
    }

}

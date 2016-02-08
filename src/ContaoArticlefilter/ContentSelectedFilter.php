<?php

namespace ContaoArticlefilter;

/**
 * Class ContentSelectedFilter based on version of Stefan Gandlau <stefan@gandlau.net>
 *
 */
class ContentSelectedFilter extends \ContentElement
{
    protected $strTemplate = 'ce_articlefilter_selected';
    protected $arrCriteria = [];

    public function generate()
    {
        $arrArticle = $this->Database->prepare('SELECT * from tl_article where id=?')->execute($this->pid)->fetchAssoc();
        if (!$arrArticle['articlefilter_enable'])
        {
            return;
        }
        $this->arrCriteria = deserialize($arrArticle['articlefilter_criteria'], true);
        return parent::generate();
    }

    protected function compile()
    {
        if (!is_array($this->arrCriteria) || count($this->arrCriteria) < 1)
        {
            return;
        }

        $res = $this->Database->prepare('SELECT t1.*, t2.title grp from tl_articlefilter_criteria t1, tl_articlefilter_groups t2 where t1.pid=t2.id AND t1.id IN ('.implode(',',
                $this->arrCriteria).') ORDER BY t2.sortindex,t1.sorting')->execute();

        if ($res->numRows < 1)
        {
            return;
        }

        $arrGroups = [];
        while ($res->next())
        {
            if (!is_array($arrGroups[$res->grp]))
            {
                $arrGroups[$res->grp] = [];
            }
            $arrGroups[$res->grp][] = $res->title;
        }

        if (count($arrGroups) < 1)
        {
            return;
        }

        $this->Template->selectedCriteria = $arrGroups;
    }

}

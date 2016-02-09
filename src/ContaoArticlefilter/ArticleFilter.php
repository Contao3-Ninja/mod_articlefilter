<?php

namespace ContaoArticlefilter;

/**
 * Class ArticleFilter based on version of Stefan Gandlau <stefan@gandlau.net>
 *
 */
class ArticleFilter extends \Controller
{

    /* ajax or form-request */
    protected $isAjax = false;

    /* pagination object */
    protected $pagination;

    /* data storage */
    protected $filterGroups         = [];
    protected $filterCriteria       = [];
    protected $searchFilterText     = [];
    protected $searchFilterCriteria = [];
    protected $arrPages             = [];

    protected $no_filter = true;
    protected $hasFilter = false;
    protected $afstype;
    public $sorting = 't2.sorting';
    protected $showAll = false;

    protected $articlefilter_groupbypage = false;
    protected $resultCount = 0;
    protected $results = [];

    public function __construct($rootid = false)
    {
        $this->Import('Database');
        $this->Import('Input');
        if (\Input::get('isAjax') == 1)
        {
            $this->isAjax = true;
        }
        $this->prepareFilter($rootid ? $rootid : 0);
    }

    public function run()
    {
        if ($this->showAll && !$this->hasFilter)
        {
            $res = \Database::getInstance()
                ->prepare('SELECT t1.*, t1.title atitle, t2.title ptitle from tl_article t1, tl_page t2 WHERE t1.articlefilter_enable=? AND t1.published=? AND t1.pid = t2.id ORDER BY '.$this->sorting)
                ->execute(1, 1);
            if ($res->numRows == 0)
            {
                $this->results     = [];
                $this->resultCount = 0;
                return;
            }
            $arrArticles = [];
            while ($res->next())
            {
                $row = $res->row();
                if ($this->articlefilter_groupbypage)
                {
                    if (!is_array($arrArticles[$res->ptitle]))
                    {
                        $arrArticles[$res->ptitle] = [];
                    }
                    $arrArticles[$res->ptitle][] = [
                        'title'   => $res->title,
                        'teaser'  => $res->teaser,
                        'href'    => $this->generatePageLink($res->pid, $res->alias),
                        'image'   => $this->prepareArticleImage($row, $this->generatePageLink($res->pid, $res->alias))
                    ];
                }
                else
                {
                    $arrArticles[] = [
                        'title'   => $res->title,
                        'teaser'  => $res->teaser,
                        'href'    => $this->generatePageLink($res->pid, $res->alias),
                        'image'   => $this->prepareArticleImage($row, $this->generatePageLink($res->pid, $res->alias))
                    ];
                }
            }

            $this->resultCount = count($arrArticles);
            $this->results     = $arrArticles;

            return;
        }
        if (!$this->hasFilter)
        {
            $this->resultCount = 0;
            $this->results     = [];
            return;
        }

        /* find all article */
        $res = \Database::getInstance()->prepare('SELECT t1.*, t1.title atitle, t2.title ptitle, t2.pageTitle pageTitle from tl_article t1, tl_page t2 WHERE t1.articlefilter_enable=? AND t1.published=? AND t1.pid = t2.id AND t1.pid IN ('.implode(',',
                $this->arrPages).') ORDER BY '.$this->sorting)->execute(1, 1);
        if ($res->numRows == 0)
        {
            return;
        }
        $arrArticles = [];
        while ($res->next())
        {
            $row = $res->row();
            $ac = deserialize($res->articlefilter_criteria);
            if (!is_array($ac))
            {
                continue;
            }
            if ($this->afstype == 'matchAny')
            {
                if (count(array_intersect($ac, $this->searchFilterCriteria)))
                {
                    if ($this->articlefilter_groupbypage)
                    {
                        if (!is_array($arrArticles[$res->ptitle]))
                        {
                            $arrArticles[$res->ptitle] = [];
                        }
                        $arrArticles[$res->ptitle][] = $this->createEntry($row);
                    }
                    else
                    {
                        $arrArticles[] = $this->createEntry($row);
                    }
                }
            }
            else
            {
                $allMatch = true;
                foreach ($this->searchFilterCriteria as $filter)
                {
                    if (!in_array($filter, $ac)) {
                        $allMatch = false;
                    }
                }

                if ($allMatch)
                {
                    if ($this->articlefilter_groupbypage)
                    {
                        if (!is_array($arrArticles[$res->ptitle]))
                        {
                            $arrArticles[$res->ptitle] = array();
                        }
                        $arrArticles[$res->ptitle][] = $this->createEntry($row);
                    }
                    else
                    {
                        $arrArticles[] = $this->createEntry($row);
                    }
                }
            }

        }

        $this->resultCount = count($arrArticles);
        $this->results     = $arrArticles;
    }

    private function createEntry($row)
    {
        $arr = [
            'title'   => $row['title'],
            'teaser'  => $row['teaser'],
            'href'    => $this->generatePageLink($row['pid'], $row['alias']),
            'image'    => $this->prepareArticleImage($row, $this->generatePageLink($row['pid'], $row['alias']))
        ];
        return $arr;
    }

    protected function prepareArticleImage($arrRow, $href)
    {

        if ($arrRow['addImage'] && strlen($arrRow['singleSRC']) && file_exists(TL_ROOT.'/'.$arrRow['singleSRC'])) {
            $arrSize = deserialize($arrRow['size']);
            $arrMargin = deserialize($arrRow['imagemargin']);
            $floating = $arrRow['floating'];
            $alt = $arrRow['alt'];
            $caption = $arrRow['caption'];
            $fullsize = $arrRow['fullsize'];

            $thumb = $this->getImage($arrRow['singleSRC'], $arrSize[0], $arrSize[1], $arrSize[2]);
            $strImage = sprintf('<img src="%s" alt="%s" title="%s"/>', $thumb, $alt, $caption);
            $strImage = sprintf('<a href="%s">%s</a>', $href, $strImage);

            return ($strImage);
        }
    }

    protected function prepareFilter($rootid)
    {
        $this->filterGroups   = $this->readFilterGroups();
        $this->filterCriteria = $this->readFilterCriteria();
        $this->arrPages       = $this->getPageIdsByPid($rootid);

    }

    protected function readFilterGroups()
    {
        $arrAllGroups = [];
        $res          = \Database::getInstance()->prepare('SELECT * from tl_articlefilter_groups')->execute();
        while ($res->next())
        {
            $arrAllGroups[$res->id] = $res->title;
        }
        return $arrAllGroups;
    }

    protected function readFilterCriteria()
    {
        $arrAllCriteria = [];
        $res            = \Database::getInstance()->prepare('SELECT * from tl_articlefilter_criteria')->execute();
        while ($res->next())
        {
            $arrAllCriteria[$res->id] = $res->title;
        }
        return $arrAllCriteria;
    }

    protected function generatePageLink($pageid, $alias)
    {
        $res = \Database::getInstance()->prepare('SELECT id, alias from tl_page where id=?')->execute($pageid);
        return ($this->generateFrontendUrl($res->fetchAssoc()).'?articles='.$alias);
    }

    public function getPageIdsByPid($pid)
    {
        $res = \Database::getInstance()
            ->prepare('SELECT * from tl_page where pid=? AND published=? AND ( (start = "" || start < NOW()) && (stop = "" OR stop > NOW()))')
            ->execute($pid, 1);
        if ($res->numRows == 0)
        {
            return [];
        }
        while ($res->next())
        {
            $arrPages[] = $res->id;
            $subPages   = $this->getPageIdsByPid($res->id);
            if ($subPages != false)
            {
                $arrPages = array_merge($arrPages, $subPages);
            }
        }
        if (count($arrPages) > 0)
        {
            return $arrPages;
        }

        return falses;
    }

    public function __set($key, $value)
    {
        switch (strtolower($key))
        {
            case 'selectedfilter':
            {
                if (is_array($value) && count($value) > 0)
                {
                    $this->no_filter = false;
                    /* collect selected filter */
                    foreach ($value as $group => $criteria)
                    {
                        if (is_array($criteria))
                        {
                            foreach ($criteria as $c)
                            {
                                if (!strlen($c))
                                {
                                    continue;
                                }
                                $this->hasFilter = true;
                                $this->searchFilterText[] = [
                                    'group'     => $this->filterGroups[$group],
                                    'criteria'  => $this->filterCriteria[$c]
                                ];
                                $this->searchFilterCriteria[] = $c;
                            }
                        }
                        else
                        {
                            if ($criteria == '')
                            {
                                continue;
                            }
                            $this->hasFilter = true;
                            $this->searchFilterText[] = [
                                'group'     => $this->filterGroups[$group],
                                'criteria'  => $this->filterCriteria[$criteria]
                            ];
                            $this->searchFilterCriteria[] = $criteria;
                        }
                    }
                }
                else
                {
                    $this->resultCount = 0;
                }
            }
            break;

            case 'afstype':
                $this->afstype = $value;
            break;

            case 'sorting':
                $this->sorting = $value;
            break;

            case 'showall':
                $this->showAll = $value;
            break;

            case 'groupbypage':
                $this->articlefilter_groupbypage = $value;
            break;
        }
    }

    public function __get($key)
    {
        switch (strtolower($key))
        {
            case 'no_filter':
                return ($this->no_filter);
            break;

            case 'hasfilter':
                return ($this->hasFilter);
            break;

            case 'resultcount':
                return ($this->resultCount);
            break;

            case 'results':
                return ($this->results);
            break;

            case 'searchstrings':
                return ($this->searchFilterText);
            break;

            case 'groupbypage':
                return ($this->articlefilter_groupbypage);
            break;
        }
    }
}

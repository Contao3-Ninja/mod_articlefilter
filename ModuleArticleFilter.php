<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    articlefilter
 * @license    LGPL 
 * @filesource
 */


/**
 * Class ModuleArticleFilter
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    articlefilter
 */


  class ModuleArticleFilter extends Module {
    
    protected $strTemplate = 'mod_articlefilter';

    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### ArticleFilter ###';
        return($t->parse());
      }
      
      /* ajax queries */
      if($this->Input->get('isAjax') == '1') {
        $filter = $this->Input->get('af_filter');
        $objFilter = new ArticleFilter($this->getRootIdFromUrl());
        $objFilter->selectedFilter = $this->Input->get('af_filter');
        $objFilter->afstype = $this->Input->get('afstype');
		
        $objFilter->run();
        print(json_encode(array('resultCount' => $objFilter->resultCount)));
        die();
      }
      return(parent::generate());
    }
    
    protected function compile() {
      $GLOBALS['TL_JAVASCRIPT']['articlefilter'] = 'system/modules/mod_articlefilter/html/articlefilter.js';
      $arrGroups = deserialize($this->af_groups);
      $arrSelected = $this->Input->get('af_filter');
      $res = $this->Database->prepare('SELECT * from tl_af_groups where id IN ('. implode(',', $arrGroups) .') AND published=1 ORDER BY sortindex')->execute();
      if($res->numRows > 0) {
        $arrBoxes = array();
        $this->Template->hasGroups = true;
        $id = 0;
        while($res->next()) {
          $arrCriteria = $this->loadCriteriaByGroup($res->id);
          if(is_array($arrCriteria)) {
            $id++;
            $objCB = new FrontendTemplate($res->template);
            $objCB->items = $arrCriteria;
            $objCB->title = $res->title;
            $objCB->name = $res->id;
            $objCB->selected = $arrSelected[$res->id];
            $objCB->id = $id;
            $arrBoxes[] = $objCB->parse();
          }
        }
        /* jumpTo Page */
        $arrJump = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo)->fetchAssoc();
        $this->Template->criterias = implode("\n", $arrBoxes);
        $this->Template->href = $this->generateFrontendUrl($arrJump);
        $this->Template->lblSubmit = $GLOBALS['TL_LANG']['articlefilter']['lblSubmit'];
        $this->Template->selectedMatchType = $this->Input->get('afstype') ? $this->Input->get('afstype') : $this->af_defaultfilter;
        $this->Template->lblMatches = $GLOBALS['TL_LANG']['articlefilter']['lblMatches'];
        $this->Template->lblAny = $GLOBALS['TL_LANG']['articlefilter']['lblAny'];
        $this->Template->lblAll = $GLOBALS['TL_LANG']['articlefilter']['lblAll'];
      }
    }
    
    protected function loadCriteriaByGroup($pid) {
      $res = $this->Database->prepare('SELECT * from tl_af_criteria WHERE pid=? AND published=? ORDER BY sorting')->execute($pid, 1);
      if($res->numRows == 0) return(false);
      
      $arrCriteria = array();
      while($res->next()) {
        $arrCriteria[$res->id] = $res->title;
      }
      return($arrCriteria);
    }
    
  }

?>
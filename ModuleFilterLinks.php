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
 * @copyright  Stefan Gandlau 2010
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    mod_articlefilter
 * @license    LGPL 
 * @filesource
 */


/**
 * Class ModuleFilterLinks
 *
 * @copyright  Stefan Gandlau 2010
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    mod_articlefilter
 */


  class ModuleFilterLinks extends Module {
    
    protected $strTemplate = 'mod_articlefilter_links';
    protected $objFilter;
    protected $arrJump = array();
    
    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### Article Filter (Links) ###';
        return($t->parse());
      }
      
      $this->arrJump = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo)->fetchAssoc();
      $this->objFilter = new ArticleFilter();
      return(parent::generate());
    }
    
    protected function compile() {
      /* get current filter */
      $arrFilter = $this->Input->get('af_filter');
      $searchCriteria = array();
      if(is_array($arrFilter)) {
        foreach($arrFilter as $grp => $filter)
          foreach($filter as $c)
            $searchCriteria[] = $c;
            
        $arrHTML = array($this->generateFilterBox($arrFilter));    
      }
      $arrValidFilter = $this->findValidFilter($searchCriteria);
      if(count($arrValidFilter) == 0) {
        $this->Template->groups = implode("\n", $arrHTML);
        return;
      }
      
      $arrFilterIDs = array_keys($arrValidFilter);
      $arrGroups = array();
      $res = $this->Database->prepare('SELECT t1.*, t2.title grp, t2.id gid from tl_af_criteria t1, tl_af_groups t2 WHERE t1.id IN ('. implode(',', $arrFilterIDs) .') AND t1.pid=t2.id AND t1.published=? ORDER BY t2.sortindex, t1.sorting')->execute(1);
      while($res->next()) {
        if(!is_array($arrGroups[$res->grp]))
          $arrGroups[$res->grp] = array();
          
        $arrGroups[$res->grp][] = array(
          'href' => $this->generateFilterLink($arrFilter, $res->row()),
          'title' => $res->title,
          'count' => $arrValidFilter[$res->id]
        );
      }

      foreach($arrGroups as $title => $grp) {
        $objT = new FrontendTemplate('mod_filterlink_group');
        $objT->title = $title;
        $objT->links = $grp;
        $arrHTML[] = $objT->parse();
      }
      
      $this->Template->groups = implode("\n", $arrHTML);
    }
    
    protected function generateFilterBox($filter) {
      $objT = new FrontendTemplate('mod_filterlink_set');
      $objT->title = $GLOBALS['TL_LANG']['articlefilter']['selectedFilter'];
      $baseurl = $this->generateFrontendUrl($this->arrJump);
      $objT->baseurl = $baseurl;
      $objT->removeAll = $GLOBALS['TL_LANG']['articlefilter']['removeAll'];
      $arrLinks = array();
      
      if(!is_array($filter) || count($filter) == 0) return('');
      foreach($filter as $grp => $f) {
        foreach($f as $id) {
          $arrAdd[] = sprintf('af_filter[%s][]=%s', $grp, $id);
          $lbl[] = $this->getFilterTitle($id);
        }
      }
      for($x = 0; $x < count($arrAdd); $x++) {
        $add = $arrAdd;
        unset($add[$x]);
        $arrLinks[] = array('href' => sprintf('%s?%s', $baseurl, implode('&', $add)), 'title' => $lbl[$x]);
      }
      $objT->links = $arrLinks;
      return($objT->parse());
    }
    
    protected function getFilterTitle($id) {
      $res = $this->Database->prepare('SELECT * from tl_af_criteria where id=?')->execute($id);
      if($res->numRows == 0) return('');
      $row = $res->fetchAssoc();
      return($row['title']);
    }
    
    protected function generateFilterLink($arrFilter, $arrItem = false) {
      if(is_array($arrItem))
        $strURL = sprintf('?af_filter[%s][]=%s', $arrItem['gid'], $arrItem['id']);
      else
        $strURL = '';
      if(is_array($arrFilter))
        foreach($arrFilter as $group => $filter)
          foreach($filter as $key => $val)
            $strURL .= sprintf('&af_filter[%s][]=%s', $group, $val);
        
      return($this->generateFrontendUrl($this->arrJump) . $strURL);
    }
    
    protected function findValidFilter($searchCriteria) {
      $rootid = $this->getRootIdFromUrl();
      $arrPages = $this->objFilter->getPageIdsByPid($rootid);
      $res = $this->Database->prepare('SELECT * from tl_article where pid IN ('. implode(',', $arrPages) .') AND published=? AND af_enable=? AND ((stop = "" OR stop > NOW()) && (start = "" OR start < NOW()))')->execute(1, 1);
      if($res->numRows == 0) {
        return(array());
      }
      $arrCriteria = array();
      while($res->next()) {
        $selectedCriteria = deserialize($res->af_criteria);
        if(!is_array($selectedCriteria)) continue;
        if(is_array($searchCriteria) && count($searchCriteria) > 0) {
          $isValid = true;
          foreach($searchCriteria as $c)
            if(!in_array($c, $selectedCriteria))
              $isValid = false;
                
          if(!$isValid) continue;
        }
        foreach($selectedCriteria as $crit) {
          if(in_array($crit, $searchCriteria)) continue;
          if(!array_key_exists($crit, $arrCriteria))
            $arrCriteria[$crit] = 0;
          $arrCriteria[$crit]++;
        }
      }
      return($arrCriteria);
    }
    
  }

?>
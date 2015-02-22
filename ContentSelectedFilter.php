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
 * Class ContentSelectedFilter
 *
 * @copyright  Stefan Gandlau 2010
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    mod_articlefilter
 */


  class ContentSelectedFilter extends ContentElement {
    
    protected $strTemplate = 'ce_af_selected';
    protected $arrCriteria = array();
    
    public function generate() {
     
      $arrArticle = $this->Database->prepare('SELECT * from tl_article where id=?')->execute($this->pid)->fetchAssoc();
      if(!$arrArticle['af_enable'])
        return;
        
      $this->arrCriteria = deserialize($arrArticle['af_criteria'], true);
      return(parent::generate());
    }
    
    protected function compile() {
      if(!is_array($this->arrCriteria) || count($this->arrCriteria) < 1)
        return;
        
      $res = $this->Database->prepare('SELECT t1.*, t2.title grp from tl_af_criteria t1, tl_af_groups t2 where t1.pid=t2.id AND t1.id IN ('. implode(',', $this->arrCriteria) .') ORDER BY t2.sortindex,t1.sorting')->execute();
      if($res->numRows < 1)
        return;
      
      $arrGroups = array();
      while($res->next()) {
        if(!is_array($arrGroups[$res->grp]))
          $arrGroups[$res->grp] = array();
        $arrGroups[$res->grp][] = $res->title;
      }
      
      if(count($arrGroups) < 1)
        return;
      
      $this->Template->selectedCriteria = $arrGroups;
    }
    
  }

?>
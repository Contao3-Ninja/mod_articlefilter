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
 * Class ModuleFilterResults
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    articlefilter
 */


  class ModuleFilterResults extends Module {
    
    protected $strTemplate = 'mod_articlefilter_results';

    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### Filter Results ###';
        return($t->parse());
      }
      
      return(parent::generate());
    }
   
    protected function compile() {
    
      if($this->Input->get('isAjax') == '1') return;
      $objFilter = new ArticleFilter($this->getRootIdFromUrl());
      $objFilter->selectedFilter = $this->Input->get('af_filter');
      $objFilter->afstype = $this->Input->get('afstype');
      $objFilter->sorting = $this->af_sorting;
      $objFilter->showAll = true;
      
      
      
      $objFilter->run();
      
      
      /* search articles matching filter */
      if($objFilter->resultCount > 0) {

        $resultCount = $objFilter->resultCount;
        $results = $objFilter->results;
        
        if($this->perPage > 0 && $resultCount > $this->perPage) {
          $objPagination = new Pagination($resultCount, $this->perPage);
          $this->Template->pagination = $objPagination->generate();
          
          $page = $this->Input->get('page');
          if($page == '' || $page < 1)
            $page = 1;
            
          $offset = ($page - 1) * $this->perPage;
          $results = array_slice($results, $offset, $this->perPage);
        }
        
        
          
        $this->Template->resultCount = $resultCount;
        $this->Template->results = $results;

        $this->Template->showFilter = $this->af_showfilter;
        $this->Template->selectedFilter = $objFilter->searchStrings;
        $this->Template->selectedFilterHeadline = sprintf($GLOBALS['TL_LANG']['articlefilter']['selectedFilterHeadline'], $resultCount);
      } else {
        $this->Template->no_filter = true;
      }
      
    }
    
    
    
  }

?>
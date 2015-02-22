<?php
  array_insert($GLOBALS['BE_MOD']['content'], 1, array(
    'articlefilter' => array(
      'tables' => array('tl_af_groups', 'tl_af_criteria'),
      'icon' => 'system/modules/mod_articlefilter/html/icon.png'
    )
  ));
  
  $GLOBALS['FE_MOD']['application']['articlefilter'] = 'ModuleArticleFilter';
  $GLOBALS['FE_MOD']['application']['articlefilter_links'] = 'ModuleFilterLinks';
  $GLOBALS['FE_MOD']['application']['articlefilter_results'] = 'ModuleFilterResults';
  
  $GLOBALS['TL_CTE']['includes']['mod_af_selected'] = 'ContentSelectedFilter';
  
?>
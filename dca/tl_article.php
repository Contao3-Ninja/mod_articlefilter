<?php 

  $GLOBALS['TL_DCA']['tl_article']['fields']['af_enable'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_article']['af_enable'],
    'inputType' => 'checkbox',
  	'eval' => array('submitOnChange' => true)
  );
  
  $GLOBALS['TL_DCA']['tl_article']['fields']['af_criteria'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_article']['af_criteria'],
    'inputType' => 'checkbox',
  	'options_callback' => array('tl_article_af', 'loadAFOptions'),
    'eval' => array('multiple' => true)
  );
  
  $GLOBALS['TL_DCA']['tl_article']['subpalettes']['af_enable'] = 'af_criteria';
  $GLOBALS['TL_DCA']['tl_article']['palettes']['default'] .= ';{title_articlefilter},af_enable';
  $GLOBALS['TL_DCA']['tl_article']['palettes']['__selector__'][] = 'af_enable';
  
  class tl_article_af extends Backend {
  
    public function loadAFOptions() {
      $res = $this->Database->prepare('SELECT * FROM tl_af_groups ORDER BY sorting')->execute();
      if($res->numRows == 0) return(array());
      $arrRes = array();
      while($res->next()) {
        $r = $this->Database->prepare('SELECT * from tl_af_criteria where pid=? ORDER BY sorting')->execute($res->id);
        if($r->numRows > 0) {
          while($r->next()) {
            if(!is_array($arrRes[$res->title]))
              $arrRes[$res->title] = array();
              
            $arrRes[$res->title][$r->id] = $r->title;
          }
        }
      }
      return($arrRes);
    }
  
  }
  
  

?>
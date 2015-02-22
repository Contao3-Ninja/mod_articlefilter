<?php

  $GLOBALS['TL_DCA']['tl_module']['fields']['af_groups'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['af_groups'],
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_af_groups.title',
    'eval' => array('mandatory' => true, 'multiple' => true)
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['af_defaultfilter'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['af_defaultfilter'],
    'inputType' => 'select',
    'options' => array('matchAny' => $GLOBALS['TL_LANG']['articlefilter']['lblAny'], 'matchAll' => $GLOBALS['TL_LANG']['articlefilter']['lblAll']),
    'default' => 'matchAll'
  );

  $GLOBALS['TL_DCA']['tl_module']['fields']['af_showfilter'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_module']['af_showfilter'],
    'inputType' => 'checkbox'
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['af_sorting'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['af_sorting'],
    'inputType' => 'select',
    'options' => array(
      't2.sorting',
      't1.tstamp desc',
      'ptitle',
      'atitle'
    ),
    'reference' => $GLOBALS['TL_LANG']['tl_module']['af_sortings'],
    'default' => 'tstamp'
  );
  
  $GLOBALS['TL_DCA']['tl_module']['palettes']['articlefilter'] = '{title_legend},name,headline,type;{config_legend},af_groups,af_defaultfilter;{redirect_legend},jumpTo;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['articlefilter_links'] = '{title_legend},name,headline,type;{redirect_legend},jumpTo;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['articlefilter_results'] = '{title_legend},name,headline,type;{config_legend},af_showfilter,af_sorting,perPage;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
?>
<?php

$GLOBALS['TL_DCA']['tl_module']['fields']['articlefilter_groups'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_module']['articlefilter_groups'],
    'inputType'  => 'checkbox',
    'foreignKey' => 'tl_articlefilter_groups.title',
    'eval'       => ['mandatory' => true, 'multiple' => true],
    'sql'        => "text NULL"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['articlefilter_defaultfilter'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['articlefilter_defaultfilter'],
    'inputType' => 'select',
    'options'   => ['matchAny' => $GLOBALS['TL_LANG']['articlefilter']['lblAny'], 'matchAll' => $GLOBALS['TL_LANG']['articlefilter']['lblAll']],
    'default'   => 'matchAll',
    'sql'       => "varchar(32) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['articlefilter_showfilter'] = [
    'label'      => $GLOBALS['TL_LANG']['tl_module']['articlefilter_showfilter'],
    'inputType'  => 'checkbox',
    'sql'        => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['articlefilter_sorting'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['articlefilter_sorting'],
    'inputType' => 'select',
    'options'   => [
        't2.sorting',
        't1.tstamp desc',
        'ptitle',
        'atitle'
    ],
    'reference' => $GLOBALS['TL_LANG']['tl_module']['articlefilter_sortings'],
    'default'   => 'tstamp',
    'sql'       => "varchar(32) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['palettes']['articlefilter']         = '{title_legend},name,headline,type;{config_legend},articlefilter_groups,articlefilter_defaultfilter;{redirect_legend},jumpTo;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['articlefilter_links']   = '{title_legend},name,headline,type;{config_legend},articlefilter_groups,articlefilter_defaultfilter;{redirect_legend},jumpTo;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['articlefilter_results'] = '{title_legend},name,headline,type;{config_legend},articlefilter_showfilter,articlefilter_sorting,perPage;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

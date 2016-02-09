<?php

$this->loadLanguageFile('tl_content');

$GLOBALS['TL_DCA']['tl_article']['fields']['addImage'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['addImage'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => ['submitOnChange'=>true],
    'sql'                     => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_article']['fields']['singleSRC'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_content']['singleSRC'],
    'exclude'             => true,
    'inputType'           => 'fileTree',
    'eval'                => ['fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'mandatory'=>true],
    'sql'			      => "binary(16) NULL"
    ];

$GLOBALS['TL_DCA']['tl_article']['fields']['articlefilter_enable'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_article']['articlefilter_enable'],
    'inputType'           => 'checkbox',
    'eval'                => ['submitOnChange' => true],
    'sql'                 => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_article']['fields']['articlefilter_criteria'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_article']['articlefilter_criteria'],
    'inputType'           => 'checkbox',
    'options_callback'    => ['tl_article_af', 'loadAFOptions'],
    'eval'                => ['multiple' => true],
    'sql'                 => "text NULL"
];

$GLOBALS['TL_DCA']['tl_article']['palettes']['__selector__'][]          = 'addImage';
$GLOBALS['TL_DCA']['tl_article']['palettes']['__selector__'][]          = 'articlefilter_enable';
$GLOBALS['TL_DCA']['tl_article']['subpalettes']['addImage']             = 'singleSRC';
$GLOBALS['TL_DCA']['tl_article']['subpalettes']['articlefilter_enable'] = 'articlefilter_criteria';

$GLOBALS['TL_DCA']['tl_article']['palettes']['default']                 = str_replace(
    'teaser;', 'teaser,addImage;', $GLOBALS['TL_DCA']['tl_article']['palettes']['default'])
    .';{title_articlefilter},articlefilter_enable';

class tl_article_af extends Backend
{
    public function loadAFOptions()
    {
        $res = $this->Database->prepare('SELECT * FROM tl_articlefilter_groups ORDER BY sorting')->execute();
        $arrRes = [];
        if ($res->numRows == 0)
        {
            return $arrRes;
        }

        while ($res->next())
        {
            $r = $this->Database->prepare('SELECT * from tl_articlefilter_criteria where pid=? ORDER BY sorting')->execute($res->id);
            if ($r->numRows > 0)
            {
                while ($r->next())
                {
                    if (!is_array($arrRes[$res->title]))
                    {
                        $arrRes[$res->title] = [];
                    }
                    $arrRes[$res->title][$r->id] = $r->title;
                }
            }
        }
        return $arrRes;
    }
}

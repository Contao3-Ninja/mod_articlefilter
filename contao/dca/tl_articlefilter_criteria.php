<?php

$GLOBALS['TL_DCA']['tl_articlefilter_criteria'] = [

    // Config
    'config' => [
        'dataContainer' => 'Table',
        'ptable'        => 'tl_articlefilter_groups',
        'sql'           => ['keys' => ['id'  => 'primary']]
    ],

    // List
    'list' => [
        'sorting' => [
            'mode'                  => 4,
            'fields'                => ['sorting'],
            'headerFields'          => ['title'],
            'panelLayout'           => 'search,limit',
            'child_record_callback' => ['tl_articlefilter_criteria', 'listCriteria']
        ],

        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ]
        ],
        'operations' => [
            'edit' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_articlefilter_criteria']['edit'],
                'href'       => 'act=edit',
                'icon'       => 'edit.gif'
            ],
            'copy' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['copy'],
                'href'       => 'act=paste&amp;mode=copy',
                'icon'       => 'copy.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
            'cut' => [

                'label'      => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['cut'],
                'href'       => 'act=paste&amp;mode=cut',
                'icon'       => 'cut.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
            'delete' => [

                'label'      => &$GLOBALS['TL_LANG']['tl_articlefilter_criteria']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\')) return false; Backend.getScrollOffset();"'
            ]
        ]
    ],

    // Palettes
    'palettes' => [
        'default' => 'title;published',
    ],

    // Fields
    'fields' => [
        'id'      => ['sql' => "int(10) unsigned NOT NULL auto_increment"],
        'pid'     => ['sql' => "int(10) unsigned NOT NULL default '0'"],
        'tstamp'  => ['sql' => "int(10) unsigned NOT NULL default '0'"],
        'sorting' => ['sql' => "int(10) unsigned NOT NULL default '0'"],

        'title' => [
            'label'      => &$GLOBALS['TL_LANG']['tl_articlefilter_criteria']['title'],
            'exclude'    => true,
            'search'     => true,
            'inputType'  => 'text',
            'eval'       => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'        => "varchar(255) NOT NULL default ''"

        ],
        'published' => [
            'label'      => &$GLOBALS['TL_LANG']['tl_articlefilter_criteria']['published'],
            'exclude'    => true,
            'filter'     => true,
            'flag'       => 2,
            'inputType'  => 'checkbox',
            'eval'       => ['doNotCopy' => true],
            'sql'        => "char(1) NOT NULL default ''"
        ]
    ]
];

class tl_articlefilter_criteria extends Backend
{
    public function listCriteria($arrRow)
    {
        $key = $arrRow['published'] ? 'published' : 'unpublished';
        return '<div class="cte_type '.$key.'">'.$arrRow['title'].'</div>'."\n";
    }

}

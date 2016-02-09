<?php

$GLOBALS['TL_DCA']['tl_articlefilter_groups'] = [

	// Config
	'config' => [
		'dataContainer'               => 'Table',
		'ctable'                      => ['tl_articlefilter_criteria'],
		'switchToEdit'                => true,
		'sql'                         => ['keys' => ['id'  => 'primary']]
	],

	// List
	'list' => [
		'sorting' => [
			'mode'                    => 1,
			'fields'                  => ['title'],
		  	'flag'                    => 11,
			'panelLayout'             => 'search,limit'
		],
		'label' => [
			'fields'                  => ['title'],
			'format'                  => '%s'
		],
		'global_operations' => [
			'all' => [
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			]
		],
		'operations' => [
			'edit' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['edit'],
				'href'                => 'table=tl_articlefilter_criteria',
				'icon'                => 'edit.gif'
			],
			'copy' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();"',
			],
			'cut' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();"',
		  	],
			'delete' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			]
		]
	],

	// Palettes
	'palettes' => ['default' => 'title,template;published'],

	// Fields
	'fields' => [

		'id'      => ['sql' => "int(10) unsigned NOT NULL auto_increment"],
		'pid'     => ['sql' => "int(10) unsigned NOT NULL default '0'"],
		'tstamp'  => ['sql' => "int(10) unsigned NOT NULL default '0'"],
		'sorting' => ['sql' => "int(10) unsigned NOT NULL default '0'"],

		'title' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['title'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'search'                  => true,
			'eval'                    => ['mandatory'=>true],
			'sql'                     => "varchar(255) NOT NULL default ''"
		],
		'template' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['template'],
			'inputType'               => 'select',
			'options'                 => $this->getTemplateGroup('mod_articlefilter_box_'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		],
		'published' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['published'],
			'inputType'               => 'checkbox',
			'eval'                    => ['doNotCopy' => true],
			'sql'                     => "char(1) NOT NULL default ''"
		]
	]
];

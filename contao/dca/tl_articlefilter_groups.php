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
            'toggle' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['toggle'],
                'icon'                => 'visible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => ['tl_articlefilter_groups', 'toggleIcon']
            ],
			'edit' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['edit'],
				'href'                => 'table=tl_articlefilter_criteria',
				'icon'                => 'edit.gif'
			],
            'editheader' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['editheader'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif',
                'button_callback'     => array('tl_articlefilter_groups', 'editHeader')
            ),
			'delete' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_articlefilter_groups']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			],
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

class tl_articlefilter_groups extends Backend
{
    public function editHeader($row, $href, $label, $title, $icon, $attributes)
    {
        if (!$this->User->canEditFieldsOf('tl_articlefilter_groups'))
        {
            return Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
        }
        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen(Input::get('tid')))
        {
            $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->hasAccess('tl_articlefilter_groups::disable', 'alexf'))
        {
            return '';
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.$row['disable'];

        if ($row['disable'])
        {
            $icon = 'invisible.gif';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="' . ($row['disable'] ? 0 : 1) . '"').'</a> ';
    }

    /**
     * Disable/enable
     *
     * @param integer       $intId
     * @param boolean       $blnVisible
     * @param DataContainer $dc
     */
    public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
    {
        // Set the ID and action
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');

        if ($dc)
        {
            $dc->id = $intId; // see #8043
        }

        // Check the field access
        if (!$this->User->hasAccess('tl_articlefilter_groups::disable', 'alexf'))
        {
            $this->log('Not enough permissions to activate/deactivate articlefilter group ID "'.$intId.'"', __METHOD__, TL_ERROR);
            $this->redirect('contao/main.php?act=error');
        }

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_articlefilter_groups']['fields']['disable']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_articlefilter_groups']['fields']['disable']['save_callback'] as $callback)
            {
                if (is_array($callback))
                {
                    $this->import($callback[0]);
                    $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, ($dc ?: $this));
                }
                elseif (is_callable($callback))
                {
                    $blnVisible = $callback($blnVisible, ($dc ?: $this));
                }
            }
        }

        $time = time();

        // Update the database
        $this->Database
            ->prepare("UPDATE tl_articlefilter_groups SET tstamp=$time, published='" . ($blnVisible ? '' : 1) . "' WHERE id=?")
            ->execute($intId);

    }

}

<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

  $GLOBALS['TL_DCA']['tl_af_groups'] = array(
	
		// Config
		'config' => array(
			'dataContainer'               => 'Table',
			'ctable'                      => array('tl_af_criteria'),
			'switchToEdit'                => true,
		),
	
		// List
		'list' => array
		(
			'sorting' => array
			(
				'mode'                    => 1,
				'fields'                  => array('sortindex'),
			  'flag'                    => 11,
				'panelLayout'             => 'search,limit'
			),
			'label' => array
			(
				'fields'                  => array('title'),
				'format'                  => '%s'
			),
			'global_operations' => array
			(
				'all' => array
				(
					'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
					'href'                => 'act=select',
					'class'               => 'header_edit_all',
					'attributes'          => 'onclick="Backend.getScrollOffset();"'
				)
			),
			'operations' => array
			(
				'edit' => array
				(
					'label'               => &$GLOBALS['TL_LANG']['tl_af_groups']['edit'],
					'href'                => 'table=tl_af_criteria',
					'icon'                => 'edit.gif'
				),
				'copy' => array
        (
	        'label'               => &$GLOBALS['TL_LANG']['tl_af_groups']['copy'],
	        'href'                => 'act=paste&amp;mode=copy',
	        'icon'                => 'copy.gif',
	        'attributes'          => 'onclick="Backend.getScrollOffset();"',
	      ),
	      'cut' => array
	      (
	        'label'               => &$GLOBALS['TL_LANG']['tl_af_groups']['cut'],
	        'href'                => 'act=paste&amp;mode=cut',
	        'icon'                => 'cut.gif',
	        'attributes'          => 'onclick="Backend.getScrollOffset();"',
	      ),
				'delete' => array
				(
					'label'               => &$GLOBALS['TL_LANG']['tl_af_groups']['delete'],
					'href'                => 'act=delete',
					'icon'                => 'delete.gif',
					'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
				)
			)
		),
	
		// Palettes
		'palettes' => array
		(
			'default'                     => 'title,template,sortindex;published'
		),
	
		// Fields
		'fields' => array
		(
			'title' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['tl_af_groups']['title'],
				'inputType'               => 'text',
				'exclude'                 => true,
				'search'                  => true,
				'eval'                    => array('mandatory'=>true)
			),
      'template' => array(
        'label'                   => &$GLOBALS['TL_LANG']['tl_af_groups']['template'],
        'inputType'               => 'select',
        'options'                 => $this->getTemplateGroup('mod_af_box_')
      ),
      'sortindex' => array(
        'label'                   => &$GLOBALS['TL_LANG']['tl_af_groups']['sortindex'],
        'inputType'               => 'text',
        'eval'                    => array('rgxp' => 'digit', 'mandatory' => true),
        'default'                 => 10
      ),
      'published' => array(
        'label'                   => &$GLOBALS['TL_LANG']['tl_af_groups']['published'],
        'inputType'               => 'checkbox',
        'eval'                    => array('doNotCopy' => true)
      )
		)
	);

?>
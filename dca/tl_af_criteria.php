<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005-2009 Leo Feyer
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
 * Table tl_af_criteria
 */
$GLOBALS['TL_DCA']['tl_af_criteria'] = array
(

  // Config
  'config' => array
  (
    'dataContainer'               => 'Table',
    'ptable'                      => 'tl_af_groups',
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 4,
      'fields'                  => array('sorting'),
      'headerFields'            => array('title'),
      'panelLayout'             => 'search,limit',
      'child_record_callback'   => array('tl_af_criteria', 'listCriteria')
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
        'label'               => &$GLOBALS['TL_LANG']['tl_af_criteria']['edit'],
        'href'                => 'act=edit',
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
        'label'               => &$GLOBALS['TL_LANG']['tl_af_criteria']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    'default'                     => 'title;published',
  ),



  // Fields
  'fields' => array
  (
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_af_criteria']['title'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class' => 'w50')
    ),
    'published' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_af_criteria']['published'],
      'exclude'                 => true,
      'filter'                  => true,
      'flag'                    => 2,
      'inputType'               => 'checkbox',
      'eval'                    => array('doNotCopy'=>true)
    )
  )
);

class tl_af_criteria extends Backend {
  
  public function listCriteria($arrRow) {
    $key = $arrRow['published'] ? 'published' : 'unpublished';
    return '<div class="cte_type ' . $key . '">' . $arrRow['title'] . '</div>' . "\n";
  }

}

?>
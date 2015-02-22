<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Mod_articlefilter
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'ModuleFilterLinks'     => 'system/modules/mod_articlefilter/ModuleFilterLinks.php',
	'ModuleArticleFilter'   => 'system/modules/mod_articlefilter/ModuleArticleFilter.php',
	'ContentSelectedFilter' => 'system/modules/mod_articlefilter/ContentSelectedFilter.php',
	'ArticleFilter'         => 'system/modules/mod_articlefilter/ArticleFilter.php',
	'ModuleFilterResults'   => 'system/modules/mod_articlefilter/ModuleFilterResults.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_articlefilter_results' => 'system/modules/mod_articlefilter/templates',
	'mod_articlefilter_links'   => 'system/modules/mod_articlefilter/templates',
	'mod_filterlink_group'      => 'system/modules/mod_articlefilter/templates',
	'mod_articlefilter'         => 'system/modules/mod_articlefilter/templates',
	'mod_af_box_selectmulti'    => 'system/modules/mod_articlefilter/templates',
	'mod_af_box'                => 'system/modules/mod_articlefilter/templates',
	'mod_af_box_checkbox'       => 'system/modules/mod_articlefilter/templates',
	'mod_af_box_radio'          => 'system/modules/mod_articlefilter/templates',
	'mod_af_box_select'         => 'system/modules/mod_articlefilter/templates',
	'mod_filterlink_set'        => 'system/modules/mod_articlefilter/templates',
	'ce_af_selected'            => 'system/modules/mod_articlefilter/templates',
));

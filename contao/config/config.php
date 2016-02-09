<?php

array_insert($GLOBALS['BE_MOD']['content'], 1, [
    'articlefilter' => [
        'icon'           => 'system/modules/articlefilter/assets/icon.png',
        'tables'         => ['tl_articlefilter_groups', 'tl_articlefilter_criteria']
    ]
]);

$GLOBALS['FE_MOD']['application']['articlefilter']            = '\ContaoArticleFilter\ModuleArticleFilter';
$GLOBALS['FE_MOD']['application']['articlefilter_links']      = '\ContaoArticleFilter\ModuleFilterLinks';
$GLOBALS['FE_MOD']['application']['articlefilter_results']    = '\ContaoArticleFilter\ModuleFilterResults';


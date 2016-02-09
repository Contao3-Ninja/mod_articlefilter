<?php

array_insert($GLOBALS['BE_MOD']['content'], 1, [
    'articlefilter' => [
        'icon'           => 'system/modules/articlefilter/assets/icon.png',
        'tables'         => ['tl_articlefilter_groups', 'tl_articlefilter_criteria']
    ]
]);

$GLOBALS['FE_MOD']['application']['articlefilter_form']       = '\ContaoArticlefilter\Modules\ModuleFilterForm';
$GLOBALS['FE_MOD']['application']['articlefilter_links']      = '\ContaoArticlefilter\Modules\ModuleFilterLinks';
$GLOBALS['FE_MOD']['application']['articlefilter_results']    = '\ContaoArticlefilter\Modules\ModuleFilterResults';


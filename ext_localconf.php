<?php
defined('TYPO3_MODE') || die();

call_user_func(function($extensionKey) {

    if (empty($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][\BeFlo\T3BrokenLinks\Service\InvalidLinkService::CACHE_NAME])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][\BeFlo\T3BrokenLinks\Service\InvalidLinkService::CACHE_NAME] = [];
    }
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][\BeFlo\T3BrokenLinks\Service\InvalidLinkService::CACHE_NAME]['backend'] = \TYPO3\CMS\Core\Cache\Backend\FileBackend::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['getTable'][] = \BeFlo\T3BrokenLinks\Hook\DatabaseRecordListHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawFooter'][] = \BeFlo\T3BrokenLinks\Hook\PageLayoutViewHook::class;

    \BeFlo\T3BrokenLinks\Registry\AnalyzerRegistry::getInstance()
        ->register(\BeFlo\T3BrokenLinks\Analyzer\RteAnalyzer::NAME, \BeFlo\T3BrokenLinks\Analyzer\RteAnalyzer::class)
        ->register(\BeFlo\T3BrokenLinks\Analyzer\InputLinkAnalyzer::NAME, \BeFlo\T3BrokenLinks\Analyzer\InputLinkAnalyzer::class)
    ;


    \BeFlo\T3BrokenLinks\Registry\LinkHandlerRegistry::getInstance()
        ->register(\BeFlo\T3BrokenLinks\LinkHandler\PageLinkHandler::NAME, \BeFlo\T3BrokenLinks\LinkHandler\PageLinkHandler::class)
        ->register(\BeFlo\T3BrokenLinks\LinkHandler\FileLinkHandler::NAME, \BeFlo\T3BrokenLinks\LinkHandler\FileLinkHandler::class)
    ;

}, 't3_broken_links');

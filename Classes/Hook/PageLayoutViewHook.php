<?php


namespace BeFlo\T3BrokenLinks\Hook;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 *
 * @author Florian Peters <fpeters1392@googlemail.com>
 */


use BeFlo\T3BrokenLinks\Service\InvalidLinkService;
use BeFlo\T3BrokenLinks\Service\LinkCheckService;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawFooterHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageLayoutViewHook extends AbstractBackendNotification implements PageLayoutViewDrawFooterHookInterface
{
    /**
     * @param PageLayoutView $parentObject
     * @param array          $info
     * @param array          $row
     */
    public function preProcess(PageLayoutView &$parentObject, &$info, array &$row)
    {
        static $alreadyRun = false;
        if ($alreadyRun === false) {
            GeneralUtility::makeInstance(LinkCheckService::class)->checkTables($parentObject->id);
            $service = GeneralUtility::makeInstance(InvalidLinkService::class);
            if(!$service->isCacheWarmedUp($parentObject->id)) {
                GeneralUtility::makeInstance(LinkCheckService::class)->checkTables($parentObject->id);
            }
            $elements = $service->getElementsForPid($parentObject->id);
            $table = 'tt_content';
            $this->handleOccurrences($elements, $table);
            $alreadyRun = true;
        }
    }

}
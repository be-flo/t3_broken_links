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
use TYPO3\CMS\Backend\RecordList\RecordListGetTableHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList;

class DatabaseRecordListHook extends AbstractBackendNotification implements RecordListGetTableHookInterface
{
    /**
     * @param string             $table
     * @param int                $pageId
     * @param string             $additionalWhereClause
     * @param string             $selectedFieldsList
     * @param DatabaseRecordList $parentObject
     */
    public function getDBlistQuery($table, $pageId, &$additionalWhereClause, &$selectedFieldsList, &$parentObject)
    {
        $service = GeneralUtility::makeInstance(InvalidLinkService::class);
        if (!$service->isCacheWarmedUp($pageId)) {
            GeneralUtility::makeInstance(LinkCheckService::class)->checkTables($pageId);
        }
        $elements = $service->getElementsForPid($pageId);
        $this->handleOccurrences($elements, $table);
    }
}
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


use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractBackendNotification
{

    /**
     * @param array  $elements
     * @param string $table
     */
    protected function handleOccurrences(array $elements, string $table): void
    {
        if (!empty($elements[$table]) && is_array($elements[$table])) {
            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
            foreach ($elements[$table] as $recordUid => $fields) {
                $flashMessage = GeneralUtility::makeInstance(FlashMessage::class,
                    $this->getMessage($table, $fields),
                    $this->getTitle($table, $recordUid),
                    FlashMessage::INFO
                );
                $messageQueue->addMessage($flashMessage);
            }
        }
    }

    /**
     * @param string $table
     * @param array  $fields
     *
     * @return string
     */
    protected function getMessage(string $table, array $fields): string
    {
        $languageService = $this->getLanguageService();
        $label = $languageService->sL(
            'LLL:EXT:t3_broken_links/Resources/Private/Language/locallang_be.xlf:broken_links.fields'
        );

        $labels = [];
        foreach ($fields as $field) {
            $fieldLabel = $field;
            if (!empty($GLOBALS['TCA'][$table]['columns'][$field]['label'])) {
                $fieldLabel = $languageService->sL($GLOBALS['TCA'][$table]['columns'][$field]['label']) ?? $fieldLabel;
            }
            $labels[] = $fieldLabel;
        }

        return $label . implode(', ', $labels);
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return GeneralUtility::makeInstance(LanguageService::class);
    }

    /**
     * @param string $table
     * @param int    $recordUid
     *
     * @return string
     */
    protected function getTitle(string $table, int $recordUid): string
    {
        $label = $this->getLanguageService()->sL(
            'LLL:EXT:t3_broken_links/Resources/Private/Language/locallang_be.xlf:broken_links.table'
        );
        $tableLabel = $table;
        if (!empty($GLOBALS['TCA'][$table]['ctrl']['title'])) {
            $tableLabel = $this->getLanguageService()->sL($GLOBALS['TCA'][$table]['ctrl']['title']) ?? $tableLabel;
        }

        return $label . $tableLabel . ' [UID: ' . $recordUid . ']';
    }
}
<?php


namespace BeFlo\T3BrokenLinks\Service;

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


use BeFlo\T3BrokenLinks\Analyzer\AnalyzerInterface;
use BeFlo\T3BrokenLinks\Registry\AnalyzerRegistry;
use Generator;
use SplObjectStorage;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LinkCheckService
{
    /**
     * @var SplObjectStorage|AnalyzerInterface[]
     */
    protected $availableAnalyzer;

    /**
     * @var array
     */
    protected $baseTca = [];

    /**
     * @var InvalidLinkService
     */
    protected $invalidLinkService;

    /**
     * LinkCheckService constructor.
     */
    public function __construct()
    {
        $this->availableAnalyzer = AnalyzerRegistry::getInstance()->getAll();
        $this->invalidLinkService = GeneralUtility::makeInstance(InvalidLinkService::class);
    }

    /**
     * @param int        $pid
     * @param array|null $overrideTca
     */
    public function checkTables(int $pid = 0, array $overrideTca = null): void
    {
        $this->setBaseTca($overrideTca)->processRelevantData($this->aggregateRelevantTablesAndFields(), $pid);
        GeneralUtility::makeInstance(InvalidLinkService::class)->markCacheAsWarmedUp($pid);
    }

    /**
     * @param array    $relevantTablesAndFields
     * @param int|null $pid
     */
    private function processRelevantData(array $relevantTablesAndFields, int $pid): void
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        foreach ($relevantTablesAndFields as $table => $fields) {
            if (!empty($fields)) {
                $connection = $connectionPool->getConnectionForTable($table);
                $fieldNames = array_keys($fields);
                $fieldNames[] = 'uid';
                $fieldNames[] = 'pid';
                $qb = $connection->createQueryBuilder();
                $query = $qb->select(...array_unique($fieldNames))
                    ->from($table)
                    ->where($qb->expr()->eq('pid', $pid))
                    ->execute();
                while (false !== ($row = $query->fetch())) {
                    $this->handleFieldValuesByConfiguration($table, $row, $fields);
                }
            }
        }
    }

    /**
     * @param string $table
     * @param array  $databaseRow
     * @param array  $fieldConfigurations
     */
    private function handleFieldValuesByConfiguration(string $table, array $databaseRow, array $fieldConfigurations): void
    {
        foreach ($databaseRow as $fieldName => $value) {
            if (!empty($fieldConfigurations[$fieldName])) {
                /** @var AnalyzerInterface $analyzer */
                foreach ($fieldConfigurations[$fieldName] as $analyzer) {
                    if (!$analyzer->analyze($value)) {
                        $this->invalidLinkService->storeInvalidLink($table, (int)$databaseRow['pid'], (int)$databaseRow['uid'], $fieldName);
                    }
                }
            }
        }
    }

    /**
     * @param array|null $tca
     *
     * @return LinkCheckService
     */
    private function setBaseTca(?array $tca): self
    {
        if (empty($tca) && !empty($GLOBALS['TCA']) && is_array($GLOBALS['TCA'])) {
            $tca = $GLOBALS['TCA'];
        }
        $this->baseTca = (array)$tca;

        return $this;
    }

    /**
     * @return array
     */
    private function aggregateRelevantTablesAndFields(): array
    {
        $result = [];
        foreach ($this->loopOverTableConfigurationArray($this->baseTca) as $config) {
            $analyzer = $this->getAnalyzerForFieldConfiguration($config['config']);
            if ($analyzer->count() > 0) {
                $result[$config['table']][$config['fieldName']] = $analyzer;
            }
        }

        return $result;
    }

    /**
     * @param array $tableConfigurationArray
     *
     * @return Generator
     */
    private function loopOverTableConfigurationArray(array $tableConfigurationArray): Generator
    {
        foreach ($tableConfigurationArray as $tableName => $tableConfiguration) {
            if (!empty($tableConfiguration['columns']) && is_array($tableConfiguration['columns'])) {
                foreach ($tableConfiguration['columns'] as $fieldName => $fieldConfiguration) {
                    yield ['table' => $tableName, 'fieldName' => $fieldName, 'config' => $fieldConfiguration];
                }
            }
            if (!empty($tableConfiguration['types']) && is_array($tableConfiguration['types'])) {
                foreach ($tableConfiguration['types'] as $type) {
                    if (!empty($type['columnsOverrides']) && is_array($type['columnsOverrides'])) {
                        foreach ($type['columnsOverrides'] as $column => $override) {
                            if (!empty($tableConfiguration['columns'][$column]) && is_array($tableConfiguration['columns'][$column])) {
                                $config = array_merge_recursive($tableConfiguration['columns'][$column], $override);
                                yield ['table' => $tableName, 'fieldName' => $column, 'config' => $config];
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $fieldConfiguration
     *
     * @return SplObjectStorage
     */
    private function getAnalyzerForFieldConfiguration(array $fieldConfiguration): SplObjectStorage
    {
        $result = new SplObjectStorage();
        foreach ($this->availableAnalyzer as $analyzer) {
            if ($analyzer->istMatchingForFieldConfiguration($fieldConfiguration)) {
                $result->attach($analyzer);
            }
        }

        return $result;
    }
}
<?php


namespace BeFlo\T3BrokenLinks\LinkHandler;

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


use Doctrine\DBAL\FetchMode;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractLinkHandler implements LinkHandlerInterface
{

    /**
     * @param string $table
     * @param int    $uid
     *
     * @return bool
     */
    public function checkIfRecordExist(string $table, int $uid): bool
    {
        $result = false;
        if($uid > 0) {
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
            $qb = $connection->createQueryBuilder();
            $row = $qb->select('uid')
                ->from('sys_file')
                ->where($qb->expr()->eq('uid', $uid))
                ->execute()->fetch(FetchMode::ASSOCIATIVE);

            $result = !empty($row);
        }

        return $result;
    }
}
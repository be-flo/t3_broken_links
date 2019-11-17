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


use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class InvalidLinkService
{
    const CACHE_NAME = 'INVALID_LINK_CACHE';

    private const LIVING_CACHE_IDENT = 'CACHE_WARMED_UP';

    /**
     * @var FrontendInterface
     */
    private $cache;

    /**
     * InvalidLinkService constructor.
     *
     * @throws NoSuchCacheException
     */
    public function __construct()
    {
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache(self::CACHE_NAME);
    }

    /**
     * @param string $table
     * @param int    $pid
     * @param int    $uid
     * @param string $fieldName
     */
    public function storeInvalidLink(string $table, int $pid, int $uid, string $fieldName): void
    {
        $existing = [];
        $identifier = $this->getIdentifierForPage($pid);
        if ($this->cache->has($identifier)) {
            $existing = (array)$this->cache->get($identifier);
        }
        $existing[$table][$uid][] = $fieldName;
        $existing[$table][$uid] = array_unique($existing[$table][$uid]);
        $this->cache->set($identifier, $existing);
    }

    /**
     * @param int $pid
     *
     * @return string
     */
    private function getIdentifierForPage(int $pid): string
    {
        return 'page_' . $pid;
    }

    /**
     * @param int $pid
     *
     * @return array
     */
    public function getElementsForPid(int $pid): array
    {
        $result = [];
        $identifier = $this->getIdentifierForPage($pid);
        if ($this->cache->has($identifier)) {
            $result = (array)$this->cache->get($identifier);
        }

        return $result;
    }

    /**
     * @param int $pid
     *
     * @return bool
     */
    public function isCacheWarmedUp(int $pid): bool
    {
        return $this->cache->has(self::LIVING_CACHE_IDENT . $pid);
    }

    /**
     * @param int $pid
     */
    public function markCacheAsWarmedUp(int $pid): void
    {
        $this->cache->set(self::LIVING_CACHE_IDENT . $pid, true);
    }
}
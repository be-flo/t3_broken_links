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
class PageLinkHandler extends AbstractLinkHandler
{
    const NAME = 'page_link';

    /**
     * @param string $link
     *
     * @return bool
     */
    public function matchLink(string $link): bool
    {
        return strpos($link, 't3://page?') === 0;
    }

    /**
     * @param string $link
     *
     * @return bool
     */
    public function isValidLink(string $link): bool
    {
        $pageUid = (int)substr($link, strlen('t3://page?uid='));

        return $this->checkIfRecordExist('sys_file', $pageUid);
    }

}
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
interface LinkHandlerInterface
{

    /**
     * This method check if the given link could be handled by this link handler
     *
     * @param string $link
     *
     * @return bool
     */
    public function matchLink(string $link): bool;

    /**
     * This method validate the given link and return either true if the link is valid or false if not
     *
     * @param string $link
     *
     * @return bool
     */
    public function isValidLink(string $link): bool;
}
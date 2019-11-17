<?php


namespace BeFlo\T3BrokenLinks\Analyzer;

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


use BeFlo\T3BrokenLinks\LinkHandler\LinkHandlerInterface;
use BeFlo\T3BrokenLinks\Registry\LinkHandlerRegistry;

class RteAnalyzer implements AnalyzerInterface
{
    const NAME = 'rte_analyzer';

    /**
     * @param string $fieldContent
     *
     * @return bool
     */
    public function analyze(?string $fieldContent): bool
    {
        $result = true;
        preg_match_all("(t3:\/\/[\w?&=]*)", $fieldContent, $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $match) {
                if (!$this->isValidLink($match)) {
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $match
     *
     * @return bool
     */
    private function isValidLink(string $match): bool
    {
        $result = true;
        $availableLinkHandler = LinkHandlerRegistry::getInstance()->getAll();
        /** @var LinkHandlerInterface $linkHandler */
        foreach ($availableLinkHandler as $linkHandler) {
            if ($linkHandler->matchLink($match) && !$linkHandler->isValidLink($match)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param array $fieldConfiguration
     *
     * @return bool
     */
    public function istMatchingForFieldConfiguration(array $fieldConfiguration): bool
    {
        $result = false;
        if (
            !empty($fieldConfiguration['config']['type']) && $fieldConfiguration['config']['type'] === 'text'
            && !empty($fieldConfiguration['config']['enableRichtext']) && $fieldConfiguration['config']['enableRichtext'] == true
        ) {
            $result = true;
        }

        return $result;
    }

}
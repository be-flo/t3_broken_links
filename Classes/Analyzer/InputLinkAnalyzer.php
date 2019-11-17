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


class InputLinkAnalyzer extends RteAnalyzer
{
    const NAME = 'link_analyzer';

    /**
     * @param array $fieldConfiguration
     *
     * @return bool
     */
    public function istMatchingForFieldConfiguration(array $fieldConfiguration): bool
    {
        $result = false;
        if(
            !empty($fieldConfiguration['config']['type']) && $fieldConfiguration['config']['type'] == 'input'
            && !empty($fieldConfiguration['config']['renderType']) && $fieldConfiguration['config']['renderType'] == 'inputLink'
        ) {
            $result = true;
        }

        return $result;
    }

}
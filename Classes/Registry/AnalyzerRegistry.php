<?php


namespace BeFlo\T3BrokenLinks\Registry;

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
use BeFlo\T3BrokenLinks\Exceptions\MissingInterfaceException;

/**
 * Class AnalyzerRegistry
 *
 * @package BeFlo\T3BrokenLinks\Registry
 */
class AnalyzerRegistry extends AbstractRegistry
{

    /**
     * @var string
     */
    protected $registryIdentifier = 'analyzer';

    /**
     * @param string $className
     *
     * @throws MissingInterfaceException
     */
    protected function validateClassName(string $className): void
    {
        if (!in_array(AnalyzerInterface::class, class_implements($className))) {
            throw new MissingInterfaceException(
                sprintf('The analyzer "%s" must implement the interface "%s"',
                    $className, AnalyzerInterface::class)
            );
        }
    }
}
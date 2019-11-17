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


use BeFlo\T3BrokenLinks\Exceptions\MissingInterfaceException;
use BeFlo\T3BrokenLinks\Exceptions\ObjectNotFoundException;
use Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use SplObjectStorage;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractRegistry implements SingletonInterface, LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * @var string
     */
    protected $registryIdentifier = '';

    /**
     * AnalyzerRegistry constructor.
     */
    protected function __construct()
    {
        $this->setLogger(GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__));
    }

    /**
     * @return AnalyzerRegistry
     */
    public static function getInstance(): AbstractRegistry
    {
        $class = get_called_class();

        return new $class;
    }

    /**
     * Register an analyzer. For the usage in the "ext_localconf.php"
     *
     * @param string $identifier
     * @param string $analyzerClassName
     *
     * @return AnalyzerRegistry
     * @throws MissingInterfaceException
     */
    public function register(string $identifier, string $analyzerClassName): self
    {
        $this->validateClassName($analyzerClassName);
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['t3_broken_links'][$this->registryIdentifier][$identifier] = $analyzerClassName;

        return $this;
    }

    /**
     * @param string $className
     *
     * @throws MissingInterfaceException
     */
    abstract protected function validateClassName(string $className): void;

    /**
     * @param string $identifier
     *
     * @return AnalyzerRegistry
     */
    public function remove(string $identifier): self
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['t3_broken_links'][$this->registryIdentifier][$identifier]);

        return $this;
    }

    /**
     * @return SplObjectStorage
     */
    public function getAll(): SplObjectStorage
    {
        $result = new SplObjectStorage();
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['t3_broken_links'][$this->registryIdentifier])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['t3_broken_links'][$this->registryIdentifier])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['t3_broken_links'][$this->registryIdentifier] as $identifier => $analyzer) {
                try {
                    $this->validateClassName($analyzer);
                    $object = GeneralUtility::makeInstance($analyzer);
                    $result->attach($object);
                } catch (Exception $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }

        return $result;
    }

    /**
     * @param string $identifier
     *
     * @return object
     * @throws MissingInterfaceException
     * @throws ObjectNotFoundException
     */
    public function get(string $identifier)
    {
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['t3_broken_links'][$this->registryIdentifier][$identifier])) {
            $analyzerClass = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['t3_broken_links'][$this->registryIdentifier][$identifier];
            $this->validateClassName($analyzerClass);
            $result = GeneralUtility::makeInstance($analyzerClass);
        }
        if (empty($result)) {
            throw new ObjectNotFoundException(sprintf('No class found for the identifier "%s"!', $identifier));
        }

        return $result;
    }

    /**
     * Disable the clone method to prevent multiple instances of this object
     */
    protected function __clone()
    {
        // You shall not clone
    }

}
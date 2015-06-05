<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the class that binds the bootstrapper library to the application
 */
namespace RDev\Applications\Bootstrappers;
use RDev\Applications\Bootstrappers\Dispatchers\IDispatcher as IBootstrapperDispatcher;
use RDev\Applications\Bootstrappers\IO\IBootstrapperIO;
use RDev\Applications\Tasks\Dispatchers\IDispatcher as ITaskDispatcher;
use RDev\Applications\Tasks\TaskTypes;

class ApplicationBinder
{
    /** @var BootstrapperRegistry The registry of bootstrappers */
    private $bootstrapperRegistry = null;
    /** @var IBootstrapperDispatcher The bootstrapper dispatcher */
    private $bootstrapperDispatcher = null;
    /** @var ITaskDispatcher The task dispatcher */
    private $taskDispatcher = null;
    /** @var IBootstrapperIO The bootstrapper reader/writer */
    private $bootstrapperIO = null;
    /** @var array The list of global bootstrapper classes */
    private $globalBootstrapperClasses = [];

    /**
     * @param IBootstrapperRegistry $bootstrapperRegistry The registry of bootstrappers
     * @param IBootstrapperDispatcher $bootstrapperDispatcher The bootstrapper dispatcher
     * @param IBootstrapperIO $bootstrapperIO The bootstrapper reader/writer
     * @param ITaskDispatcher $taskDispatcher The task dispatcher
     * @param array $globalBootstrapperClasses The list of global bootstrapper classes
     */
    public function __construct(
        IBootstrapperRegistry $bootstrapperRegistry,
        IBootstrapperDispatcher $bootstrapperDispatcher,
        IBootstrapperIO $bootstrapperIO,
        ITaskDispatcher $taskDispatcher,
        array $globalBootstrapperClasses
    )
    {
        $this->bootstrapperRegistry = $bootstrapperRegistry;
        $this->bootstrapperDispatcher = $bootstrapperDispatcher;
        $this->bootstrapperIO = $bootstrapperIO;
        $this->taskDispatcher = $taskDispatcher;
        $this->globalBootstrapperClasses = $globalBootstrapperClasses;

        // Global bootstrappers should always be registered first
        $this->bootstrapperRegistry->registerBootstrapperClasses($this->globalBootstrapperClasses);
    }

    /**
     * Configures the bootstrappers with the application
     *
     * @param array $kernelBootstrapperClasses The list of kernel-specific bootstrapper classes
     * @param bool $forceEagerLoading Whether or not to force all bootstrappers to use eager loading
     * @param bool $useCache Whether or not to cache bootstrapper settings
     * @param string $cachedRegistryFilePath The location of the bootstrapper registry cache file
     */
    public function bindToApplication(array $kernelBootstrapperClasses, $forceEagerLoading, $useCache, $cachedRegistryFilePath = "")
    {
        $this->bootstrapperDispatcher->forceEagerLoading($forceEagerLoading);
        $this->bootstrapperRegistry->registerBootstrapperClasses($kernelBootstrapperClasses);

        // Register the task to dispatch the bootstrappers
        $this->taskDispatcher->registerTask(
            TaskTypes::PRE_START,
            function () use ($useCache, $cachedRegistryFilePath)
            {
                if($useCache && !empty($cachedRegistryFilePath))
                {
                    $this->bootstrapperIO->read($cachedRegistryFilePath, $this->bootstrapperRegistry);
                }
                else
                {
                    $this->bootstrapperRegistry->setBootstrapperDetails();
                }

                $this->bootstrapperDispatcher->dispatch($this->bootstrapperRegistry);
            }
        );
    }
}
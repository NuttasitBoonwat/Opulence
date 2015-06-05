<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks the HTTP application for use in testing
 */
namespace RDev\Tests\Framework\Tests\HTTP\Mocks;
use Monolog\Logger;
use RDev\Applications\Application;
use RDev\Applications\Bootstrappers\BootstrapperRegistry;
use RDev\Applications\Bootstrappers\Dispatchers\Dispatcher;
use RDev\Applications\Environments\Environment;
use RDev\Applications\Paths;
use RDev\Applications\Tasks\Dispatchers\Dispatcher as TaskDispatcher;
use RDev\Applications\Tasks\TaskTypes;
use RDev\Framework\Tests\HTTP\ApplicationTestCase as BaseApplicationTestCase;
use RDev\IoC\Container;
use RDev\IoC\IContainer;
use RDev\Tests\Applications\Mocks\MonologHandler;

class ApplicationTestCase extends BaseApplicationTestCase
{
    /** @var array The list of bootstrapper classes to include */
    private static $bootstrappers = [
        "RDev\\Framework\\Bootstrappers\\HTTP\\Requests\\Request",
        "RDev\\Framework\\Bootstrappers\\HTTP\\Routing\\Router",
        "RDev\\Framework\\Bootstrappers\\HTTP\\Views\\TemplateFunctions"
    ];

    /**
     * {@inheritdoc}
     */
    protected function getGlobalMiddleware()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getKernelLogger()
    {
        $logger = new Logger("application");
        $logger->pushHandler(new MonologHandler());

        return $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function setApplication()
    {
        // Create and bind all of the components of our application
        $paths = new Paths([
            "configs" => __DIR__ . "/../../configs"
        ]);
        $taskDispatcher = new TaskDispatcher();
        $environment = new Environment(Environment::TESTING);
        $container = new Container();
        $container->bind(Paths::class, $paths);
        $container->bind(TaskDispatcher::class, $taskDispatcher);
        $container->bind(Environment::class, $environment);
        $container->bind(IContainer::class, $container);
        $this->application = new Application($paths, $taskDispatcher, $environment, $container);

        // Setup the bootstrappers
        $bootstrapperRegistry = new BootstrapperRegistry($paths, $environment);
        $bootstrapperDispatcher = new Dispatcher($taskDispatcher, $container);
        $bootstrapperRegistry->registerEagerBootstrapper(self::$bootstrappers);
        $taskDispatcher->registerTask(TaskTypes::PRE_START, function () use ($bootstrapperDispatcher, $bootstrapperRegistry)
        {
            $bootstrapperDispatcher->dispatch($bootstrapperRegistry);
        });
    }
}
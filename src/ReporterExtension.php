<?php

namespace Behat\Reporter;

use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class ReporterExtension
 * @package Behat\Reporter
 */
class ReporterExtension implements Extension
{
    const HTML_FORMATTER_ID = 'reporter.formatter.html';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'reporter';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $formatter = new Definition('Behat\Reporter\Formatter\HtmlFormatter', array(
            new Definition('Behat\Reporter\Handler\EventHandler'),
            new Definition('Behat\Behat\Output\Printer\ConsoleOutputPrinter')
        ));

        $container->setDefinition(self::HTML_FORMATTER_ID, $formatter)
            ->addTag(OutputExtension::FORMATTER_TAG);
    }
}

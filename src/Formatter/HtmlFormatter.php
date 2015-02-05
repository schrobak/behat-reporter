<?php

namespace Behat\Reporter\Formatter;

use Behat\Reporter\Handler\EventHandler;
use Behat\Testwork\EventDispatcher\TestworkEventDispatcher;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class HtmlFormatter
 * @package Behat\Reporter\Formatter
 */
class HtmlFormatter implements Formatter
{
    /**
     * @var EventHandler
     */
    private $eventHandler;
    /**
     * @var OutputPrinter
     */
    private $outputPrinter;

    /**
     * @param EventHandler $eventHandler
     * @param OutputPrinter $outputPrinter
     */
    public function __construct(EventHandler $eventHandler, OutputPrinter $outputPrinter)
    {
        $this->eventHandler = $eventHandler;
        $this->outputPrinter = $outputPrinter;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TestworkEventDispatcher::AFTER_ALL_EVENTS => 'handleEvent'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'html';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Advanced HTML Formatter';
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputPrinter()
    {
        return $this->outputPrinter;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter($name, $value)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($name)
    {
    }

    /**
     * @param Event $event
     */
    public function handleEvent(Event $event)
    {
        $this->eventHandler->handle($event);
    }
}

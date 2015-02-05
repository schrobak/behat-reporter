<?php

namespace spec\Behat\Reporter\Formatter;

use Behat\Reporter\Handler\EventHandler;
use Behat\Testwork\EventDispatcher\TestworkEventDispatcher;
use Behat\Testwork\Output\Printer\OutputPrinter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class HtmlFormatterSpec
 * @package spec\Behat\Reporter\Formatter
 */
class HtmlFormatterSpec extends ObjectBehavior
{
    /**
     * @var EventHandler
     */
    private $eventHandler;
    /**
     * @var OutputPrinter
     */
    private $outputPrinter;

    public function let(EventHandler $eventHandler, OutputPrinter $outputPrinter)
    {
        $this->eventHandler = $eventHandler;
        $this->outputPrinter = $outputPrinter;

        $this->beConstructedWith($eventHandler, $outputPrinter);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Behat\Reporter\Formatter\HtmlFormatter');
    }

    public function it_implements_Formatter_interface()
    {
        $this->shouldImplement('Behat\Testwork\Output\Formatter');
    }

    public function it_is_named_html()
    {
        $this->getName()->shouldBe('html');
    }

    public function it_has_cool_description()
    {
        $this->getDescription()->shouldBe('Advanced HTML Formatter');
    }

    public function it_has_output_printer()
    {
        $this->getOutputPrinter()->shouldReturn($this->outputPrinter);
    }

    public function it_subscribes_after_all_events()
    {
        $this->getSubscribedEvents()->shouldBe(array(
            TestworkEventDispatcher::AFTER_ALL_EVENTS => 'handleEvent'
        ));
    }

    public function it_handles_event_using_event_handler(Event $event)
    {
        $this->eventHandler->handle($event)->shouldBeCalled();

        $this->handleEvent($event);
    }
}

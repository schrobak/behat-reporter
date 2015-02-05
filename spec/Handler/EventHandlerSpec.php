<?php

namespace spec\Behat\Reporter\Handler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EventHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Behat\Reporter\Handler\EventHandler');
    }
}

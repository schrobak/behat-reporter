<?php

namespace spec\Behat\Reporter;

use Behat\Reporter\ReporterExtension;
use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ReporterExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Behat\Reporter\ReporterExtension');
    }

    public function it_implements_Extension_interface()
    {
        $this->shouldImplement('Behat\Testwork\ServiceContainer\Extension');
    }

    public function it_has_reporter_config_key()
    {
        $this->getConfigKey()->shouldReturn('reporter');
    }

    public function it_loads_proper_definitions(
        ContainerBuilder $containerBuilder,
        Definition $definition
    ) {
        $definition->addTag(OutputExtension::FORMATTER_TAG)->shouldBeCalled();
        $containerBuilder->setDefinition(
            ReporterExtension::HTML_FORMATTER_ID,
            Argument::which('getClass', 'Behat\Reporter\Formatter\HtmlFormatter')
        )->willReturn($definition)->shouldBeCalled();

        $this->load($containerBuilder, array());
    }
}

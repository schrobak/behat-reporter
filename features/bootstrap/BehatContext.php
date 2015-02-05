<?php

namespace Behat\Reporter\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use \PHPUnit_Framework_Assert as Assert;

class BehatContext implements SnippetAcceptingContext
{
    /**
     * @var Filesystem
     */
    private static $fileSystem;
    /**
     * @var string
     */
    private static $testDir;
    /**
     * @var string
     */
    private $workingDir;
    /**
     * @var string
     */
    private $phpBin;
    /**
     * @var Process
     */
    private $process;

    /**
     * @BeforeSuite
     */
    public static function prepareTestDirecotory()
    {
        self::$fileSystem = new Filesystem();
        self::$testDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'behat-reporter';

        if (self::$fileSystem->exists(self::$testDir)) {
            self::$fileSystem->remove(self::$testDir);
        } else {
            self::$fileSystem->mkdir(self::$testDir);
        }
    }

    /**
     * @BeforeScenario
     */
    public function prepareTestEnvironment()
    {
        $phpFinder = new PhpExecutableFinder();
        if (false === $php = $phpFinder->find()) {
            throw new \RuntimeException('Unable to find the PHP executable.');
        }

        $this->workingDir = self::$testDir . DIRECTORY_SEPARATOR . md5(microtime() * rand(0, 10000));
        self::$fileSystem->mkdir($this->workingDir . '/features/bootstrap');

        $this->phpBin = $php;
        $this->process = new Process(null);
    }

    /**
     * @Given there is config file with:
     * @param PyStringNode $string
     */
    public function thereIsConfigFileWith(PyStringNode $string)
    {
        $config = $this->workingDir . '/behat.yml';
        self::$fileSystem->dumpFile($config, $string->getRaw());
    }

    /**
     * @Given there is feature context
     */
    public function thereIsContext()
    {
        $file = $this->workingDir . '/features/bootstrap/FeatureContext.php';
        self::$fileSystem->dumpFile($file, $this->getFeatureContext());
        Assert::assertTrue(self::$fileSystem->exists($file));
    }

    /**
     * @Given there is feature :feature with:
     * @param string $feature
     * @param PyStringNode $string
     */
    public function thereIsFeatureWith($feature, PyStringNode $string)
    {
        $file = $this->workingDir . '/features/' . $feature . '.feature';
        self::$fileSystem->dumpFile($file, $string->getRaw());
        Assert::assertTrue(self::$fileSystem->exists($file));
    }

    /**
     * @When I run behat with :args
     * @param string $args
     */
    public function iRunBehatWith($args)
    {
        $argumentsString = strtr($args, array('\'' => '"'));

        $this->process->setWorkingDirectory($this->workingDir);
        $this->process->setCommandLine(
            sprintf(
                '%s %s %s %s',
                $this->phpBin,
                escapeshellarg(BEHAT_BIN_PATH),
                $argumentsString,
                strtr('--format-settings=\'{"timer": false}\'', array('\'' => '"', '"' => '\"'))
            )
        );
        $this->process->start();
        $this->process->wait();
    }

    /**
     * @Then the :file file should exists
     * @param string $file
     */
    public function theReportHtmlFileShouldExists($file)
    {
        $file = $this->workingDir . DIRECTORY_SEPARATOR . $file;
        Assert::assertTrue(self::$fileSystem->exists($file));
    }

    /**
     * @Then it should pass
     */
    public function itShouldPass()
    {
        if (0 !== $this->process->getExitCode()) {
            echo $this->process->getCommandLine();
            echo 'Actual output:' . PHP_EOL . PHP_EOL . $this->getOutput();
        }

        Assert::assertSame(0, $this->process->getExitCode());
    }

    private function getOutput()
    {
        $output = $this->process->getErrorOutput() . $this->process->getOutput();

        // Normalize the line endings in the output
        if ("\n" !== PHP_EOL) {
            $output = str_replace(PHP_EOL, "\n", $output);
        }

        // Replace wrong warning message of HHVM
        $output = str_replace('Notice: Undefined index: ', 'Notice: Undefined offset: ', $output);

        return trim(preg_replace("/ +$/m", '', $output));
    }

    private function getFeatureContext()
    {
        return <<<'TAG'
<?php

class FeatureContext implements \Behat\Behat\Context\Context
{
    private $apples = 0;

    /**
     * @Given I have :count apple(s)
     */
    public function iHaveApples($count) {
        $this->apples = (int) $count;
    }

    /**
     * @When I ate :count apple(s)
     */
    public function iAteApples($count) {
        $this->apples -= $count;
    }

    /**
     * @When I found :count apple(s)
     */
    public function iFoundApples($count) {
        $this->apples += $count;
    }

    /**
     * @Then I should have :count apple(s)
     */
    public function iShouldHaveApples($count) {
        \PHPUnit_Framework_Assert::assertEquals($count, $this->apples);
    }
}

TAG;
    }
}

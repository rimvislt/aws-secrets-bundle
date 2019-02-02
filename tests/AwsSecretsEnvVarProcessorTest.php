<?php declare(strict_types=1);
/**
 * This file belongs to Bandit. All rights reserved
 */

namespace Tests\AwsSecretsBundle;

use Aws\Result;
use Aws\SecretsManager\SecretsManagerClient;
use AwsSecretsBundle\AwsSecretsEnvVarProcessor;
use AwsSecretsBundle\Provider\AwsSecretsEnvVarProviderInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class AwsSecretsEnvVarProcessorTest
 * @package Tests\AwsSecretsBundle
 * @author  Joe Mizzi <themizzi@me.com>
 */
class AwsSecretsEnvVarProcessorTest extends TestCase
{
    /** @var AwsSecretsEnvVarProcessor */
    private $processor;

    /** @var AwsSecretsEnvVarProviderInterface */
    private $provider;

    protected function setUp()
    {
        $this->provider = $this->prophesize(AwsSecretsEnvVarProviderInterface::class);

        $this->processor = new AwsSecretsEnvVarProcessor(
            $this->provider->reveal(),
            false,
            ','
        );
    }

    /**
     * @test
     */
    public function it_calls_closure_if_no_processor(): void
    {
        $this->processor->setIgnore(true);

        $callCount = 0;
        $this->processor->getEnv(
            'aws',
            'AWS_SECRET',
            function ($name) use (&$callCount) {
                $callCount++;
                return 'value';
            }
        );
        $this->assertEquals(2, $callCount);
    }

    /**
     * @test
     */
    public function it_calls_closure_if_null(): void
    {
        $callCount = 0;
        $this->processor->getEnv(
            'aws',
            'AWS_SECRET',
            function ($name) use (&$callCount) {
                $callCount++;
                return null;
            }
        );
        $this->assertEquals(2, $callCount);
    }

    /**
     * @test
     */
    public function it_returns_string_for_key(): void
    {
        $this->provider->get('prefix/db')->willReturn('{"key":"value"}');

        $callCount = 0;
        $value = $this->processor->getEnv(
            'aws',
            'AWS_SECRET',
            function (string $name) use (&$callCount) {
                $callCount++;
                if ($callCount === 1) {
                    return 'prefix/db,key';
                }

                return null;
            }
        );
        $this->assertEquals('value', $value);
    }

    /**
     * @test
     */
    public function it_returns_string(): void
    {
        $callCount = 0;
        $this->provider->get('prefix/db')->willReturn('value');

        $value = $this->processor->getEnv(
            'aws',
            'AWS_SECRET',
            function (string $name) use (&$callCount) {
                $callCount++;
                if ($callCount === 1) {
                    return 'prefix/db';
                }

                return null;
            }
        );

        $this->assertEquals(1, $callCount);
        $this->assertEquals('value', $value);
    }
}

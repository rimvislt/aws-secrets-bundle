<?php declare(strict_types=1);
/**
 * This file belongs to Bandit. All rights reserved
 */

namespace Tests\AwsSecretsBundle;

use Aws\Result;
use Aws\SecretsManager\SecretsManagerClient;
use AwsSecretsBundle\AwsSecretsEnvVarProcessor;
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

    /** @var SecretsManagerClient */
    private $secretsManagerClient;

    protected function setUp()
    {
        $this->secretsManagerClient = $this->prophesize(SecretsManagerClient::class);
        $this->processor = new AwsSecretsEnvVarProcessor(
            $this->secretsManagerClient->reveal(),
            ','
        );
    }

    /**
     * @test
     */
    public function it_calls_closure_if_ignored(): void
    {
        $this->processor = new AwsSecretsEnvVarProcessor(
            $this->secretsManagerClient->reveal(),
            ',',
            true
        );
        $this->processor->getEnv(
            'aws',
            'AWS_SECRET',
            function (string $name) {
                $this->assertEquals('AWS_SECRET', $name);
            }
        );
    }

    /**
     * @test
     */
    public function it_returns_string_for_key(): void
    {
        $this->secretsManagerClient->getSecretValue(
            [
                AwsSecretsEnvVarProcessor::AWS_SECRET_ID => 'prefix/db',
            ]
        )->willReturn(
            new Result(
                [
                    AwsSecretsEnvVarProcessor::AWS_SECRET_STRING => json_encode(
                        [
                            'key' => 'value',
                        ]
                    ),
                ]
            )
        );

        $value = $this->processor->getEnv(
            'aws',
            'AWS_SECRET',
            function (string $name) {
                return 'prefix/db,key';
            }
        );
        $this->assertEquals('value', $value);
    }

    /**
     * @test
     */
    public function it_returns_string(): void
    {
        $json = json_encode(
            [
                'key' => 'value',
            ]
        );

        $this->secretsManagerClient->getSecretValue(
            [
                AwsSecretsEnvVarProcessor::AWS_SECRET_ID => 'prefix/db',
            ]
        )->willReturn(
            new Result(
                [
                    AwsSecretsEnvVarProcessor::AWS_SECRET_STRING => $json,
                ]
            )
        );

        $value = $this->processor->getEnv(
            'aws',
            'AWS_SECRET',
            function (string $name) {
                return 'prefix/db';
            }
        );

        $this->assertEquals($json, $value);
    }
}
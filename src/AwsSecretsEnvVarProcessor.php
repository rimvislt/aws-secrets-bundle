<?php declare(strict_types=1);
/**
 * This file belongs to Bandit. All rights reserved
 */

namespace AwsSecretsBundle;

use Aws\SecretsManager\SecretsManagerClient;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Class AwsSecretsEnvVarProcessor
 * @package AwsSecretsBundle
 * @author  Joe Mizzi <themizzi@me.com>
 */
class AwsSecretsEnvVarProcessor implements EnvVarProcessorInterface
{
    public const AWS_SECRET_ID = 'SecretId';
    public const AWS_SECRET_STRING = 'SecretString';

    private $secretsManagerClient;
    private $delimiter;
    private $ignore;
    private $secrets = [];

    public function __construct(
        SecretsManagerClient $secretsManagerClient,
        string $delimiter = ',',
        bool $ignore = false
    ) {
        $this->secretsManagerClient = $secretsManagerClient;
        $this->delimiter = $delimiter;
        $this->ignore = $ignore;
    }

    /**
     * Returns the value of the given variable as managed by the current instance.
     *
     * @param string $prefix The namespace of the variable
     * @param string $name The name of the variable within the namespace
     * @param \Closure $getEnv A closure that allows fetching more env vars
     *
     * @return mixed
     *
     * @throws RuntimeException on error
     */
    public function getEnv($prefix, $name, \Closure $getEnv)
    {
        if (!$this->ignore) {
            $parts = explode($this->delimiter, $getEnv($name));

            if (\count($parts) !== 2) {
                throw new RuntimeException('AWS Env Var should have two parts');
            }

            if (!isset($this->secrets[$parts[0]])) {
                $this->secrets[$parts[0]] = json_decode(
                    $this->secretsManagerClient
                        ->getSecretValue([self::AWS_SECRET_ID => $parts[0]])
                        ->get(self::AWS_SECRET_STRING),
                    true
                );
            }

            return (string)$this->secrets[$parts[0]][$parts[1]];
        }


        return $getEnv($name);
    }

    /**
     * @return string[] The PHP-types managed by getEnv(), keyed by prefixes
     * @codeCoverageIgnore
     */
    public static function getProvidedTypes(): array
    {
        return [
            'aws' => 'bool|int|float|string',
        ];
    }
}
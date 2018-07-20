<?php declare(strict_types=1);

namespace AwsSecretsBundle;

use Aws\SecretsManager\SecretsManagerClient;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Class AwsSecretsEnvVarProcessor
 * @package AwsSecretsBundle
 * @author  Joe Mizzi <joe@casechek.com>
 */
class AwsSecretsEnvVarProcessor implements EnvVarProcessorInterface
{
    public const AWS_SECRET_ID = 'SecretId';
    public const AWS_SECRET_STRING = 'SecretString';

    private $secretsManagerClient;
    private $delimiter;
    private $secrets = [];
    private $decodedSecrets = [];

    public function __construct(
        ?SecretsManagerClient $secretsManagerClient,
        string $delimiter = ','
    ) {
        $this->secretsManagerClient = $secretsManagerClient;
        $this->delimiter = $delimiter;
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
        $value = $getEnv($name);

        if ($this->secretsManagerClient !== null
            && $value !== null
        ) {
            $parts = explode($this->delimiter, $value);
            if (!isset($this->secrets[$parts[0]])) {
                $this->secrets[$parts[0]] =
                    $this->secretsManagerClient
                        ->getSecretValue([self::AWS_SECRET_ID => $parts[0]])
                        ->get(self::AWS_SECRET_STRING);
            }

            if (isset($parts[1])) {
                if (!isset($this->decodedSecrets[$parts[0]])) {
                    $this->decodedSecrets[$parts[0]] = json_decode($this->secrets[$parts[0]], true);
                }
                return (string)$this->decodedSecrets[$parts[0]][$parts[1]];
            }

            return $this->secrets[$parts[0]];
        }

        return $getEnv($value);
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

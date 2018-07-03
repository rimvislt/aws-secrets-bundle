<?php declare(strict_types=1);

namespace AwsSecretsBundle\DependencyInjection;

use Aws\SecretsManager\SecretsManagerClient;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Class SecretsManagerFactory
 * @package AwsSecretsBundle\DependencyInjection
 * @author  Joe Mizzi <joe@casechek.com>
 */
class SecretsManagerClientFactory
{
    private $awsRegion;
    private $awsVersion;
    private $awsKey;
    private $awsSecret;
    private $ignore;

    public function __construct(?string $awsRegion, ?string $awsVersion, ?string $awsKey, ?string $awsSecret, bool $ignore = false)
    {
        $this->awsRegion = $awsRegion;
        $this->awsVersion = $awsVersion;
        $this->awsKey = $awsKey;
        $this->awsSecret = $awsSecret;
        $this->ignore = $ignore;
    }

    public function createSecretsManagerClient(): ?SecretsManagerClient
    {
        if ($this->ignore) {
            return null;
        }

        if ($this->awsRegion === null || $this->awsVersion === null || $this->awsKey === null || $this->awsSecret === null) {
            throw new RuntimeException('AWS Credentials required if aws env vars not ignored');
        }

        return new SecretsManagerClient([
            'region' => $this->awsRegion,
            'version' => $this->awsVersion,
            'credentials' => [
                'key' => $this->awsKey,
                'secret' => $this->awsSecret,
            ],
        ]);
    }
}
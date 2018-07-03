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
    public static function createSecretsManagerClient(
        ?string $awsRegion,
        ?string $awsVersion,
        ?string $awsKey,
        ?string $awsSecret,
        bool $ignore = false
    ): ?SecretsManagerClient {
        if ($ignore) {
            return null;
        }

        if ($awsRegion === null || $awsVersion === null || $awsKey === null || $awsSecret === null) {
            throw new RuntimeException('AWS Credentials required if aws env vars not ignored');
        }

        return new SecretsManagerClient(
            [
                'region' => $awsRegion,
                'version' => $awsVersion,
                'credentials' => [
                    'key' => $awsKey,
                    'secret' => $awsSecret,
                ],
            ]
        );
    }
}
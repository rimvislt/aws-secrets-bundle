<?php declare(strict_types=1);

namespace AwsSecretsBundle\DependencyInjection;

use Aws\SecretsManager\SecretsManagerClient;

/**
 * Class SecretsManagerFactory
 * @package AwsSecretsBundle\DependencyInjection
 * @author  Joe Mizzi <joe@casechek.com>
 *
 * @codeCoverageIgnore
 */
class SecretsManagerClientFactory
{
    public static function createSecretsManagerClient(
        ?array $clientConfig,
        bool $ignore = false
    ): ?SecretsManagerClient {
        if ($ignore) {
            return null;
        }

        return new SecretsManagerClient($clientConfig);
    }
}

<?php declare(strict_types=1);

namespace AwsSecretsBundle\DependencyInjection;

use Aws\Credentials\CredentialProvider;
use Aws\SecretsManager\SecretsManagerClient;

/**
 * Class SecretsManagerClientFactory
 * @package AwsSecretsBundle\DependencyInjection
 * @author  Joe Mizzi <joe@casechek.com>
 *
 * @codeCoverageIgnore
 */
class SecretsManagerClientFactory
{
    /**
     * @param array $config
     * @return SecretsManagerClient
     */
    public function createClient(array $config): SecretsManagerClient
    {
        if (
            $config['credentials']['key'] === null ||
            $config['credentials']['secret'] === null
        ) {
            unset(
                $config['credentials']['key'],
                $config['credentials']['secret']
            );
        }

        if ($config['ecs_enabled']) {
            $provider = CredentialProvider::ecsCredentials();
            $memoizedProvider = CredentialProvider::memoize($provider);
            $config['credentials'] = $memoizedProvider;
        }

        return new SecretsManagerClient($config);
    }
}

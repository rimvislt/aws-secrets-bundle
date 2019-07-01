<?php declare(strict_types=1);

namespace AwsSecretsBundle\DependencyInjection;

use Aws\Credentials\CredentialProvider;
use Aws\SecretsManager\SecretsManagerClient;

/**
 * Class SecretsManagerClientFactory
 * @package AwsSecretsBundle\DependencyInjection
 * @author  James Matsumura <james@casechek.com>
 *
 * @codeCoverageIgnore
 */
class SecretsManagerClientFactory
{
    /**
     * @param array $credentialsConfig
     * @param bool $ecsEnabled
     * @return SecretsManagerClient
     */
    public function createClient(array $credentialsConfig, bool $ecsEnabled): SecretsManagerClient
    {
        if (!$ecsEnabled) {
            unset(
                $credentialsConfig['credentials']['key'],
                $credentialsConfig['credentials']['secret']
            );
        } else {
            $provider = CredentialProvider::ecsCredentials();
            $memoizedProvider = CredentialProvider::memoize($provider);
            $credentialsConfig['credentials'] = $memoizedProvider;
        }

        return new SecretsManagerClient($credentialsConfig);
    }
}

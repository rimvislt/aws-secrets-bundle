<?php declare(strict_types=1);

namespace AwsSecretsBundle\DependencyInjection;

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
     * @param string $region
     * @param null|string $key
     * @param null|string $secret
     * @param null|string $version
     * @return SecretsManagerClient
     */
    public function createClient(
        string $region,
        string $version,
        ?string $key,
        ?string $secret
    ): SecretsManagerClient
    {
        $config = [
            'region' => $region,
            'version' => $version
        ];

        if ($key && $secret) {
            $config['credentials'] = [
                'key' => $key,
                'secret' => $secret
            ];
        }

        return new SecretsManagerClient($config);
    }
}

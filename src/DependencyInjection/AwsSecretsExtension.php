<?php declare(strict_types=1);

namespace AwsSecretsBundle\DependencyInjection;

use Aws\SecretsManager\SecretsManagerClient;
use AwsSecretsBundle\AwsSecretsEnvVarProcessor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AwsSecretsExtension
 * @package AwsSecretsBundle\DependencyInjection
 * @author  Joe Mizzi <joe@casechek.com>
 *
 * @codeCoverageIgnore
 */
class AwsSecretsExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $configs = $this->processConfiguration($configuration, $configs);

        $container->setParameter('aws_secrets.aws_region', $configs['aws_region']);
        $container->setParameter('aws_secrets.aws_version', $configs['aws_version']);
        $container->setParameter('aws_secrets.ignore', $configs['ignore']);
        $container->setParameter('aws_secrets.aws_key', $configs['aws_key']);
        $container->setParameter('aws_secrets.aws_secret', $configs['aws_secret']);
        $container->setParameter('aws_secrets.delimiter', $configs['delimiter']);

        $container->register('aws_secrets.secrets_manager_client', SecretsManagerClient::class)
            ->setFactory([SecretsManagerClientFactory::class, 'createSecretsManagerClient'])
            ->addArgument('%aws_secrets.aws_region%')
            ->addArgument('%aws_secrets.aws_version%')
            ->addArgument('%aws_secrets.aws_key%')
            ->addArgument('%aws_secrets.aws_secret%')
            ->addArgument('%aws_secrets.ignore%')
            ->setPublic(false);

        $container->setAlias('aws_secrets.client', 'aws_secrets.secrets_manager_client')
            ->setPublic(true);

        $container->register('aws_secrets.env_var_processor', AwsSecretsEnvVarProcessor::class)
            ->setArgument('$secretsManagerClient', new Reference('aws_secrets.client'))
            ->setArgument('$delimiter', '%aws_secrets.delimiter%')
            ->addTag('container.env_var_processor');
    }
}

<?php declare(strict_types=1);
/**
 * This file belongs to Casechek. All rights reserved
 */

namespace AwsSecretsBundle\Command;

use Aws\SecretsManager\SecretsManagerClient;
use AwsSecretsBundle\AwsSecretsEnvVarProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author  Joe Mizzi <joe@casechek.com>
 */
class AwsSecretValueCommand extends Command
{
    private $secretsManagerClient;

    public function __construct(SecretsManagerClient $secretsManagerClient = null)
    {
        parent::__construct();
        $this->secretsManagerClient = $secretsManagerClient;
    }

    protected function configure()
    {
        $this->setName('aws:secret-value');
        $this->setDescription('Get a value from the AWS Secrets Manager');
        $this->addArgument('secret', InputArgument::REQUIRED, 'Secret value to retreive');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $value = $this->secretsManagerClient
            ->getSecretValue([AwsSecretsEnvVarProcessor::AWS_SECRET_ID => $input->getArgument('secret')])
            ->get(AwsSecretsEnvVarProcessor::AWS_SECRET_STRING);

        $output->writeln($value);
    }
}

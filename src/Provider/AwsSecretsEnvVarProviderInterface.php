<?php
/**
 * This file belongs to Casechek. All rights reserved
 */

namespace AwsSecretsBundle\Provider;

interface AwsSecretsEnvVarProviderInterface
{
    public function get($name);
}

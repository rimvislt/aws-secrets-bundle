# AWS Secrets Bundle

Easy loading of AWS Secrets Manager Secrets for Symfony

[![Build Status](https://travis-ci.org/incompass/aws-secrets-bundle.svg?branch=master)](https://travis-ci.org/incompass/aws-secrets-bundle)

## Installation

    $ composer require incompass/aws-secrets-bundle

## Configuration

```yaml
aws_secrets:
  aws_region: ~
  aws_version: ~
  aws_key: ~
  aws_secret: ~
  delimiter: ','    # delimiter to separate key from secret name
  ignore: false     # pass through aws (for local dev environments set to true)
```

## Usage

Set an env var to an AWS Secret Manager Secret name like so:

    AWS_SECRET=secret_name

Set a parameter to this environment variable with the aws processor:

```yaml
parameters:
    my_parameter: '%env(aws:AWS_SECRET)%'
```

Your secret will now be loaded at runtime!
<?php
declare(strict_types=1);

namespace LoyaltyCorp\EasyApiToken\Factories;

use LoyaltyCorp\EasyApiToken\Decoders\ApiKeyAsBasicAuthUsernameDecoder;
use LoyaltyCorp\EasyApiToken\Decoders\BasicAuthDecoder;
use LoyaltyCorp\EasyApiToken\Decoders\JwtTokenDecoder;
use LoyaltyCorp\EasyApiToken\Decoders\JwtTokenInQueryDecoder;
use LoyaltyCorp\EasyApiToken\Exceptions\InvalidConfigurationException;
use LoyaltyCorp\EasyApiToken\External\Auth0JwtDriver;
use LoyaltyCorp\EasyApiToken\External\FirebaseJwtDriver;
use LoyaltyCorp\EasyApiToken\External\Interfaces\JwtDriverInterface;
use LoyaltyCorp\EasyApiToken\Interfaces\EasyApiTokenDecoderInterface;
use LoyaltyCorp\EasyApiToken\Tokens\Factories\JwtEasyApiTokenFactory;

class EasyApiDecoderFactory
{
    /**
     * @var array
     */
    private $config;

    /**
     * EasyApiDecoderFactory constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Build a named TokenFactory.
     *
     * @param string $configKey Key of configuration found in the configuration.
     *
     * @return \LoyaltyCorp\EasyApiToken\Interfaces\EasyApiTokenDecoderInterface
     *
     * @throws \LoyaltyCorp\EasyApiToken\Exceptions\InvalidConfigurationException
     */
    public function build(string $configKey): EasyApiTokenDecoderInterface
    {
        if (\count($this->config) === 0) {
            throw new InvalidConfigurationException('Could not find a valid configuration.');
        }
        if (\array_key_exists($configKey, $this->config) === false) {
            throw new InvalidConfigurationException(
                \sprintf('Could not find EasyApiToken for key: %s.', $configKey)
            );
        }

        $decoderType = $this->config[$configKey]['type'] ?? '';

        switch ($decoderType) {
            case 'basic':
                return new BasicAuthDecoder();
            case 'user-apikey':
                return new ApiKeyAsBasicAuthUsernameDecoder();
            case 'jwt-header':
                return $this->createJwtHeaderDecoder($this->config[$configKey]);
            case 'jwt-param':
                return $this->createJwtParamDecoder($this->config[$configKey]);
        }
        throw new InvalidConfigurationException(
            \sprintf('Invalid EasyApiToken decoder type: %s configured for key: %s.', $decoderType, $configKey)
        );
    }

    private function createJwtHeaderDecoder(array $configuration) {
        $driverName = $configuration['driver'];
        $options = $configuration['options'];

        $driver = $this->createJwtDriver($driverName, $options);
        return new JwtTokenDecoder(new JwtEasyApiTokenFactory($driver));
    }

    private function createJwtParamDecoder(array $configuration) {
        $driverName = $configuration['driver'];
        $options = $configuration['options'];

        $driver = $this->createJwtDriver($driverName, $options);
        return new JwtTokenInQueryDecoder(new JwtEasyApiTokenFactory($driver), $options['param']);
    }

    private function createJwtDriver($driver, $options): JwtDriverInterface
    {
        switch ($driver) {
            case 'auth0':
                return $this->createAuth0Driver($options);
            case 'firebase':
                return $this->createFirebaseDriver($options);
        }
        //todo add exception handler here
    }

    /**
     * @param $options
     * @return \LoyaltyCorp\EasyApiToken\External\Auth0JwtDriver
     */
    private function createAuth0Driver($options): \LoyaltyCorp\EasyApiToken\External\Auth0JwtDriver
    {
        $driver = new Auth0JwtDriver(
            $options['valid_audiences'],
            $options['authorized_iss'],
            $options['private_key'],
            $options['audience_for_encode'] ?? null,
            $options['allowed_algos'] ?? null
        );
        return $driver;
    }

    /**
     * @param $options
     * @return \LoyaltyCorp\EasyApiToken\External\FirebaseJwtDriver
     */
    private function createFirebaseDriver($options): \LoyaltyCorp\EasyApiToken\External\FirebaseJwtDriver
    {
        $driver = new FirebaseJwtDriver(
            $options['algo'],
            $options['public_key'],
            $options['private_key'],
            $options['allowed_algos'] ?? null,
            $options['leeway'] ?? null
        );
        return $driver;
    }
}

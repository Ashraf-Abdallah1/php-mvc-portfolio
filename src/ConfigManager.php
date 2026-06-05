<?php

namespace Framework;

use Exception;

class ConfigManager
{
    /** @var string[] */
    private array $defaults = array(
        'APP_ENV' => 'production'
    );

    /** @var string[] */
    public array $config = array();

    /**
     * @param string[] $config
     */
    public function __construct(array $config = array())
    {
        $this->config = array_merge($this->defaults, $config);
    }

    /**
     * @param string $key
     * @return string
     * @throws Exception
     */
    public function get(string $key): string
    {
        $env = getenv($key);
        if ($env !== false) {
            return $env;
        }

        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        throw new Exception("Config item: $key not set");
    }

    /**
     * @throws Exception
     */
    public function isProduction(): bool
    {
        return (strtoupper($this->get('APP_ENV')) === 'PRODUCTION');
    }
}

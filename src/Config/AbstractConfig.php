<?php declare(strict_types=1);

namespace App\Config;

use App\Config\Exception\ConfigFileNotFoundException;
use App\Config\Exception\ConfigKeyNotFoundException;

abstract class AbstractConfig
{
    /**
     * @var string[]
     */
    private $configArray;

    /**
     * @param string $key
     * @return string
     * @throws ConfigKeyNotFoundException
     * @throws ConfigFileNotFoundException
     */
    protected function getKey(string $key): string
    {
        $configArray = $this->getConfigArray();

        if (!array_key_exists($key, $configArray)) {
            throw ConfigKeyNotFoundException::create($key, $this->getConfigFilePath());
        }

        return $configArray[$key];
    }

    /**
     * @return string[]
     * @throws ConfigFileNotFoundException
     */
    private function getConfigArray(): array
    {
        if (null === $this->configArray) {
            $this->configArray = $this->readInConfigFile($this->getConfigFilePath());
        }

        return $this->configArray;
    }

    /**
     * @return string
     */
    abstract protected function getConfigFilePath(): string;

    /**
     * @param string $configFile
     * @return string[]
     * @throws ConfigFileNotFoundException
     */
    protected function readInConfigFile(string $configFile): array
    {
        if (!is_file($configFile) || !is_readable($configFile)) {
            throw ConfigFileNotFoundException::create($configFile);
        }

        return include $configFile;
    }
}

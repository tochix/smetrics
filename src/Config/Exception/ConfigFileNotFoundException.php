<?php declare(strict_types=1);

namespace App\Config\Exception;

final class ConfigFileNotFoundException extends AbstractConfigException
{
    private const MESSAGE = 'Config file "%s" does not exist or is not readable.';

    /**
     * @param string $configFile
     * @return ConfigFileNotFoundException
     */
    public static function create(string $configFile): ConfigFileNotFoundException
    {
        $msg = sprintf(self::MESSAGE, $configFile);
        return new self($msg);
    }
}

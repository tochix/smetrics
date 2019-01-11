<?php declare(strict_types=1);

namespace App\Config\Exception;

final class ConfigKeyNotFoundException extends AbstractConfigException
{
    private const MESSAGE = 'Config key "%s" not found in "%s" config file';

    /**
     * @param $key
     * @param $configFile
     * @return ConfigKeyNotFoundException
     */
    public static function create($key, $configFile): ConfigKeyNotFoundException
    {
        $msg = sprintf(self::MESSAGE, $key, $configFile);
        return new self($msg);
    }
}

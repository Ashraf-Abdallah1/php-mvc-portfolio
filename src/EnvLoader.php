<?php

namespace Framework;

use Exception;

class EnvLoader
{
    /**
     * @throws Exception
     */
    public static function load(string $file): array
    {
        if (!file_exists($file)) {
            throw new Exception(".env file not found: $file");
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $config = [];

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $config[trim($key)] = trim($value);
        }

        return $config;
    }
}

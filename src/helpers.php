<?php

use Illuminate\Support\Arr;

if (!function_exists('get_base_dir')) {
    function get_base_dir(): string
    {
        if (!empty(getenv('APP_PATH'))) {
            return getenv('APP_PATH');
        }

        return dirname(__FILE__, 4);
    }
}

if (!function_exists('running_in_console')) {
    $consoleFakeReturn = null;

    function running_in_console(?array $tempArgv = null, $consoleFakeReturn = null): bool
    {
        global $argv, $consoleFakeReturn;
        $arguments = func_get_args();

        $tempArgv = Arr::get($arguments, 0, $argv);

        return $consoleFakeReturn ?? !strrpos(Arr::get($tempArgv, 0, ''), '/bin/phpunit') && php_sapi_name() == "cli";
    }
}

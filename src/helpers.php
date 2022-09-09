<?php

if (!function_exists('get_base_dir')) {
    function get_base_dir()
    {
        if (!empty(getenv('APP_PATH'))) {
            return getenv('APP_PATH');
        }

        $vendorDir = dirname(dirname(__FILE__));

        return dirname(dirname($vendorDir));
    }
}

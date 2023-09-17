<?php

if (! function_exists('logger')) {

    function logger(string $message = '', array $context = [])
    {
        error_log($message.' : '.json_encode($context));
    }
}

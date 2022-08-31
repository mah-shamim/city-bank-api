<?php

if (!function_exists('logger')) {
    /**
     * @param string $message
     * @param array $context
     */
    function logger(string $message = '', array $context = [])
    {
        error_log($message . ' : ' . json_encode($context));
    }
}

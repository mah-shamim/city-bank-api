<?php

if (!function_exists('logger')) {
    /**
     * @param string $message
     * @param array $context
     */
    function logger($message = '', $context = [])
    {
        error_log($message . ' : ' . json_encode($context));
    }
}

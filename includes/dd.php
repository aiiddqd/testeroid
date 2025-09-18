<?php

// if function don't exists
if (! function_exists('dd')) {

    require_once __DIR__.'/kint.phar';
    function dd(...$data)
    {
        s(...$data);
    }
}
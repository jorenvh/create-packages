<?php

if (! function_exists('objectify') )
{
    /**
     * Make an object of the given var
     *
     * @param $var
     * @return mixed
     */
    function objectify($var)
    {
        return json_decode(json_encode($var));
    }
}
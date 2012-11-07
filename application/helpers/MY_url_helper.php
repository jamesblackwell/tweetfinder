<?php

function remove_http($url)
{
    $url = trim($url);
    $url = preg_replace('/^http:\/\//', '', $url);
    $url = preg_replace('/^https:\/\//', '', $url);
    return $url;
}

function get_domain_name($url)
{
    $url = prep_url($url);
    $url = rtrim(parse_url($url, PHP_URL_HOST), '/');
    return remove_www($url);
}

function remove_www($url)
{
    return str_replace('www.', '', $url);
}

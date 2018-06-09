<?php


namespace Siewwp\LaravelServiceConsumer\Contracts;


interface HttpClient
{
    public function getHmacHandler();

    public function defaultConfigs();
    
    public function request($method, $path = '', array $options = []);
}
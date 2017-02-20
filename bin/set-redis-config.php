<?php
use RedisTools\Configurator;
use RedisTools\Hosts;

require dirname(__DIR__) . '/vendor/autoload.php';

$instances = Hosts::parseServices(getenv('VCAP_SERVICES'));
$configurator = new Configurator(getenv('CONFIG_ALIAS'));

foreach ($instances as $instance) {
    echo 'Setting config for ' . $instance['host'] . '...' . PHP_EOL;

    $params = [
        'scheme' => 'tcp',
        'host' => $instance['host'],
        'port' => $instance['port']
    ];
    $options = $instance['options'];

    $client = new Predis\Client($params, $options);

    echo 'Current policy is ' . $configurator->getEvictionPolicy($client) . '.' . PHP_EOL;

    // Evict least recently used keys when memory runs out, regardless of any items' expiry times
    $configurator->setEvictionPolicy($client, 'allkeys-lru');

    $client->disconnect();

    echo 'Done configuring ' . $instance['host'] . '.' . PHP_EOL;
}

echo 'All done!' . PHP_EOL;

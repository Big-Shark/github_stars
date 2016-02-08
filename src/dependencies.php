<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};


$container['github'] = function ($c) {
    $settings = $c->get('settings')['github'];
    return new GitHub($settings['username'], $settings['accessToken'], $c->get('cache'), $c->get('logger'));
};


$container['cache'] = function ($c) {
    $client = $c->get('memcached');
    $cache = new \MatthiasMullie\Scrapbook\Adapters\Memcached($client);
    return new \MatthiasMullie\Scrapbook\Psr6\Pool($cache);
};

$container['memcached'] = function ($c) {
    $settings = $c->get('settings')['memcached'];
    $client = new \Memcached();
    $client->addServer($settings['host'], $settings['port']);
    return $client;
};
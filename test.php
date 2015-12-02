<?php

$route = new Route();
$route->inc();
echo $route->getVal(), "\n";
$route->inc(10);
echo $route->getVal(), "\n";
$route->dec(10);
echo $route->getVal(), "\n";

$config = new Config();
$config->inc();
echo $config->getVal(), "\n";
$config->inc(10);
echo $config->getVal(), "\n";
$config->dec(10);
echo $config->getVal(), "\n";

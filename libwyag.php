<?php
require_once __DIR__ . '/vendor/autoload.php';

use Console\App\Commands\CatFileCommand;
use Console\App\Commands\HashObjectCommand;
use Symfony\Component\Console\Application;
use Console\App\Commands\InitCommand;
use Console\App\Commands\LogCommand;
use Console\App\Commands\TestCommand;

function main()
{
    $app = new Application();
    $app->add(new InitCommand());
    $app->add(new TestCommand());
    $app->add(new CatFileCommand());
    $app->add(new HashObjectCommand());
    $app->add(new LogCommand());
    $app->run();
}

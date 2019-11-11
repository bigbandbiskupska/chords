<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


class RouterFactory
{
    /**
     * @return Nette\Application\IRouter
     */
    public static function createRouter()
    {
        $router = new RouteList;
        $router[] = new Route('[home]', 'Front:Test:default');
        $router[] = new Route('stats', 'Front:Test:stats');
        $router[] = new Route('test/finished', 'Front:Test:answer');
        $router[] = new Route('test/question/<question>', 'Front:Chords:default');
        $router[] = new Route('test/question/<question>/answer', 'Front:Chords:answer');
        $router[] = new Route('<presenter>/<action>[/<id>]', 'Front:Chords:default');
        return $router;
    }
}

<?php

declare(strict_types=1);

namespace App\Core;

use Nette\Application\Routers\RouteList;
use Nette\Routing\Route;
use Nette\StaticClass;


final class RouterFactory
{
    use StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList;
        $router
            ->withPath("_system")
            ->addRoute("<presenter>/<action>[/<id>]", [
                "module" => "Admin",
                "presenter" => [
                    Route::Value => "Home",
                    Route::FilterTable => [
                        "sprava-ubytovani" => "Accommodation",
                        "sprava-aktivit" => "Activity",
                        "sprava-dotazu" => "Question"
                    ],
                ],
                "action" => [
                    Route::Value => "default",
                    Route::FilterTable => [

                    ]
                ],
            ])
            ->end();

        $router
            ->withModule("Front")
            ->addRoute('<presenter>/<action>[/<id>]', [
                "presenter" => [
                    Route::Value => "Home",
                    Route::FilterTable => [

                    ]
                ],
                "action" => [
                    Route::Value => "default",
                    Route::FilterTable => []
                ]
            ]);
        return $router;
    }
}

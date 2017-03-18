<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList();
		$router[] = new Route('/[<id historie|kostel|>/]', 'Homepage:default');
		$router[] = new Route('/ministranti/[<navigation-date>]', 'Acolyte:default');
		$router[] = new Route('/intence/[<navigation-date>]', 'Intention:default');
		$router[] = new Route('/intence/tisk/[<date>]', 'Intention:export');
		$router[] = new Route('/celebranti/[<navigation-date>]', 'Priest:default');
		$router[] = new Route('/celebranti/tisk/[<date>]', 'Priest:export');
		$router[] = new Route('<presenter>[/<action>][/<id>]', 'Homepage:default');
		return $router;
	}

}

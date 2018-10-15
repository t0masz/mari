<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route;


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
		$router[] = new Route('/intence/tisk/[<date>]', 'Intention:export');
		$router[] = new Route('/intence/vykaz/[<date>]', 'Intention:statement');
		$router[] = new Route('/intence/editace/<date>/<id>', 'Intention:edit');
		$router[] = new Route('/intence/smazat/<date>/<id>', 'Intention:delete');
		$router[] = new Route('/intence/protokol/[<navigation-date>]', 'Intention:protocol');
		$router[] = new Route('/intence/kody/', 'Intention:code');
		$router[] = new Route('/intence/kody/novy', 'Intention:addCode');
		$router[] = new Route('/intence/[<navigation-date>]', 'Intention:default');
		$router[] = new Route('/celebranti/[<navigation-date>]', 'Priest:default');
		$router[] = new Route('/celebranti/tisk/[<date>]', 'Priest:export');
		$router[] = new Route('<presenter>[/<action>][/<id>]', 'Homepage:default');
		return $router;
	}

}

<?php
namespace App\Helpers;

class Helpers
{

	public static function czechDate($date, $format = NULL)
	{
		$days = array(
					  1 => 'pondělí',
					  'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota', 'neděle'
					  );
		$days_abbr = array(
						   1 => 'po',
						   'út', 'st', 'čt', 'pá', 'so', 'ne'
						   );
		switch($format) {
			case 'full':
				$czechDate = $days[date('N', strtotime($date))] . ' ' . date('j. n.', strtotime($date));
				break;
			case 'abbr':
				$czechDate = $days_abbr[date('N', strtotime($date))] . ' ' . date('j. n.', strtotime($date));
				break;
			default:
				$czechDate = date('j. n.', strtotime($date));
		}
		return $czechDate;
	}
}

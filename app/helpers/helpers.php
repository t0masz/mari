<?php
namespace App\Helpers;

class Helpers
{

	public static function czechDate($date, $format = NULL)
	{
		$days = [1 => 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota', 'neděle'];
		$days_abbr = [1 => 'po', 'út', 'st', 'čt', 'pá', 'so', 'ne'];
		$months = [1 => 'leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec'];
		switch($format) {
			case 'month':
				$czechDate = $months[date('n', strtotime($date))] ;
				break;
			case 'monthY':
				$czechDate = $months[date('n', strtotime($date))] . ' ' . date('Y', strtotime($date));
				break;
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

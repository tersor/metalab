<?php

	Class FormatUtil{

		public static function formatDecimal($string){
			return  number_format( str_replace(",", ".", (double)$string) , 2, '.', '');
		}

		public static function formatDate($string){
			$Date = new DateTime($string);
			return $Date->format('Y-m-d');
		}
	}


?>
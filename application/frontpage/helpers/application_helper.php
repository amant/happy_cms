<?php

	/*function hex2bin($hexString)
	{
			$hexLenght = strlen($hexString);
			// only hex numbers is allowed
			if ($hexLenght % 2 != 0 || preg_match("/[^\da-fA-F]/",$hexString)) return FALSE;

			unset($binString);
			$binString = '';

			for ($x = 1; $x <= $hexLenght/2; $x++)
			{
					$binString .= chr(hexdec(substr($hexString,2 * $x - 2,2)));
			}

			return $binString;
	}*/
	
	function encode($word)
	{
		return $word;
//		$ci = get_instance();
//		$encoded = bin2hex($word);
//		$encoded = dechex($encoded);
//		return $encoded;
	}

	function decode($word)
	{
		return $word;
//		$decoded = hexdec($word);
//		$decoded = hex2bin($decoded);
//		return $decoded;
	}

	function property_url_title(array $property)
	{
		$url = array(encode($property['id']));

		// Set url
		if(trim($property['code']) !== '')
		{
			$url['code'] = url_title($property['code']);
		}

		if(trim($property['title_en']) !== '')
		{
			$url['title'] = url_title(strtolower($property['title_en']));
		}

		return site_url('property/detail/' . implode('/', $url));
	}


	function trim_nl($string)
	{
		return str_replace("\r\n", "", $string);
	}

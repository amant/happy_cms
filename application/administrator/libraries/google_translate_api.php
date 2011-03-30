<?php
	class Google_Translate_API
	{
		/**
		 * Translate using google api
		 *
		 * @param string $from
		 * @param string $to
		 * @param string $text
		 * @return string
		 */
		function translate($from, $to, $text)
		{
			$translated = '';

			$text = urlencode($text);

			$google_translator_url = "http://translate.google.com/translate_a/t?client=t&text=$text&hl=$from&sl=$from&tl=$to&multires=0&sc=1";
			$jhtml = $this->postPage($google_translator_url);
			return $jhtml;

			//$jhtml = '[[["liebe dich","love you","",""]],,"en",,[["liebe dich",[5],1,,654,0,2,0]],[["love you",5,[["liebe dich",654,1,],["love you",107,1,],["liebe euch",0,1,],["lieben",0,1,],["liebe Sie",0,1,]],[[0,8]],"love you"]],,,,3,[]]';
//			if($jhtml !== "")
//			{
				// TODO: user reg expression to extract string, and convert to JSON, remove this substr()

//				$start = strpos($jhtml, '[[["') + 3;
//				$end = strpos($jhtml, ']],,"') - 3;
//				$json_string = '[[' . addcslashes(substr($jhtml, $start, $end), "'") . ']]';
//				return $json_string;

				//$str = '[["\"Ich habe ein [client], die mehr als {}, um glücklich schmutzige Arbeit zu tun ist.\" ","\"I have a [client] which is more than {happy} to do dirty work.\"","",""],["Ihre einzige! ","Your\'s only!","",""],[": \u0026",":\u0026","",""]]';
				//$data = json_decode($this->removeTrailingCommas(utf8_encode($json_string)));

//				$data = utf8_decode($json_string);
//				print_r($data);
//				print_r(json_decode($data));
//				echo 'texti';
//				die();
//
//
//				// Index 0 of JSON array will content translated string
//				foreach($data as $value)
//				{
//					$translated .= $value[0];
//				}
//			}
//			else
//			{
//				return $translated;
//			}
		}
		
		function removeTrailingCommas($json)
		{
			$json=preg_replace('/,\s*([\]}])/m', '$1', $json);
			return $json;
		}

		// post form data to a given url using curl libs
		function postPage($url)
		{
			$html = "";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);

			//curl_setopt($ch, CURLOPT_POST, 1);
			//curl_setopt($ch, CURLOPT_POSTFIELDS, $opts["data"]);

			$html = curl_exec($ch);
			if (curl_errno($ch))
			{
				$html = "";
			}
			curl_close($ch);
			return $html;
			
		}
	}
?>
<?php
	class Testing extends CI_Controller
	{
		function __construct()
		{
			parent::__construct();
		}

		function _unserialize($data)
		{
			$data = @unserialize(strip_slashes($data));

			if (is_array($data))
			{
				foreach ($data as $key => $val)
				{
					if (is_string($val))
					{
						$data[$key] = str_replace('{{slash}}', '\\', $val);
					}
				}

				return $data;
			}

			return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
		}
	
		function index()
		{
			var_dump($_SERVER['DOCUMENT_ROOT']);
			die();
			
			var_dump($this->_unserialize($_COOKIE['ci_session_admin']));
			die();
			
			$pass        = 'a';
			$salt        = 'admin@happy-igniter.com';
			echo encrypt($pass, $salt);
			die();


			$this->load->library('ecdc');
			$chiper_text = $this->ecdc->encrypt($pass, $salt);
			echo $chiper_text . '<br/>';
			echo $this->ecdc->decrypt($chiper_text, $salt);	


			$pass        = 'adminadmin';
			$salt        = 'admin@localhost.com';
			
			$this->load->library('encryption');
			
			$chiper_text = $this->encryption->encrypt($salt, $pass);
			echo $chiper_text . '<br/>';
			
			echo $this->encryption->decrypt($salt, $chiper_text);
			die();
		}

		function xml()
		{
			$this->load->library('xml');

			if ($this->xml->load("views/welcome/welcome"))
			{
				print_r($this->xml->parser());
			}
			else
			{
				echo "error!";
			}
		}

		function yaml()
		{
			$this->load->library('spyc');

			$yaml = Spyc::YAMLLoad('views/welcome/welcome');
			print_r($yaml);
		}

		function email()
		{
			$this->load->library('email');
			$config['protocol'] = 'sendmail';
			$config['mailpath'] = '/usr/sbin/sendmail';
			$config['charset']  = 'iso-8859-1';
			$config['wordwrap'] = true;

			$this->email->initialize($config);

			$this->email->from('no-reply@firstclass-home.com', 'firstclass home');
			$this->email->to('aman@codeartsnepal.com');

			$this->email->subject('Email Test');
			$this->email->message('Testing the email class.');

			$this->email->send();

			echo $this->email->print_debugger();
		}

		function page1()
		{
			$this->session->set_flashdata('value', 'What');
			print_r($this->session->flashdata('value'));
		}

		function page2()
		{
			print_r($this->session->userdata);
		}

		function encryption()
		{
			/*$plain_text = 'Developer79870&%*&';
			$salt = 'salt456';
			$chiper = encrypt($plain_text, $salt);

			echo 'plain text : ' . $plain_text . "<br/>";
			echo 'chiper text : ' . $chiper . "<br/>";

			$dechiper = decrypt($chiper, 'salt456');

			echo 'dechiper text : ' . $dechiper . "<br/>";*/

			$this->load->library('encryption');

			$plain_text = 'developer';
			$salt       = 'encrypt456';

			$chiper = $this->encryption->encrypt($salt, $plain_text);

			echo 'plain text : ' . $plain_text . "<br/>";
			echo 'chiper text : ' . $chiper . "<br/>";

			$dechiper = $this->encryption->decrypt($salt, $chiper);

			echo 'dechiper text : ' . $dechiper . "<br/>";
		}

		function md5()
		{
			$salt = md5(uniqid(rand(), true));
			$pass = sha1($salt . 'puppy');
			echo $pass;
		}

		function pie()
		{
			set_include_path($this->config->item('include_path_library') . PATH_SEPARATOR . get_include_path());
			require_once('pChart/pData.php');
			require_once('pChart/pChart.php');

			//$visit = array(2293, 1460, 342, 282, 54, 13, 12, 4, 2, 2, 2, 1, 1, 1);
			//$browswer = array('Internet Explorer', 'Firefox', 'Safari', 'Chrome', 'Opera', 'Playstation 3', 'Mozilla', 'Mozilla Compatible Agent', 'Camino', 'NetFront', 'SeaMonkey', 'BlackBerry9000', 'IE with Chrome Frame', 'Playstation Portable');


			$visit    = array(
				2293,
				1460,
				342,
				282,
				54,
				13,
				12,
				4,
				40
			);
			$browswer = array(
				'Internet Explorer',
				'Firefox',
				'Safari',
				'Chrome',
				'Opera',
				'Playstation 3',
				'Mozilla',
				'Mozilla Compatible Agent',
				'Camino'
			);

			// Dataset definition
			$DataSet = new pData();
			$DataSet->AddPoint($visit, "Serie1");
			$DataSet->AddPoint($browswer, "Serie2");
			$DataSet->AddAllSeries();
			$DataSet->SetAbsciseLabelSerie("Serie2");

			// Initialise the graph
			$Test = new pChart(580, 200);
			$Test->drawFilledRoundedRectangle(7, 7, 373, 193, 5, 240, 240, 240);
			$Test->drawRoundedRectangle(5, 5, 375, 195, 5, 230, 230, 230);

			// Draw the pie chart
			$Test->setFontProperties($this->config->item('document_root') . "/public/fonts/tahoma.ttf", 8);
			$Test->drawPieGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 150, 90, 110, PIE_PERCENTAGE, true, 50, 20, 5);
			$Test->drawPieLegend(310, 15, $DataSet->GetData(), $DataSet->GetDataDescription(), 250, 250, 250);

			$Test->Render($this->config->item('document_root') . '/public/images/watchdog/' . "by_browser.png");
			echo '<img src="' . base_url() . 'public/images/watchdog/by_browser.png" />';
		}

		function decode_json()
		{
			$json = '{"property_id":1,"selected1":[],"selected2":[],"selected3":["2011/3/1","2011/3/2","2011/3/3","2011/3/4","2011/3/5"],"selected4":[]}';
			//$json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
			var_dump(json_decode($json, true));
			die();
		}

		function date_diff()
		{
			$from = '1-1-2011';
			$to   = '12-1-2011';

			$start_date = explode('-', $from);
			$end_date   = explode('-', $to);


			$from = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];
			$to   = $end_date[2] . '-' . $end_date[1] . '-' . $end_date[0];

			print_r($this->get_date_range($from, $to));
			die();
		}

		/**
		 * Generate date range
		 * @param string $from (yyyy-mm-dd)
		 * @param string $to (yyyy-mm-dd)
		 * @return array
		 */
		function get_date_range($from, $to)
		{
			// takes two dates formatted as YYYY-MM-DD and creates an
			// inclusive array of the dates between the from and to dates.
			$aryRange = array();


			$from = explode('-', $from);
			$to   = explode('-', $to);

			$iDateFrom = mktime(1, 0, 0, $from[1], $from[2], $from[0]);
			$iDateTo   = mktime(1, 0, 0, $to[1], $to[2], $to[0]);

			if ($iDateTo >= $iDateFrom)
			{
				array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry

				while ($iDateFrom < $iDateTo)
				{
					$iDateFrom += 86400; // add 24 hours
					array_push($aryRange, date('Y-m-d', $iDateFrom));
				}
			}
			return $aryRange;
		}
	}
?>
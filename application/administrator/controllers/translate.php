<?php
  class Translate extends CI_Controller
  {
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$from = $_POST['from'];
		$to = $_POST['to'];
		$text = $_POST['text'];

		$trans = app_google_translate($from, $to, $text);
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		//header('Content-type: application/json; charset=UTF-8');
		header('Content-type: text/javascript; charset=UTF-8');

		// remove all ,,
		$trans = str_replace(',,', ',', $trans);

		// remove all ,, second time
		$trans = str_replace(',,', ',', $trans);

		// Remove all trailing ,]
		$trans = str_replace(',]', ']', $trans);

		$trans = utf8_encode($trans);
		
		if($to === 'ru')
		{
			$this->load->library('convert_charset');
			$trans = $this->convert_charset->Convert(utf8_decode($trans));
		}
		echo '{"data":' . $trans . '}';
		die();
		
//		echo '{"data":[[["was für ein schöner Tag?","what a lovely day?","",""]],"en",[["was für ein",[5],1,551,0,3,0],["schöner Tag",[6],1,883,3,5,0],["?",[7],1000,5,6,0]],[["what a",5,[["was für ein",551,1],["was ein",0,1],["Was für eine",0,1],["was eine",0,1]],[[0,6]],"what a lovely day?"],["lovely day",6,[["schöner Tag",883,1],["schönen Tag",0,1],["herrlicher Tag",0,1],["wunderschöner Tag",0,1],["wunderschönen Tag",0,1]],[[7,17]],""],["?",7,[["?",1000,1]],[[17,18]],""]],3,[]]}';
//		die();
	}
  }
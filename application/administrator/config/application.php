<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	$config['itask_version'] = "2.1 (beta)";
	$config['default_currency'] = 'NC';
	$config['default_currency_symbol'] = 'Rs';
	$config['default_unit'] = 'US';
	
	$config['unsubscribe'] = 'newsletter/unsubscribe/';
	$config['debug_newsletter'] = 'aman.tuladhar@gmail.com';

	$config['google_api_key'] = 'ABQIAAAAVPczRPQhmUz9ROM_0ZIFJxSX-pmDxkGDAXM1QdaRibi8oJ9gTxSJ-ukVF6lrjwft-hJWSywh8MT7Uw';

	// Week days
	$config['week_days'] = array(
		1 => 'Monday',
		2 => 'Tuesday',
		3 => 'Wednesday',
		4 => 'Thrusday',
		5 => 'Friday',
		6 => 'Saturday',
		7 => 'Sunday'
	);

	// Interval
	$config['intervals'] = array(
		1 => 'per week',
		2 => 'per day',
		3 => 'according to consumption',
		4 => 'global'
	);

	// Mandatory option
	$config['mandatory_options'] = array(
		1 => 'optional',
		2 => 'obligatory',
		3 => 'if pet',
		4 => 'to bring along',
		5 => 'carry out by onself',
		6 => 'none',
		7 => 'inclusive'
	);
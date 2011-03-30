<?php
	// ******************************************************************************
	// A reversible password encryption routine by:
	// Copyright 2003-2009 by A J Marston <http://www.tonymarston.net>
	// Distributed under the GNU General Public Licence
	// Modification: May 2007, M. Kolar <http://mkolar.org>:
	// No need for repeating the first character of scramble strings at the end;
	// instead using the exact inverse function transforming $num2 to $num1.
	// Modification: Jan 2009, A J Marston <http://www.tonymarston.net>:
	// Use mb_substr() if it is available (for multibyte characters).
	// ******************************************************************************

	class Encryption
	{
		private $_scramble1; // 1st string of ASCII characters
		private $_scramble2; // 2nd string of ASCII characters
		private $_errors; // array of error messages
		private $_adj; // 1st adjustment value (optional)
		private $_mod; // 2nd adjustment value (optional)

		/**
		 * class constructor
		 */
		function __construct()
		{
			$this->_errors = array();

			// Each of these two strings must contain the same characters, but in a different order.
			// Use only printable characters from the ASCII table.
			// Do not use single quote, double quote or backslash as these have special meanings in PHP.
			// Each character can only appear once in each string.
			$this->_scramble1 = '! #$%&()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_`abcdefghijklmnopqrstuvwxyz{|}~';
			$this->_scramble2 = 'f^jAE]okIOzU[2&q1{3`h5w_794p@6s8?BgP>dFV=m D<TcS%Ze|r:lGK/uCy.Jx)HiQ!#$~(;Lt-R}Ma,NvW+Ynb*0X';

			if (strlen($this->_scramble1) <> strlen($this->_scramble2))
			{
				trigger_error('** SCRAMBLE1 is not same length as SCRAMBLE2 **', E_USER_ERROR);
			}

			$this->_adj = 1.75; // this value is added to the rolling fudgefactors
			$this->_mod = 3; // if divisible by this the adjustment is made negative
		}

		/**
		 * Return an adjustment value  based on the contents of $fudgefactor
		 * NOTE: $fudgefactor is passed by reference so that it can be modified
		 *
		 * @param <type> $fudgefactor
		 * @return <type>
		 */
		private function _applyFudgeFactor(&$fudgefactor)
		{
			$fudge         = array_shift($fudgefactor); // extract 1st number from array
			$fudge         = $fudge + $this->_adj; // add in adjustment value
			$fudgefactor[] = $fudge; // put it back at end of array

			if (!empty($this->_mod)) // if modifier has been supplied
			{
				if ($fudge % $this->_mod == 0) // if it is divisible by modifier
				{
					$fudge = $fudge * -1; // make it negative
				}
			}

			return $fudge;

		}

		/**
		 * Check that $num points to an entry in $this->scramble1
		 *
		 * @param <type> $num
		 * @return <type>
		 */
		private function _checkRange($num)
		{
			$num = round($num); // round up to nearest whole number

			$limit = strlen($this->_scramble1);

			while ($num >= $limit)
			{
				$num = $num - $limit; // value too high, so reduce it
			}
			while ($num < 0)
			{
				$num = $num + $limit; // value too low, so increase it
			}

			return $num;

		}

		/**
		 * convert $key into an array of numbers
		 *
		 * @param <type> $key
		 * @return <type>
		 */
		private function _convertKey($key)
		{
			if (empty($key))
			{
				$this->_errors[] = 'No value has been supplied for the encryption key';
				return;
			}

			$array[] = strlen($key); // first entry in array is length of $key

			$tot = 0;
			for ($i = 0; $i < strlen($key); $i++)
			{
				// extract a (multibyte) character from $key
				if (function_exists('mb_substr'))
				{
					$char = mb_substr($key, $i, 1);
				}
				else
				{
					$char = substr($key, $i, 1);
				}

				// identify its position in $scramble1
				$num = strpos($this->_scramble1, $char);
				if ($num === false)
				{
					$this->_errors[] = "Key contains an invalid character ($char)";
					return;
				}

				$array[] = $num; // store in output array
				$tot     = $tot + $num; // accumulate total for later
			}

			$array[] = $tot; // insert total as last entry in array
			return $array;
		}

		/**
		 * decrypt string into its original form
		 *
		 * @param <type> $key
		 * @param <type> $source
		 * @return <type>
		 */
		public function decrypt($key, $source)
		{
			$this->_errors = array();

			// convert $key into a sequence of numbers
			$fudgefactor = $this->_convertKey($key);
			if ($this->_errors)
				return;

			if (empty($source))
			{
				$this->_errors[] = 'No value has been supplied for decryption';
				return;
			}

			$target  = null;
			$factor2 = 0;

			for ($i = 0; $i < strlen($source); $i++)
			{
				// extract a (multibyte) character from $source
				if (function_exists('mb_substr'))
				{
					$char2 = mb_substr($source, $i, 1);
				}
				else
				{
					$char2 = substr($source, $i, 1);
				}

				// identify its position in $scramble2
				$num2 = strpos($this->_scramble2, $char2);
				if ($num2 === false)
				{
					$this->_errors[] = "Source string contains an invalid character ($char2)";
					return;
				}

				// get an adjustment value using $fudgefactor
				$adj = $this->_applyFudgeFactor($fudgefactor);

				$factor1 = $factor2 + $adj; // accumulate in $factor1
				$num1    = $num2 - round($factor1); // generate offset for $scramble1
				$num1    = $this->_checkRange($num1); // check range
				$factor2 = $factor1 + $num2; // accumulate in $factor2

				// extract (multibyte) character from $scramble1
				if (function_exists('mb_substr'))
				{
					$char1 = mb_substr($this->_scramble1, $num1, 1);
				}
				else
				{
					$char1 = substr($this->_scramble1, $num1, 1);
				}

				// append to $target string
				$target .= $char1;

			}

			return rtrim($target);
		}

		/**
		 * encrypt string into a garbled form
		 *
		 * @param <type> $key
		 * @param <type> $source
		 * @param <type> $sourcelen
		 * @return <type>
		 */
		public function encrypt($key, $source, $sourcelen = 50)
		{
			$this->_errors = array();

			// convert $key into a sequence of numbers
			$fudgefactor = $this->_convertKey($key);
			if ($this->_errors)
				return;

			if (empty($source))
			{
				$this->_errors[] = 'No value has been supplied for encryption';
				return;
			}

			// pad $source with spaces up to $sourcelen
			$source = str_pad($source, $sourcelen);

			$target  = null;
			$factor2 = 0;

			for ($i = 0; $i < strlen($source); $i++)
			{
				// extract a (multibyte) character from $source
				if (function_exists('mb_substr'))
				{
					$char1 = mb_substr($source, $i, 1);
				}
				else
				{
					$char1 = substr($source, $i, 1);
				}

				// identify its position in $scramble1
				$num1 = strpos($this->_scramble1, $char1);
				if ($num1 === false)
				{
					$this->_errors[] = "Source string contains an invalid character ($char1)";
					return;
				}

				// get an adjustment value using $fudgefactor
				$adj = $this->_applyFudgeFactor($fudgefactor);

				$factor1 = $factor2 + $adj; // accumulate in $factor1
				$num2    = round($factor1) + $num1; // generate offset for $scramble2
				$num2    = $this->_checkRange($num2); // check range
				$factor2 = $factor1 + $num2; // accumulate in $factor2

				// extract (multibyte) character from $scramble2
				if (function_exists('mb_substr'))
				{
					$char2 = mb_substr($this->_scramble2, $num2, 1);
				}
				else
				{
					$char2 = substr($this->_scramble2, $num2, 1);
				}

				// append to $target string
				$target .= $char2;

				//echo "char1=$char1, num1=$num1, adj= $adj, factor1= $factor1, num2=$num2, char2=$char2, factor2= $factor2<br />\n";
			}
			return $target;
		}

		/**
		 * the adjustment value
		 * @return <type>
		 */
		public function getAdjustment()
		{
			return $this->_adj;
		}

		/**
		 * return the modulus value
		 *
		 * @return <type>
		 */
		public function getModulus()
		{
			return $this->_mod;

		}

		/**
		 * set the adjustment value
		 *
		 * @param <type> $adj
		 */
		public function setAdjustment($adj)
		{
			$this->_adj = (float) $adj;

		}

		/**
		 * set the modulus value
		 *
		 * @param <type> $mod
		 */
		public function setModulus($mod)
		{
			$this->_mod = (int) abs($mod); // must be a positive whole number

		}
	}
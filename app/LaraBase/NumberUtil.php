<?php 
	namespace App\LaraBase;

	class NumberUtil{
		public function convert_number_to_wordsEn($number) {
		   
		    $hyphen      = '-';
		    $conjunction = ' and ';
		    $separator   = ', ';
		    $negative    = 'negative ';
		    $decimal     = ' point ';
		    $dictionary  = array(
		        0                   => 'zero',
		        1                   => 'one',
		        2                   => 'two',
		        3                   => 'three',
		        4                   => 'four',
		        5                   => 'five',
		        6                   => 'six',
		        7                   => 'seven',
		        8                   => 'eight',
		        9                   => 'nine',
		        10                  => 'ten',
		        11                  => 'eleven',
		        12                  => 'twelve',
		        13                  => 'thirteen',
		        14                  => 'fourteen',
		        15                  => 'fifteen',
		        16                  => 'sixteen',
		        17                  => 'seventeen',
		        18                  => 'eighteen',
		        19                  => 'nineteen',
		        20                  => 'twenty',
		        30                  => 'thirty',
		        40                  => 'fourty',
		        50                  => 'fifty',
		        60                  => 'sixty',
		        70                  => 'seventy',
		        80                  => 'eighty',
		        90                  => 'ninety',
		        100                 => 'hundred',
		        1000                => 'thousand',
		        1000000             => 'million',
		        1000000000          => 'billion',
		        1000000000000       => 'trillion',
		        1000000000000000    => 'quadrillion',
		        1000000000000000000 => 'quintillion'
		    );
		   
		    if (!is_numeric($number)) {
		        return false;
		    }
		   
		    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		        // overflow
		        trigger_error(
		            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
		            E_USER_WARNING
		        );
		        return false;
		    }

		    if ($number < 0) {
		        return $negative . $this->convert_number_to_wordsEn(abs($number));
		    }
		   
		    $string = $fraction = null;
		   
		    if (strpos($number, '.') !== false) {
		        list($number, $fraction) = explode('.', $number);
		    }
		   
		    switch (true) {
		        case $number < 21:
		            $string = $dictionary[$number];
		            break;
		        case $number < 100:
		            $tens   = ((int) ($number / 10)) * 10;
		            $units  = $number % 10;
		            $string = $dictionary[$tens];
		            if ($units) {
		                $string .= $hyphen . $dictionary[$units];
		            }
		            break;
		        case $number < 1000:
		            $hundreds  = $number / 100;
		            $remainder = $number % 100;
		            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
		            if ($remainder) {
		                $string .= $conjunction . $this->convert_number_to_wordsEn($remainder);
		            }
		            break;
		        default:
		            $baseUnit = pow(1000, floor(log($number, 1000)));
		            $numBaseUnits = (int) ($number / $baseUnit);
		            $remainder = $number % $baseUnit;
		            $string = $this->convert_number_to_wordsEn($numBaseUnits) . ' ' . $dictionary[$baseUnit];
		            if ($remainder) {
		                $string .= $remainder < 100 ? $conjunction : $separator;
		                $string .= $this->convert_number_to_wordsEn($remainder);
		            }
		            break;
		    }
		   
		    if (null !== $fraction && is_numeric($fraction)) {
		        $string .= $decimal;
		        $words = array();
		        foreach (str_split((string) $fraction) as $number) {
		            $words[] = $dictionary[$number];
		        }
		        $string .= implode(' ', $words);
		    }
		   
		    return $string;
		}
		public function number($number){
			if (!isset($number))
				return 0;
			if ($number == '')
				return 0;
			$number = str_replace(',', '', $number);
			$number = str_replace('.', ',', $number);

			return $number; 
		}
		public function convert_number_to_words($number) {
			$hyphen      = ' ';
			$conjunction = ' ';
			$separator   = ' ';
			$negative    = 'âm ';
			$decimal     = ' phẩy ';
			$dictionary  = array(
			0                   => 'Không',
			1                   => 'Một',
			2                   => 'Hai',
			3                   => 'Ba',
			4                   => 'Bốn',
			5                   => 'Năm',
			6                   => 'Sáu',
			7                   => 'Bảy',
			8                   => 'Tám',
			9                   => 'Chín',
			10                  => 'Mười',
			11                  => 'Mười Một',
			12                  => 'Mười Hai',
			13                  => 'Mười Ba',
			14                  => 'Mười Bốn',
			15                  => 'Mười Lăm',
			16                  => 'Mười Sáu',
			17                  => 'Mười Bảy',
			18                  => 'Mười Tám',
			19                  => 'Mười Chín',
			20                  => 'Hai Mươi',
			30                  => 'Ba Mươi',
			40                  => 'Bốn Mươi',
			50                  => 'Năm Mươi',
			60                  => 'Sáu Mươi',
			70                  => 'Bảy Mươi',
			80                  => 'Tám Mươi',
			90                  => 'Chín Mươi',
			100                 => 'Trăm',
			1000                => 'Ngàn',
			1000000             => 'Triệu',
			1000000000          => 'Tỷ',
			1000000000000       => 'Nghìn tỷ',
			1000000000000000    => 'Ngàn Triệu Triệu',
			1000000000000000000 => 'Tỷ Tỷ'
			);
			 
			if (!is_numeric($number)) {
				return false;
			}
			 
			if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
				// overflow
				trigger_error(
					'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
					E_USER_WARNING
				);
				return false;
			}
			 
			if ($number < 0) {
				return $negative . $this->convert_number_to_words(abs($number));
			}
			 
			$string = $fraction = null;
			 
			if (strpos($number, '.') !== false) {
				list($number, $fraction) = explode('.', $number);
			}
			 
			switch (true) {
				case $number < 21:
					$string = $dictionary[$number];
					break;
				case $number < 100:
					$tens   = ((int) ($number / 10)) * 10;
					$units  = $number % 10;
					$string = $dictionary[$tens];
					if ($units) {
						$string .= $hyphen . $dictionary[$units];
					}
					break;
				case $number < 1000:
					$hundreds  = $number / 100;
					$remainder = $number % 100;
					$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
					if ($remainder) {
						$a = $this->convert_number_to_words($remainder);
					$string .= $conjunction . $this->convert_number_to_words($remainder);
					}
					break;
				default:
					$baseUnit = pow(1000, floor(log($number, 1000)));
					$numBaseUnits = (int) ($number / $baseUnit);
					$remainder = $number % $baseUnit;
					$string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
					if ($remainder) {
						$string .= $remainder < 100 ? $conjunction : $separator;
						$string .= $this->convert_number_to_words($remainder);
					}
				break;
			}
			 
			if (null !== $fraction && is_numeric($fraction)) {
				$string .= $decimal;
				$words = array();
				foreach (str_split((string) $fraction) as $number) {
					$words[] = $dictionary[$number];
				}
					$string .= implode(' ', $words);
			}
			 
			return $string;
		}

		public function num_to_letters($num, $uppercase = true) {
	        $letters = '';
	        while ($num > 0) {
	            $code = ($num % 26 == 0) ? 26 : $num % 26;
	            $letters .= chr($code + 64);
	            $num = ($num - $code) / 26;
	        }
	        return ($uppercase) ? strtoupper(strrev($letters)) : strrev($letters);
	    }
	}
?>
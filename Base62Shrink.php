<?php

class Base62Shrink {
	private static $init = TRUE;
	private static $b62array;
	private static $b62object;
	private static $LZWarray = array();
	private static $LZWobject = array();

	public static function compress($text = '') {
		if (self::$init) self::init();
		return $text ? self::base8To62(self::deltaShrinkToBase8(self::compressLZW($text))) : '';
	}

	public static function decompress($compressed = '') {
		if (self::$init) self::init();
		return $compressed ? self::decompressLZW(self::deltaUnshrinkFromBase8(self::base62To8($compressed))) : '';
	}

	private static function init() {
		self::$b62array = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
		self::$b62object = array_flip(self::$b62array);
		for ($i = 0; $i < 256; ++$i) self::$LZWobject[self::$LZWarray[$i] = chr($i)] = $i;
		self::$init = FALSE;
	}

	private static function base8To62($base8 = '') {
		$base8 = strlen($base8) % 2 ? '1' . $base8 : '0' . $base8 . '0';
		for ($base62 = '', $i = 0, $j = strlen($base8); ++$i < $j; ++$i) {
			$base62 .= self::$b62array[intVal((substr($base8, $i - 1, 2)), 8)];
		}
		return $base62;
	}

	private static function base62To8($base62 = '') {
		for ($base8 = '', $i = 0, $j = strlen($base62); $i < $j; ++$i) {
			$base8 .= str_pad(base_convert((string) self::$b62object[$base62[$i]], 10, 8), 2, '0', STR_PAD_LEFT);
		}
		return $base8[0] === '1' ? substr($base8, 1) : substr($base8, 1, -1);
	}

	private static function deltaShrinkToBase8($intArray = array()) {
		for ($shrinked = '', $i = 0, $j = count($intArray), $k = 0; $i < $j; $k = $l) {
			$k = ($l = $intArray[$i++]) - $k;
			$shrinked .= ($k < 0 ? '7' : '6') . base_convert((string) $k, 10, 6);
		}
		return substr($shrinked, 1);
	}

	private static function deltaUnshrinkFromBase8($base8 = '') {
		$compArray = explode('6', strtr($base8, array('7' => '6-')));
		for ($intArray = array(), $i = 0, $j = count($compArray), $k = 0; $i < $j; $k = $l) {
			$intArray[] = ($l = ($k + intVal($compArray[$i++], 6)));
		}
		return $intArray;
	}

	private static function compressLZW($text = '') {
		$dict = self::$LZWobject; $w = ''; $result = array(); $dictSize = 256;

		for ($i = 0, $j = mb_strlen($text); $i < $j; ++$i) {
			$c = mb_substr($text, $i, 1);
			$wc = $w . $c;
			if (isset($dict[$wc])) $w = $wc;
			else {
				$result[] = $dict[$w];
				$dict[$wc] = $dictSize++;
				$w = $c;
			}
		}

		if ($w !== '') $result[] = $dict[$w];
		return $result;
	}

	private static function decompressLZW($compressed = array()) {
		$dict = self::$LZWarray; $dictSize = 256;

		$result = $w = chr($compressed[0]);
		for ($i = 1, $j = count($compressed); $i < $j; ++$i) {
			$k = $compressed[$i];
			if (isset($dict[$k])) $entry = $dict[$k];
			else if ($k === $dictSize) $entry = $w . mb_substr($w, 0, 1);
			else return NULL;

			$dict[$dictSize++] = $w . mb_substr($entry, 0, 1);
			$result .= $w = $entry;
		}
		return $result;
	}
}

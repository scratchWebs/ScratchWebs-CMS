<?php

class swCommon 
{
	const SQL_DATE_FORMAT = "Y-m-d g:i:s";
	
	public static function startsWith($string, $startsWith)
	{
		$length = strlen($startsWith);
		return (substr($string, 0, $length) === $startsWith);
	}
	public static function endsWith($string, $endsWith)
	{
		$length = strlen($endsWith);
		$start = $length * -1;
		return (substr($string, $start) === $endsWith);
	}
	public static function getIncludeContents($filename)
	{
		ob_start();
		include(PATH_CONTROLS . $filename);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	public static function array_unshift_withkey(&$array,$key,$value)
	{
		$array = array_reverse($array,true);
		$array[$key] = $value;
		$array = array_reverse($array,true);
		return $array;
	}
	public static function convert_smart_quotes($string)
	{
		return iconv('UTF-8', 'ASCII//TRANSLIT', $string);
	}
}

?>
<?php

class swCommon {
	
	static public function startsWith($string, $startsWith)
	{
		$length = strlen($startsWith);
		return (substr($string, 0, $length) === $startsWith);
	}
	static public function endsWith($string, $endsWith)
	{
		$length = strlen($endsWith);
		$start = $length * -1;
		return (substr($string, $start) === $endsWith);
	}
	static public function getIncludeContents($filename)
	{
		ob_start();
		include(PATH_CONTROLS . $filename);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
}

?>
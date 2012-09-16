<?php
require "cms.php";

echo "<p>Creating Export file(s)...</p>";

// first ensure we don't run out of memory
ini_set('memory_limit', '300M');

// variable declarations
$imageCount = 0;
$totalImageCount = 0;
$maxImagesPerFile = 15;
$outputFileName = "images";
$outputFormat = ".sql";
$fileCount = 1;
$outputFiles = array();

$result = mysql_query('SELECT * FROM tblImages ORDER BY img_id ASC');

while (($data = mysql_fetch_array($result)) == true)
{
	$sql = "INSERT INTO tblImages ";
	$sql.= "(delete_flag," .
			"enabled," .
			"img_code_ref," .
			"img_name," .
			"img_desc_short," .
			"img_desc_long," .
			"img_width," .
			"img_height," .
			"img_type," .
			"img_data_thumb," .
			"img_data_preview," .
			"img_data_large," .
			"img_data_original," .
			"img_featured," .
			"img_order," .
			"img_internal_count," .
			"img_external_count," .
			"img_fk_gallery_id," .
			"img_fk_section_id," .
			"img_fk_pg_id) ";

	$sql.= "VALUES (";
	$sql.= $data["delete_flag"] . ',';
	$sql.= $data["enabled"] . ',';
	$sql.= '\'' . $data["img_code_ref"] . '\',';
	$sql.= '\'' . $data["img_name"] . '\',';
	$sql.= '\'' . $data["img_desc_short"] . '\',';
	$sql.= '\'' . $data["img_desc_long"] . '\',';
	$sql.= $data["img_width"] . ',';
	$sql.= $data["img_height"] . ',';
	$sql.= '\'' . $data["img_type"] . '\',';
	$sql.= '\'' . addslashes($data["img_data_thumb"]) . '\',';
	$sql.= '\'' . addslashes($data["img_data_preview"]) . '\',';
	$sql.= '\'' . addslashes($data["img_data_large"]) . '\',';
	$sql.= '\'' . addslashes($data["img_data_original"]) . '\',';
	$sql.= $data["img_featured"] . ',';
	$sql.= $data["img_order"] . ',';
	$sql.= $data["img_internal_count"] . ',';
	$sql.= $data["img_external_count"] . ',';
	$sql.= (isset($data["img_fk_gallery_id"])) ? $data["img_fk_gallery_id"] . ',' : 'NULL,';
	$sql.= (isset($data["img_fk_section_id"])) ? $data["img_fk_section_id"] . ',' : 'NULL,';
	$sql.= (isset($data["img_fk_pg_id"])) ? $data["img_fk_pg_id"] : 'NULL';
	$sql.= ");\n";
	
	$imageCount++;
	$totalImageCount++;
	
	// if we have exceeded the max file count
	if ($imageCount > $maxImagesPerFile) {
		$fileCount++;
		$imageCount = 0;
	}
	
	// make the filename
	$tmpFileName = $outputFileName . $fileCount . $outputFormat;
	
	// add filename to list
	if (!in_array($tmpFileName,$outputFiles)) array_push($outputFiles,$tmpFileName);
	
	// write to file
	$fileHandle = fopen($tmpFileName, 'a') or die("can't open file");
	fwrite($fileHandle, $sql);
	fclose($fileHandle);
}

echo "<p>Complete! " . $totalImageCount . " Images processed</p>";

// create zip file
$zip = new ZipArchive();
$zipFileName = "imageExport.zip";
if ($zip->open($zipFileName, ZIPARCHIVE::OVERWRITE)!==TRUE) {
    exit("cannot open <$zipFileName>\n");
}

//add the files
foreach ($outputFiles as $file) {
	$zip->addFile($file,$file);
}

echo '<p>The zip archive contains ' . $zip->numFiles . ' sql files</p>';
$zip->close();
echo '<p><a href="' . $zipFileName . '">' . $zipFileName . '</a> ('. filesize($zipFileName) / 1000000 .'MB)</p>';

// delete files that were added to the zip archive
foreach($outputFiles as $file) {
	unlink($file);
}

?>
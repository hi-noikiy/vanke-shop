<?php


require_once 'readExcel/reader.php';


// ExcelFile($filename, $encoding);
$data = new Spreadsheet_Excel_Reader();


// Set output Encoding.
//$data->setOutputEncoding('CP1251');
//$data->setOutputEncoding('gb2312');
$data->setOutputEncoding('utf-8');

/***
* if you want you can change 'iconv' to mb_convert_encoding:
* $data->setUTFEncoder('mb');
*
**/

/***
* By default rows & cols indeces start with 1
* For change initial index use:
* $data->setRowColOffset(0);
*
**/



/***
*  Some function for formatting output.
* $data->setDefaultFormat('%.2f');
* setDefaultFormat - set format for columns with unknown formatting
*
* $data->setColumnFormat(4, '%.3f');
* setColumnFormat - set format for column (apply only to number fields)
*
**/

$data->read('test.xls');

/*


 $data->sheets[0]['numRows'] - count rows
 $data->sheets[0]['numCols'] - count columns
 $data->sheets[0]['cells'][$i][$j] - data from $i-row $j-column

 $data->sheets[0]['cellsInfo'][$i][$j] - extended info about cell
    
    $data->sheets[0]['cellsInfo'][$i][$j]['type'] = "date" | "number" | "unknown"
        if 'type' == "unknown" - use 'raw' value, because  cell contain value with format '0.00';
    $data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format 
    $data->sheets[0]['cellsInfo'][$i][$j]['colspan'] 
    $data->sheets[0]['cellsInfo'][$i][$j]['rowspan'] 
*/

error_reporting(E_ALL ^ E_NOTICE);

for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
		echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
	}
	echo "<br />";

}


//print_r($data);
//print_r($data->formatRecords);


//-------------以下为附加函数，需要时可调用---------------------------------
//excel里面时间是从1900-1-1开始以天计算，比如1900-1-1是1，1970-1-1是25569.
//当用phpexcel或其他excel类把日期从excel中读取出来后，可用下面的函数转成成类似 2009-1-1 格式的时间。
//echo exceltimtetophp($data->sheets[0]['cells'][$i][$j]);  
/*
function exceltimtetophp($days,$time=false)
{
	if(is_numeric($days))
	{
		$jd = GregorianToJD(1, 1, 1970);
		$gregorian = JDToGregorian($jd+intval($days)-25569);
		$myDate = explode('/',$gregorian);
		$myDateStr= str_pad($myDate[2],4,'0', STR_PAD_LEFT)."-".str_pad($myDate[0],2,'0',STR_PAD_LEFT)."-".str_pad($myDate[1],2,'0', STR_PAD_LEFT).($time?"00:00:00":'');
		return $myDateStr;
	}
	return $time;
}
*/
//-------------以上为附加函数，需要时可调用---------------------------------
?>


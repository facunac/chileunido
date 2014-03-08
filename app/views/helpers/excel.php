<?php 
set_time_limit(10); // Set maximum execution time to 10 seconds.
error_reporting(E_ALL ^E_NOTICE); // Notice errors break the Excel file format.

define('TEXT_FORMAT',     0);
define('NUM_FORMAT',      1);
define('MONEY_FORMAT',    2);
define('DATE_FORMAT',     3);
define('TIME_FORMAT',     4);
define('DATETIME_FORMAT', 5);

vendor('Spreadsheet/Excel/Writer');

class excelHelper extends Helper {

  var $workbook;
  var $worksheets = array();
  var $formats    = array();
  
  var $font   = 'Arial';
  var $size   = 10;
  var $align  = 'left';
  var $valign = 'vcenter';
  var $bold   = 0;
  var $italic = 0;


  function excelHelper($filename = 'data.xls') {
    $this->workbook =& new Spreadsheet_Excel_Writer();
    $this->workbook->setTempDir(TMP."cache");

    $this->workbook->setVersion(8); // Set workbook to Excel 97 (for UTF-8 support)
  }


  function _getFormatArray($params = NULL) {
    $temp = array('font'   => $this->font,
                  'size'   => $this->size,
                  'bold'   => $this->bold,
                  'italic' => $this->italic,
                  'align'  => $this->align,
                  'valign' => $this->valign);
    if(isset($params)) {
      foreach($params as $key => $value) {
        $temp[$key] = $value;
      }
    }
    return $temp;
  }


  function _GetExcelEpoch() {
    return GregorianToJD(1,1,1900); // Windows Excel epoch
  }


  function initFormats() {
    // initialize default formats:
    $text = $this->_getFormatArray();
    $text['textwrap'] = 1;
    $text['numformat'] = '@';
    $this->formats[TEXT_FORMAT] =& $this->workbook->addformat($text);

    $num = $this->_getFormatArray();
    $num['align'] = 'right';
    $this->formats[NUM_FORMAT] =& $this->workbook->addformat($num);

    $num['numformat'] = '[$EUR-413] #,##0.00;[$EUR-413] #,##0.00-';
    $this->formats[MONEY_FORMAT] =& $this->workbook->addformat($num);

    $num['numformat'] = 'dd-mm-yyyy';
    $this->formats[DATE_FORMAT] =& $this->workbook->addformat($num);

    $num['numformat'] = 'hh:mm:ss';
    $this->formats[TIME_FORMAT] =& $this->workbook->addformat($num);

    $num['numformat'] = 'dd-mm-yyyy hh:mm:ss';
    $this->formats[DATETIME_FORMAT] =& $this->workbook->addformat($num);
  }


  function &AddWorksheet($name = NULL) {
    $this->worksheets[] =& $this->workbook->addWorksheet($name);
    $this->worksheets[count($this->worksheets) - 1]->setInputEncoding('UTF-8');
    return $this->worksheets[count($this->worksheets) - 1];
  }


  function AddFormat($params) {
    $this->formats[] =& $this->workbook->addformat($this->_getFormatArray($params));
    return (count($this->formats) - 1);
  }


  function setColor($index, $color) {
    if(!is_array($color)) {
      $temp = str_split($color, 2);
      $color[] = hexdec($temp[0]);
      $color[] = hexdec($temp[1]);
      $color[] = hexdec($temp[2]);
    }
    return $this->workbook->setCustomColor($index, $color[0], $color[1], $color[2]);
  }


  function MysqlDatetimeToExcel($datetime) {
    $tmp = explode(" ", $datetime);
    $date = explode("-", $tmp[0]);
    if(isset($tmp[1])) $time = explode(":", $tmp[1]);
    $date1 = GregorianToJD($date[1],$date[2],$date[0]);
    $epoch = $this->_GetExcelEpoch();
    $frac = (($time[0] * 60 * 60) + ($time[1] * 60) + $time[2])/(24*60*60);
    
    return ($date1 - $epoch + 2 + $frac);
  }


  function TimestampToExcel($timestamp) {
    return $this->MysqlDatetimeToExcel(date("d-m-Y H:i:s", $timestamp));
  }


  function write(&$worksheet, $row, $col, $token, $format = 0) {
    return $worksheet->write($row, $col, $token, $this->formats[$format]);
  }
  

  function OutputFile() {
    $this->workbook->Close();
    echo file_get_contents($this->workbook->filename, "rb");
  }

}
?>
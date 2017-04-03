<?php 
define("ACREATEXPORT_XLS_FORCE_STRING", "");
class AcreatExportXLS extends AcreatExport
{
	var $force_string = array();
	
	/* ---
	* EXPORT
	*/
	function export() 
	{
		$this->force_string = explode(",", ACREATEXPORT_XLS_FORCE_STRING);
		vendor("PhpWriteExcel/class.writeexcel_workbook.inc");
		vendor("PhpWriteExcel/class.writeexcel_worksheet.inc");
		
		$filename = sprintf("%s.%s.xls", $this->_id, time());
		$fname = tempnam(TMP, $filename);
		
		$workbook = &new writeexcel_workbook($fname);
		$worksheet = &$workbook->addworksheet();
		
		# Create a format for the column headings
		$header =& $workbook->addformat();
		$header->set_bold();
		$header->set_align("center");
		$header->set_bottom($bottom=1);
		
		if( $this->_cols ) {
			# Write out the cols
			$i=0;
			foreach($this->_cols as $id=>$col) {
				$worksheet->write_string(0, $i, $col, $header);
				$i++;
			}
		}
		
		$i=0;
		while($row = $this->_fetch() ) {
			$keys = array_keys($this->_cols);
			for($j=0; $j<count($keys); $j++) {
				$data = $this->format($row[$keys[$j]]);
				if( array_search($keys[$j], $this->force_string) !== false )
					$worksheet->write_string($i+1, $j, $data );
				else
					$worksheet->write($i+1, $j, $data );
			}
			$i++; 
		}
		
		$workbook->close();
		
		header("Content-Type: application/x-msexcel; name=\"$filename\"");
		header("Content-Disposition: inline; filename=\"$filename\"");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);	
	}
	/* ---
	* FORMAT
	* Vérifie et la nature des données, et reformat eventuellement
	*/	
	function format($data) 
	{
		// Date (yyyy-mm-dd)
		if( preg_match("/(\d{4})-(\d{2})-(\d{2})/",$data, $matches) ) 
			$data = sprintf('%s/%s/%s', $matches[3], $matches[2], $matches[1] );
			
		return $data;
	}
}
?>
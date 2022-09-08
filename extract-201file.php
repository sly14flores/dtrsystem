<?php

$eid = (isset($_GET['eid'])) ? $_GET['eid'] : 0;

$columns = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S");
$fields = array("A"=>"employee_fid","B"=>"deparment","C"=>"employee_lastname","D"=>"employee_firstname","E"=>"employee_middlename","F"=>"employee_gender","G"=>"employee_dob","H"=>"employee_address","I"=>"employee_contacts","J"=>"employee_position","K"=>"employee_attainment","L"=>"employee_eligibility","M"=>"employee_years","N"=>"employee_appointment","O"=>"employee_sss","P"=>"employee_gsis","Q"=>"employee_philhealth","R"=>"employee_hdmf","S"=>"employee_tin");

require_once 'Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("DTR System - 201 File")
							 ->setLastModifiedBy("DTR System - 201 File")
							 ->setTitle("201 File Extract")
							 ->setSubject("201 File Extract")
							 ->setDescription("201 File Extract")
							 ->setKeywords("201 File Extract")
							 ->setCategory("Reports");

require 'config.php';

$objPHPExcel->getDefaultStyle()->getFont()->setSize(14);

for ($n=0; $n<count($columns); ++$n) {
	$objPHPExcel->getActiveSheet()->getColumnDimension($columns[$n])->setAutoSize(true);
}

$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue("A1","Employee ID")
			->setCellValue("B1","Department")
			->setCellValue("C1","Last Name")
			->setCellValue("D1","First Name")
			->setCellValue("E1","Middle Name")
			->setCellValue("F1","Gender")
			->setCellValue("G1","Date of Birth")
			->setCellValue("H1","Address")
			->setCellValue("I1","Contact No(s)")
			->setCellValue("J1","Position")
			->setCellValue("K1","Educational Attainment")
			->setCellValue("L1","Eligibility")
			->setCellValue("M1","Years in service")
			->setCellValue("N1","Status of appointment")
			->setCellValue("O1","SSS")
			->setCellValue("P1","GSIS")
			->setCellValue("Q1","Philhealth")
			->setCellValue("R1","HDMF")
			->setCellValue("S1","TIN");				
			
			for ($n=0; $n<count($columns); ++$n) {
				$objPHPExcel->getActiveSheet()->getStyle($columns[$n]."1")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($columns[$n]."1")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($columns[$n]."1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($columns[$n]."1")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);				
			}
			
			$objPHPExcel->getActiveSheet()->getRowDimension("1")->setRowHeight(25);
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:S1')->getFill()
						->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()->setARGB('bdbdbd');
						
$c = 2;

$fullname = "";
$sql = "SELECT employee_fid, if(employee_dept = 0,'',(select dept_name from departments where dept_id = employee_dept)) deparment, employee_lastname, employee_firstname, employee_middlename, employee_gender, employee_dob, employee_address, employee_contacts, employee_position, employee_attainment, employee_eligibility, employee_years, employee_appointment, employee_sss, employee_gsis, employee_philhealth, employee_hdmf, employee_tin, concat(employee_lastname, ', ', employee_firstname, ' ', employee_middlename) full_name FROM employees WHERE employee_id = $eid";
db_connect();
	$rs = $db_con->query($sql);
	$rc = $rs->num_rows;
	if ($rc) {
		for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array(MYSQLI_ASSOC);
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$c", $rec[$fields['A']])
					->setCellValue("B$c", $rec[$fields['B']])
					->setCellValue("C$c", $rec[$fields['C']])
					->setCellValue("D$c", $rec[$fields['D']])
					->setCellValue("E$c", $rec[$fields['E']])
					->setCellValue("F$c", $rec[$fields['F']])
					->setCellValue("G$c", $rec[$fields['G']])
					->setCellValue("H$c", $rec[$fields['H']])
					->setCellValue("I$c", $rec[$fields['I']])
					->setCellValue("J$c", $rec[$fields['J']])
					->setCellValue("K$c", $rec[$fields['K']])
					->setCellValue("L$c", $rec[$fields['L']])
					->setCellValue("M$c", $rec[$fields['M']])
					->setCellValue("N$c", $rec[$fields['N']])
					->setCellValue("O$c", $rec[$fields['O']])
					->setCellValue("P$c", $rec[$fields['P']])
					->setCellValue("Q$c", $rec[$fields['Q']])
					->setCellValue("R$c", $rec[$fields['R']])
					->setCellValue("S$c", $rec[$fields['S']]);
					
		for ($n=0; $n<count($columns); ++$n) {
			$objPHPExcel->getActiveSheet()->getStyle($columns[$n].$c)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($columns[$n].$c)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($columns[$n].$c)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($columns[$n].$c)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		}					
		
		$objPHPExcel->getActiveSheet()->getRowDimension($c)->setRowHeight(25);		
		
		$c++;
		
		$fullname = $rec['full_name'];
		}
	}
db_close();
	
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Licenses');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);		

// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// $objWriter->save(str_replace('.php', '.xlsx', __FILE__));

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="201-file ' . $fullname . ' ' . date("Y-m-d") . '.xlsx"');
header('Cache-Control: max-age=0');

/*
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
*/

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>
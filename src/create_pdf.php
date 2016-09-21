<?php

require '../lib/fpdf/fpdf.php';
require 'login_check.php';

class PDF extends FPDF{

	function BasicTable($header, $data){
		$x1 = 70;
		$x2 = 205;
		
		$i = 0;
		$count = 0;
		foreach($data as $row){
			$this->Cell(100, 7, 'Tweet No. : '.(++$count),'',0,'L',0);
			$this->Ln();
			
			for($i=0; $i < count($row); $i++){
				
				$this->Cell($x1, 7, $header[$i],'LTR',0,'L',0);
				
				if($this->GetStringWidth($row[$i]) > $x2 || $header[$i]=='media' || $header[$i]=='links'){
					if($header[$i]=='media' || $header[$i]=='links'){
						
						if(count($row[$i]) == 0){
							$this->Cell($x2, 7, ' ', 'LTR',0,'L',0);
						}else{
							$this->Cell($x2, 7, $row[$i][0],'LTR',0,'L',0);
						}
						$this->Ln();
						
						for($p=1; $p < count($row[$i]); $p++){
							$this->Cell($x1, 7, ' ','LR',0,'L',0);
							$this->Cell($x2, 7, $row[$i][$p],'LR',0,'L',0);
							$this->Ln();
						}
					
					}else{
						$parts = ceil($this->GetStringWidth($row[$i]) / $x2);
						$sub_str_len = ceil(strlen($row[$i])/$parts);
						
						$sub_str_arr = array();
						
						for($p=0; $p < strlen($row[$i]); $p+=$sub_str_len){
							$sub_str_arr[] = substr($row[$i], $p, $sub_str_len);
						}
						
						$this->Cell($x2, 7, $sub_str_arr[0],'LTR',0,'L',0);
						$this->Ln();
						
						for($p=1; $p < count($sub_str_arr); $p++){
							$this->Cell($x1, 7, ' ','LR',0,'L',0);
							$this->Cell($x2, 7, $sub_str_arr[$p],'LR',0,'L',0);
							$this->Ln();
						}
					}
				}else{
					$this->Cell($x2, 7, $row[$i],'LTR',0,'L',0);
					$this->Ln();
				}
			}
			$this->Cell($x1+$x2, 7, '','T',0,'L',0);
			$this->Ln();
			$this->Ln();
		}
		
	}
	
}

if(verify_vars($_POST['pdf_filename'], $_POST['obj_var'])){
	$obj = json_decode($_POST['obj_var']);
	$filename = $_POST['pdf_filename'];
}else{
	header('Location: /index.php');
}

$pdf = new PDF('L','mm','A4');
$header = $obj->keys;
$data = $obj->values;
$pdf->SetFont('Arial','',12);
$pdf->AddPage();
$pdf->BasicTable($header,$data);
$pdf->Output('D', $filename.'.pdf');

function verify_vars(){
	foreach(func_get_args() as $arg){
		if(isset($arg) && !empty($arg)) continue;
		else return false;
	}
	return true;
}

?>
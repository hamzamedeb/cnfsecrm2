<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once dirname(__FILE__) . '/../viewers/ContentViewer.php';  

class Vtiger_PDF_EventsAttesContentViewer extends Vtiger_PDF_ContentViewer {

	protected $headerRowHeight = 8;
	protected $onSummaryPage   = false;  
 
	function __construct() {
		// NOTE: General A4 PDF width ~ 189 (excluding margins on either side)
			
		$this->cells = array( // Name => Width
			'Code'    => 30,
			'Name'    => 55,
			'Quantity'=> 20,
			'Price'   => 20,
			'Discount'=> 19,
			'Tax'     => 16,
			'Total'   => 30
		);
	}
	
	function initDisplay($parent) {

		$pdf = $parent->getPDF();
		$contentFrame = $parent->getContentFrame();
                //var_dump($contentFrame);
		$pdf->MultiCell($contentFrame->w, $contentFrame->h, "", 1, 'L', 0, 1, $contentFrame->x, $contentFrame->y);
		
		// Defer drawing the cell border later.
		if(!$parent->onLastPage()) {
			$this->displayWatermark($parent);
		}
		
		// Header	
		$offsetX = 0;
                $pdf->SetFont('','B');
		foreach($this->cells as $cellName => $cellWidth) {
			$cellLabel = ($this->labelModel)? $this->labelModel->get($cellName, $cellName) : $cellName;
			$pdf->MultiCell($cellWidth, $this->headerRowHeight, $cellLabel, 1, 'L', 0, 1, $contentFrame->x+$offsetX, $contentFrame->y);
			$offsetX += $cellWidth;
		}
		$pdf->SetFont('','');
		// Reset the y to use
		$contentFrame->y += $this->headerRowHeight;
	}
	
	function drawCellBorder($parent, $cellHeights=False) {		
		$pdf = $parent->getPDF();
		$contentFrame = $parent->getContentFrame();
		
		if(empty($cellHeights)) $cellHeights = array();

		$offsetX = 0;
		foreach($this->cells as $cellName => $cellWidth) {
			$cellHeight = isset($cellHeights[$cellName])? $cellHeights[$cellName] : $contentFrame->h;

			$offsetY = $contentFrame->y-$this->headerRowHeight;			
			
			$pdf->MultiCell($cellWidth, $cellHeight, "", 1, 'L', 0, 1, $contentFrame->x+$offsetX, $offsetY);
			$offsetX += $cellWidth;
		}
	}

	function display($parent) {
		$this->displayPreLastPage($parent);
		//$this->displayLastPage($parent);
	}

	function displayPreLastPage($parent) {

		$models = $this->contentModels;
                $totalModels = count($models);
		$pdf = $parent->getPDF();

		$parent->createPageAttestation();
	}

	function displayLastPage($parent) {
		// Add last page to take care of footer display
		if($parent->createLastPage()) {
			$this->onSummaryPage = false;
		}
	}

	function drawStatusWaterMark($parent) {
		$pdf = $parent->getPDF();
              
		$waterMarkPositions=array("30","180");
		$waterMarkRotate=array("45","50","180");

		$pdf->SetFont('Arial','B',50);
		$pdf->SetTextColor(230,230,230);
		$pdf->Rotate($waterMarkRotate[0], $waterMarkRotate[1], $waterMarkRotate[2]);
		$pdf->Text($waterMarkPositions[0], $waterMarkPositions[1], 'created');
		$pdf->Rotate(0);
		$pdf->SetTextColor(0,0,0);
	}
}
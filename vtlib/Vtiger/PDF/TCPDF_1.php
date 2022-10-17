<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'libraries/tcpdf/config/lang/eng.php';
require_once 'libraries/tcpdf/tcpdf.php';

class Vtiger_PDF_TCPDF extends TCPDF {

	protected $FontFamily;

	public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8') {
		parent::__construct($orientation, $unit, $format, $unicode, $encoding);
		$this->SetFont('','',10);
		$this->setFontFamily('times');
	}

	function getFontSize() {
		return $this->FontSizePt;
	}

	function setFontFamily($family) {
		$this->FontFamily = $family;
	}

	function GetStringHeight($sa,$w) {
		if(empty($sa)) return 0;
		
		$sa = str_replace("\r","",$sa);
		// remove the last newline
		if (substr($sa,-1) == "\n")
		$sa = substr($sa,0,-1);

		$blocks = explode("\n",$sa);
		$wmax = $w - (2 * $this->cMargin);

		$lines = 0;
		$spacesize = $this->GetCharWidth(32);
		foreach ($blocks as $block) {
			if (!empty($block)) {
				$words = explode(" ",$block);

				$cw = 0;
				for ($i = 0;$i < count($words);$i++) {
					if ($i != 0) $cw += $spacesize;

					$wordwidth = $this->GetStringWidth($words[$i]);
					$cw += $wordwidth;

					if ($cw > $wmax) { // linebreak
						$cw = $wordwidth;
						$lines++;
					}
				}
			}

			$lines++;
		}

		return ($lines * ($this->FontSize * $this->cell_height_ratio)) + 2;
	}

	function SetFont($family, $style='', $size='') {
		if($family == '') {
			$family = $this->FontFamily;
		}
		//Select a font; size given in points
		if ($size == 0) {
			$size = $this->FontSizePt;
		}
		// try to add font (if not already added)
		$fontdata =  $this->AddFont($family, $style);
		$this->FontFamily = $fontdata['family'];
		$this->FontStyle = $fontdata['style'];
		$this->CurrentFont = &$this->fonts[$fontdata['fontkey']];
		$this->SetFontSize($size);
	}
        
                                        /* wjalthecv2 */
        public function Footer() {
            $headerFrame_w = 189.99905555556;
            $w = 50;
            $h = 23;            
            //$this->Image("test/logo/partenaire.jpg", 6, 273, $w, $h);
            $this->SetFont('freeserif', '', 8);
            $footer = 'Organisme de formation professionnelle continue exonéré de TVA en vertu de l\'article 202A à 202D de l\'annexe II du CGI "et article 261.4.4 du code général des impôts';
            $contentHeight = $this->GetStringHeight($footer, $headerFrame_w);
            $this->MultiCell($headerFrame_w - 71, $contentHeight, $footer, 0, 'L', 0, 1, 20, 278);

            $footer = 'SARL au capital de 8000 euros - SIRET :482 379 302 00029N° TVA Intracommunautaire :8559A - Code NAF : 8559A TÈlÈphone : 01.84.16.38.25 TÈlÈcopie : 09.72.33.02.35
 N° de dÈclaration d\'acivitÈ : 11755161475 ';
            $contentHeight = $this->GetStringHeight($footer, $headerFrame_w);
            $this->MultiCell($headerFrame_w - 71, $contentHeight, $footer, 0, 'L', 0, 1, 20, 289);

            $displayFormat = ' %s';
            $contentHeight = $this->GetStringHeight($displayFormat, $headerFrame_w / 2.0);
            $this->SetFont('freeserif', 'B', 12);
            //$txt_page = 'Page ' . sprintf($displayFormat, $this->getPage());           
            $this->SetFont('freeserif', '', 10);
            $this->SetTextColor(0, 0, 0);
        }
}
?>
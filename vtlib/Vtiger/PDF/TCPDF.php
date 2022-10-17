<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */
require_once 'libraries/tcpdf/config/lang/eng.php';
require_once 'libraries/tcpdf/tcpdf.php';

/*
  uni_cnfsecrm - v2 - modif 100
  TCPDF => TCPDF2
  TCPDF_FONTS => TCPDF_FONTS2
 */

class Vtiger_PDF_TCPDF extends TCPDF2 {

    protected $FontFamily;

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8') {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding);
        $this->SetFont('', '', 10);
        $this->setFontFamily('times');
    }

    function getFontSize() {
        return $this->FontSizePt;
    }

    function setFontFamily($family) {
        $this->FontFamily = $family;
    }

    function GetStringHeight($sa, $w) {
        if (empty($sa))
            return 0;

        $sa = str_replace("\r", "", $sa);
        // remove the last newline
        if (substr($sa, -1) == "\n")
            $sa = substr($sa, 0, -1);

        $blocks = explode("\n", $sa);
        $wmax = $w - (2 * $this->cMargin);

        $lines = 0;
        $spacesize = $this->GetCharWidth(32);
        foreach ($blocks as $block) {
            if (!empty($block)) {
                $words = explode(" ", $block);

                $cw = 0;
                for ($i = 0; $i < count($words); $i++) {
                    if ($i != 0)
                        $cw += $spacesize;

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

    function SetFont($family, $style = '', $size = '') {
        if ($family == '') {
            $family = $this->FontFamily;
        }
        //Select a font; size given in points
        if ($size == 0) {
            $size = $this->FontSizePt;
        }
        // try to add font (if not already added)
        $fontdata = $this->AddFont($family, $style);
        $this->FontFamily = $fontdata['family'];
        $this->FontStyle = $fontdata['style'];
        $this->CurrentFont = &$this->fonts[$fontdata['fontkey']];
        $this->SetFontSize($size);
    }

    /* uni_cnfsecrm */

    /* public function Footer() {
      $y = 270;
      $x = 1;
      // font trebuchet simple
      $trebuchet = TCPDF_FONTS2::addTTFfont('test/font/Trebuchet.ttf', 'TrueTypeUnicode', '', 96);
      //font trebuchet italic
      $trebuchetmsitalic = TCPDF_FONTS2::addTTFfont('test/font/trebuchetmsitalic.ttf', 'TrueTypeUnicode', '', 96);
      //font trebuchet bold
      $trebuchetbd = TCPDF_FONTS2::addTTFfont('test/font/trebucbd.ttf', 'TrueTypeUnicode', '', 96);
      //font trebuchet bold/italic
      $trebuchetbi = TCPDF_FONTS2::addTTFfont('test/font/trebuchetbi.ttf', 'TrueTypeUnicode', '', 96);

      $this->SetTextColor(0, 0, 0);
      $ville = "PARIS";
      $code_postale = "75001";
      $adresse = "231 rue St Honoré";
      $nom = "C.N.F.S.E.";
      $this->SetFont($trebuchet, '', 7);

      $this->MultiCell(200, 5, $nom . ' ' . $adresse . ' ' . $code_postale . ' ' . $ville . " - SAS au capital de 8000 euros", 0, 'C', 0, 1, $x, $y);

      $siret = "482 379 302 00029";
      $n_tva = "FR59482379302";
      $code_naf = "8559A";
      $telephone = "01.84.16.38.25";
      $telecopie = "09.72.33.02.35";
      $num_declaration = "11755161475";

      $footer_info = "SIRET :" . $siret . " - N° TVA Intracommunautaire : " . $n_tva . " - Code NAF : " . $code_naf;
      $this->MultiCell(195, 5, $footer_info, 0, 'C', 0, 1, $x, $y + 5);

      $footer_n_decl = "11755161475";
      $this->MultiCell(200, 5, " Téléphone : " . $telephone . " - N° de déclaration d'activité : " . $num_declaration, 0, 'C', 0, 1, $x, $y + 10);
      } */

    /* uni_cnfsecrm - v2 - modif 185 - DEBUT */

    public function Footer() { 
        $y = 270;
        $x = 1;
        // font trebuchet simple
        $trebuchet = TCPDF_FONTS2::addTTFfont('test/font/Trebuchet.ttf', 'TrueTypeUnicode', '', 96);
        //font trebuchet italic
        $trebuchetmsitalic = TCPDF_FONTS2::addTTFfont('test/font/trebuchetmsitalic.ttf', 'TrueTypeUnicode', '', 96);
        //font trebuchet bold
        $trebuchetbd = TCPDF_FONTS2::addTTFfont('test/font/trebucbd.ttf', 'TrueTypeUnicode', '', 96);
        //font trebuchet bold/italic
        $trebuchetbi = TCPDF_FONTS2::addTTFfont('test/font/trebuchetbi.ttf', 'TrueTypeUnicode', '', 96);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont($trebuchet, '', 7);

        $this->MultiCell(200, 5, "C.N.F.S.E. - 231 rue St Honoré 75001 PARIS – 7 place Henri IV 94220 Charenton-le-Pont", 0, 'C', 0, 1, $x, $y);
        $this->MultiCell(195, 5, "SAS au capital de 8.000 euros - SIRET :482 379 302 00029 - Code NAF : 8559A", 0, 'C', 0, 1, $x, $y + 5);
        $this->MultiCell(200, 5, "Téléphone : 01.84.16.38.25 - N° de déclaration d'activité : 11755161475", 0, 'C', 0, 1, $x, $y + 10);
    }

    /* uni_cnfsecrm - v2 - modif 185 - FIN */
}

?>
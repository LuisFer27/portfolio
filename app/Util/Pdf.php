<?php
class UtilPdf extends TCPDF
{

    public $atr_name = '000.pdf';
    public $atr_title = '';
    public $atr_isAvailable = NULL;
    public $atr_pdf = NULL;
    public $atr_keywords = '';
    public $atr_creator = '';
    public $atr_author = '';
    public $atr_subject = '';
    public $atr_ttf = array();
    public $atr_colors = array(
        'green' => array(28, 186, 84),
        'black' => array(23, 39, 42),
        'red' => array(189, 37, 44)
    );

    public $x = 0;
    public $y = 0;



    public function __construct($cf)
    {
        $this->available();
        $app = Aif::$application;

        $this->atr_author = isset($cf['author']) ? $cf['author'] : 'PDF AIF/' . $app::$name;
        $this->atr_creator = isset($cf['creator']) ? $cf['Creator'] : 'PDF AIF/' . $app::$name;
        $this->atr_title = isset($cf['title']) ? $cf['title'] : '';
        $this->atr_subject = isset($cf['subject']) ? $cf['subject'] : '';
        $this->atr_keywords = isset($cf['keywords']) ? $cf['keywords'] : '';

        parent::__construct(PDF_PAGE_ORIENTATION, 'px', 'LETTER', true, 'UTF-8', false);
        $this->SetCreator($this->atr_creator);
        $this->SetAuthor(strtoupper($app::$name) . ' S.A. DE C.V.');
        $this->SetTitle($this->atr_title);
        $this->SetSubject($this->atr_subject);
        $this->SetKeywords($this->atr_keywords);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
    }

    public function available()
    {
        if ($this->atr_isAvailable === NULL) $this->atr_isAvailable = class_exists('TCPDF');
        if ($this->atr_isAvailable) return TRUE;
        die('Required class TCPDF...');
        return FALSE;
    }

    public function outContent($name)
    {
        $this->available();
        $this->Output($name, 'I');
    }

    public function h1($text = '', $x = 0, $y = 0)
    {
    }


    public function text2($x = 0, $y = 0, $text = '', $font = 'regular')
    {
        if (is_string($x)) return $this->text($this->x, $this->y, $x, $y);
        elseif (is_string($y)) return $this->text($x, $this->y, $y, $text);
    }

    public function addColor($name, $color)
    {
        $this->atr_colors[$name] = $color;
    }

    public function selColor($name, $color = NULL)
    {
        if (is_array($color)) $this->atr_colors[$name] = $color;
        if (isset($this->atr_colors[$name])) {
            list($R, $G, $B) = $this->atr_colors[$name];
            $this->SetTextColor($R, $G, $B);
        }
    }


    public function txt($x, $y, $text, $sz = 10, $color = 'black', $font = 'roboto', $spacing = 0, $stretching = 100)
    {
        $this->setFontStretching($stretching);
        $this->setFontSpacing($spacing);
        $this->selColor($color);
        $this->SetFont($font, '', $sz, '');
        $this->SetXY($x, $y);
        $this->writeHTML($text, true, 0, true, true);
    }

    public function cel($x, $y, $width, $text, $sz = 10, $color = 'black', $font = 'roboto', $align = 'justify')
    {
        $text = str_replace('<b>', '<b style="font-family:' . $font . ';">', $text);
        $text = '<table style="width:' . ($width + ($width * .25)) . 'px;" cellspacing="0" cellpadding="1" border="0">' .
            '<tr><td style="text-align:' . $align . ';">' . $text . '</td></tr></table>';
        $this->txt($x, $y, $text, $sz, $color, $font);
    }


    public function addSvg($image, $x = 0, $y = 0, $w = 0, $h = 0, $alpha = 1)
    {
        $this->SetAlpha($alpha);
        $this->ImageSVG($image, $x, $y, $w, $h);
        $this->SetAlpha(1);
    }

    public function selFont($name, $style = '', $size = 14)
    {
        if (isset($this->atr_ttf[$name])) {
            $this->SetFont($this->atr_ttf[$name], $style, $size, '');
        }
    }

    public function addTtf($file, $name = NULL, $style = '')
    {
        if (file_exists($file)) {
            $this->atr_ttf[$name] = TCPDF_FONTS::addTTFfont($file, '', '', 32);
        } else die('No existes: ' . $file);
    }

    public function dxSize($cz, $n = 0)
    {
        if (is_array($cz)) {
            $t = 0;
            $_cz = array($t);
            foreach ($cz as $c) $_cz[] = ($t += $c);
            return $_cz;
        } else {
            $cz = array();
            for ($x = 0; $x <= $n; $x++) $cz[] = $x / $n;
        }
        return $cz;
    }

    public function drawTable($col, $row, $x, $y, $w, $h, $sty = 'black', $cz = 0, $rz = 0)
    {
        $cz = $this->dxSize($cz, $col);
        $rz = $this->dxSize($rz, $row);
        $style =  array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        for ($c = 0; $c <= $col; $c++) {
            $cx = $cz[$c] * $w;
            $this->Line($x + $cx, $y, $x + $cx, $y + $h, $style);
            for ($r = 0; $r <= $row; $r++) {
                $ry = $rz[$r] * $h;
                $this->Line($x, $y + $ry, $x + $w, $y + $ry, $style);
            }
        }
    }
}

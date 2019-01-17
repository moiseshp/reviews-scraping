<?php

namespace App\Console\Commands\Scraping;

trait ScrapingHelper
{
    public function str_clean( $s )
    {
        $s = str_replace("\r\n","",$s);
        $s = preg_replace('!\s+!',' ',$s);
        return trim( $s );
    }

    public function standardize_rating( $rating, $bestRating )
    {
        $standard = 10;
        $rating = ($standard * $rating) / $bestRating;
        return $rating;
    }

    public function format_date( $d )
    {
        $s = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre','del','de'];
        $r = ['01','02','03','04','05','06','07','08','09','10','11','12','',''];
        $d = str_replace($s,$r,$d);
        $d = $this->str_clean( $d );
        $d = str_replace(' ','-',$d);
        return $d;
    }
}

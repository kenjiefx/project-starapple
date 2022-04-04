<?php

$basePM = 2.903;
$fontSize = 6.432;
$breakpoints = ['mobile',
                '@media screen and (max-width: 768px) and (min-width: 601px)',
                '@media screen and (max-width: 992px) and (min-width: 769px)',
                '@media screen and (max-width: 992px) and (min-width: 769px)',
                '@media screen and (max-width: 1200px) and (min-width: 993px)',
                '@media only screen and (min-width: 1201px)'];

foreach ($breakpoints as $breakpoint) {
    if ($breakpoint!=='mobile') {
        echo $breakpoint.'{<br>';
    }

    $variant = 1;
    $tmpBasePM = $basePM;
    $tmpFontSize = $fontSize;

    while ($variant<21) {
        echo ".padding-${variant} {";
            echo " padding:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".padding-top-${variant} {";
            echo " padding-top:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".padding-bottom-${variant} {";
            echo " padding-bottom:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".padding-left-${variant} {";
            echo " padding-left:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".padding-right-${variant} {";
            echo " padding-right:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".padding-top-bottom-${variant} {";
            echo " padding-top:${tmpBasePM}px; ";
            echo " padding-bottom:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".padding-right-left-${variant} {";
            echo " padding-right:${tmpBasePM}px; ";
            echo " padding-left:${tmpBasePM}px; ";
        echo "}<br>";

        echo ".margin-${variant} {";
            echo " margin:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".margin-top-${variant} {";
            echo " margin-top:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".margin-bottom-${variant} {";
            echo " margin-bottom:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".margin-left-${variant} {";
            echo " margin-left:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".margin-right-${variant} {";
            echo " margin-right:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".margin-top-bottom-${variant} {";
            echo " margin-top:${tmpBasePM}px; ";
            echo " margin-bottom:${tmpBasePM}px; ";
        echo "}<br>";
        echo ".margin-right-left-${variant} {";
            echo " margin-right:${tmpBasePM}px; ";
            echo " margin-left:${tmpBasePM}px; ";
        echo "}<br>";

        echo '<br>';
        echo ".font-size-${variant} {";
            echo " font-size:${tmpFontSize}px; ";
        echo "}<br>";

        $variant++;
        $tmpBasePM = round($tmpBasePM+($tmpBasePM*0.134),4);
        $tmpFontSize = round($tmpFontSize+($tmpFontSize*0.104),4);
    }
    $basePM = $basePM+0.639;
    $fontSize = $fontSize+0.239;
    if ($breakpoint!=='mobile') {
        echo '}';
    }
    echo '<br><br>';
}

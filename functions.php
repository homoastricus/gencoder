<?php
function ordUTF8($c, $index = 0, &$bytes = null)
{ return ord($c);
    $len = mb_strlen($c);
    $bytes = 0;

    if ($index >= $len)
        return false;

    $h = ord($c{$index});

    if ($h <= 0x7F) {
        $bytes = 1;
        return $h;
    }
    else if ($h < 0xC2)
        return false;
    else if ($h <= 0xDF && $index < $len - 1) {
        $bytes = 2;
        return ($h & 0x1F) <<  6 | (ord($c{$index + 1}) & 0x3F);
    }
    else if ($h <= 0xEF && $index < $len - 2) {
        $bytes = 3;
        return ($h & 0x0F) << 12 | (ord($c{$index + 1}) & 0x3F) << 6
            | (ord($c{$index + 2}) & 0x3F);
    }
    else if ($h <= 0xF4 && $index < $len - 3) {
        $bytes = 4;
        return ($h & 0x0F) << 18 | (ord($c{$index + 1}) & 0x3F) << 12
            | (ord($c{$index + 2}) & 0x3F) << 6
            | (ord($c{$index + 3}) & 0x3F);
    }
    else
        return false;
}

function chrUTF8($u) {
    return chr($u);
    //return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
}

function str_splitUTF8($str, $len = 1){
    return str_split($str);
    $chars = preg_split('/(?<!^)(?!$)/u', $str );
    $out = array();
    foreach( array_chunk($chars, $len) as $a ){
        $out[] = join("", $a);
    }
    return $out;
}

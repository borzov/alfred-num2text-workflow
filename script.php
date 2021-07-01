<?php

$type = $argv[1];
$text = $argv[2];

/**
 * Возвращает сумму прописью
 * @uses morph(...)
 */
function num2str($num, $ucfirst = false)
{
    $nul = 'ноль';
    $ten = array(
        array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять')
    );
    $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
    $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
    $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
    $unit = array(
        array('копейка' , 'копейки',   'копеек',     1),
        array('рубль',    'рубля',     'рублей',     0),
        array('тысяча',   'тысячи',    'тысяч',      1),
        array('миллион',  'миллиона',  'миллионов',  0),
        array('миллиард', 'миллиарда', 'миллиардов', 0),
    );
 
    list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub) > 0) {
        foreach (str_split($rub, 3) as $uk => $v) {
            if (!intval($v)) continue;
            $uk = sizeof($unit) - $uk - 1;
            $gender = $unit[$uk][3];
            list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
            // mega-logic
            $out[] = $hundred[$i1]; // 1xx-9xx
            if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; // 20-99
            else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; // 10-19 | 1-9
            // units without rub & kop
            if ($uk > 1) $out[] = morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
        }
    } else {
        $out[] = $nul;
    }
    $out[] = morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
    $out[] = $kop . ' ' . morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
    $result = trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    if ($ucfirst) {
        $result = mb_strtolower($result, 'UTF-8');
        $result = mb_strtoupper(mb_substr($result, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($result, 1, null,'UTF-8');
    }
    return $result;
}
 
/**
 * Склоняем словоформу
 */
function morph($n, $f1, $f2, $f5) 
{
    $n = abs(intval($n)) % 100;
    if ($n > 10 && $n < 20) return $f5;
    $n = $n % 10;
    if ($n > 1 && $n < 5) return $f2;
    if ($n == 1) return $f1;
    return $f5;
}

/**
 * Расчтываем НДС
 */
function nds($sum, $tax = 20) 
{
    preg_replace('~\D+~','', $sum);
    preg_replace('~\D+~','', $tax);
    $vat = round($sum / ($tax + 100) * $tax);
    return sprintf("%d руб. (%s), в т.ч. НДС (%d%%) %d руб. (%s)", $sum, num2str($sum, true), $tax, $vat, num2str($vat, true));
}

switch ($type) {
  case 'n2t':
    $output = num2str($text);
    break;
  case 'nds':
    $output = nds($text);
    break;
}
 
echo $output;
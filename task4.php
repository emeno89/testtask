<?php

echo sumLargeNumbers('2378', '78656743');

/**
 * Функция складывает 2 больших числа, представленных строками
 *
 * Сложение осуществляется столбиком до полного урезания длины строк справа налево + вычисляется остаток
 *
 * @param string $number1
 * @param string $number2
 * @return string
 */
function sumLargeNumbers(string $number1, string $number2): string
{
    $ost = 0;
    $result = '';
    do {
        $sym1 = intval(substr($number1, strlen($number1) - 1, 1));
        $sym2 = intval(substr($number2, strlen($number2) - 1, 1));

        $sumDigits = $sym1 + $sym2;

        $digit = $sumDigits % 10 + $ost;

        $result = $digit.$result;

        $ost = intval($sumDigits / 10);

        $number1 = substr($number1, 0, strlen($number1) - 1);
        $number2 = substr($number2, 0, strlen($number2) - 1);

    } while (!empty($number1) || !empty($number2) || $ost);

    return $result;
}
<?php

require __DIR__ . '/vendor/autoload.php';

$type = $argv[1];
$text = $argv[2];

// Удаляем все, кроме цирф
preg_replace('/[^0-9]/', '', $value);

// Инициализируем текстовый процессов
$processor = new \Borzov\NumToText\TextProcessor();

// Определяем необходимый тип действий
switch ($type) {
    case 'n2t':
        $output = $processor->num2str($text);
        break;
    case 'nds':
        $output = $processor->nds($text);
        break;
}

echo $output;
<?php

require 'vendor/autoload.php';

$file = 'export.csv';
$path = getcwd();

$data = [
    [
        'column1' => 1,
        'column2' => 1,
    ],
    [
        'column1' => 2,
        'column2' => 2,
    ],
];

$csv = new \Dakov\CSV($path);

$csv->setFile($file);
$csv->create($data);

$data = [
    [
        'column1' => 3,
        'column2' => 3,
    ],
    [
        'column1' => 4,
        'column2' => 4,
    ],
    [
        'column1' => 5,
        'column2' => 5,
    ],
];

$csv->append($data);

print_r($csv->read());
print_r($csv->readReverse());
print_r($csv->readReverse(2));

//$csv->download();

unlink($csv->getFile());


#CSV tool
This is a very powerful tool for reading, creating, updating or downloading a csv file.
Also you can read file from the end.

##Installation
The preferred way to install this tool is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist stdakov/csv "*"
```

or add

```
"stdakov/csv": "*"
```


##Usage

```php
require 'vendor/autoload.php';
```


Optional:
if you want you can use custom filename. 
It there is no custom filename it will create file with name date("Y-m-d_H:i:s") . '_' . export.csv 

```php
$file = 'export.csv'; //It is optional
```

Also you can show in witch folder file will be saved.

```php
$path = 'somePath'; //It is optional
```

Create instance

```php
$csv = new \Dakov\CSV();
```

or with custom file and path

```php
$csv = new \Dakov\CSV($path);
$csv->setFile($file);
```

Example data for insert:

```php
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
```

Insert and create file with data

```php
$csv->create($data);
```

Example data for append:

```php
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
```

Append some data to the file:

```php
$csv->append($data);
```

Return assoc array with all data:

```php
print_r($csv->read());
```

Return assoc array with all reverse data: 

```php
print_r($csv->readReverse());
```

You can set line limit (it is optional)

```php
print_r($csv->readReverse(2));
```

Download file

```php
$csv->download();
```

also you can delete file after download 

```php
$csv->download(true);
```

The MIT License (MIT)
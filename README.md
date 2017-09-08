# EasyExpress ![CI status](https://travis-ci.org/royen-go/EasyExpress.svg?branch=master)

EasyExpress is a PHP library for use ShunFeng APIs.

## Requirement

1. PHP >= 5.5.9
2. PHP cURL 扩展
3. PHP OpenSSL 扩展

> SDK 对所使用的框架并无特别要求

## Installation

```shell
composer require "easyexpress/shunfeng:~1.0" -vvv

```

## Usage

基本使用:

```php

$config = [
    'debug' => true,
    'appID' => '000***11',
    'appKey' => 'AC9DA1B7452***775118CA8DB1237431',
    'custID' => '7550***174',
    'log' => [
        'level' => 'debug',
        'file' => '/tmp/express.log'
    ]

];

$app = new \EasyExpress\Foundation\Application($config);

$order = $app->order;

$order->queryProductAdditional();

```


## Documentation

> 强烈建议看懂顺丰文档后再来使用本 SDK。
> https://open.sf-express.com/doc/sf_openapi_document_V1.pdf


## License

MIT


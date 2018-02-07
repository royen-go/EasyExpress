# EasyExpress ![CI status](https://travis-ci.org/royen-go/EasyExpress.svg?branch=master)

顺丰sdk 
基于([royen-go/EasyExpress](https://github.com/royen-go/EasyExpress))
对应官方文档：[https://open.sf-express.com/doc/sf_openapi_document_V1.pdf](https://open.sf-express.com/doc/sf_openapi_document_V1.pdf)

SDK QQ群：665052579
## Requirement
1. PHP >= 5.5.9
2. PHP cURL 扩展
3. PHP OpenSSL 扩展
> SDK 对所使用的框架并无特别要求

## Installation
```shell
composer require usails/shunfeng -vvv
```

## Usage
基本使用:（以附加服务查询为例）
`
$config = [
    'debug' => true,
    'appID' => '000***11',
    'appKey' => 'AC9DA1B7452***775118CA8DB1237431',
    'custID' => '7550***174',
    'mode' = 'dev',
    'log' => [
        'level' => 'debug',
        'file' => '/tmp/express.log'
    ]
];
$app = new \EasyExpress\Foundation\Application($config);
$order = $app->order;
$order->queryProductAdditional();
`

## Documentation

[wiki](https://github.com/royen-go/EasyExpress/wiki)

> 强烈建议看懂顺丰文档后再来使用本 SDK。
> https://open.sf-express.com/doc/sf_openapi_document_V1.pdf

## License

MIT


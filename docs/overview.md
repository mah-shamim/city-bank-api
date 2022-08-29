# API Overviews

## Revision history
| No  |  Service  |  Description  |    Version  |
|----:|-----------|---------------|:-----------:|
|1| doAuthenticate | Do authenticate service will provide you the access token| 2.0 | 
|2| doTransfer | Do transfer service will help you to send a new transaction|2.0|
|3| getTnxStatus | Get transaction status service will help you to get the transaction status|2.0| 
|4| doAmendmentOrCancel | Do amendment or cancel service will help you to send the transaction cancel/amendment request|2.0|
|5| getBalance |Get balance service will help to know the available balance|2.0| 
|6| bkashCustomerValidation |bKash customer validation service will help you to validate the beneficiary bkash number before send the bkash transaction|2.1| 
|7| doBkashTransfer |Do bKash transfer service call will help you to send a bkash transaction|2.1| 
|8| getBkashTransferStatus |This service call will provide you the bkash transaction status|2.1|

## Package Methods

All services provided by city bank api area created. form simplification you don't have to "**doAuthenticate**" service
this will be call inside other services to get auth token.

### doAuthenticate

Do authenticate service will provide you the access token by providing following parameter value.

### Parameters
|Parameters|Description|API Presence|Package Presence|
|----------|-----------|:------:|:------:|
|username | API Username | mandatory | optional (default from config) |
|password | API Password | mandatory | optional (default from config) |
|exchangeCompany | Exchange Company Name | mandatory | optional (default from config) |


### Uses

```php
<?php 
//Laravel
$client = \MahShamim\CityBank\Facades\CityBank::init()->doAuthenticate();

//Non-Laravel
$client = new \MahShamim\CityBank\CityBank();
$client->doAuthenticate('username', 'password', 'exchange_company');
```

### Response
```php
<?php
$token = $client->token();
hpflzoIqnu16062020294920003624ieS
```




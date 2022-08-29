# API Overviews

All services provided by city bank api area created. form simplification you don't have to "**doAuthenticate**" service
this will be call inside other services to get auth token.

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


## doTransfer

Do transfer service will help you to send a new transaction by providing following parameter values.

#### Parameters

|Field|Description|Presence|
|-----|-----------|--------|
| issue_date | Date of issue | Mandatory|
| reference_no | Transaction Reference no | Mandatory |
| pin_no | Pin number of the transaction | Not mandatory |
| remitter_name | Remitter Name | Mandatory |
| beneficiary_name | Beneficiary Name | Mandatory |
| beneficiary_account_no | Beneficiary Account No. | Mandatory for CBL A/C pay |
| beneficiary_bank_account_type | Beneficiary Account Type | Mandatory for CBL A/C pay |
| beneficiary_bank_name | Beneficiary Bank Name | Mandatory for CBL A/C pay |
| beneficiary_bank_branch_name | Beneficiary Bank Branch Name | Mandatory for CBL A/C pay |
| beneficiary_address | Beneficiary address | Not Mandatory |
| beneficiary_id_no | Beneficiary user id no | Not Mandatory | 
| branch_routing_number | Routing No. | Mandatory for CBL A/C pay |
| amount_in_taka | Amount in BDT. | Mandatory (only digit) |
| purpose_of_payment | Purpose of the payment | Mandatory |
| issuing_country | Issuing country name(ISO ALPHA-2 Code) | Mandatory |
| remitter_code | Remitter code | Not Mandatory | 
| special_instruction |  Special Instruction of the transaction | Not Mandatory |
| mode_of_payment | Payment mode (Cash, CBL Account, Other Bank) | 1=Cash<br/>2=CBL Account<br/>3=Other Bank | 
| beneficiary_bank_code | Beneficiary Bank Code | As per Bank List provided| 
| beneficiary_city | Beneficiary City | Mandatory |
| beneficiary_id_type | Beneficiary Id Type | Not Mandatory|
|beneficiary_mobile_phone_no |Beneficiary Phone |Not Mandatory |
|remitter_address |Remitter Address |Not Mandatory|
|remitter_id_passport_no |Remitter ID Number |Mandatory|
|remitterIDType |Remitter ID Type |1=International Passport<br/>2=Work Permit<br/>3=Identification ID<br/>4=Social Security<br/>5=Residence Permit<br/>9=Others |
|remitter_mobile_no |Remitter phone number |Not mandatory|

#### Uses

```php
$data = [

];

$reference_no = '123456789';

$response = $client->doTransfer($reference_no, $data)->execute();
```

## getTnxStatus

Get transaction status service will help you to get the transaction status.

#### Parameters

|Field|Description|Presence|
|-----|-----------|--------|
| reference_no | Transaction Reference no | Mandatory |

#### Uses

```php
$reference_no = '123456789';

$response = $client->getTnxStatus($reference_no)->execute();
```

## doAmendmentOrCancel

Do amendment or cancel service will help you to send the transaction cancel/amendment request.

#### Parameters

|Field|Description|Presence|
|-----|-----------|--------|
| reference_no | Transaction Reference no | Mandatory |
| amend_query | Transaction Cancellation reason in details | Not Mandatory |

#### Uses

```php
$reference_no = '123456789';

$amend_query = 'testing purpose only';

$response = $client->doAmendmentOrCancel($reference_no, $amend_query)->execute();
```

## getBalance

Get balance service will help to know the available balance.

#### Uses

```php
$response = $client->getBalance()->execute();
```

## bkashCustomerValidation

bKash customer validation service will help you to validate the beneficiary bkash number before send the transaction.

#### Parameters

|Field|Description|Presence|
|-----|-----------|--------|
| mobile_number | Bkash A/C mobile number | Mandatory|
| full_name | Bkash A/C holder full name | Not Mandatory |

#### Uses

```php
$data = [

];

$mobile_no = '+8801123456789';
$full_name= "Unknown Person";

$response = $client->bkashCustomerValidation($mobile_no, $full_name)->execute();
```

## doBkashTransfer

Do bKash transfer service will help you to send a bkash transaction.

#### Parameters

|Field|Description|Presence|
|-----|-----------|--------|
| issue_date | Date of issue | Mandatory|
| reference_no | Transaction Reference no | Mandatory |
| pin_no | Pin number of the transaction | Not mandatory |
| remitter_name | Remitter Name | Mandatory |
| beneficiary_name | Beneficiary Name | Mandatory |
| beneficiary_account_no | Beneficiary Account No. | Mandatory for CBL A/C pay |
| beneficiary_bank_account_type | Beneficiary Account Type | Mandatory for CBL A/C pay |
| beneficiary_bank_name | Beneficiary Bank Name | Mandatory for CBL A/C pay |
| beneficiary_bank_branch_name | Beneficiary Bank Branch Name | Mandatory for CBL A/C pay |
| beneficiary_address | Beneficiary address | Not Mandatory |
| beneficiary_id_no | Beneficiary user id no | Not Mandatory | 
| branch_routing_number | Routing No. | Mandatory for CBL A/C pay |
| amount_in_bdt | Amount in BDT. | Mandatory (only digit) |
| purpose_of_payment | Purpose of the payment | Mandatory |
| issuing_country | Issuing country name(ISO ALPHA-2 Code) | Mandatory |
| remitter_code | Remitter code | Not Mandatory | 
| special_instruction |  Special Instruction of the transaction | Not Mandatory |
| mode_of_payment | Payment mode (Cash, CBL Account, Other Bank) | 1=Cash<br/>2=CBL Account<br/>3=Other Bank | 
| beneficiary_bank_code | Beneficiary Bank Code | As per Bank List provided| 
| beneficiary_city | Beneficiary City | Mandatory |
| beneficiary_id_type | Beneficiary Id Type | Not Mandatory|
|beneficiary_mobile_phone_no |Beneficiary Phone |Not Mandatory |
|remitter_address |Remitter Address |Not Mandatory|
|remitter_id_passport_no |Remitter ID Number |Mandatory|
|remitterIDType |Remitter ID Type |1=International Passport<br/>2=Work Permit<br/>3=Identification ID<br/>4=Social Security<br/>5=Residence Permit<br/>9=Others |
|remitter_mobile_no |Remitter phone number |Not mandatory|

#### Uses

```php
$data = [

];

$reference_no = '123456789';

$response = $client->doBkashTransfer($reference_no, $data)->execute();
```



## getBkashTnxStatus

This service call will provide you the bkash transaction status.

#### Parameters

|Field|Description|Presence|
|-----|-----------|--------|
| reference_no | Transaction Reference no | Mandatory |

#### Uses

```php
$reference_no = '123456789';

$response = $client->getBkashTnxStatus($reference_no)->execute();
```

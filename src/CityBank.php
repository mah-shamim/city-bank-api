<?php

namespace MahShamim\CityBank;

use Illuminate\Support\Facades\Log;

class CityBank
{
    /**
     * CityBank API configuration.
     *
     * @var array
     */
    private $config;

    /**
     * CityBank API Url.
     *
     * @var string
     */
    private $apiUrl;

    private $status = 'sandbox';

    /**
     * CityBankApiService constructor.
     */
    public function __construct()
    {
        $this->config = config('city-bank');
        if ($this->config['mode'] === 'sandbox') {
            $this->apiUrl = 'https://'.$this->config[$this->status]['app_host'].'/nrb_api_test/dynamicApi.php?wsdl';
            $this->status = 'sandbox';
        } else {
            $this->apiUrl = 'https://'.$this->config[$this->status]['app_host'].'/dynamicApi.php?wsdl';
            $this->status = 'live';
        }
    }

    /**
     * Do authenticate service will provide you the access token by providing following parameter value
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function doAuthenticate()
    {
        $return = 'AUTH_FAILED';
        $xml_string = '
            <auth_info xsi:type="urn:auth_info">
                <username xsi:type="xsd:string">'.$this->config[$this->status]['username'].'</username>
                <password xsi:type="xsd:string">'.$this->config[$this->status]['password'].'</password>
                <exchange_company xsi:type="xsd:string">'.$this->config[$this->status]['exchange_company'].'</exchange_company>
            </auth_info>
        ';
        $soapMethod = 'doAuthenticate';
        $response = $this->connectionCheck($xml_string, $soapMethod);
        $returnValue = json_decode($response->doAuthenticateResponse->Response, true);
        if ($returnValue['message'] == 'Successful') {
            $return = $returnValue['token'];
        }

        return $return;
    }

    /**
     * Do transfer service will help you to send a new transaction by providing following parameter value
     *
     * @param $inputData
     * @return mixed
     *
     * @throws \Exception
     */
    public function doTransfer($inputData)
    {
        $doAuthenticate = $this->doAuthenticate();
        if ($doAuthenticate != 'AUTH_FAILED' || $doAuthenticate != null) {
            if ($inputData->bank_id == 17) {
                $mode_of_payment = 'CBL Account';
            } else {
                $mode_of_payment = 'Other Bank';
            }
            if ($inputData->recipient_type_name == 'Cash') {
                $mode_of_payment = 'Cash';
            }
            if ($inputData->recipient_type_name == 'Cash Pickup') {
                $mode_of_payment = 'Cash';
            }
            $xml_string = '
                <Transaction xsi:type="urn:Transaction">
                    <token xsi:type="xsd:string">'.$doAuthenticate.'</token>
                    <reference_no xsi:type="xsd:string">'.$inputData->reference_no.'</reference_no>
                    <remitter_name xsi:type="xsd:string">'.$inputData->sender_first_name.'</remitter_name>
                    <remitter_code xsi:type="xsd:string">'.$inputData->sender_mobile.'</remitter_code>
                    <remitter_iqama_no xsi:type="xsd:string"></remitter_iqama_no>
                    <remitter_id_passport_no xsi:type="xsd:string">'.$inputData->sender_id_number.'</remitter_id_passport_no>
                    <issuing_country xsi:type="xsd:string">'.$inputData->sender_id_issue_country.'</issuing_country>
                    <beneficiary_name xsi:type="xsd:string">'.((isset($inputData->receiver_first_name) ? $inputData->receiver_first_name : null).(isset($inputData->receiver_middle_name) ? ' '.$inputData->receiver_middle_name : null).(isset($inputData->receiver_last_name) ? ' '.$inputData->receiver_last_name : null)).'</beneficiary_name>
            ';
            if ($mode_of_payment != 'Cash') {
                $xml_string .= '
                        <beneficiary_account_no xsi:type="xsd:string">'.$inputData->bank_account_number.'</beneficiary_account_no>
                        <beneficiary_bank_account_type xsi:type="xsd:string">Savings</beneficiary_bank_account_type>
                        <beneficiary_bank_name xsi:type="xsd:string">'.$inputData->bank_name.'</beneficiary_bank_name>
                        <beneficiary_bank_branch_name xsi:type="xsd:string">'.$inputData->bank_branch_name.'</beneficiary_bank_branch_name>
                        <branch_routing_number xsi:type="xsd:string">'.(isset($inputData->location_routing_id[1]->bank_branch_location_field_value) ? $inputData->location_routing_id[1]->bank_branch_location_field_value : null).'</branch_routing_number>
                ';
            }
            $xml_string .= '
                    <amount_in_taka xsi:type="xsd:string">'.$inputData->transfer_amount.'</amount_in_taka>
                    <purpose_of_payment xsi:type="xsd:string">'.$inputData->purpose_of_remittance.'</purpose_of_payment>
                    <beneficiary_mobile_phone_no xsi:type="xsd:string">'.$inputData->receiver_contact_number.'</beneficiary_mobile_phone_no>
                    <beneficiary_id_type xsi:type="xsd:string"></beneficiary_id_type>
                    <pin_no xsi:type="xsd:string"></pin_no>
                    <remitter_address xsi:type="xsd:string">'.$inputData->sender_address.'</remitter_address>
                    <remitter_mobile_no xsi:type="xsd:string">'.$inputData->sender_mobile.'</remitter_mobile_no>
                    <beneficiary_address xsi:type="xsd:string">'.$inputData->receiver_address.'</beneficiary_address>
                    <beneficiary_id_no xsi:type="xsd:string"></beneficiary_id_no>
                    <special_instruction xsi:type="xsd:string">NA</special_instruction>
                    <mode_of_payment xsi:type="xsd:string">'.$mode_of_payment.'</mode_of_payment>
                    <issue_date xsi:type="xsd:string">'.date('Y-m-d', strtotime($inputData->created_date)).'</issue_date>
                    <!--Optional:-->
                    <custom_field_name_1 xsi:type="xsd:string">?</custom_field_name_1>
                    <custom_field_value_1 xsi:type="xsd:string">?</custom_field_value_1>
                    <custom_field_name_2 xsi:type="xsd:string">?</custom_field_name_2>
                    <custom_field_value_2 xsi:type="xsd:string">?</custom_field_value_2>
                    <custom_field_name_3 xsi:type="xsd:string">?</custom_field_name_3>
                    <custom_field_value_3 xsi:type="xsd:string">?</custom_field_value_3>
                    <custom_field_name_4 xsi:type="xsd:string">?</custom_field_name_4>
                    <custom_field_value_4 xsi:type="xsd:string">?</custom_field_value_4>
                    <custom_field_name_5 xsi:type="xsd:string">?</custom_field_name_5>
                    <custom_field_value_5 xsi:type="xsd:string">?</custom_field_value_5>
                    <custom_field_name_6 xsi:type="xsd:string">?</custom_field_name_6>
                    <custom_field_value_6 xsi:type="xsd:string">?</custom_field_value_6>
                    <custom_field_name_7 xsi:type="xsd:string">?</custom_field_name_7>
                    <custom_field_value_7 xsi:type="xsd:string">?</custom_field_value_7>
                    <custom_field_name_8 xsi:type="xsd:string">?</custom_field_name_8>
                    <custom_field_value_8 xsi:type="xsd:string">?</custom_field_value_8>
                    <custom_field_name_9 xsi:type="xsd:string">?</custom_field_name_9>
                    <custom_field_value_9 xsi:type="xsd:string">?</custom_field_value_9>
                    <custom_field_name_10 xsi:type="xsd:string">?</custom_field_name_10>
                    <custom_field_value_10 xsi:type="xsd:string">?</custom_field_value_10>
                </Transaction>
            ';
            $soapMethod = 'doTransfer';
            $response = $this->connectionCheck($xml_string, $soapMethod);
            if (isset($response) && $response != false && $response != null) {
                $returnValue = json_decode($response->doTransferResponse->Response, true);
            } else {
                $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
            }
        } else {
            $returnValue = ['message' => 'AUTH_FAILED INVALID USER INFORMATION', 'status' => 103];
        }

        return $returnValue;
    }

    /**
     * Get transaction status service will help you to get the transaction status
     *
     * @param $inputs_data
     * reference_no like system transaction number
     * @return mixed
     *
     * @throws \Exception
     */
    public function getTnxStatus($inputs_data)
    {
        $doAuthenticate = $this->doAuthenticate();
        if ($doAuthenticate != 'AUTH_FAILED' || $doAuthenticate != null) {
            $xml_string = '
                <transaction_status xsi:type="urn:transaction_status">
                    <!--You may enter the following 2 items in any order-->
                    <token xsi:type="xsd:string">'.$doAuthenticate.'</token>
                    <reference_no xsi:type="xsd:string">'.$inputs_data['reference_no'].'</reference_no>
                </transaction_status>
            ';
            $soapMethod = 'getTnxStatus';
            $response = $this->connectionCheck($xml_string, $soapMethod);
            if (isset($response) && $response != false && $response != null) {
                $returnValue = json_decode($response->getTnxStatusResponse->Response, true);
            } else {
                $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
            }
        } else {
            $returnValue = ['message' => 'AUTH_FAILED INVALID USER INFORMATION', 'status' => 103];
        }

        return $returnValue;
    }

    /**
     * Do amendment or cancel service will help you to send the transaction cancel/amendment request
     *
     * @param $inputData
     * reference_no like system transaction number, amend_query like cancel/amendment
     * @return mixed
     *
     * @throws \Exception
     */
    public function doAmendmentOrCancel($inputData)
    {
        $doAuthenticate = $this->doAuthenticate();
        if ($doAuthenticate != 'AUTH_FAILED' || $doAuthenticate != null) {
            $xml_string = '
                <txn_amend_cancel xsi:type="urn:txn_amend_cancel">
                    <!--You may enter the following 3 items in any order-->
                    <token xsi:type="xsd:string">'.$doAuthenticate.'</token>
                    <reference_no xsi:type="xsd:string">'.$inputData['reference_no'].'</reference_no>
                    <amend_query xsi:type="xsd:string">'.$inputData['amend_query'].'</amend_query>
                </txn_amend_cancel>
            ';
            $soapMethod = 'doAmendmentOrCancel';
            $response = $this->connectionCheck($xml_string, $soapMethod);
            if (isset($response) && $response != false && $response != null) {
                $returnValue = json_decode($response->doAmendmentOrCancelResponse->Response, true);
            } else {
                $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
            }
        } else {
            $returnValue = ['message' => 'AUTH_FAILED INVALID USER INFORMATION', 'status' => 103];
        }

        return $returnValue;
    }

    /**
     * Get balance service will help to know the available balance
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getBalance()
    {
        $doAuthenticate = $this->doAuthenticate();
        if ($doAuthenticate != 'AUTH_FAILED' || $doAuthenticate != null) {
            $xml_string = '
                <get_balance xsi:type="urn:get_balance">
                    <token xsi:type="xsd:string">'.$doAuthenticate.'</token>
                </get_balance>
            ';
            $soapMethod = 'getBalance';
            $response = $this->connectionCheck($xml_string, $soapMethod);
            if (isset($response) && $response != false && $response != null) {
                $returnValue = json_decode($response->getBalanceResponse->Response, true);
            } else {
                $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
            }
        } else {
            $returnValue = ['message' => 'AUTH_FAILED INVALID USER INFORMATION', 'status' => 103];
        }

        return $returnValue;
    }

    /**
     * bKash customer validation service will help you to validate the beneficiary bkash number before send the transaction
     *
     * @param $inputData
     * receiver_first_name like receiver name
     * bank_account_number like receiver bkash number or wallet number
     * @return mixed
     *
     * @throws \Exception
     */
    public function bkashCustomerValidation($inputData)
    {
        $doAuthenticate = $this->doAuthenticate();
        if ($doAuthenticate != 'AUTH_FAILED' || $doAuthenticate != null) {
            $xml_string = '
                <bkash_customer_validation xsi:type="urn:bkash_customer_validation">
                    <!--You may enter the following 3 items in any order-->
                    <token xsi:type="xsd:string">'.$doAuthenticate.'</token>
                    <fullName xsi:type="xsd:string">'.$inputData['receiver_first_name'].'</fullName>
                    <mobileNumber xsi:type="xsd:string">'.$inputData['bank_account_number'].'</mobileNumber>
                </bkash_customer_validation>
            ';
            $soapMethod = 'bkashCustomerValidation';
            $response = $this->connectionCheck($xml_string, $soapMethod);
            if (isset($response) && $response != false && $response != null) {
                $returnValue = json_decode($response->bkashCustomerValidationResponse->Response, true);
            } else {
                $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
            }
        } else {
            $returnValue = ['message' => 'AUTH_FAILED INVALID USER INFORMATION', 'status' => 103];
        }

        return $returnValue;
    }

    /**
     * bKash customer validation service will help you to validate the beneficiary bkash number before send the transaction
     *
     * @param $inputData
     * bank_account_number like receiver bkash number or wallet number
     * @return mixed
     *
     * @throws \Exception
     */
    public function bkashValidation($inputData)
    {
        $doAuthenticate = $this->doAuthenticate();
        if ($doAuthenticate != 'AUTH_FAILED' || $doAuthenticate != null) {
            $xml_string = '
                <bkash_customer_details xsi:type="urn:bkash_customer_validation">
                    <!--You may enter the following 3 items in any order-->
                    <token xsi:type="xsd:string">'.$doAuthenticate.'</token>
                    <mobileNumber xsi:type="xsd:string">'.$inputData['bank_account_number'].'</mobileNumber>
                </bkash_customer_details>
            ';
            $soapMethod = 'getBkashCustomerDetails';
            $response = $this->connectionCheck($xml_string, $soapMethod);
            if (isset($response) && $response != false && $response != null) {
                $returnValue = json_decode($response->getBkashCustomerDetailsResponse->Response, true);
            } else {
                $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
            }
        } else {
            $returnValue = ['message' => 'AUTH_FAILED INVALID USER INFORMATION', 'status' => 103];
        }

        return $returnValue;
    }

    /**
     * Do bKash transfer service will help you to send a bkash transaction
     *
     * @param $input_data
     * @return mixed
     *
     * @throws \Exception
     */
    public function doBkashTransfer($inputData)
    {
        $doAuthenticate = $this->doAuthenticate();
        if ($doAuthenticate != 'AUTH_FAILED' || $doAuthenticate != null) {
            $xml_string = '
                <do_bkash_transfer xsi:type="urn:do_bkash_transfer">
                    <!--You may enter the following 18 items in any order-->
                    <token xsi:type="xsd:string">'.$doAuthenticate.'</token>
                    <amount_in_bdt xsi:type="xsd:string">'.$inputData->transfer_amount.'</amount_in_bdt>
                    <reference_no xsi:type="xsd:string">'.$inputData->reference_no.'</reference_no>
                    <remitter_name xsi:type="xsd:string">'.$inputData->sender_first_name.'</remitter_name>
                    <remitter_dob xsi:type="xsd:string">'.$inputData->sender_date_of_birth.'</remitter_dob>
                    <!--Optional:-->
                    <remitter_iqama_no xsi:type="xsd:string"/>
                    <remitter_id_passport_no xsi:type="xsd:string">'.$inputData->sender_id_number.'</remitter_id_passport_no>
                    <!--Optional:-->
                    <remitter_address xsi:type="xsd:string">'.$inputData->sender_address.'</remitter_address>
                    <remitter_mobile_no xsi:type="xsd:string">'.$inputData->sender_mobile.'</remitter_mobile_no>
                    <issuing_country xsi:type="xsd:string">'.$inputData->sender_id_issue_country.'</issuing_country>
            ';
            if (isset($inputData->wallet_account_actual_name) && $inputData->wallet_account_actual_name != '') {
                $xml_string .= '
                    <beneficiary_name xsi:type="xsd:string">'.(isset($inputData->wallet_account_actual_name) ? $inputData->wallet_account_actual_name : null).'</beneficiary_name>
            ';
            } else {
                $xml_string .= '
                    <beneficiary_name xsi:type="xsd:string">'.((isset($inputData->receiver_first_name) ? $inputData->receiver_first_name : null).(isset($inputData->receiver_middle_name) ? ' '.$inputData->receiver_middle_name : null).(isset($inputData->receiver_last_name) ? ' '.$inputData->receiver_last_name : null)).'</beneficiary_name>
            ';
            }
            $xml_string .= '
                    <beneficiary_city xsi:type="xsd:string">'.(isset($inputData->receiver_city) ? $inputData->receiver_city : 'Dhaka').'</beneficiary_city>
                    <!--Optional:-->
                    <beneficiary_id_no xsi:type="xsd:string"></beneficiary_id_no>
                    <!--Optional:-->
                    <beneficiary_id_type xsi:type="xsd:string"></beneficiary_id_type>
                    <purpose_of_payment xsi:type="xsd:string">'.$inputData->purpose_of_remittance.'</purpose_of_payment>
                    <beneficiary_mobile_phone_no xsi:type="xsd:string">'.$inputData->bank_account_number.'</beneficiary_mobile_phone_no>
                    <!--Optional:-->
                    <beneficiary_address xsi:type="xsd:string">'.$inputData->receiver_address.'</beneficiary_address>
                    <issue_date xsi:type="xsd:string">'.date('Y-m-d', strtotime($inputData->created_date)).'</issue_date>
                </do_bkash_transfer>
            ';
            $soapMethod = 'doBkashTransfer';
            $response = $this->connectionCheck($xml_string, $soapMethod);
            if (isset($response) && $response != false && $response != null) {
                $returnValue = json_decode($response->doBkashTransferResponse->Response, true);
            } else {
                $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
            }
        } else {
            $returnValue = ['message' => 'AUTH_FAILED INVALID USER INFORMATION', 'status' => 103];
        }

        return $returnValue;
    }

    /**
     * This service call will provide you the bkash transaction status.
     *
     * @param $inputData
     * reference_no like system transaction number
     * @return mixed
     *
     * @throws \Exception
     */
    public function getBkashTnxStatus($inputData)
    {
        $doAuthenticate = $this->doAuthenticate();
        if ($doAuthenticate != 'AUTH_FAILED' || $doAuthenticate != null) {
            $xml_string = '
                <bkash_transfer_status xsi:type="urn:bkash_transfer_status">
                    <!--You may enter the following 2 items in any order-->
                    <token xsi:type="xsd:string">'.$doAuthenticate.'</token>
                    <reference_no xsi:type="xsd:string">'.$inputData['reference_no'].'</reference_no>
                </bkash_transfer_status>
            ';
            $soapMethod = 'getBkashTransferStatus';
            $response = $this->connectionCheck($xml_string, $soapMethod);
            if (isset($response) && $response != false && $response != null) {
                $returnValue = json_decode($response->getBkashTransferStatusResponse->Response, true);
            } else {
                $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
            }
        } else {
            $returnValue = ['message' => 'AUTH_FAILED INVALID USER INFORMATION', 'status' => 103];
        }

        return $returnValue;
    }

    /**
     * @param $xml_post_string
     * @param $method
     * @return \SimpleXMLElement
     *
     * @throws \Exception
     */
    public function connectionCheck($xml_post_string, $method)
    {
        $xml_string = $this->xmlGenerate($xml_post_string, $method);
        Log::info($method.'<br>'.$xml_string);
        $headers = [
            'Host: '.$this->config[$this->status]['app_host'],
            'Content-type: text/xml;charset="utf-8"',
            'Content-length: '.strlen($xml_string),
            'SOAPAction: '.$method,
        ];

        // PHP cURL  for connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // execution
        $response = curl_exec($ch);
        Log::error($method.' CURL reported error: ');
        if ($response === false) {
            throw new \Exception(curl_error($ch), curl_errno($ch));
        }
        curl_close($ch);
        $response1 = str_replace('<SOAP-ENV:Body>', '', $response);
        $response2 = str_replace('</SOAP-ENV:Body>', '', $response1);
        $response = str_replace('xmlns:ns1="urn:dynamicapi"', '', $response2);
        $response = str_replace('ns1:', '', $response); //dd($response);
        Log::info($method.'<br>'.$response);

        return simplexml_load_string($response);
    }

    /**
     * @param $string
     * @param $method
     * @return string
     */
    public function xmlGenerate($string, $method)
    {
        $xml_string = '<?xml version="1.0" encoding="utf-8"?>
            <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:dynamicapi">
                <soapenv:Header/>
                <soapenv:Body>
                    <urn:'.$method.' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                        '.$string.'
                    </urn:'.$method.'>
                </soapenv:Body>
            </soapenv:Envelope>
        ';

        return $xml_string;
    }
}

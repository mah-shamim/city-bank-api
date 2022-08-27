<?php

namespace MahShamim\CityBank;

use Exception;

class CityBank
{
    /**
     * @var Config
     */
    public $config;

    /**
     * @var Request
     */
    public $request;

    /**
     * CityBank constructor.
     *
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = [])
    {
        $this->config = new Config($config);

        $this->request = new Request($this->config);

        $this->doAuthenticate();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function token()
    {
        if (is_null($this->request->token)) {
            $this->doAuthenticate();
        }

        return $this->request->token;
    }

    /**
     * Render the api payload as xml string
     *
     * @return string
     *
     * @throws Exception
     */
    public function xml()
    {
        return $this->request->getXml();
    }

    /**
     * Execute the Request api
     * @return array
     * @throws Exception
     */
    public function execute()
    {
        return $this->request->connect();
    }

    /**
     * Authenticate service will provide you the access token by providing following parameter value.
     *
     * @return void
     * @throws Exception
     * @since 2.0.0
     */
    public function doAuthenticate()
    {
        $payload = [
            'username' => $this->config->username,
            'password' => $this->config->password,
            'exchange_company' => $this->config->company
        ];

        $response = $this->request
            ->method(Config::METHOD_AUTHENTICATE)
            ->payload('auth_info', $payload)
            ->connect();

        $this->request->token = (isset($response['token']) && $response['token'] != Config::AUTH_FAILED)
            ? $response['token']
            : null;
    }

    /**
     * Get balance service will help to know the available balance
     *
     * @return self
     * @throws Exception
     * @since 2.0.0
     */
    public function getBalance()
    {
        $payload = [];

        $this->request = $this->request
            ->method(Config::METHOD_BALANCE)
            ->payload('get_balance', $payload);

        return $this;
    }

    /**
     * Do transfer service will help you to send a new transaction by providing following parameter value
     *
     * @param $transferData
     * @return self
     * @throws Exception
     * @since 2.0.0
     */
    public function transfer($transferData)
    {
        $payload = [];
        $this->request
            ->method(Config::METHOD_TRANSFER)
            ->payload('Transaction', $payload);

        if ($transferData->bank_id == 17): $mode_of_payment = 'CBL Account';
        else: $mode_of_payment = 'Other Bank'; endif;
        if ($transferData->recipient_type_name == 'Cash'): $mode_of_payment = 'Cash'; endif;
        if ($transferData->recipient_type_name == 'Cash Pickup'): $mode_of_payment = 'Cash'; endif;
        $xml_string = '<Transaction xsi:type="urn:Transaction">
                    <reference_no xsi:type="xsd:string">' . $transferData->reference_no . '</reference_no>
                    <remitter_name xsi:type="xsd:string">' . $transferData->sender_first_name . '</remitter_name>
                    <remitter_code xsi:type="xsd:string">' . $transferData->sender_mobile . '</remitter_code>
                    <remitter_iqama_no xsi:type="xsd:string"></remitter_iqama_no>
                    <remitter_id_passport_no xsi:type="xsd:string">' . $transferData->sender_id_number . '</remitter_id_passport_no>
                    <issuing_country xsi:type="xsd:string">' . $transferData->sender_id_issue_country . '</issuing_country>
                    <beneficiary_name xsi:type="xsd:string">' . ((isset($transferData->receiver_first_name) ? $transferData->receiver_first_name : null) . (isset($transferData->receiver_middle_name) ? ' ' . $transferData->receiver_middle_name : null) . (isset($transferData->receiver_last_name) ? ' ' . $transferData->receiver_last_name : null)) . '</beneficiary_name>
            ';
        if ($mode_of_payment != 'Cash'):
            $xml_string .= '
                        <beneficiary_account_no xsi:type="xsd:string">' . $transferData->bank_account_number . '</beneficiary_account_no>
                        <beneficiary_bank_account_type xsi:type="xsd:string">Savings</beneficiary_bank_account_type>
                        <beneficiary_bank_name xsi:type="xsd:string">' . $transferData->bank_name . '</beneficiary_bank_name>
                        <beneficiary_bank_branch_name xsi:type="xsd:string">' . $transferData->bank_branch_name . '</beneficiary_bank_branch_name>
                        <branch_routing_number xsi:type="xsd:string">' . (isset($transferData->location_routing_id[1]->bank_branch_location_field_value) ? $transferData->location_routing_id[1]->bank_branch_location_field_value : null) . '</branch_routing_number>
                ';
        endif;
        $xml_string .= '<amount_in_taka xsi:type="xsd:string">' . $transferData->transfer_amount . '</amount_in_taka>
                    <purpose_of_payment xsi:type="xsd:string">' . $transferData->purpose_of_remittance . '</purpose_of_payment>
                    <beneficiary_mobile_phone_no xsi:type="xsd:string">' . $transferData->receiver_contact_number . '</beneficiary_mobile_phone_no>
                    <beneficiary_id_type xsi:type="xsd:string"></beneficiary_id_type>
                    <pin_no xsi:type="xsd:string"></pin_no>
                    <remitter_address xsi:type="xsd:string">' . $transferData->sender_address . '</remitter_address>
                    <remitter_mobile_no xsi:type="xsd:string">' . $transferData->sender_mobile . '</remitter_mobile_no>
                    <beneficiary_address xsi:type="xsd:string">' . $transferData->receiver_address . '</beneficiary_address>
                    <beneficiary_id_no xsi:type="xsd:string"></beneficiary_id_no>
                    <special_instruction xsi:type="xsd:string">NA</special_instruction>
                    <mode_of_payment xsi:type="xsd:string">' . $mode_of_payment . '</mode_of_payment>
                    <issue_date xsi:type="xsd:string">' . date('Y-m-d', strtotime($transferData->created_date)) . '</issue_date>
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
                </Transaction>';
        $soapMethod = 'doTransfer';
        $apiResponse = $this->connect($xml_string, $soapMethod);
        if (isset($apiResponse) && $apiResponse != false && $apiResponse != null):
            $returnValue = json_decode($apiResponse->doTransferResponse->Response, true);
        else:
            $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
        endif;
        return $returnValue;
    }

    /**
     * Get transaction status service will help you to get the transaction status
     *
     * @param string $reference
     * @return self
     * @throws Exception
     * @since 2.0.0
     */
    public function getTnxStatus($reference)
    {
        $payload = ['reference_no' => $reference];

        $this->request
            ->method(Config::METHOD_TRANSFER_STATUS)
            ->payload('transaction_status', $payload);

        return $this;
    }

    /**
     * Do amendment or cancel service will help you to send the transaction cancel/amendment request
     *
     * @param string
     * @param string
     * @return self
     * @throws Exception
     * @since 2.0.0
     */
    public function doAmendmentOrCancel($reference, $details = '?')
    {
        $payload = [
            'reference_no' => $reference,
            'amend_query' => $details
        ];

        $this->request
            ->method(Config::METHOD_AMENDMENT_OR_CANCEL)
            ->payload('txn_amend_cancel', $payload);

        return $this;
    }

    /**
     * bKash customer validation service will help you to validate the beneficiary bkash number before send the transaction
     *
     * @param $inputData
     * bank_account_number like receiver bkash number or wallet number
     * @return mixed
     * @throws Exception
     * @since 2.1.0
     */
    public function bkashValidation($inputData)
    {
        $doAuthenticate = $this->doAuthenticate();
        if ($doAuthenticate != 'AUTH_FAILED' || $doAuthenticate != null):
            $xml_string = '
                <bkash_customer_details xsi:type="urn:bkash_customer_validation">
                    <!--You may enter the following 3 items in any order-->
                    <token xsi:type="xsd:string">' . $doAuthenticate . '</token>
                    <mobileNumber xsi:type="xsd:string">' . $inputData['bank_account_number'] . '</mobileNumber>
                </bkash_customer_details>
            ';
            $soapMethod = 'getBkashCustomerDetails';
            $apiResponse = $this->connectionCheck($xml_string, $soapMethod);
            if (isset($apiResponse) && $apiResponse != false && $apiResponse != null):
                $returnValue = json_decode($apiResponse->getBkashCustomerDetailsResponse->Response, true);
            else:
                $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
            endif;
        else:
            $returnValue = ['message' => 'AUTH_FAILED INVALID USER INFORMATION', 'status' => 103];
        endif;
        return $returnValue;
    }

    /**
     * Do bKash transfer service will help you to send a bkash transaction
     *
     * @param array
     * @return mixed
     * @throws Exception
     * @since 2.1.0
     */
    public function bkashTransfer($inputData)
    {
        $doAuthenticate = $this->doAuthenticate();
        if ($doAuthenticate != 'AUTH_FAILED' || $doAuthenticate != null):
            $xml_string = '
                <do_bkash_transfer xsi:type="urn:do_bkash_transfer">
                    <!--You may enter the following 18 items in any order-->
                    <token xsi:type="xsd:string">' . $doAuthenticate . '</token>
                    <amount_in_bdt xsi:type="xsd:string">' . $inputData->transfer_amount . '</amount_in_bdt>
                    <reference_no xsi:type="xsd:string">' . $inputData->reference_no . '</reference_no>
                    <remitter_name xsi:type="xsd:string">' . $inputData->sender_first_name . '</remitter_name>
                    <remitter_dob xsi:type="xsd:string">' . $inputData->sender_date_of_birth . '</remitter_dob>
                    <!--Optional:-->
                    <remitter_iqama_no xsi:type="xsd:string"/>
                    <remitter_id_passport_no xsi:type="xsd:string">' . $inputData->sender_id_number . '</remitter_id_passport_no>
                    <!--Optional:-->
                    <remitter_address xsi:type="xsd:string">' . $inputData->sender_address . '</remitter_address>
                    <remitter_mobile_no xsi:type="xsd:string">' . $inputData->sender_mobile . '</remitter_mobile_no>
                    <issuing_country xsi:type="xsd:string">' . $inputData->sender_id_issue_country . '</issuing_country>
            ';
            if (isset($inputData->wallet_account_actual_name) && $inputData->wallet_account_actual_name != ''):
                $xml_string .= '
                    <beneficiary_name xsi:type="xsd:string">' . (isset($inputData->wallet_account_actual_name) ? $inputData->wallet_account_actual_name : null) . '</beneficiary_name>
            ';
            else:
                $xml_string .= '
                    <beneficiary_name xsi:type="xsd:string">' . ((isset($inputData->receiver_first_name) ? $inputData->receiver_first_name : null) . (isset($inputData->receiver_middle_name) ? ' ' . $inputData->receiver_middle_name : null) . (isset($inputData->receiver_last_name) ? ' ' . $inputData->receiver_last_name : null)) . '</beneficiary_name>
            ';
            endif;
            $xml_string .= '
                    <beneficiary_city xsi:type="xsd:string">' . (isset($inputData->receiver_city) ? $inputData->receiver_city : 'Dhaka') . '</beneficiary_city>
                    <!--Optional:-->
                    <beneficiary_id_no xsi:type="xsd:string"></beneficiary_id_no>
                    <!--Optional:-->
                    <beneficiary_id_type xsi:type="xsd:string"></beneficiary_id_type>
                    <purpose_of_payment xsi:type="xsd:string">' . $inputData->purpose_of_remittance . '</purpose_of_payment>
                    <beneficiary_mobile_phone_no xsi:type="xsd:string">' . $inputData->bank_account_number . '</beneficiary_mobile_phone_no>
                    <!--Optional:-->
                    <beneficiary_address xsi:type="xsd:string">' . $inputData->receiver_address . '</beneficiary_address>
                    <issue_date xsi:type="xsd:string">' . date('Y-m-d', strtotime($inputData->created_date)) . '</issue_date>
                </do_bkash_transfer>
            ';
            $soapMethod = 'doBkashTransfer';
            $apiResponse = $this->connectionCheck($xml_string, $soapMethod);
            if (isset($apiResponse) && $apiResponse != false && $apiResponse != null):
                $returnValue = json_decode($apiResponse->doBkashTransferResponse->Response, true);
            else:
                $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
            endif;
        else:
            $returnValue = ['message' => 'AUTH_FAILED INVALID USER INFORMATION', 'status' => 103];
        endif;
        return $returnValue;
    }

    /**
     * This service call will provide you the bkash transaction status.
     *
     * @param $inputData
     * reference_no like system transaction number
     * @return mixed
     * @throws Exception
     * @since 2.1.0
     */
    public function bkashTnxStatus($inputData)
    {
        $doAuthenticate = $this->doAuthenticate();
        if ($doAuthenticate != 'AUTH_FAILED' || $doAuthenticate != null):
            $xml_string = '
                <bkash_transfer_status xsi:type="urn:bkash_transfer_status">
                    <!--You may enter the following 2 items in any order-->
                    <token xsi:type="xsd:string">' . $doAuthenticate . '</token>
                    <reference_no xsi:type="xsd:string">' . $inputData['reference_no'] . '</reference_no>
                </bkash_transfer_status>
            ';
            $soapMethod = 'getBkashTransferStatus';
            $apiResponse = $this->connectionCheck($xml_string, $soapMethod);
            if (isset($apiResponse) && $apiResponse != false && $apiResponse != null):
                $returnValue = json_decode($apiResponse->getBkashTransferStatusResponse->Response, true);
            else:
                $returnValue = ['message' => 'Transaction response Found', 'status' => 5000];
            endif;
        else:
            $returnValue = ['message' => 'AUTH_FAILED INVALID USER INFORMATION', 'status' => 103];
        endif;
        return $returnValue;
    }
}

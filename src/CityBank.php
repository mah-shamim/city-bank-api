<?php

namespace MahShamim\CityBank;

use Exception;

/**
 * Class CityBank
 *
 * This class provides the details related to Remittance API.
 * This APIs is used to initiate payment request from
 * Mobile client/others exchange house.
 *
 * @property Config $config
 * @property Request $request
 *
 * @package MahShamim\CityBank
 */
class CityBank
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Request
     */
    private $request;

    /**
     * CityBank constructor.
     *
     * @param array $config
     *
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
     *
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
     *
     * @return array
     *
     * @throws Exception
     */
    public function execute()
    {
        return $this->request->connect();
    }

    /**
     * Do authenticate service will provide you the access token
     *
     * @return void
     *
     * @throws Exception
     *
     * @since 2.0.0
     */
    public function doAuthenticate()
    {
        $payload = [
            'username' => $this->config->username,
            'password' => $this->config->password,
            'exchange_company' => $this->config->company,
        ];

        $response = $this->request
            ->method(Config::AUTHENTICATE)
            ->payload('auth_info', $payload)
            ->connect();

        $this->request->token = (isset($response['token']) && $response['token'] != Config::AUTH_FAILED)
            ? $response['token']
            : null;
    }

    /**
     * Do transfer service will help you to send a new transaction by providing following parameter value
     *
     * @param $reference
     * @param array $data
     * @return self
     *
     * @throws Exception
     * @since 2.0.0
     */
    public function doTransfer($reference, $data = [])
    {
        $payload = ['reference_no' => $reference];

        $payload['remitter_name'] = isset($data['remitter_name']) ? $data['remitter_name'] : '';
        $payload['remitter_code'] = isset($data['remitter_code']) ? $data['remitter_code'] : '';
        $payload['remitter_iqama_no'] = isset($data['remitter_iqama_no']) ? $data['remitter_iqama_no'] : '';
        $payload['remitter_id_passport_no'] = isset($data['remitter_id_passport_no']) ? $data['remitter_id_passport_no'] : '';
        $payload['issuing_country'] = isset($data['issuing_country']) ? $data['issuing_country'] : '';
        $payload['beneficiary_name'] = isset($data['beneficiary_name']) ? $data['beneficiary_name'] : '';
        if (in_array($payload['mode_of_payment'], ['CBL Account', 'Other Bank'])) {
            $payload['beneficiary_account_no'] = isset($data['beneficiary_account_no']) ? $data['beneficiary_account_no'] : '';
            $payload['beneficiary_bank_account_type'] = isset($data['beneficiary_bank_account_type']) ? $data['beneficiary_bank_account_type'] : '';
            $payload['beneficiary_bank_name'] = isset($data['beneficiary_bank_name']) ? $data['beneficiary_bank_name'] : '';
            $payload['beneficiary_bank_branch_name'] = isset($data['beneficiary_bank_branch_name']) ? $data['beneficiary_bank_branch_name'] : '';
            $payload['branch_routing_number'] = isset($data['branch_routing_number']) ? $data['branch_routing_number'] : '';
        }
        $payload['amount_in_bdt'] = isset($data['amount_in_bdt']) ? $data['amount_in_bdt'] : '';
        $payload['purpose_of_payment'] = isset($data['purpose_of_payment']) ? $data['purpose_of_payment'] : '';
        $payload['beneficiary_mobile_phone_no'] = isset($data['beneficiary_mobile_phone_no']) ? $data['beneficiary_mobile_phone_no'] : '?';
        $payload['beneficiary_id_type'] = isset($data['beneficiary_id_type']) ? $data['beneficiary_id_type'] : '';
        $payload['pin_no'] = isset($data['pin_no']) ? $data['pin_no'] : '';
        $payload['remitter_address'] = isset($data['remitter_address']) ? $data['remitter_address'] : '';
        $payload['remitter_mobile_no'] = isset($data['remitter_mobile_no']) ? $data['remitter_mobile_no'] : '';
        $payload['beneficiary_address'] = isset($data['beneficiary_address']) ? $data['beneficiary_address'] : '';
        $payload['beneficiary_id_no'] = isset($data['beneficiary_id_no']) ? $data['beneficiary_id_no'] : '';
        $payload['special_instruction'] = isset($data['special_instruction']) ? $data['special_instruction'] : 'NA';
        $payload['mode_of_payment'] = isset($data['mode_of_payment']) ? $data['mode_of_payment'] : '';
        $payload['issue_date'] = isset($data['issue_date']) ? $data['issue_date'] : '';
        for ($i = 1; $i <= 10; $i++) {
            $payload['custom_field_name_' . $i] = isset($data['custom_field_name_' . $i]) ? $data['custom_field_name_' . $i] : '?';
            $payload['custom_field_value_' . $i] = isset($data['custom_field_value_' . $i]) ? $data['custom_field_value_' . $i] : '?';
        }
        $this->request
            ->method(Config::TRANSFER)
            ->payload('Transaction', $payload);

        return $this;
    }

    /**
     * Get transaction status service will help you to get the transaction status
     *
     * @param string $reference
     * @return self
     *
     * @throws Exception
     *
     * @since 2.0.0
     */
    public function getTnxStatus($reference)
    {
        $payload = ['reference_no' => $reference];

        $this->request
            ->method(Config::TRANSFER_STATUS)
            ->payload('transaction_status', $payload);

        return $this;
    }

    /**
     * Do amendment or cancel service will help you to send the transaction cancel/amendment request
     *
     * @param string
     * @param string
     * @return self
     *
     * @throws Exception
     *
     * @since 2.0.0
     */
    public function doAmendmentOrCancel($reference, $details = '?')
    {
        $payload = ['reference_no' => $reference, 'amend_query' => $details,];

        $this->request
            ->method(Config::AMENDMENT_OR_CANCEL)
            ->payload('txn_amend_cancel', $payload);

        return $this;
    }

    /**
     * Get balance service will help to know the available balance
     *
     * @return self
     *
     * @throws Exception
     *
     * @since 2.0.0
     */
    public function getBalance()
    {
        $payload = [];

        $this->request = $this->request
            ->method(Config::BALANCE)
            ->payload('get_balance', $payload);

        return $this;
    }

    /**
     * bKash customer validation service will help you to validate the beneficiary bkash number before send the transaction
     *
     * @param $mobileNumber
     * @param string $fullName
     * @return self
     *
     * @throws Exception
     *
     * @since 2.1.0
     */
    public function doBkashCustomerValidation($mobileNumber, $fullName = '?')
    {
        $payload = ['mobileNumber' => $mobileNumber];

        if ($fullName != '?') {
            $payload = ['fullName' => $fullName];
        }

        $this->request
            ->method(Config::BKASH_CUSTOMER_VALIDATION)
            ->payload('bkash_customer_validation', $payload);

        return $this;
    }

    /**
     * Do Bkash transfer service will help you to send a bkash transaction
     *
     * @param string|integer $reference
     * @param array $data
     * @return self
     * @throws Exception
     * @since 2.1.0
     */
    public function doBkashTransfer($reference, $data = [])
    {
        $payload = ['reference_no' => $reference];

        try {
            $payload['amount_in_bdt'] = isset($data['amount_in_bdt']) ? $data['amount_in_bdt'] : 0;
            $payload['remitter_name'] = isset($data['remitter_name']) ? $data['remitter_name'] : '?';
            $payload['remitter_dob'] = isset($data['remitter_dob']) ? $data['remitter_dob'] : '?';

            if (isset($data['remitter_iqama_no'])) {
                $payload['remitter_iqama_no'] = $data['remitter_iqama_no'];
            }

            $payload['remitter_id_passport_no'] = isset($data['remitter_id_passport_no']) ? $data['remitter_id_passport_no'] : '2';

            if (isset($data['remitter_address'])) {
                $payload['remitter_address'] = $data['remitter_address'];
            }

            $payload['remitter_mobile_no'] = isset($data['remitter_mobile_no']) ? $data['remitter_mobile_no'] : '?';
            $payload['issuing_country'] = isset($data['issuing_country']) ? $data['issuing_country'] : '?';
            $payload['beneficiary_name'] = isset($data['beneficiary_name']) ? $data['beneficiary_name'] : '?';
            $payload['beneficiary_city'] = isset($data['beneficiary_city']) ? $data['beneficiary_city'] : '?';

            if (isset($data['beneficiary_id_no'])) {
                $payload['beneficiary_id_no'] = $data['beneficiary_id_no'];
                $payload['beneficiary_id_type'] = isset($data['beneficiary_id_type']) ? $data['beneficiary_id_type'] : '';
            }

            $payload['beneficiary_id_no'] = isset($data['beneficiary_id_no']) ? $data['beneficiary_id_no'] : '?';
            $payload['purpose_of_payment'] = isset($data['purpose_of_payment']) ? $data['purpose_of_payment'] : '?';
            $payload['beneficiary_mobile_phone_no'] = isset($data['beneficiary_mobile_phone_no']) ? $data['beneficiary_mobile_phone_no'] : '?';

            if (isset($data['beneficiary_address'])) {
                $payload['beneficiary_address'] = $data['beneficiary_address'];
            }

            $payload['issue_date'] = isset($data['issue_date']) ? $data['issue_date'] : date('Y-m-d');
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        $this->request
            ->method(Config::BKASH_TRANSFER)
            ->payload('do_bkash_transfer', $payload);

        return $this;
    }

    /**
     * This service call will provide you the bkash transaction status.
     *
     * @param $reference
     * @return mixed
     *
     * @throws Exception
     * @since 2.1.0
     */
    public function getBkashTnxStatus($reference)
    {
        $payload = ['reference_no' => $reference];

        $this->request
            ->method(Config::BKASH_TRANSFER_STATUS)
            ->payload('transaction_status', $payload);

        return $this;
    }
}

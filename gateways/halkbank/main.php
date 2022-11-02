<?php

    /**
     * halkbank
     *
     * @package    -
     * @author     Milad Abooali <m.abooali@hotmail.com>
     * @copyright  -
     * @license    -
     * @version    1.0
     */

    class halkbank
    {

        /**
         * @var string $api_url_3d 3D API Post URL
         * @var string $api_url_curl XML API Post URL
         * @var int $api_client Client ID
         * @var string $api_pass Client Password
         * @var string $api_name API Name
         * @var array $response Last response from bank
         * @var int $cc_id credit card id
         * @var float $USDTRY USD/TRY rate
         * @var int $order_id payment order id
         */
        private
            $api_url_3d,
            $api_url_curl,
            $api_client,
            $api_pass,
            $api_name,
            $api_currency;
        public
            $error,
            $response,
            $cc_id,
            $USDTRY,
            $order_id;


        function __construct($path='./') {
            include_once $path.'config.php';
            $this->api_url_3d    =   $api_url_3d;
            $this->api_url_curl  =   $api_url_curl;
            $this->api_client    =   $api_client;
            $this->api_pass      =   $api_pass;
            $this->api_name      =   $api_name;
            $this->api_currency  =   $api_currency;
        }

        /**
         * Send requests to bank
         * @param string $xml_string Data sending to the bank
         * @return array|false if there is not curl error return bank response in array else false
         */
        private function call(string $xml_string)
        {
            $connect  = "DATA=<?xml version=\"1.0\" encoding=\"ISO-8859-9\"?><CC5Request>";
            $connect .= "<Name>".$this->api_name."</Name>";
            $connect .= "<Password>".$this->api_pass."</Password>";
            $connect .= "<ClientId>".$this->api_client."</ClientId>";
            $connect .= "<Currency>".$this->api_currency."</Currency>";
            $request  = $connect.$xml_string."</CC5Request>";
            $ch = curl_init($this->api_url_curl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "$request");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                $this->error = curl_error($ch);
                $response = null;
            } else {
                $xml = simplexml_load_string($response);
                $json = json_encode($xml);
                $response = json_decode($json,TRUE);
                $this->response = $response;
            }
            curl_close($ch);
            return $response;
        }

        /**
         * Processing bank response
         * @param array $response Bank response in array
         * @return int|bool return false on error
         */
        public function callBack(array $response)
        {
            $response['ccID'] = $this->cc_id;
            $response['USDTRY'] = $this->USDTRY;
            global $db;
            $data['data'] = json_encode($response);
            return $db->updateId('payment_orders',$this->order_id,$data);
        }

        /**
         * Request Payment
         * @param array $request payment data in array
         * @return array|null Bank response in array or false on error
         */
        public function authPay(array $request)
        {
            $xml  ="<Type>Auth</Type>";
            $xml .="<Total>".$request['amount']."</Total>";
            $xml .="<Number>".$request['card_num']."</Number>";
            $xml .="<Expires>".$request['exp_mm']."/".$request['exp_yy']."</Expires>";
            $xml .="<Cvv2Val>".$request['cvv']."</Cvv2Val>";
            $xml .="<OrderId>$this->order_id</OrderId>";
            $response = $this->call($xml);
            if ($response) {
                $this->response = $response;
                $this->callBack($response);
                if ($response['Response'] === 'Approved') {
                    $data['status'] = 1;
                    global $db;
                    $db->updateId('payment_orders', $this->order_id, $data);
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        /**
         * Get Order History from Bank
         * @param int $order_id Payment order id
         * @return array|false  Bank response in array or false on error
         */
        public function historyOrder(int $order_id)
        {
            $xml  ="<OrderId>$order_id</OrderId>";
            $xml .="<Extra><ORDERHISTORY>QUERY</ORDERHISTORY></Extra>";
            $response = $this->call($xml);
            $this->response = $response;
            return $response;
        }

        /**
         * Refund by transaction id
         * @param int $transaction_id Bank transaction id
         * @param int $order_id
         * @return array|null   Bank response in array or false on error
         */
        public function refundTransaction(string $transaction_id, int $order_id)
        {
            $xml  ="<Type>Void</Type>";
            $xml .="<TransId>".$transaction_id."</TransId>";
            $response = $this->call($xml);
            if ($response) {

                $this->response = $response;
                if ($response['Response'] === 'Approved') {

                    $append['data'] = json_encode($response);
                    global $db;
                    $where = 'id='.$order_id;
                    $db->append('payment_orders','data', $where, $append);

                    $data['status'] = 0;
                    $db->updateId('payment_orders',$order_id, $data);
                    $crm_transactions_id = $db->selectId('payment_orders',$order_id, 'transactions_id')['transactions_id'];

                    $trans_status['status'] = 'Canceled';
                    $db->updateId('transactions',$crm_transactions_id, $trans_status);

                    $crm_transaction = new Transaction();
                    $crm_transaction->addComment($crm_transactions_id,'Transaction canceled and payment refunded');

                }
            }
            return $response['Response'] === 'Approved';
        }

        /**
         * Refund by order id
         * @param int $order_id CRM=Bank order id
         * @return array|null Bank response in array or false on error
         */
        public function refundOrder(int $order_id)
        {
            $xml  ="<Type>Credit</Type>";
            $xml .="<OrderId>".$order_id."</OrderId>";
            $response = $this->call($xml);
            if ($response) {

                $this->response = $response;
                if ($response['Response'] === 'Approved') {

                    $append['data'] = json_encode($response);
                    global $db;
                    $where = 'id='.$order_id;
                    $db->append('payment_orders','data', $where, $append);

                    $data['status'] = 0;
                    $db->updateId('payment_orders',$order_id, $data);
                    $crm_transactions_id = $db->selectId('payment_orders',$order_id, 'transactions_id')['transactions_id'];

                    $trans_status['status'] = 'Canceled';
                    $db->updateId('payment_orders',$crm_transactions_id, $trans_status);

                }
            }
            return $response['Response'] === 'Approved';
        }

    }
<?php

    /**
     * Paynet
     *
     * @package    -
     * @author     Milad Abooali <m.abooali@hotmail.com>
     * @copyright  -
     * @license    -
     * @version    1.0
     */

    class paynet
    {

        public
            $config;

        function __construct($path='./')
        {
            $this->config = [
                'secret' => 'sck_V6g5cTqVCRfjxvbMBhGeT5ll6oi3',
                'pub'    => 'pbk_H5qLzZuBYVe11HH0BYnKTikLbquN',
                'url'    => 'https://api.paynet.com.tr/v1/mailorder/create'
            ];
        }

        public function create_payment_link($order_data)
        {
            $post_data = array(
                'amount'            => floatval($order_data['amount']),
                'expire_date'       => 1,
                'name_surname'      => $_SESSION['email'],
                'send_mail'         => false,
                'send_sms'          => false,
                'reference_no'      => md5($order_data['id']),
                'succeed_url'       => 'https://'.Broker['crm_url'].'/gateways/paynet/callback.php?o='.$order_data['id'].'&u='.$_SESSION['id'].'&s=ok',
                'error_url'         => 'https://'.Broker['crm_url'].'/gateways/paynet/callback.php?o='.$order_data['id'].'&u='.$_SESSION['id'].'&s=e',
                //'confirmation_url'  => 'https://'.Broker['crm_url'].'/gateways/paynet/callback.php?o='.$order_data['id'].'&u='.$_SESSION['id']
            );

            $response = $this->send_post($this->config['url'] ,$post_data);
            if ($response['code']===0 && isset($response['url']))
            {
                return $response;
            }
            else
            {
                echo('create_payment_link !!');
                print_r($response);
            }
        }

        private function send_post($post_url, $post_data)	{

            $options = array(
                'http' => array(
                    'header'  =>"Accept: application/json; charset=UTF-8\r\n".
                        "Content-type: application/json; charset=UTF-8\r\n".
                        "Authorization: Basic ".$this->config['secret'],
                    'method'  => 'POST',
                    'content' => json_encode($post_data),
                    'ignore_errors' => true
                ),
            );

            $context  = stream_context_create($options);
            $result = json_decode(@file_get_contents($post_url, false, $context), 1);

            return $result;
        }

    }
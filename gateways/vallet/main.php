<?php

    /**
     * Vallet
     *
     * @package    -
     * @author     Milad Abooali <m.abooali@hotmail.com>
     * @copyright  -
     * @license    -
     * @version    1.0
     */

    class vallet
    {

        public
            $config;

        function __construct($path='./')
        {
            $this->config = [
                'userName' => 'sintaki_api',
                'shopCode' => '7136',
                'password' => '04ec09868ce64e087983876ca8972a2288d26b3d',
                'hash'     => 'wNwUvxFT',
                'url'  =>'https://www.vallet.com.tr/api/v1/create-payment-link'
            ];
        }

        private function hash_generate($string)
        {
            $hash = base64_encode(pack('H*',sha1($this->config['userName'].$this->config['password'].$this->config['shopCode'].$string.$this->config['hash'])));
            return $hash;
        }

        public function create_payment_link($order_data)
        {
            $post_data = array(
                'userName' => $this->config['userName'],
                'password' => $this->config['password'],
                'shopCode' => $this->config['shopCode'],
                'productName' => 'Credit',
                'productData' => array(
                    array(
                        'productName'=>'Credit',
                        'productPrice'=>floatval($order_data['amount']),
                        'productType'=>'DIJITAL_URUN',
                    ),
                ),
                'productType' => 'DIJITAL_URUN',
                'productsTotalPrice' => floatval($order_data['amount']),
                'orderPrice' => floatval($order_data['amount']),
                'currency' => 'TRY',
                'orderId' => $order_data['id'],
                'locale' => 'en',
                //'conversationId' => $order_data['conversationId'],
                'buyerName' => 'Ehteram',
                'buyerSurName' => 'Mohtaram',
                'buyerGsmNo' => '905427513322',
                'buyerIp' => '1.1.1.1',
                'buyerMail' => 'credit@mail.com',
                'buyerAdress' => ' ESNAF SARAYI N 385',
                'buyerCountry' => 'Turkey',
                'buyerCity' => 'Merkez',
                'buyerDistrict' => 'Eskisehir',
                'callbackOkUrl' => 'https://'.Broker['crm_url'].'/gateways/vallet/callback.php?o='.$order_data['id'].'&u='.$_SESSION['id'].'&s=ok',
                'callbackFailUrl' => 'https://'.Broker['crm_url'].'/gateways/vallet/callback.php?o='.$order_data['id'].'&u='.$_SESSION['id'].'&s=fail',
                'module'=>'NATIVE_PHP'
            );
            $post_data['hash'] = $this->hash_generate($post_data['orderId'].$post_data['currency'].$post_data['orderPrice'].$post_data['productsTotalPrice'].$post_data['productType'].$post_data['callbackOkUrl'].$post_data['callbackFailUrl']);

            $response = $this->send_post($this->config['url'] ,$post_data);
            if ($response['status']=='success' && isset($response['payment_page_url']))
            {
                return $response;
            }
            else
            {
                echo('create_payment_link !!');
                print_r($response);
            }
        }

        private function send_post($post_url,$post_data)	{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$post_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1) ;
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['SERVER_NAME']);
            $result_origin = curl_exec($ch);
            $response = array();
            if (curl_errno($ch))
            {
                $response = array(
                    'status'=>'error',
                    'errorMessage'=>'Curl Geçersiz bir cevap aldı',
                );
            }
            else
            {
                $result = json_decode($result_origin,true);
                if (is_array($result))
                {
                    $response = (array) $result;
                }
                else
                {
                    $response = array(
                        'status'=>'error',
                        'errorMessage'=>'Dönen cevap Array değildi',
                    );
                }
            }
            curl_close($ch);
            return $response;
        }

    }
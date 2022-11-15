<?php

/**
 * mt5API
 * 17:01 AM Friday, Dec 20, 2020 | M.Abooali
 */

class mt5API {

    private $server;
    private $connection;
    public $Error;
    public $Response;

    function __construct() {
        $this->server = new CMT5Request();
        $this->connection = ($this->server->Init('mt5.tradeclan.co.uk:443') && $this->server->Auth(1000,"@Sra7689227",3500,"WebManager"));
        if($this->connection) $this->Error[] = 'Connection';
    }

    function __destruct() {
        $this->server->Shutdown();
    }

    /**
     * Get
     * @param string $path
     * @param null|array $data
     * @example get('/api/user/get', ['login'=>100001])
     */
    public function get($path, $data=null)
    {
        $query = $path;
        if($data) {
            $params = http_build_query($data,'','&');
            $params = str_replace("+", "%20", $params);
            $query .= '?'.$params;
        }
        $this->Response = ($this->connection) ? json_decode($this->server->Get($query)) : 'MT5 connection was lost';
        $this->Error = $this->server->E;
    }

    /**
     * Post
     * @param string $path
     * @param null|array $data
     * @param null|string $json_data  // json_encode($array)
     * @example pot('/api/user/update', ['login'=>100001],'{"City":"shiraz", "Country":"Iran"}')
     */
    public function post($path, $data=null, $json_data="{}")
    {
        $query = $path;
        if($data) {
            $params = http_build_query($data,'','&');
            $params = str_replace("+", "%20", $params);
            $query .= '?'.$params;
        }
        $this->Response = ($this->connection) ? json_decode($this->server->Post($query, $json_data)) : 'MT5 connection was lost';
        $this->Error = $this->server->E;
    }

    /**
     * @param $login
     * @param $amount
     * @param $comment
     */
    public function updateBalance($login, $amount, $comment)
    {
        $data['login'] = $login;
        $data['type'] = 2;
        $data['balance'] = $amount;
        $data['comment'] = $comment;
        $data['check_margin'] = 0;
        return $this->get("/api/trade/balance", $data);
    }

}
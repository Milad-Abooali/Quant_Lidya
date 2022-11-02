<?php

/**
 * Wallet
 * @package    -
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  -
 * @license    -
 * @version    1.0.17
 * @update     2:55 PM Thursday, November 4, 2021
 */

class wallet
{

    private $db;

    /**
     * Constructor.
     */
    function __construct()
    {
        global $db;
        $this->db = $db;
    }

    /**
     * Enable Wallet Type
     * @param int $user_id
     * @param int $wallet_type
     */
    public function creatNew($user_id, $wallet_type)
    {
        $where = "user_id=$user_id AND wallet_type=$wallet_type";
        if(!$this->db->exist('user_wallets',$where)) {
            $insert['user_id']      =   $user_id;
            $insert['type_id']      =   $wallet_type;
            $insert['created_by']   =   $_SESSION['id'];
            return $this->db->insert('user_wallets', $insert);
        } else {
            return false;
        }
    }

    /**
     * Get Broker Wallet Types
     * @param $broker_id
     * @return array|bool
     */
    public function getWalletTypes($unit_id)
    {
        $where = "unit_id=$unit_id";
        return $this->db->select('wallet_types',$where);
    }

    /**
     * Get User Walletd
     * @param $user_id
     * @return array|bool
     */
    public function getUserWallet($user_id)
    {
        $where = "user_id=$user_id";
        return $this->db->select('user_wallets',$where,'*',0,'type_id');
    }

    /**
     * Get Wallet By Id
     * @param $wallet_id
     * @return array|bool
     */
    public function getWalletById($wallet_id)
    {
        $wallet = $this->db->selectId('user_wallets',$wallet_id);
        if ($wallet)
           $wallet['type']  = $this->db->selectId('wallet_types',$wallet['type_id']);
        return $wallet;
    }

    /**
     * Get Wallet By User And Wallet Type
     * @param $user_id
     * @param $wallet_type
     * @return array|bool
     */
    public function getWalletByUserType($user_id,$wallet_type)
    {
        $where = "user_id=$user_id AND type_id=$wallet_type";
        $wallet = $this->db->selectRow('user_wallets',$where);
        if ($wallet)
            $wallet['type']  = $this->db->selectId('wallet_types',$wallet['type_id']);
        return $wallet;
    }

    /**
     * Get Wallet Balance By ID
     * @param $wallet_id
     * @return mixed
     */
    public function getBalance($wallet_id)
    {
        return $this->db->selectId('user_wallets',$wallet_id,'balance')['balance'];
    }

    /**
     * Update Balance By Id
     * @param $wallet_id
     * @param $volume
     * @return mixed
     */
    public function updateBalance($wallet_id, $volume)
    {
        $this->db->increase('user_wallets', 'balance', "id=$wallet_id", $volume);
        return $this->getBalance($wallet_id);
    }

    /**
     * Add Transaction
     * @param $transaction
     * @return false|int
     */
    public function addTransaction($transaction)
    {
        return $this->db->insert('wallet_transactions', $transaction);
    }

    /**
     * Add Request
     * @param $transaction
     * @return false|int
     */
    public function addRequest($request)
    {
        return $this->db->insert('wallet_requests', $request);
    }

    /**
     * Add Docs
     * @param $request_id
     * @param string $file_name
     * @return bool|int|mysqli_result|string|null
     */
    public function addDoc($request_id, $file_name) {
        $doc_data['req_id'] = $request_id;
        $doc_data['filename'] = $file_name;
        $doc_data['created_by'] =  $_SESSION['id'];
        $doc_data['created_at'] =  $this->db->DATE;
        return $this->db->insert('wallet_req_docs', $doc_data);
    }

    /**
     * Add Comment
     * @param $request_id
     * @param string $comment
     * @return bool|int|mysqli_result|string|null
     */
    public function addComment($request_id, $comment) {
        $comment_data['req_id'] = $request_id;
        $comment_data['comment'] = $comment;
        $comment_data['created_at'] = $this->db->DATE;
        $comment_data['created_by'] = $_SESSION['id'];
        return $this->db->insert('wallet_req_comment', $comment_data);
    }

}
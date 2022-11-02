<?php

    /**
     * Class Transaction
     *
     * Transaction | Email Manager
     *
     * @package    -
     * @author     Milad Abooali <m.abooali@hotmail.com>
     * @copyright  -
     * @license    -
     * @version    1.0
     */

    class Transaction
    {

        private $theme,$db,$table;

        function __construct($theme=0) {
            global $db;
            $this->db = $db;
            $this->t_transactions = 'transactions';
            $this->t_transactions_docs = 'transactions_docs';
            $this->t_transactions_comment = 'transactions_comment';
            $this->t_tp = 'tp';
        }

        /**
         * Add New Transaction
         * @param string $type
         * @param int $amount
         * @param string $source
         * @param string $destination
         * @param int $user_id
         * @param null|string $comment
         * @return array|bool
         */
        public function add($type, $amount, $source, $destination, $user_id, $comment=null) {
            $transaction_data['type'] = $type;
            $transaction_data['amount'] = $amount;
            $transaction_data['source'] = $source;
            $transaction_data['destination'] = $destination;
            $transaction_data['user_id'] = $user_id;
            $transaction_data['created_at'] = $this->db->DATE;
            $transaction_data['created_by'] = $_SESSION['id'];
            $transaction_data['status'] = "Pending";
            if(($type=='deposit') && ($source==3)) $transaction_data['status'] = "Payment";
            $transaction_id = $this->db->insert($this->t_transactions, $transaction_data);
            if ($transaction_id && $comment) $this->addComment($transaction_id, $comment);
            return ($transaction_id) ?? false;
        }

        /**
         * Add Docs
         * @param int $transaction_id
         * @param string $file_name
         * @return bool|int|mysqli_result|string|null
         */
        public function addDoc($transaction_id, $file_name) {
            $doc_data['transaction_id'] = $transaction_id;
            $doc_data['filename'] = $file_name;
            $doc_data['created_by'] =  $_SESSION['id'];
            $doc_data['created_at'] =  $this->db->DATE;
            return $this->db->insert($this->t_transactions_docs, $doc_data);
        }

        /**
         * Update Transaction status
         * @param int $transaction_id
         * @param $status
         * @return bool|int|mysqli_result|string|null
         */
        public function setStatus($transaction_id, $status) {
            $transaction_data['status'] = $status;
            $transaction_data['updated_by'] = $_SESSION['id'];
            $this->addComment($transaction_id, 'Transaction status changed to '.$status);
            return $this->db->updateId($this->t_transactions, $transaction_id, $transaction_data);
        }

        /**
         * Done Transaction by User
         * @param int $transaction_id
         * @param string $comment
         * @return bool|int|mysqli_result|string|null
         */
        public function done($transaction_id) {

            // Update User type to Trader
            $user_id = $this->db->selectId($this->t_transactions, $transaction_id)['user_id'];

            $userManager = new userManager();
            $user = $userManager->getCustom($user_id, 'type');

            if ($user['type'] == 'Leads') {
                $up_users['type'] = 'Trader';
                $this->db->updateId('users', $user_id, $up_users);
                $up_user_extra['type'] = 2;
                $where = 'user_id='.$user_id;
                $this->db->updateAny('user_extra', $up_user_extra, $where);
                $this->addComment($transaction_id, "Changed user $user type to Trader by CRM.");
            }

            $transaction_data['status'] = 'Done';
            $this->addComment($transaction_id, 'Transaction has been done.');
            return $this->db->updateId($this->t_transactions, $transaction_id, $transaction_data);
        }

        /**
         * Cancel Transaction by User
         * @param int $transaction_id
         * @param string $comment
         * @return bool|int|mysqli_result|string|null
         */
        public function cancel($transaction_id, $comment=null) {
            $transaction_data['status'] = "Canceled";
            $transaction_data['updated_by'] = $_SESSION['id'];
            $transaction_data['updated_at'] = $this->db->DATE;
            $this->addComment($transaction_id, ($comment) ?? 'Transaction was canceled by '.$_SESSION['username']);
            return $this->db->updateId($this->t_transactions, $transaction_id, $transaction_data);
        }

        /**
         * Load User Transactions
         * @param int $user_id
         * @return array|bool
         */
        public function userRequsets($user_id) {
            $where = "user_id=".$user_id;
            return $this->db->select($this->t_transactions, $where);
        }

        /**
         * Load Transaction Docs
         * @param int $transaction_id
         * @return array|bool
         */
        public function loadDocs($transaction_id) {
            $where = "transaction_id=".$transaction_id;
            return $this->db->select($this->t_transactions_docs, $where);
        }

        /**
         * Load Transactions by Id
         * @param int $transaction_id
         * @return array|bool
         */
        public function loadTransactionByID($transaction_id) {
            return $this->db->selectId($this->t_transactions, $transaction_id);
        }

        /**
         * Load Transactions
         * @param $where
         * @return array|bool
         */
        public function loadTransaction($where) {
            return $this->db->select($this->t_transactions, $where);
        }

        /**
         * Check Transaction Waiting
         * @param int $user_id
         * @return array|bool
         */
        public function checkTransactionWaiting($user_id) {
            $user_id = $this->db->escape($user_id);
            $where = "user_id=$user_id AND status IN ('Pending','Payment')";
            return $this->db->exist($this->t_transactions, $where);
        }

        /**
         * Get Transaction Waiting
         * @param int $user_id
         * @return array|bool
         */
        public function getTransactionWaiting($user_id) {
            $where = "user_id=$user_id AND status IN ('Pending','Payment')";
            return $this->db->selectRow($this->t_transactions, $where);
        }

        /**
         * Desk Verify
         * @param int $transaction_id
         * @param int $user_id
         * @param string $side
         * @return bool|int|mysqli_result|string|null
         */
        public function verify($transaction_id, $user_id, $side) {
            $v_data[$side] = $this->db->DATE;
            $v_data['updated_by'] = $user_id;
            $v_data['updated_at'] = $this->db->DATE;
            $this->addComment($transaction_id, "$side has been approved.");
            return $this->db->updateId($this->t_transactions, $transaction_id, $v_data);
        }

        /**
         * Finance Verify
         * @param int $transaction_id
         * @param string $comment
         * @param null|string|array $doc
         * @return bool|int|mysqli_result|string|null
         */
        public function financeVerify($transaction_id, $comment, $doc=null) {
            $v_data['finance_verify'] = $this->db->DATE;
            $v_data['updated_by'] = $_SESSION['id'];
            $v_data['updated_at'] = $this->db->DATE;
            $this->addComment($transaction_id, $comment);
            if ($doc) addDoc($transaction_id, $doc);
            return $this->db->updateId($this->t_transactions, $transaction_id, $v_data);
        }

        /**
         * Treasury Verify
         * @param int $transaction_id
         * @param string $comment
         * @param null|string|array $doc
         * @return bool|int|mysqli_result|string|null
         */
        public function treasuryVerify($transaction_id, $comment, $doc=null) {
            $v_data['treasury_verify'] = $this->db->DATE;
            $v_data['updated_by'] = $_SESSION['id'];
            $v_data['updated_at'] = $this->db->DATE;
            $this->addComment($transaction_id, $comment);
            if ($doc) addDoc($transaction_id, $doc);
            return $this->db->updateId($this->t_transactions, $transaction_id, $v_data);
        }

        /**
         * Not Approve
         * @param int $transaction_id
         * @param string $comment
         * @param null|string|array $doc
         * @return bool|int|mysqli_result|string|null
         */
        private function notApprove($transaction_id, $comment, $doc=null) {
            $transaction_data['status'] = "Not Approved";
            $transaction_data['updated_by'] = $_SESSION['id'];
            $transaction_data['updated_at'] = $this->db->DATE;
            $this->addComment($transaction_id, $comment);
            if ($doc) addDoc($transaction_id, $doc);
            return $this->db->updateId($this->t_transactions, $transaction_id, $transaction_data);
        }

        /**
         * Add Transaction Comment
         * @param int $transaction_id
         * @param string $comment
         * @return bool|int|mysqli_result|string|null
         */
        public function addComment($transaction_id, $comment) {
            $comment_data['transaction_id'] = $transaction_id;
            $comment_data['comment'] = $comment;
            $comment_data['created_at'] = $this->db->DATE;
            $comment_data['created_by'] = $_SESSION['id'];
            return $this->db->insert($this->t_transactions_comment, $comment_data);
        }

        /**
         * Load Comment
         * @param int $transaction_id
         * @return array|bool
         */
        public function loadComment($transaction_id) {
            $where = 'transaction_id='.$transaction_id;
            return $this->db->select($this->t_transactions_comment, $where);
        }

        /**
         * Timeline Events
         * @param $transaction_id
         * @return mixed
         */
        public function timeline($transaction_id) {
            $output = array();
            $sql = "
                SELECT *,'doc' FROM `transactions_docs` WHERE `transaction_id`=$transaction_id
                UNION
                SELECT *,'comment' FROM `transactions_comment` WHERE `transaction_id`=$transaction_id
                Order by created_at DESC
            ";
            $result = $this->db->run($sql);
            if($result) while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) $output[] = $row;
            mysqli_free_result($result);
            return $output;
        }

    }
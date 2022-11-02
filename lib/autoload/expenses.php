<?php

/**
 * expenses Class
 * 12:08 PM Thursday, May 5, 2022 | M.Abooali
 */

class expenses{

    static function add($user_id, $amount, $type, $payee=null, $comment=null)
    {
        // Insert new expense to database
        $new_expenses = array(
            'user_id'    => $user_id,
            'amount'     => $amount,
            'type'       => $type[0],
            'o_type'     => ($type[0]==='Others') ? $type[1] : null,
            'payee'      => $payee,
            'comment'    => $comment,
            'created_by' => $_SESSION['id']
        );
        global $db;
        $new_expenses_id = $db->insert('users_expenses',$new_expenses);

        // Add action Log
        global $actLog;
        $actLog->add('Expenses Add',$new_expenses_id,boolval($new_expenses_id), json_encode($new_expenses));

        // Return Inserted ID
        return $new_expenses_id;
    }

    static function delete($expense_id)
    {
        // Process
        global $db;
        // get requested expenses to archive
        $req_expenses = $db->selectId('users_expenses',$expense_id);
        // Delete expenses from DB
        $delete = $db->deleteId('users_expenses',$expense_id);

        // Add action Log
        global $actLog;
        $actLog->add('Expenses Delete',$expense_id, boolval($delete), json_encode($req_expenses));

        // Return action result
        return $delete;
    }

    static function list($filters=null)
    {
        global $db;
        $where = ($filters) ? "'".implode("'='", $filters)."'" : null;
        return $db->select('users_expenses',$where);
    }

}
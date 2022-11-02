<?php

class taskManager
{
    
    
    /**
     * Constructor.
     */
    function __construct()
    {
        global $db;
        $this->db = $db;
    }

    /**
     * Add Task
     * @param string $manager
     * @param string $assigned_to
     * @param string $name
     * @param string $description
     * @param string $deadline
     * @param int $status
     * @param string $finished_at
     * @param string $type
     * @param int $priority
     */
    public function Add($manager, $assigned_to, $name, $description, $deadline, $status, $type, $priority)
    {
        $insert['manager']      =   $manager;
        $insert['assigned_to']  =   $assigned_to;
        $insert['name']         =   $name;
        $insert['description']  =   $description;
        $insert['deadline']     =   $deadline;
        $insert['status']       =   $status;
        $insert['type']         =   $type;
        $insert['priority']     =   $priority;
        $insert['created_by']   =   $_SESSION['id'];
        $result = $this->db->insert('tasks', $insert);
        
        // Add actLog
        global $actLog; $actLog->add('Add Task',$result,boolval($result),json_encode($insert));
        
        return $result;
    }
    
    /**
     * Update Task
     * @param string $id
     * @param string $manager
     * @param string $assigned_to
     * @param string $name
     * @param string $description
     * @param string $deadline
     * @param int $status
     * @param string $finished_at
     * @param string $type
     * @param int $priority
     * @param string $updated_at
     * @param int $updated_by
     */
    public function Update($id, $manager, $assigned_to, $name, $description, $deadline, $status, $type, $priority)
    {
        global $db;
        $update['manager']      =   $manager;
        $update['assigned_to']  =   $assigned_to;
        $update['name']         =   $name;
        $update['description']  =   $description;
        $update['deadline']     =   $deadline;
        $update['status']       =   $status;
        //$update['finished_at']  =   $finished_at;
        $update['type']         =   $type;
        $update['priority']     =   $priority;
        $update['updated_at']   =   $db->DATE;
        $update['updated_by']   =   $_SESSION['id'];
        $result = $this->db->updateId('tasks', $id, $update);
        
        // Add actLog
        global $actLog; $actLog->add('Update Task',$result,boolval($result),json_encode($update));
        
        return $result;
    }
    
    public function Delete($id)
    {
        deleteId('tasks', $id, $key='id');
    }
    
    public function Finish($id, $status)
    {
        global $db;
        $update['status']       =   $status;
        $update['finished_at']  =   $db->DATE;
        $update['updated_at']   =   $db->DATE;
        $update['updated_by']   =   $_SESSION['id'];
        $result = $this->db->updateId('tasks', $id, $update);
        
        // Add actLog
        global $actLog; $actLog->add('Finish Task',$result,boolval($result),json_encode($update));
        
        return $result;
    }
    
    public function Pin($id, $pin)
    {
        global $db;
        $update['pin']          =   $pin;
        $update['updated_at']   =   $db->DATE;
        $update['updated_by']   =   $_SESSION['id'];
        $result = $this->db->updateId('tasks', $id, $update);
        
        // Add actLog
        global $actLog; $actLog->add('Pin Task',$result,boolval($result),json_encode($update));
        
        return $result;
    }


}
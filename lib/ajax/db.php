<?php

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

// Check Tables
function checkTables() {
    $output = new stdClass();
    $tables = $_POST['tables'] ?? false;
    if($tables) {
        global $db;
        $sql = "CHECK TABLE ";
        $tables = implode('`,`',$tables);
        $sql .= "`$tables`";
        $output->res = $db->query($sql);
    }
    else {
        $output->e = 'No table selected';
    }
    $output = json_encode($output);
    global $actLog; $actLog->add('Database',null,1, $output);
    echo $output;
}

// Repair Tables
function repairTables() {
    $output = new stdClass();
    $tables = $_POST['tables'] ?? false;
    if($tables) {
        global $db;
        $sql = "REPAIR TABLE ";
        $tables = implode('`,`',$tables);
        $sql .= "`$tables`";
        $output->res = $db->query($sql);
    }
    else {
        $output->e = 'No table selected';
    }
    $output = json_encode($output);
    global $actLog; $actLog->add('Database',null,1, $output);
    echo $output;
}

// Optimize Tables
function optimizeTables() {
    $output = new stdClass();
    $tables = $_POST['tables'] ?? false;
    if($tables) {
        global $db;
        $sql = "OPTIMIZE TABLE ";
        $tables = implode('`,`',$tables);
        $sql .= "`$tables`";
        $output->res = $db->query($sql);
    }
    else {
        $output->e = 'No table selected';
    }
    $output = json_encode($output);
    global $actLog; $actLog->add('Database',null,1, $output);
    echo $output;
}

// Backup Tables
function exportTable() {
    $output = new stdClass();
    $tables = $_POST['tables'] ?? false;
    if($tables) {
        $dbhost = DB_admin['hostname'];
        $dbuser = DB_admin['username'];
        $dbpass = DB_admin['password'];
        $dbname = DB_admin['name'];
        
        $fileName = implode("-", $tables);
        $sql_backup_file = date("YmdHis").'_'.$fileName.'.sql.gz';
        $path = str_replace('/_sessions','', Broker['session_path']);
        $file = $path.'/backup/'.$sql_backup_file;
        
        $tables = implode(' ',$tables);
        $dump = "mysqldump --opt -h $dbhost --no-tablespaces -u $dbuser -p$dbpass $dbname $tables | gzip > $file";
//        $output->dump = $dump;
        exec("($dump) 2>&1", $stdout, $result);
        $output->res['result'] = $result;
        $output->res['stdout'] = $stdout;
        $output->res['filename'] = $sql_backup_file;
    }
    else {
        $output->e = 'No table selected';
    }
    $output = json_encode($output);
    global $actLog; $actLog->add('Database',null,1, $output);
    echo $output;
}

// Optimize Tables
function listFiles() {
    $output = new stdClass();
    $table = $_POST['table'] ?? false;
    if($table) {
        $output->res = [];
        $path = str_replace('/_sessions','', Broker['session_path']);
        $path = $path."/backup/*_$table.sql.gz";
        $output->test = $path;

        foreach (glob($path) as $file) {
            $output->res[] = [filemtime($file), basename($file)];
        }
        sort($output->res);
    }
    else {
        $output->e = 'No table selected';
    }
    $output = json_encode($output);
    global $actLog; $actLog->add('Database',null,1, $output);
    echo $output;
}

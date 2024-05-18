<?php

     /**
     * Database Adaptor using mysqli for MySQL and MariaDB
     *
     * @package    App\Core\Database
     * @category   Lib
     * @author     Milad Abooali <m.abooali@hotmail.com>
     * @copyright  2012 - 2020 Codebox
     * @license    http://codebox.ir/license/1_0.txt  Codebox License 1.0
     * @version    1.4.9
     */
    class iSQL
    {
        /**
         * @var string $hostname    Server IP or hostname
         * @var int $port   Server port
         * @var string $database Database name
         * @var string $username Database username
         * @var string $password Database password
         * @var string $prefix Database tables prefix
         * @var string $DATE Server Date (y-m-d H:i:s)
         * @var object $LINK MySQL Connection object
         */
        private $hostname, $port , $database , $username , $password , $prefix, $sql=array();
        public  $DATE, $LINK;

        /**
         * iSQL Constructor.
         * @param array $database Database Connection Information
         */
        function __construct($database)
        {

            $this->hostname = $database['hostname'];
            $this->port     = $database['port'];
            $this->username = $database['username'];
            $this->password = $database['password'];
            $this->database = $database['name'];
            $this->prefix   = $database['prefix'];
            $this->DATE     = date("y-m-d H:i:s");
            $this->LINK = mysqli_connect('p:'.$this->hostname, $this->username, $this->password, $this->database, $this->port);
            if (mysqli_connect_errno()) {throw new RuntimeException("Connect failed: %s\n", mysqli_connect_error());}
            mysqli_set_charset($this->LINK,'utf8');
        }

        /**
         * iSQL Destructor.
         */
        function __destruct()
        {
            global  $___DB__LOG;
            $___DB__LOG[] = $this->log();        
            mysqli_close($this->LINK);
        }

        /**
         * Run
         * @param  string $sql Generated query
         * @param  bool $insert Should return inserted id
         * @return mixed    Return mySQL Object or false on error
         */
        private function _run($sql, $insert=false){
            $insert_id = null;
            $this->sql[] = $sql;
            $sql_number = count($this->sql)-1;
            $result = mysqli_query($this->LINK, $sql) or false;
            ($result!=false) ?: $this->error[$sql_number] =  "Error: ".mysqli_error($this->LINK);
            if ($insert && $result) {$result = mysqli_insert_id($this->LINK);}
            return ($this->error[$sql_number]) ? false : $result;
        }

        /**
         * Run SQL
         * @param string $sql Generated query
         * @return object|false     Return mySQL Object or false on error
         */
        public function run($sql)
        {
            return $this->_run($sql);
        }

        /**
         * Escape inputs.
         * @param  array|string $input Auto detect if array pass and do escape for all value, but only support 1 level
         * @return  mixed
         */
        public function escape($input)
        {
            $escaped = null;
            if ($input) {
                if (is_array($input)) {
                    foreach ($input as $key => $value)
                    {
                        $key = mysqli_real_escape_string($this->LINK, $key);
                        $value = mysqli_real_escape_string($this->LINK, $value);
                        $escaped[$key] = $value;

                    }
                } else {
                    $escaped = mysqli_real_escape_string($this->LINK, $input);
                }
            }

            return ($escaped) ?? false;
        }

        /**
         * Query Maker
         * Mixed Limit,Order and group with query.
         * @param  string $sql Generated query
         * @param  int|null $limit  Add 'LIMIT' to result, pass through on null.
         * @param  string|null $order   Add 'ORDER BY' to result, pass through on null.
         * @param  string|null $group   Add 'GROUP BY' to result, pass through on null.
         * @return  array|false Return array if any result; false on error or empty result.
         */
        public function query($sql, $limit=null, $order=null, $group=null)
        {
            $order = $this->escape($order);
            $limit = intval($this->escape($limit));
            $group = $this->escape($group);
            (!$group) ?: $sql.=" GROUP BY $group ";
            (!$order) ?: $sql.=" ORDER BY $order ";
            (!$limit) ?: $sql.=" LIMIT $limit ";

            $result = $this->_run($sql);
            $output=array();
            if(is_object($result)) {
                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    $output[] = $row;
                }
                mysqli_free_result($result);
            }
            return ($output) ? $output : false;
        }

        /**
         * Get database server version.
         * @return string|false   Return version(string) or false on error.
         */
        public function ver()
        {
            $result = $this->query("SELECT version() as ver")[0]['ver'];
            return $result;
        }

        /**
         * Check if the table exist or not.
         * @param  string $table   Table name.
         * @return  bool    Return true if table is exist and false if not exist.
         */
        public function isTable($table)
        {
            $table = $this->escape($table);
            $result = $this->_run("show tables like '$table'");
            return (mysqli_fetch_array($result, MYSQLI_ASSOC)) ? true : false;
        }

        /**
         * Get the table information.
         * @param  string $table    Table name.
         * @return  array|false Return array if any result; false on error or empty result.
         */
        public function tableInfo($table)
        {
            $table = $this->escape($table);
            return ($this->query("show table status from ".$this->database." WHERE Name='$table'")[0]) ?? false;
        }

        /**
         * Get the table columns list.
         * @param  string $table    Table name.
         * @return  array|false Return array if any result; false on error or empty result.
         */
        public function tableCol($table)
        {
            $table = $this->escape($table);
            $result = $this->query("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE TABLE_NAME='$table' AND TABLE_SCHEMA='$this->database'");
            if ($result) foreach ($result as $col) $columns[] = $col['COLUMN_NAME'];
            return ($columns) ?? False;
        }

        /**
         * Truncate the table.
         * @param  string $table    Table name.
         * @return  bool
         */
        public function empty($table)
        {
            $table = $this->escape($table);
            return $this->_run("TRUNCATE TABLE `$table`");
        }

        /**
         * Delete row by key and val.
         * Only affect on first row (limit 1)
         * @param string $table    Table name.
         * @param int|string $id    Row key value
         * @param string $key   Key column
         * @return bool
         */
        public function deleteId($table, $id, $key='id') {
            $table = $this->escape($table);
            $id = $this->escape($id);
            $key = $this->escape($key);
            $id = intval($this->escape($id));
            return $this->_run("DELETE FROM `$table` Where `$key`='$id' limit 1");
        }

        /**
         * Delete multi row.
         * @param string $table    Table name.
         * @param string|null $where    WHERE Condition
         * @param string|null $end  End date for Time range
         * @param string $start     Start date for Time range
         * @return bool
         */
        public function deleteAny($table, $where=null, $end=null, $start='0000-00-00') {
            $table = $this->escape($table);
            $end       = $this->escape($end);
            $start     = $this->escape($start);
            $sql       = "DELETE FROM $table WHERE ";
            $sql      .= (!$where) ? null : " $where AND ";
            $sql      .= (!$end) ? ' 1 ': " DATE(timestamp) between '$start' and '$end' ";
            return $this->_run($sql);
          }

        /**
         * Insert row.
         * @param array $input  Contain value for key same as every column
         * @param string $table    Table name.
         * @return int|false    Return inserted id if any result; false on error.
         *
         */
        public function insert($table, $input, $update=0)
        {
            $table = $this->escape($table);
            $data = array();
            foreach($input as $k => $v)
            {
                $key         = $this->escape($k);
                $data[$key]  = $this->escape($v);
            }
            $columns = implode(", ",array_keys($data));
            $values  = implode("', '", $data);
            if($update){
                $sql = "REPLACE INTO `$table` ($columns) VALUES ('$values')";
            } else {
                $sql = "INSERT INTO `$table` ($columns) VALUES ('$values')";
            }

            return $this->_run($sql,1);
        }

        /**
         * Update query maker
         * @param string $table    Table name.
         * @param array $data   Contain value for key same as every column
         * @return string   Mixed sql query for update
         */
        private function _updateSQL($table, $data)
        {
            $table = $this->escape($table);
            $sql    = "UPDATE `$table` SET";
            foreach ($data as $k => $v) {
                $column  = $this->escape($k);
                $value   = $this->escape($v);
                $sql    .= " $column='$value'";
                end($data);
                $sql    .= ($k === key($data)) ? null : ',';
            }
            return $sql;
        }

        /**
         * Update row by id
         * @param int $id   row id
         * @param array $data   Contain value for key same as every column
         * @param string $table    Table name.
         * @return bool
         */
        public function updateId($table, $id, $data)
        {
            $table   = $this->escape($table);
            $id      = intval($this->escape($id));
            $sql     = $this->_updateSQL($table, $data);
            $sql    .= " WHERE id=$id";
            return $this->_run($sql);
        }

        /**
         * Update Multi row by id
         * @param string $ids | Ids separated by ','
         * @param array $data   Contain value for key same as every column
         * @param string $table    Table name.
         * @return bool
         */
        public function updateMultiIds($table, $ids, $data)
        {
            $table    = $this->escape($table);
            $ids      = intval($this->escape($ids));
            $sql     = $this->_updateSQL($table, $data);
            $sql    .= " WHERE id IN ($ids)";
            return $this->_run($sql);
        }

        /**
         * Update multi row
         * @param array $data   Contain value for key same as every column
         * @param string $table Table name.
         * @param string|null $where    WHERE Condition
         * @param string|null $end  End date for Time range
         * @param string $start Start date for Time range
         * @return bool
         */
        public function updateAny($table, $data, $where=null, $end=null, $start='0000-00-00')
        {
            $table    = $this->escape($table);
            $end       = $this->escape($end);
            $start     = $this->escape($start);
            $sql       = $this->_updateSQL($table, $data).' WHERE ';
            $sql      .= (!$where) ? null : " $where AND ";
            $sql      .= (!$end) ? ' 1 ': " DATE(timestamp) between '$start' and '$end' ";
            return $this->_run($sql);
        }

        /**
         * Increase column value
         * @param string $column Target $column
         * @param string|null $where    WHERE Condition
         * @param int $count value to add
         * @param string $table Table name
         * @return bool
         */
        public function increase($table, $column, $where=null, $count=1)
        {
            $table      = $this->escape($table);
            $column     = $this->escape($column);
            $count      = intval ($this->escape($count));
            $sql        = "UPDATE $table SET $column=$column+$count";
            (!$where)  ?: $sql.=" WHERE $where ";
            return $this->_run($sql);
        }

        /**
         * Decrease value
         * @param string $column Target $column
         * @param string|null $where    WHERE Condition
         * @param int $count value to add
         * @param string $table Table name
         * @return bool
         */
        public function decrease($table, $column, $where=null, $count=1)
        {
            $table      = $this->escape($table);
            $column     = $this->escape($column);
            $count      = intval ($this->escape($count));
            $sql        = "UPDATE $table SET $column=$column-$count";
            (!$where)  ?: $sql.=" WHERE $where ";
            return $this->_run($sql);
        }

        /**
         * Append string.
         * @param string $table Table name
         * @param string $column Target $column
         * @param string|null $where    WHERE Condition
         * @param $string Appended String
         * @return bool
         */
        public function append($table, $column, $where=null,$string)
        {
            $table      = $this->escape($table);
            $column     = $this->escape($column);
            $sql        = "UPDATE $table SET $column = CONCAT($column, '$string')";
            (!$where)  ?: $sql.=" WHERE $where ";
            return $this->_run($sql);
        }

        /**
         * Check if row exist.
         * @param string|null $where    WHERE Condition
         * @param string|null $end  End date for Time range
         * @param string $start Start date for Time range
         * @param string $table Table name
         * @return int|false
         */
        public function exist($table, $where=null, $end=null, $start='000-00-00')
        {
            $table      = $this->escape($table);
            $start      = $this->escape($start);
            $end        = $this->escape($end);
            $sql        = "SELECT * FROM $table WHERE ";
            $sql       .= (!$where) ? null : " $where AND ";
            $sql       .= (!$end) ? ' 1 ': " DATE(timestamp) between '$start' and '$end' ";
            $result     = $this->query($sql);
            return ($result) ? count($result) : false;
        }

        /**
         * Count column rows
         * @param string $table Table name
         * @param string|null $where    WHERE Condition
         * @param string $col   Target $column
         * @return int|false
         */
        public function countCol($table, $where=null, $col='id')
        {
            $table      = $this->escape($table);
            $sql        = "SELECT COUNT($col) as count FROM $table";
            $sql       .= (!$where) ? null : " WHERE $where";
            return $this->query($sql,1)[0]['count'];
        }

        /**
         * iSQL Count rows.
         * @param string|null $where    WHERE Condition
         * @param string|null $end  End date for Time range
         * @param string $start Start date for Time range
         * @param string $table Table name
         * @return int|bool
         */
        public function count($table, $where=null, $end=null, $start='000-00-00')
        {
            $table      = $this->escape($table);
            $start      = $this->escape($start);
            $end        = $this->escape($end);
            $sql        = "SELECT COUNT(*) as count FROM $table WHERE ";
            $sql       .= (!$where) ? null : " $where AND ";
            $sql       .= (!$end) ? ' 1 ': " DATE(timestamp) between '$start' and '$end' ";
            return $this->query($sql,1)[0]['count'];
        }

        /**
         * Sum column
         * @param string $table Table name
         * @param string $column   Target $column
         * @param string|null $where    WHERE Condition
         * @param string|null $end  End date for Time range
         * @param string $start Start date for Time range
         * @return int|bool
         */
        public function sum($table, $column, $where=null, $end=null, $start='000-00-00')
        {
            $table      = $this->escape($table);
            $start      = $this->escape($start);
            $end        = $this->escape($end);
            $column     = $this->escape($column);
            $sql        = "SELECT SUM($column) as sum FROM $table WHERE ";
            $sql       .= (!$where) ? null : " $where AND ";
            $sql       .= (!$end) ? ' 1 ': " DATE(timestamp) between '$start' and '$end' ";
            return $this->query($sql,1)[0]['sum'];
        }

        /**
         * Average column.
         * @param string $table Table name
         * @param string $column   Target $column
         * @param string|null $where    WHERE Condition
         * @param string|null $end  End date for Time range
         * @param string $start Start date for Time range
         * @return int|bool
         */
        public function avg($table, $column, $where=null, $end=null, $start='000-00-00')
        {
            $table      = $this->escape($table);
            $start      = $this->escape($start);
            $end        = $this->escape($end);
            $column     = $this->escape($column);
            $sql        = "SELECT AVG($column) as avg FROM $table WHERE ";
            $sql       .= (!$where) ? null : " $where AND ";
            $sql       .= (!$end) ? ' 1 ': " DATE(timestamp) between '$start' and '$end' ";
            return $this->query($sql,1)[0]['avg'];
        }

        /**
         * Get row status
         * @param int $id target id
         * @param string $table Table name
         * @return bool|mixed
         */
        public function getStatus($table, $id)
        {
            $table      = $this->escape($table);
            $id      = intval($this->escape($id));
            $result = $this->query("SELECT status FROM $table WHERE id=$id",1);
            return ($result) ?  $result[0]['status'] : False;
        }

        /**
         * Get row timestamp.
         * @param int $id target id
         * @param string $table Table name
         * @return bool|mixed
         */
        public function timestamp($table, $id)
        {
            $table      = $this->escape($table);
            $id      = intval($this->escape($id));
            $result = $this->query("SELECT timestamp FROM $table WHERE id=$id",1);
            return ($result) ?  $result[0]['timestamp'] : False;
        }

        /**
         * Select Rows
         * @param string $table Table name
         * @param string|null $where    WHERE Condition
         * @param string $column   Target $column
         * @param int|null $limit
         * @param string|null $order
         * @param string|null $group
         * @param string|null $end  End date for Time range
         * @param string $start Start date for Time range
         * @return array|bool
         */
        public function select($table, $where=null, $column='*', $limit=null, $order=null, $group=null, $end=null, $start='000-00-00')
        {
            $table      = $this->escape($table);
            $column     = $this->escape($column);
            $sql        = "SELECT $column FROM $table WHERE";
            $sql       .= (!$where) ? null : " $where AND ";
            $sql       .= (!$end) ? ' 1 ': " DATE(timestamp) between '$start' and '$end' ";
            return $this->query($sql, $limit, $order, $group);
        }

        /**
         * Select rRow.
         * @param string|null $where
         * @param string|null $order
         * @param string $table Table name
         * @return array|bool
         */
        public function selectRow($table, $where=null, $order=null)
        {
            $table      = $this->escape($table);
            $sql = "SELECT * FROM $table";
            (!$where) ?: $sql.=" WHERE $where ";
            return $this->query($sql, 1, $order)[0] ?? false;
        }

        /**
         * Select row by id.
         * @param int $id target id
         * @param string|null $column select column
         * @param string $table Table name
         * @return array|bool
         */
        public function selectId($table, $id, $column='*')
        {
            $table      = $this->escape($table);
            $column     = $this->escape($column);
            $id         = intval($this->escape($id));
            return $this->query("SELECT $column FROM $table WHERE id=$id",1)[0] ?? false;
        }

        /**
         * Select All.
         * @param int|null $limit
         * @param string|null $order
         * @param string $table Table name
         * @return array|bool
         */
        public function selectAll($table, $limit=null, $order=null)
        {
            $table      = $this->escape($table);
            $sql = "SELECT * FROM $table";
            return $this->query($sql, $limit, $order);
        }

        /**
         * Log query and errors.
         * @param  string $type 'e' for Error Only, 'sql' for SQL only, null and other for All.
         * @return array
         */
        public function log($type=null)
        {
            if ($type=='e') {
                return $this->error;
            } elseif ($type=='sql') {
                return $this->sql;
            } else  {
                $logs=array();
                foreach ($this->sql as $i => $sql)
                {
                    $logs[$i]['SQL']=$sql;
                    $logs[$i]['Status']= ($this->error[$i]) ?? true;
                }
                return $logs;
            }
        }

    }

### Test Pad

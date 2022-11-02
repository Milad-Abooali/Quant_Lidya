<?php

/**
 * DataTable Server Side
 * @package    -
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  -
 * @license    -
 * @version    1.0.17
 * @update     9:04 AM Friday, December 11, 2020
 */

class qsp {

    /**
     * @var iSQL $db Database iSQL Object
     */
    private $db, $debug, $dt_columns=array();

    /**
     * Constructor.
     */
    function __construct() {
        session_write_close();
        global $db;
        $this->db = $db;
        $this->debug = 1;
    }

    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     * @param array $a Array to get data from
     * @param string $prop Property to read
     * @return void
     */
    private function _pluck($a, $prop)
    {
        for ($i=0, $len=count($a); $i<$len; $i++) $this->dt_columns[] = $a[$i][$prop];
    }

    /**
     * Limit
     *
     * @param $call
     * @return string|null
     */
    private function _limit($call)
    {
        return (isset($call['start']) && $call['length'] != -1) ? "LIMIT ".intval($call['start']).", ".intval($call['length']) : null;
    }

    /**
     * Order
     *
     * @param $call
     * @param $columns
     * @return string|null
     */
    private function _Order($call, $columns)
    {
        $len_order = (isset($call['order'])) ? count($call['order']) : false;
        $order = null;
        if ($len_order) {
            $orderBy = array();
            for ($i=0; $i<$len_order; $i++) {
                $columnIdx = intval($call['order'][$i]['column']);
                $callColumn = $call['columns'][$columnIdx];
                $columnIdx = array_search($callColumn['data'], $this->dt_columns);
                $column = $columns[$columnIdx];
                if ($callColumn['orderable'] == 'true') {
                    $dir = $call['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC';
                    $orderBy[] = ''.$column['db'].' '.$dir;
                }
            }
            if ($orderBy) $order = 'ORDER BY '.implode(', ', $orderBy);
        }
        return $order;
    }

    /**
     * Mix Filters
     *
     * @param array $call
     * @param array $columns
     * @return array
     */
    private function _filterMixer ($call, $columns) {
        $is_mix = isset($call['search']) && $call['search']['value'] != '';
        $ien_columns  = (is_array($call['columns'])) ? count($call['columns']) : 0;
        if ($is_mix) {
            $str = $this->db->escape($call['search']['value']);
            for ($i=0; $i<$ien_columns; $i++) {
                $callColumn = $call['columns'][$i];
                $columnIdx = array_search($callColumn['data'], $this->dt_columns);
                $column = $columns[$columnIdx];
                if ($callColumn['searchable'] == 'true') $output['global'][] = " ".$column['db']." LIKE '%".''.$str."%'";
            }
        }
        for($i=0; $i<$ien_columns; $i++) {
            $callColumn = $call['columns'][$i];
            $columnIdx = array_search($callColumn['data'], $this->dt_columns);
            $column = $columns[$columnIdx];
            $str = $this->db->escape($callColumn['search']['value']);
            $regex = $callColumn['search']['regex'];
            if ($callColumn['searchable'] == 'true' && $str != '') $output['column'][] = ($regex == 'true') ? " ".$column['db']." REGEXP '".$str."'" : " ".$column['db']." LIKE '%".''.$str."%'";
        }
        return $output;
    }

    /**
     * Mix Filters Union
     *
     * @param array $call
     * @param array $columns
     * @return array
     */
    private function _filterMixerUnion ($call, $columns) {
        $is_mix = isset($call['search']) && $call['search']['value'] != '';
        $ien_columns  = count($call['columns']);
        if ($is_mix) {
            $str = $this->db->escape($call['search']['value']);
            for ($i=0; $i<$ien_columns; $i++) {
                $callColumn = $call['columns'][$i];
                $columnIdx = array_search($callColumn['data'], $this->dt_columns);
                $column = $columns[$columnIdx];
                if ($callColumn['searchable'] == 'true') $output['global'][] = " U.".$column['dbu']." LIKE '%".''.$str."%'";
            }
        }
        for($i=0; $i<$ien_columns; $i++) {
            $callColumn = $call['columns'][$i];
            $columnIdx = array_search($callColumn['data'], $this->dt_columns);
            $column = $columns[$columnIdx];
            $str = $this->db->escape($callColumn['search']['value']);
            $regex = $callColumn['search']['regex'];
            if ($callColumn['searchable'] == 'true' && $str != '') $output['column'][] = ($regex == 'true') ? " U.".$column['dbu']." REGEXP '".$str."'" : " U.".$column['dbu']." LIKE '%".''.$str."%'";
        }
        return $output;
    }

    /**
     * Custom Operation
     * filter columns
     *
     * @param array|string $operator Operation
     * @param array|string $columns Target Column(s)
     * @param null|array|string $params Parameter(s)
     * @return bool|string
     */
    private function _customOperation($operator, $columns, $params=null) {
        $is_single = array(
            ">",
            "<",
            "=",
            ">=",
            "<=",
            "=>",
            "=<",
            "<>",
            "!="
        );
        if(in_array($operator, $is_single) && $columns){
            if(is_array($columns)) return "(".$columns[0]." $operator ".$columns[1].")";
            return "$columns $operator '$params'";
        }

        $is_inset = array(
            "IN",
            "NOT IN",
        );
        if (in_array($operator, $is_inset) && $columns && $params) {
            $params = "'".implode("','", $params)."'";
            return "$columns $operator ($params)";
        }
        $is_between = array(
            "BETWEEN"
        );
        if (in_array($operator, $is_between) && $columns && $params) {
            foreach ($params as $k => $parm) $params[$k] = str_replace("T", " ", $parm);
            return "$columns $operator '".$params[0]."' AND '".$params[1]."'";
        }

        return false;
    }

    private function mixData($res_async, $columns)
    {
        $data = array();
        $i=0;
        if ($res_async) while($row = $res_async->fetch_row()) {
            $data[] = $row;
            foreach ($columns as $column) {
                if (isset($column['formatter'])) {
                    $file = "../includes/data-table-formatter/".$_GET['table_html'].".php";
                    if (file_exists($file)) include_once $file;
                    $func = 'c_'.$column['dt'];
                    if (function_exists ($func)) $data[$i][$column['dt']] = $func($row[$column['dt']],$i,$row);
                }
            }
            $i++;
        }
        return $data;
    }

    /**
     * Simple
     *
     *   0. Group By
     *   1. Total Counter (Async)
     *   2. Columns Sorting
     *   3. Filter Mixer
     *   4. Custom Operation
     *   5. Where
     *   6. Filtered Counter (Async)
     *   7. Limit
     *   8. Order
     *   9. Data Query
     *  10. Select Data (Async)
     *  11. Total Count (DB Result)
     *  12. Filtered Count (DB Result)
     *  13. Data (DB Result)
     *  14. Mix Data (DT Format)
     *  15. Make Output
     *  16. Debugger
     *
     * @param array $call   array from clint side (POST / GET)
     * @param string $table table name
     * @param array $columns array of columns and settings of columns
     * @param string|bool $where_fit where condition
     * @return string Datatable setting and table data in JSON
     */
    public function simple($call, $table, $columns, $where_fit=false)
    {

        // Group By
        $group_by = null;
        if ($call['GroupBy'] ?? false) $group_by  = ' GROUP BY '.$call['GroupBy'].' ';

        // Total Counter (Async)
        $db_async['total_records'] = new iSQL(DB_admin);
        $query['total_records'] = ($group_by) ? ("SELECT * FROM $table ".(($where_fit) ? " WHERE $where_fit" : null).$group_by) : ("SELECT COUNT(*) FROM $table ".(($where_fit) ? " WHERE $where_fit" : null));
        $db_async['total_records']->LINK->query($query['total_records'] , MYSQLI_ASYNC);

        // Columns Sorting
        $this->_pluck($columns, 'dt' );

        // Filter Mixer
        $filter = $this->_filterMixer($call, $columns);

        // Custom Operation
        $operations = false;
        if ($call['CustomOperation']) $operations = json_decode($call['CustomOperation'],true);
        if ($operations) foreach ($operations as $CustomOperation) {
            $item = json_decode($CustomOperation,true);
            if ($item['operator']) $filter['column'][] = $this->_customOperation($item['operator'], $item['columns'], $item['params']);
        }

        // Where
        $filter_global = ($filter['global']) ? ' ('.implode(' OR ', $filter['global']).') ' : null;
        $filter_column = ($filter['column']) ? '  '.implode(' AND ', $filter['column']) : null;
        $where = ($where_fit) ?? null;
        if ($filter_column) $where .= ($where) ? (' AND '.$filter_column) : $filter_column;
        if ($filter_global) $where .= ($where) ? (' AND '.$filter_global) : $filter_global;

        // Filtered Counter (Async)
        If ($filter) {
            $db_async['filtered_records'] = new iSQL(DB_admin);
            $query['filtered_records'] = ($group_by) ? "SELECT * FROM $table $group_by HAVING $where" : "SELECT COUNT(*) FROM $table WHERE $where";
            $db_async['filtered_records']->LINK->query($query['filtered_records'], MYSQLI_ASYNC);
        }

        // Limit
        $limit = $this->_limit($call);

        // Order
        $order = $this->_order($call, $columns);

        // Data Query
        $query['data'] = "SELECT ";
        # Columns
        $column_union = array();
        foreach ($columns as $column) $column_union[] = $column['db'];
        $query['data'] .= implode(", ", $column_union).' ';
        # Table
        $query['data'] .= " From $table ";
        # Where
        $query['data'] .= ($where && !$group_by) ? ' WHERE '.$where : null;
        # Group
        if ($group_by) $query['data'] .= $group_by;
        # Having
        $query['data'] .= ($where && $group_by) ? ' HAVING '.$where : null;
        # Order & Limit
        $query['data'] .= ' '. $order .' '. $limit;

        // Select Data
        $db_async['data'] = new iSQL(DB_admin);
        $db_async['data']->LINK->query($query['data'], MYSQLI_ASYNC);

        //  Total Count (DB Result)
        $res_async['total_records'] = $db_async['total_records']->LINK->reap_async_query();
        if($group_by) {
            $filtered_records = $total_records = ($res_async['total_records']) ? mysqli_num_rows($res_async['total_records']) : 0;
        } else {
            $filtered_records = $total_records = ($res_async['total_records']) ? $res_async['total_records']->fetch_row()[0] : 0;
        }

        //  Filtered Count (DB Result)
        If ($filter) {
            $res_async['filtered_records'] = $db_async['filtered_records']->LINK->reap_async_query();
            if($group_by) {
                $filtered_records = ($res_async['filtered_records']) ? mysqli_num_rows($res_async['filtered_records']) : 0;
            } else {
                $filtered_records = ($res_async['filtered_records']) ? $res_async['filtered_records']->fetch_row()[0] : 0;
            }
        }

        //  Data (DB Result)
        $res_async['data'] = $db_async['data']->LINK->reap_async_query();

        //  Mix Data (DT Format)
        $data = $this->mixData($res_async['data'], $columns);

        // Make Output
        $output['draw'] = isset($call['draw']) ? intval($call['draw']) : 0;
        $output['recordsTotal'] = $total_records;
        $output['recordsFiltered'] = $filtered_records;
        $output['data'] = $data;

        // Debugger
        if(Broker['maintenance'] || $this->debug) {
            if ($query) $output['debug']['query'] = $query;
            if ($filter) $output['debug']['filter'] = $filter;
            if ($table) $output['debug']['table'] = $table;
            if ($where) $output['debug']['where'] = $where;
            if ($operations) $output['debug']['operations'] = $operations;
            if ($group_by) $output['group_by'] = $call['GroupBy'];
        }

        return $output;
    }

    /**
     * Complex
     *
     *   0. Group By
     *   1. Count All (Async)
     *   2. Columns Sorting
     *   3. Filter Mixer
     *   4. Custom Operation
     *   5. Where
     *   6. Filtered Counter (Async)
     *   7. Limit
     *   8. Order
     *   9. Data Query
     *  10. Select Data (Async)
     *  11. Total Count (DB Result)
     *  12. Filtered Count (DB Result)
     *  13. Data (DB Result)
     *  14. Mix Data (DT Format)
     *  15. Make Output
     *  16. Debugger
     *
     * @param array $call array from clint side (POST / GET)
     * @param string $query_from Part of SQL query after 'FROM', accept JOIN.
     * @param array $columns array of columns and settings of columns
     * @param string|bool $where_fit where condition
     * @return string Datatable setting and table data in JSON
     */
    public function complex($call, $query_from, $columns)
    {

        // Group By
        $group_by = null;
        if ($call['GroupBy'] ?? false) $group_by  = ' GROUP BY '.$call['GroupBy'].' ';

        // Count All (Async)
        $db_async['total_records'] = new iSQL(DB_admin);
        $query['total_records'] = ($group_by) ? "SELECT * FROM $query_from $group_by" : "SELECT COUNT(*) FROM ".$query_from;
        $db_async['total_records']->LINK->query($query['total_records'] , MYSQLI_ASYNC);

        // Columns Sorting
        $this->_pluck($columns, 'dt' );

        // Filter Mixer
        $filter = $this->_filterMixer($call, $columns);

        // Custom Operation
        $operations = false;
        if ($call['CustomOperation']) $operations = json_decode($call['CustomOperation'],true);
        if ($operations) foreach ($operations as $CustomOperation) {
            $item = json_decode($CustomOperation,true);
            if($item['operator']) $filter['column'][] = $this->_customOperation($item['operator'], $item['columns'], $item['params']);
        }

        // Where
        $filter_global = ($filter['global']) ? ' ('.implode(' OR ', $filter['global']).') ' : null;
        $filter_column = ($filter['column']) ? '  '.implode(' AND ', $filter['column']) : null;

        $where = null;
        if ($filter_column) $where  = ($group_by) ? $filter_column : (' AND '.$filter_column);
        if ($filter_column && $filter_global && $group_by) $where .= ' AND ';
        if ($filter_global) $where .= ($group_by) ? $filter_global : (' AND '.$filter_global);

        // Filtered Counter (Async)
        If ($filter) {
            $db_async['filtered_records'] = new iSQL(DB_admin);
            $query['filtered_records'] = ($group_by) ? "SELECT * FROM $query_from $group_by HAVING $where" : "SELECT COUNT(*) FROM $query_from $where";
            $db_async['filtered_records']->LINK->query($query['filtered_records'], MYSQLI_ASYNC);
        }

        // Limit
        $limit = $this->_limit($call);

        // Order
        $order = $this->_order($call, $columns);

        // Data Query
        $query['data'] = "SELECT ";
        # Columns
        $column_union = array();
        foreach ($columns as $column) $column_union[] = $column['db'];
        $query['data'] .= implode(", ", $column_union).' ';
        # Tables (Join)
        $query['data'] .= " FROM $query_from ";
        # Where
        if ($where && !$group_by) $query['data'] .= $where;
        # Group
        if ($group_by) $query['data'] .= $group_by;
        #Having
        $query['data'] .= ($where && $group_by) ? ' HAVING '.$where : null;
        # Order & Limit
        $query['data'] .= ' '. $order .' '. $limit;

        // Select Data
        $db_async['data'] = new iSQL(DB_admin);
        $db_async['data']->LINK->query($query['data'], MYSQLI_ASYNC);

        //  Total Count (DB Result)
        $res_async['total_records'] = $db_async['total_records']->LINK->reap_async_query();
        if($group_by) {
            $filtered_records = $total_records = ($res_async['total_records']) ? mysqli_num_rows($res_async['total_records']) : 0;
        } else {
            $filtered_records = $total_records = ($res_async['total_records']) ? $res_async['total_records']->fetch_row()[0] : 0;
        }

        //  Filtered Count (DB Result)
        If ($filter) {
            $res_async['filtered_records'] = $db_async['filtered_records']->LINK->reap_async_query();
            if($group_by) {
                $filtered_records = ($res_async['filtered_records']) ? mysqli_num_rows($res_async['filtered_records']) : 0;
            } else {
                $filtered_records = ($res_async['filtered_records']) ? $res_async['filtered_records']->fetch_row()[0] : 0;
            }
        }

        //  Data (DB Result)
        $res_async['data'] = $db_async['data']->LINK->reap_async_query();

        //  Mix Data (DT Format)
        $data = $this->mixData($res_async['data'], $columns);

        // Make Output
        $output['draw'] = isset($call['draw']) ? intval($call['draw']) : 0;
        $output['recordsTotal'] = $total_records;
        $output['recordsFiltered'] = $filtered_records;
        $output['data'] = $data;

        // Debugger
        if(Broker['maintenance'] || $this->debug) {
            if ($query) $output['debug']['query'] = $query;
            if ($filter) $output['debug']['filter'] = $filter;
            if ($query_from) $output['debug']['query_from'] = $query_from;
            if ($where) $output['debug']['where'] = $where;
            if ($operations) $output['debug']['operations'] = $operations;
            if ($group_by) $output['group_by'] = $call['GroupBy'];
        }

        return $output;
    }

    /**
     * Union
     *
     *   1. Union Columns
     *   2. Count All (Async)
     *   3. Columns Sorting
     *   4. Filter Mixer Union
     *   5. Custom Operation
     *   6. Where
     *   7. Filtered Counter (Async)
     *   8. Limit
     *   9. Order
     *  10. Data Query
     *  11. Select Data (Async)
     *  12. Total Count (DB Result)
     *  13. Filtered Count (DB Result)
     *  14. Data (DB Result)
     *  15. Mix Data (DT Format)
     *  16. Make Output
     *  17. Debugger
     *
     * UNION
     * @param $call
     * @param $query_from
     * @param $columns
     * @param null $where_fit
     * @return mixed
     */
    public function Union($call, $query_from, $columns, $where_fit=null)
    {

        // Union Columns
        $column_union = array();
        foreach ($columns as $column) $column_union[] = $column['db'];
        
        // Count All (Async)
        $db_async['total_records'] = new iSQL(DB_admin);
        $query['total_records'] = "SELECT COUNT(*) FROM (SELECT ". implode(", ", $column_union)." FROM ".$query_from.") AS U";
        $db_async['total_records']->LINK->query($query['total_records'] , MYSQLI_ASYNC);

        // Columns Sorting
        $this->_pluck($columns, 'dt' );

        // Filter
        $filter = $this->_filterMixerUnion($call, $columns);

        // Custom Operation
        $operations = false;
        if ($call['CustomOperation']) $operations = json_decode($call['CustomOperation'],true);
        if ($operations) foreach ($operations as $CustomOperation) {
            $item = json_decode($CustomOperation,true);
            if($item['operator']) $filter['column'][] = $this->_customOperation($item['operator'], $item['columns'], $item['params']);
        }

        // Where
        $filter_global = ($filter['global']) ? ' ('.implode(' OR ', $filter['global']).') ' : null;
        $filter_column = ($filter['column']) ? '  '.implode(' AND ', $filter['column']) : null;
        $where = ($where_fit) ?? null;
        if ($filter_column) $where .= ($where) ? (' AND '.$filter_column) : $filter_column;
        if ($filter_global) $where .= ($where) ? (' AND '.$filter_global) : $filter_global;

        // Filtered Counter (Async)
        If ($filter) {
            $db_async['filtered_records'] = new iSQL(DB_admin);
            $query['filtered_records'] = "SELECT COUNT(*) FROM (SELECT ". implode(", ", $column_union) ." FROM ".$query_from.') AS U WHERE 1 '.$where;
            $db_async['filtered_records']->LINK->query($query['filtered_records'], MYSQLI_ASYNC);
        }

        // Limit
        $limit = $this->_limit($call);

        // Order
        $order = $this->_order($call, $columns);

        // Data Query
        $query['data'] = "SELECT * FROM (SELECT ";
        # Columns
        $query['data'] .= implode(", ", $column_union).' ';
        # Tables (Join)
        $query .= " FROM $query_from) AS U WHERE 1 ";
        # Where
        if ($where) $query['data'] .= $where;
        # Order & Limit
        $query['data'] .= ' '. $order .' '. $limit;

        // Select Data
        $db_async['data'] = new iSQL(DB_admin);
        $db_async['data']->LINK->query($query['data'], MYSQLI_ASYNC);

        //  Total Count (DB Result)
        $res_async['total_records'] = $db_async['total_records']->LINK->reap_async_query();
        $filtered_records = $total_records = ($res_async['total_records']) ? $res_async['total_records']->fetch_row()[0] : 0;

        //  Filtered Count (DB Result)
        If ($filter) {
            $res_async['filtered_records'] = $db_async['filtered_records']->LINK->reap_async_query();
            $filtered_records = ($res_async['filtered_records']) ? $res_async['filtered_records']->fetch_row()[0] : 0;
        }

        //  Data (DB Result)
        $res_async['data'] = $db_async['data']->LINK->reap_async_query();

        //  Mix Data (DT Format)
        $data = $this->mixData($res_async['data'], $columns);

        // Make Output
        $output['draw'] = isset($call['draw']) ? intval($call['draw']) : 0;
        $output['recordsTotal'] = $total_records;
        $output['recordsFiltered'] = $filtered_records;
        $output['data'] = $data;

        // Debugger
        if(Broker['maintenance'] || $this->debug) {
            if ($query) $output['debug']['query'] = $query;
            if ($filter) $output['debug']['filter'] = $filter;
            if ($table) $output['debug']['table'] = $table;
            if ($where) $output['debug']['where'] = $where;
            if ($where) $output['debug']['where'] = $where;
            if ($operations) $output['debug']['operations'] = $operations;
        }

        return $output;
    }

}
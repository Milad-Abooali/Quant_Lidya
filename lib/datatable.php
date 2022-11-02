<?php

    /**
     * Class DataTable
     *
     * Mahan | DataTable generatore
     *
     * @package    App\Core
     * @author     Milad Abooali <m.abooali@hotmail.com>
     * @copyright  2012 - 2020 Codebox
     * @license    http://codebox.ir/license/1_0.txt  Codebox License 1.0
     * @version    1.0
     */

    class DataTable
    {

        private $query,$columns,$table,$sql_details,$primaryKey,$qsp;

        /**
         * DataTable constructor.
         * @param $sql_details
         * @param $table
         * @param $columns
         * @param $query
         * @param string $primaryKey
         */
        function __construct($sql_details,$table,$columns,$query=null,$primaryKey='id') {
            $this->sql_details = array(
              'user' => $sql_details['username'],
              'pass' => $sql_details['password'],
              'db'   => $sql_details['name'],
              'host' => $sql_details['hostname']
            );
            $this->table = $table;
            $this->columns = $columns;
            $this->query = $query;
            $this->primaryKey = $primaryKey;
        }


        /**
         * Simple Table
         * @return false|string
         */
        public function simple () {
            global $db;
            $this->qsp = new qsp($db);
            return json_encode($this->qsp->simple($_POST, $this->table, $this->columns, $this->query));
        }

        /**
         * Complex Table
         * @return false|string
         */
        public function Complex () {
            global $db;
            $this->qsp = new qsp($db);
            return json_encode($this->qsp->Complex($_POST, $this->query, $this->columns));
        }

        /**
         * Union Table
         * @return false|string
         */
        public function Union() {
            global $db;
            $this->qsp = new qsp($db);
            return json_encode($this->qsp->Union($_POST, $this->query, $this->columns));
        }

    }
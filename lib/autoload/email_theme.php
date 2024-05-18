<?php

    /**
     * Class EmailTheme
     *
     * Mahan | Email Them
     *
     * @package    -
     * @author     Milad Abooali <m.abooali@hotmail.com>
     * @copyright  -
     * @license    -
     * @version    1.0
     */

    class Email_Theme
    {

        private $db,$table,$path;

        function __construct() {
            global $db;
            $this->db = $db;
            $this->table = 'email_themes';
            $this->path = 'includes' . DIRECTORY_SEPARATOR . 'email-themes' . DIRECTORY_SEPARATOR;

        }

        /**
         * Make Theme
         * @param $id
         * @param $data
         * @param null $message
         * @return mixed
         */
        public function make($id, $data=null, $message=null)
        {
            global $_L;
            $path = false;
            if (file_exists($this->path . $id . '.htm')) {
                $path = $this->path . $id . '.htm';
            } else {
                $path = CRM_ROOT . $this->path . $id . '.htm';
            }
            if ($path == false) {
                return false;
            }
            $content = file_get_contents($path);
            $searchVal[] = '{__ExtraMessage__}';
            $replaceVal[] = $message;
            if ($data) foreach ($data as $k => $v) {
                $searchVal[] = '{~~'.$k.'~~}';
                $replaceVal[] = $v;
            }
            return str_replace($searchVal, $replaceVal, $content);
        }

        /**
         * Load Theme
         * @param $id
         * @return mixed
         */
        public function load($id)
        {
            $output['data'] = $this->db->selectId($this->table,$id);
            $content = file_get_contents($this->path.$id.'.htm');
            $output['content'] = $content;
            return $output;
        }

        /**
         * Creat New Theme
         * @param $name
         * @param $cat
         * @return bool|int|mysqli_result|string
         */
        public function creat($name,$cat)
        {
            $data['name']      = $name;
            $data['cat']       = $cat;
            $data['update_by'] = $_SESSION['id'];
            $id = $this->db->insert($this->table,$data);
            if($id) {
                $themeFile = fopen('../'.$this->path.$id.'.htm', "w") or die("Unable to open file!");
                fclose($themeFile);
            }
            // Add actLog
            global $actLog; $actLog->add('Email',$id,1,'{"act":"Creat New Theme"}');
            return ($id) ?? false;
        }

        public function update($id,$name,$cat,$content)
        {
            $data['name']      = $name;
            $data['cat']       = $cat;
            $data['update_by'] = $_SESSION['id'];
            $update = $this->db->updateId($this->table,$id,$data);
            if($update) {
                $themeFile = fopen('../'.$this->path.$id.'.htm', "w") or die("Unable to open file!");
                fwrite($themeFile, $content);
                fclose($themeFile);
            }
            // Add actLog
            global $actLog; $actLog->add('Email',$id,1,'{"act":"Edit Theme"}');
            return $update;
        }

        /**
         * Delete Theme
         * @param $id
         * @return bool
         */
        public function delete($id)
        {
            $file = unlink($this->path.$id.'.htm');
            $database = $this->db->deleteId($this->table,$id);
            // Add actLog
            global $actLog; $actLog->add('Email',$id,1,'{"act":"Delete Theme"}');
            return $file && $database;
        }

    }
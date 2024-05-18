<?php

/**
 * Class EmailM
 *
 * Mahan | Email Manager
 *
 * @package    -
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  -
 * @license    -
 * @version    1.0
 */

class Email_M
{

    private $theme, $db, $table;

    function __construct($theme = 0)
    {
        $this->theme = $theme;
        global $db;
        $this->db = $db;
        $this->table = 'email_log';
    }

    /**
     * Send Email
     * @param array $receivers
     * @param int $theme
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public function send($receivers, $theme = 0, $subject_tag = null, $message = null)
    {
        global $_L;
        $this->theme = ($theme) ?? $this->theme;
        foreach ($receivers as $receiver) {

            $where = "user_id=" . $receiver['id'];
            $user_language = $this->db->selectRow('user_extra', $where)['language'];
            $theme_language = (strlen($user_language) > 3) ? $user_language : 'english';

            $subject = Broker['title'] . ' - ';
            $subject .= $_L->get($theme_language)['emails'][$subject_tag] ?? $subject_tag;

            $receiver['data']['subject'] = $subject;
            $receiver['data']['broker_title'] = Broker['title'];
            $receiver['data']['broker_crm'] = Broker['crm_url'];
            $receiver['data']['broker_signature'] = $_L->get($theme_language)['emails']['Broker_Signature'];


            $headers = "MIME-Version: 1.0" . "\r\n";
            if ($this->theme) {
                $path = $theme_language . DIRECTORY_SEPARATOR . $this->theme;
                $emailThem = new Email_Theme($this->theme);
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $content = $emailThem->make($path, $receiver['data'], $message);
            } else {
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $content = wordwrap($message, 70);
            }
            $headers .= "From: " . Broker['email'];
            $subject_utf = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            if (mail($receiver['email'], $subject_utf, $content, $headers)) return ($this->_log($subject, $content, $receiver));
        }
        return false;
    }

    /**
     * Log Sent Mails in Database
     * @param string $subject
     * @param string $content
     * @param array $receiver
     * @return bool
     */
    private function _log($subject, $content, $receiver)
    {
        $content = base64_encode($content);
        $content_escaped = $this->db->escape($content);
        $data['subject'] = $this->db->escape($subject);
        $data['content'] = $content_escaped;
        $data['user_id'] = $receiver['id'];
        $data['email'] = $receiver['email'];
        $lgo_id = $this->db->insert($this->table, $data);
        return $lgo_id;
    }

}
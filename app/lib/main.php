<?php

/**
 * Main
 */
class main{

    /**
     * @var mixed|string
     */
    public $role;

    public function __construct()
    {
        $this->role = $_SESSION['app']['role'] ?? 'guest';
    }

    /**
     * Check Permit
     * @param string $target
     * @param string $act
     * @param bool $echo
     * @return bool
     */
    public function checkPermit(string $target,string $act, bool $echo=false): bool
    {
        if($this->role==='admin') return true;
        global $permits;
        $res = (bool) $permits->$target[$this->role][$act] ?? false;
        if($echo && !$res)
            echo blocks::permitError($target, $act, $this->role);
        return $res;
    }

    /**
     * Update Role
     * Current session
     */
    public function updateRole(){
        $this->role = $_SESSION['app']['role'] ?? 'guest';
    }

    /**
     * Get Profile
     */
    public static function getProfile(){
        $output = new stdClass();
        global $db;
        global $userManager;
        $user   = $userManager->get($_SESSION['id']);

        // Avatar
        $where = "type='avatar' AND user_id=".$_SESSION['id'];
        $media = $db->selectRow('media', $where);
        if( $media['media'] ) $output->avatar = 'media/'.$media['media'];

        // Genereal
        $output->General['fname']   = $user['user_extra']['fname'];
        $output->General['lname']   = $user['user_extra']['lname'];
        $output->General['email']   = $user['email'];
        $output->General['phone']   = $user['user_extra']['phone'];
        $output->General['country'] = $user['user_extra']['country'];
        $output->General['unit']    = $user['unit'];
        $output->General['type']    = $user['type'];

        // Extra
        $output->Extra['city']           = $user['user_extra']['city'];
        $output->Extra['address']        = $user['user_extra']['address'];
        $output->Extra['interests']      = $user['user_extra']['interests'];
        $output->Extra['hobbies']        = $user['user_extra']['hobbies'];
        $output->Extra['job_cat']        = $db->selectId('job_category', $user['fx']['job_cat']);
        $output->Extra['job_title']      = $user['fx']['job_title'];
        $output->Extra['exp_fx']         = $user['fx']['exp_fx'];
        $output->Extra['exp_fx_year']    = $user['fx']['exp_fx_year'];
        $output->Extra['exp_cfd']        = $user['fx']['exp_cfd'];
        $output->Extra['exp_cfd_year']   = $user['fx']['exp_cfd_year'];
        $output->Extra['income']         = $db->selectId('income', $user['fx']['income']);
        $output->Extra['investment']     = $db->selectId('investment', $user['fx']['investment']);
        $output->Extra['strategy']       = $user['fx']['strategy'];

        // Documents - Bill
        $where = "type='Bill' AND user_id=".$_SESSION['id'];
        $Bill = $db->selectRow('media', $where);
        if( $Bill['media'] ){
            $output->Bill['src'] = 'media/'.$Bill['media'];
            $output->Bill['verify'] = $Bill['verify'];
        }
        // Documents - ID Card
        $where = "type='ID' AND user_id=".$_SESSION['id'];
        $IdCard = $db->selectRow('media', $where);
        if( $IdCard['media'] ){
            $output->IdCard['src'] = 'media/'.$IdCard['media'];
            $output->IdCard['verify'] = $IdCard['verify'];
        }

        // Agreement
        $output->Agreement   =
            ($user['user_extra']['date_approve']!=null && $user['user_extra']['date_approve'] != '0000-00-00 00:00:00')
            ? $user['user_extra']['date_approve']
            : false;

        // Role
        $output->Role =   appSession::checkRole();


        return $output;
    }

    public static function profileProgress(){
       $profile = self::getProfile();
        $profile_item = 0;

        if ($profile->avatar) $profile_item += 2;
        if ($profile->Role) $profile_item += 1;
        if($profile->General['fname']) $profile_item += 2;
        if($profile->General['lname']) $profile_item += 2;
        if($profile->General['email']) $profile_item += 1;
        if($profile->General['phone']) $profile_item += 5;
        if($profile->General['country']) $profile_item += 5;
        if($profile->General['unit']) $profile_item += 2;
        if($profile->General['type']) $profile_item += 1;
        if($profile->Extra['city']) $profile_item += 4;
        if($profile->Extra['address']) $profile_item += 3;
        if($profile->Extra['interests']) $profile_item += 4;
        if($profile->Extra['hobbies']) $profile_item += 4;
        if($profile->Extra['job_cat']) $profile_item += 4;
        if($profile->Extra['job_title']) $profile_item += 5;
        if($profile->Extra['exp_fx']) $profile_item += 2;
        if($profile->Extra['exp_fx_year']) $profile_item += 3;
        if($profile->Extra['exp_cfd']) $profile_item += 2;
        if($profile->Extra['exp_cfd_year']) $profile_item += 3;
        if($profile->Extra['income']) $profile_item += 5;
        if($profile->Extra['investment']) $profile_item += 5;
        if($profile->Extra['strategy']) $profile_item += 5;
        if ($profile->Bill['src']) $profile_item += 2;
        if ($profile->Bill['verify']) $profile_item += 6;
        if ($profile->IdCard['src']) $profile_item += 2;
        if ($profile->IdCard['verify']) $profile_item += 6;
        if ($profile->Agreement) $profile_item += 14;
        return ($profile_item);
    }

    /**
     * Rand String
     * @param $length
     * @return string
     */
    public static function randString( $length ) {
        $chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ123456789";
        return substr(str_shuffle($chars),0,$length);
    }

}
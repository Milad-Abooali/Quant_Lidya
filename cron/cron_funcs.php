<?php

  /**
   * cronJobs Class
   * 16:00 AM Tuesday, November 2, 2020 | M.Abooali
   */
   
  class cronJobs {

    // Add Log
    private function _addLog($func) {
        global $db;
        $insert['func_name'] = $func;
        $insert['start_time'] = date('Y-m-d H:i');
        $log_id = $db->insert('cronjobs_logs',$insert);
        if($log_id) {
            $update['last_log_id'] = $log_id;
            $where = "name='$func'";
            $db->updateAny('cronjobs', $update, $where);
        }
        return $log_id;
    }

    // Update Status
    public function updateStatus($func, $status) {
        global $db;
        if($status=='Started' or $status=='Forced') $update['last_run'] = $db->DATE;
        $update['status'] = $status;
        $where = "name='$func'";
        return $db->updateAny('cronjobs', $update, $where);
    }

    // End
    private function _end($func, $log_id, $result) {
        global $db;
        $update_log['end_time'] = $db->DATE;
        $update_log['status'] = 1;
        $update_log['result'] = $result;
        return ($db->updateId('cronjobs_logs',$log_id, $update_log)) ? $this->updateStatus($func, 'Done') : false;
    }

      // Sync Logins (CRM - Meta)
      public function syncLogins()
      {
          global $db;
          $this::updateStatus(__FUNCTION__, 'Running');
          $log_id = $this->_addLog(__FUNCTION__);

          // Get from Meta
          $output = new stdClass();
          $output->active = 0;
          $output->expired = 0;
          $mt5api = new mt5API();
          $api_params = [];
          $api_params['group'] = '*';
          $mt5api->get("/api/user/logins", $api_params);
          $e = $mt5api->Error;
          $api = $mt5api->Response;
          if ($api->retcode === "0 Done") {
              $output->res = $api->answer;
              // Get from CRM
              $where = 'expired=0';
              $table_tp = $db->select('tp', $where, 'id,login');
              // Check TP logins
              foreach ($table_tp as $tp) {
                  if (in_array($tp['login'], $output->res)) {
                      $output->active++;
                  } else {
                      $output->expired++;
                      // Update if expired
                      $update['expired'] = 1;
                      $table_tp = $db->updateId('tp', $tp['id'], $update);
                  }
              }
          } else {
              $output->e = $e;
          }
          unset($output->res);
          $this->_end(__FUNCTION__, $log_id, json_encode($output));
      }

      // Func - Followup
      public function followup()
      {
          $time_renge = 5; // MINUTE
          global $db;
          global $notify;
          $this::updateStatus(__FUNCTION__, 'Running');
          $log_id = $this->_addLog(__FUNCTION__);
          // Get List of Users with planned followup
          $time = date('Y-m-d H:i:s');
          $time_to = date('Y-m-d H:i:s', strtotime($time . " +$time_renge minutes"));

          $where = "`followup` BETWEEN '0000-00-00 00:00:00' AND '$time_to'";
          $list_user = $db->select('user_followup', $where);
          $result = array();
          $result['sql'] = end($db->log());
          if ($list_user) foreach ($list_user as $row) {
              $where = 'user_id=' . $row['user_id'];
              $user_ex = $db->selectRow('user_extra', $where);
              if ($user_ex['type'] == 1) $receiver = $user_ex['conversion'];
              if ($user_ex['type'] == 2 || $user_ex['type'] == 3) $receiver = $user_ex['retention'];
              $email = $db->selectId('users', $row['user_id'], 'email')['email'];
              if ($receiver) $notify->add('cronJob', '1', $email, $receiver);
              $result['notify'][] = array(
                  $row['user_id'],
                  $receiver
              );
          }
          $where = "`followup` BETWEEN '0000-00-00 00:00:00' AND '$time_to'";
          $db->deleteAny('user_followup', $where);
          $json_res = json_encode($result);
          $this->_end(__FUNCTION__, $log_id, $json_res);
      }

      // Func - Login To user_extra(DB Table)
      public function login2extra()
      {
          global $db;
          $this::updateStatus(__FUNCTION__, 'Running');
          $log_id = $this->_addLog(__FUNCTION__);
          // Func
          $users_logins = $db->query('SELECT user_id, GROUP_CONCAT(login) logins FROM tp GROUP BY user_id');
          $done = 0;
          if ($users_logins) foreach ($users_logins as $user) {
              $update['logins'] = $user['logins'];
              $where = "user_id=" . $user['user_id'];
              $done += boolval($db->updateAny('user_extra', $update, $where)) ? 1 : 0;
          }
          $result["done"] = $done;
          $result["total"] = ($users_logins) ? count($users_logins) : 0;
          // Report
          $json_res = json_encode($result);
          $this->_end(__FUNCTION__, $log_id, $json_res);
      }

      // Func - Profile Completion
      public function profileCompletion()
      {
          $this::updateStatus(__FUNCTION__, 'Running');
          $log_id = $this->_addLog(__FUNCTION__);
          // Get Users detail
          $sql = 'SELECT 
                        u.id,
                        u.username,
                        x.fname,
                        x.lname,
                        x.phone,
                        x.country,
                        x.city,
                        x.address,
                        x.interests,
                        x.hobbies,
                        x.job_cat,
                        x.job_title,
                        x.exp_fx,
                        x.exp_fx_year,
                        x.exp_cfd,
                        x.exp_cfd_year,
                        x.income,
                        x.investment,
                        x.strategy,
                        x.bd,
                        x.whatsapp,
                        x.telegram,
                        x.facebook,
                        x.instagram,
                        x.twitter,
                        x.lead_src,
                        x.lead_camp
                    FROM users u 
                    LEFT JOIN ( 
                            SELECT 
                                ex.user_id,
                                ex.fname,
                                ex.lname,
                                ex.phone,
                                ex.country,
                                ex.city,
                                ex.address,
                                ex.interests,
                                ex.hobbies,
                                fx.job_cat,
                                fx.job_title,
                                fx.exp_fx,
                                fx.exp_fx_year,
                                fx.exp_cfd,
                                fx.exp_cfd_year,
                                fx.income,
                                fx.investment,
                                fx.strategy,
                                gi.bd,
                                gi.whatsapp,
                                gi.telegram,
                                gi.facebook,
                                gi.instagram,
                                gi.twitter,
                                mk.lead_src,
                                mk.lead_camp
                            FROM user_extra ex 
                            LEFT JOIN user_fx fx USING(user_id)
                            LEFT JOIN user_gi gi USING(user_id)
                            LEFT JOIN user_marketing mk USING(user_id)
                    ) x on u.id=x.user_id';
          $db = new iSQL(DB_admin);
          $res = $db->run($sql);
          $i = 0;
          global $_sys;
          $_sys_Users_Completion = json_decode($_sys['Users_Completion']);
          while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
              GF::profileRateCal($row, $_sys_Users_Completion);
              $i++;
          }
          $this->_end(__FUNCTION__, $log_id, "$i Updated");
      }

      // Func - Clear Expired Session
      public function onlineSession()
      {
          global $db;
          $this::updateStatus(__FUNCTION__, 'Running');
          $log_id = $this->_addLog(__FUNCTION__);

          clearstatcache();
          $path = "../_sessions/";
          $files = scandir($path);
          unset($files[0], $files[1]);

          $data['status'] = 0;
          $db->updateAny('user_session', $data);


          if ($files) foreach ($files as $file) {

              $session_id = substr($file, 5);
              $diff_time = time() - filemtime($path . $file);

              if (filesize($path . $file) > 100 && $diff_time <= 900) {
                  $data['status'] = 1;
              } else {
                  $data['status'] = 0;
              }
              $where = "session='$session_id' AND old=0";
              $db->updateAny('user_session', $data, $where);
          }
          $output['online'] = $db->count('user_session', 'status=1');

          $this->_end(__FUNCTION__, $log_id, json_encode($output));
      }

      // Func - Marketing Report - FTD/RET
      public function marketingReport()
      {

          global $db;
          $this::updateStatus(__FUNCTION__, 'Running');
          $log_id = $this->_addLog(__FUNCTION__);
          $job_result = array();

          $date = false;
          $resL = $helper_ids = $mt_list = array();
          $sql = 'SELECT mt5_deals.login, mt5_deals.profit, mt5_deals.time, DATE(mt5_deals.time) as date FROM ' . DB_mt5['name'] . '.mt5_deals WHERE mt5_deals.Comment != "Zeroing" AND mt5_deals.Action = "2" AND mt5_deals.Profit >0 AND DATE(mt5_deals.Time) = DATE_SUB(CURDATE(), INTERVAL 0 DAY)';
          $result = $db->run($sql);
          if (is_object($result)) while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) $mt_list[] = $row;

          $userManager = new userManager();
          if ($mt_list ?? false) foreach ($mt_list as $k => $mt) {
              $date = $mt['date'];
              $where = 'login=' . $mt['login'];
              $tp = $db->selectRow('tp', $where);
              if ($tp ?? false) {
                  if ($tp['group_id'] == 2) {
                      $lead_src = $userManager->getCustom($tp['user_id'], 'lead_src')['marketing']['lead_src'];
                      if ($tp['ftd'] == $mt['time']) {
                          $resL[$lead_src]['ftd'] += $mt['profit'];
                          $resL[$lead_src]['ftd_c']++;
                      } else {
                          $resL[$lead_src]['ret'] += $mt['profit'];
                          $resL[$lead_src]['ret_c_t']++;
                      }
                  }
              }
          }
          if ($resL) foreach ($resL as $k => $source) {
              $report_save = array();
              $report_save['src'] = $k;
              $report_save['ftd'] = $source['ftd'];
              $report_save['ftd_c'] = $source['ftd_c'];
              $report_save['ret'] = $source['ret'];
              $report_save['ret_c_t'] = $source['ret_c_t'];
              $report_save['date'] = $date;
              $exist = $db->selectRow('marketing_report', "date='$date' AND src='$k'");
              if ($exist) {
                  if ($db->updateId('marketing_report', $exist['id'], $report_save)) $job_result['U']++;;
              } else {
                  if ($db->insert('marketing_report', $report_save)) $job_result['I']++;
              }
          }

          $json_res = json_encode($job_result);
          $this->_end(__FUNCTION__, $log_id, $json_res);
      }

      // Func - Marketing Report All - FTD/RET
      public function marketingReportAll()
      {

          global $db;
          $this::updateStatus(__FUNCTION__, 'Running');
          $log_id = $this->_addLog(__FUNCTION__);
          $job_result = array();

          for ($i = 180; $i >= 0; $i--) {
              usleep(100000);

              $date = false;
              $resL = $helper_ids = $mt_list = array();
              $sql = 'SELECT mt5_deals.login, mt5_deals.profit, mt5_deals.time, DATE(mt5_deals.time) as date FROM ' . DB_mt5['name'] . '.mt5_deals WHERE mt5_deals.Comment != "Zeroing" AND mt5_deals.Action = "2" AND mt5_deals.Profit >0 AND DATE(mt5_deals.Time) = DATE_SUB(CURDATE(), INTERVAL ' . $i . ' DAY)';
              $result = $db->run($sql);
              if (is_object($result)) while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) $mt_list[] = $row;

              $userManager = new userManager();
              if ($mt_list ?? false) foreach ($mt_list as $k => $mt) {
                  $date = $mt['date'];
                  $where = 'login=' . $mt['login'];
                  $tp = $db->selectRow('tp', $where);
                  if ($tp ?? false) {
                      if ($tp['group_id'] == 2) {
                          $lead_src = $userManager->getCustom($tp['user_id'], 'lead_src')['marketing']['lead_src'];
                          if ($tp['ftd'] == $mt['time']) {
                              $resL[$lead_src]['ftd'] += $mt['profit'];
                              $resL[$lead_src]['ftd_c']++;
                          } else {
                              $resL[$lead_src]['ret'] += $mt['profit'];
                              $resL[$lead_src]['ret_c_t']++;
                          }
                      }
                  }
              }
              if ($resL) foreach ($resL as $k => $source) {
                  $report_save = array();
                  $report_save['src'] = $k;
                  $report_save['ftd'] = $source['ftd'];
                  $report_save['ftd_c'] = $source['ftd_c'];
                  $report_save['ret'] = $source['ret'];
                  $report_save['ret_c_t'] = $source['ret_c_t'];
                  $report_save['date'] = $date;
                  $exist = $db->selectRow('marketing_report', "date='$date' AND src='$k'");
                  if ($exist) {
                      if ($db->updateId('marketing_report', $exist['id'], $report_save)) $job_result['U']++;;
                  } else {
                      if ($db->insert('marketing_report', $report_save)) $job_result['I']++;
                  }
              }
          }

          $json_res = json_encode($job_result);
          $this->_end(__FUNCTION__, $log_id, $json_res);
      }

      // Func - Update Staff List Table
      public function updateStaffList()
      {
          global $db;
          $this::updateStatus(__FUNCTION__, 'Running');
          $log_id = $this->_addLog(__FUNCTION__);

          $count = 0;
          $where = "type IN (4,5,6,7,8,9,10,11,12,13)";
          $staff_list = $db->select('user_extra', $where);
          if ($staff_list) foreach ($staff_list as $staff) {
              $data_insert = array(
                  'id' => $staff['user_id'],
                  'email' => $db->selectId('users', $staff['user_id'])['email'],
                  'full_name' => $staff['fname'] . ' ' . $staff['lname'],
                  'unit' => $staff['unit'],
                  'type' => $staff['type'],
                  'status' => $staff['status']
              );
              $db->insert('staff_list', $data_insert, 1);
              $count++;
          }

          $output['updated'] = $count;
          $this->_end(__FUNCTION__, $log_id, json_encode($output));
      }

      // Func - Update TP_Report_MT4 Yesterday
      public function updateTpReportMT4()
      {
          global $db;
          $this::updateStatus(__FUNCTION__, 'Running');
          $log_id = $this->_addLog(__FUNCTION__);

          $mt4_count = 0;
          $userManager = new userManager();
          $today = date("Y-m-d");

          /* Swap */
          $_db_mt4_ps = new iSQL(DB_mt4);
          $query = "SELECT LOGIN, SUM(PROFIT), SUM(SWAPS), count(LOGIN) FROM MT4_TRADES WHERE CMD IN (0,1) And DATE(`CLOSE_TIME`) = '$today' GROUP BY LOGIN";
          $_db_mt4_ps->LINK->query($query, MYSQLI_ASYNC);

          /* Bonus In */
          $_db_mt4_bins = new iSQL(DB_mt4);
          $query = "SELECT LOGIN, SUM(PROFIT), count(LOGIN) FROM MT4_TRADES WHERE CMD=6 
                              AND COMMENT NOT IN (
                                'Deposit Wire Transfer',
                                'Deposit Credit Card',
                                'Deposit',
                                'zeroutbonus',
                                'zerooutbonus',
                                'Zerout') AND PROFIT>0 AND DATE(`CLOSE_TIME`) = '$today' GROUP BY LOGIN";
          $_db_mt4_bins->LINK->query($query, MYSQLI_ASYNC);

          /* Bonus Out */
          $_db_mt4_bout = new iSQL(DB_mt4);
          $query = "SELECT LOGIN, SUM(PROFIT), count(LOGIN) FROM MT4_TRADES WHERE CMD=6 
                              AND COMMENT NOT IN (
                                'Withdrawal Wire Transfer',
                                'Withdrawal Credit Card',
                                'Withdrawal',
                                'Account Transfer') AND PROFIT<0 AND DATE(`CLOSE_TIME`) = '$today' GROUP BY LOGIN";
          $_db_mt4_bout->LINK->query($query, MYSQLI_ASYNC);

          /* Withdrawal */
          $_db_mt4_w = new iSQL(DB_mt4);
          $query = "SELECT LOGIN, SUM(PROFIT), count(LOGIN) FROM MT4_TRADES WHERE CMD=6 
                              AND COMMENT IN (
                                'Withdrawal Wire Transfer',
                                'Withdrawal Credit Card',
                                'Withdrawal',
                                'Account Transfer') AND PROFIT<0 AND DATE(`CLOSE_TIME`) = '$today' GROUP BY LOGIN";
          $_db_mt4_w->LINK->query($query, MYSQLI_ASYNC);

          /* Deposit */
          $_db_mt4_d = new iSQL(DB_mt4);
          $query = "SELECT LOGIN, SUM(PROFIT), count(LOGIN) FROM MT4_TRADES WHERE CMD=6 
                              AND COMMENT IN (
                                'Deposit Wire Transfer',
                                'Deposit Credit Card',
                                'Deposit') AND PROFIT>0 AND DATE(`CLOSE_TIME`) = '$today' GROUP BY LOGIN";
          $_db_mt4_d->LINK->query($query, MYSQLI_ASYNC);

          /* Zeroing */
          $_db_mt4_z = new iSQL(DB_mt4);
          $query = "SELECT LOGIN, SUM(PROFIT), count(LOGIN) FROM MT4_TRADES WHERE CMD=6 
                              AND COMMENT IN (
                                'zeroutbonus',
                                'zerooutbonus',
                                'Zerout') AND PROFIT>0 AND DATE(`CLOSE_TIME`) = '$today' GROUP BY LOGIN";
          $_db_mt4_z->LINK->query($query, MYSQLI_ASYNC);

          /* Login Groups */
          $_db_mt4_groups = new iSQL(DB_mt5);
          $query = "SELECT `LOGIN`,`GROUP` FROM MT4_USERS WHERE `AGENT_ACCOUNT`!=1";
          $_db_mt4_groups->LINK->query($query, MYSQLI_ASYNC);

          /* Res - Profit Swap */
          $_db_mt4_ps_res = $_db_mt4_ps->LINK->reap_async_query();

          /* Res - Bonus In */
          $_db_mt4_bins_res = $_db_mt4_bins->LINK->reap_async_query();

          /* Res - Bonus Out */
          $_db_mt4_bout_res = $_db_mt4_bout->LINK->reap_async_query();

          /* Res - Withdrawal */
          $_db_mt4_w_res = $_db_mt4_w->LINK->reap_async_query();

          /* Res - Deposit */
          $_db_mt4_d_res = $_db_mt4_d->LINK->reap_async_query();

          /* Res - Zeroing */
          $_db_mt4_z_res = $_db_mt4_z->LINK->reap_async_query();

          /* Res - Login Groups  */
          $_db_mt4_groups_res = $_db_mt4_groups->LINK->reap_async_query();

          $max_row_mt4 = max(array(
              $_db_mt4_ps_res->num_rows,
              $_db_mt4_bins_res->num_rows,
              $_db_mt4_bout_res->num_rows,
              $_db_mt4_w_res->num_rows,
              $_db_mt4_d_res->num_rows,
              $_db_mt4_z_res->num_rows
          ));

          $tps_mt4 = array();
          for ($i = 1; $i <= $max_row_mt4; $i++) {

              /* Profit & Swap */
              $ps = $_db_mt4_ps_res->fetch_row();
              if ($ps[0]) {
                  $tps_mt4[$ps[0]]['profit'] = $ps[1];
                  $tps_mt4[$ps[0]]['swap'] = $ps[2];
                  $tps_mt4[$ps[0]]['trades_count'] = $ps[3];
              }

              /* Bonus In */
              $bin = $_db_mt4_bins_res->fetch_row();
              if ($bin[0]) {
                  $tps_mt4[$bin[0]]['bonus_in'] = $bin[1];
                  $tps_mt4[$bin[0]]['bonus_in_count'] = $bin[2];
              }

              /* Bonus Out */
              $bout = $_db_mt4_bout_res->fetch_row();
              if ($bout[0]) {
                  $tps_mt4[$bout[0]]['bonus_out'] = $bout[1];
                  $tps_mt4[$bout[0]]['bonus_out_count'] = $bout[2];
              }

              /* Withdrawal */
              $w = $_db_mt4_w_res->fetch_row();
              if ($w[0]) {
                  $tps_mt4[$w[0]]['withdrawal'] = $w[1];
                  $tps_mt4[$w[0]]['withdrawal_count'] = $w[2];
              }

              /* Deposit */
              $d = $_db_mt4_d_res->fetch_row();
              if ($d[0]) {
                  $tps_mt4[$d[0]]['deposit'] = $d[1];
                  $tps_mt4[$d[0]]['deposit_count'] = $d[2];
              }

              /* Zeroing */
              $z = $_db_mt4_z_res->fetch_row();
              if ($z[0]) {
                  $tps_mt4[$z[0]]['zeroing'] = $z[1];
                  $tps_mt4[$z[0]]['zeroing_count'] = $z[2];
              }

          }

          $list_login_mt4 = implode(',', array_keys($tps_mt4));
          if ($list_login_mt4 ?? false) {
              $where = 'group_id=2 AND login IN (' . $list_login_mt4 . ')';
              $user_tps_mt4 = $db->select('tp', $where);
          }
          $login_groups_mt4 = array_column($_db_mt4_groups_res->fetch_all(), 1, 0);

          if ($user_tps_mt4 ?? false) foreach ($user_tps_mt4 as $user_tp) {

              $tps_mt4[$user_tp['login']]['retention_id'] = $user_tp['retention'];
              $tps_mt4[$user_tp['login']]['conversion_id'] = $user_tp['conversion'];

              if (date("Y-m-d", strtotime($user_tp['ftd'])) == $today) {
                  $tps_mt4[$user_tp['login']]['ret_amount'] = $tps_mt4[$user_tp['login']]['deposit'] - $user_tp['ftd_amount'];
                  $tps_mt4[$user_tp['login']]['ftd_amount'] = $user_tp['ftd_amount'];
              } else {
                  $tps_mt4[$user_tp['login']]['ret_amount'] = $tps_mt4[$user_tp['login']]['deposit'];
                  $tps_mt4[$user_tp['login']]['ftd_amount'] = 0;
              }

              $tps_mt4[$user_tp['login']]['ib_id'] = $user_tp['ib'];

              $tps_mt4[$user_tp['login']]['user_id'] = $user_tp['user_id'];
              $user_data = $userManager->getCustom($user_tp['user_id'], 'email,unit');
              $tps_mt4[$user_tp['login']]['email'] = $user_data['email'];
              $tps_mt4[$user_tp['login']]['unit'] = $user_data['unit'];
              $tps_mt4[$user_tp['login']]['login'] = $user_tp['login'];
              $tps_mt4[$user_tp['login']]['day'] = $today;

              /*
               *  ALTER TABLE `tp_report_mt4` ADD UNIQUE `login_day`(`login`, `day`);
               */
              if ($login_groups_mt4[$user_tp['login']] ?? false) {
                  $tps_mt4[$user_tp['login']]['mt4_group'] = $login_groups_mt4[$user_tp['login']];
                  $db->insert('tp_report_mt4', $tps_mt4[$user_tp['login']], 1);
                  $mt4_count++;
              }
          }

          $output['count'] = $mt4_count;
          $this->_end(__FUNCTION__, $log_id, json_encode($output));
      }

      // Func - Update TP_Report_MT5 Yesterday
      public function updateTpReportMT5()
      {
          global $db;
          $this::updateStatus(__FUNCTION__, 'Running');
          $log_id = $this->_addLog(__FUNCTION__);

          $mt5_count = $mt5_skip = 0;
          $userManager = new userManager();
          $today = date("Y-m-d");

          $where = 'type=2';
          $crm_groups = $db->select('mt_groups', $where, 'name');
          $crm_groups = array_column($crm_groups, 'name');
          array_walk($crm_groups, function (&$value, $key) {
              $value = 'real\\' . $value;
          });
          if (empty($crm_groups)) {
              $output['error'] = 'Empty MT_Groups in CRM !';
              $this->_end(__FUNCTION__, $log_id, json_encode($output));
          } else {
              /* Profit Swap */
              $_db_mt5_ps = new iSQL(DB_mt5);
              $query = "SELECT Login, SUM(Profit), SUM(Storage), count(Login) FROM mt5_deals WHERE Action IN (0,1) AND Entry IN (1,3) AND DATE(`Time`) = '$today' GROUP BY Login";
              $_db_mt5_ps->LINK->query($query, MYSQLI_ASYNC);

              /* Bonus In */
              $_db_mt5_bins = new iSQL(DB_mt5);
              $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=6 AND Profit>0 AND DATE(`Time`) = '$today' GROUP BY Login";
              $_db_mt5_bins->LINK->query($query, MYSQLI_ASYNC);

              /* Bonus Out */
              $_db_mt5_bout = new iSQL(DB_mt5);
              $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=6 AND Profit<0 AND DATE(`Time`) = '$today' GROUP BY Login";
              $_db_mt5_bout->LINK->query($query, MYSQLI_ASYNC);

              /* Withdrawal */
              $_db_mt5_w = new iSQL(DB_mt5);
              $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=2 AND Profit<0 AND Comment!='Zeroing' AND DATE(`Time`) = '$today' GROUP BY Login";
              $_db_mt5_w->LINK->query($query, MYSQLI_ASYNC);

              /* Deposit */
              $_db_mt5_d = new iSQL(DB_mt5);
              $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=2 AND Profit>0 AND Comment!='Zeroing' AND DATE(`Time`) = '$today' GROUP BY Login";
              $_db_mt5_d->LINK->query($query, MYSQLI_ASYNC);

              /* Correction */
              $_db_mt5_c = new iSQL(DB_mt5);
              $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=5 AND Comment!='Zeroing' AND DATE(`Time`) = '$today' GROUP BY Login";
              $_db_mt5_c->LINK->query($query, MYSQLI_ASYNC);

              /* Zeroing */
              $_db_mt5_z = new iSQL(DB_mt5);
              $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=2 AND Comment='Zeroing' AND DATE(`Time`) = '$today' GROUP BY Login";
              $_db_mt5_z->LINK->query($query, MYSQLI_ASYNC);

              /* Login Groups */
              $_db_mt5_groups = new iSQL(DB_mt5);
              $query = "SELECT `Login`,`Group` FROM `mt5_users` WHERE `Group` LIKE 'real%'";
              $_db_mt5_groups->LINK->query($query, MYSQLI_ASYNC);

              /* Res - Profit Swap */
              $_db_mt5_ps_res = $_db_mt5_ps->LINK->reap_async_query();

              /* Res - Bonus In */
              $_db_mt5_bins_res = $_db_mt5_bins->LINK->reap_async_query();

              /* Res - Bonus Out */
              $_db_mt5_bout_res = $_db_mt5_bout->LINK->reap_async_query();

              /* Res - Withdrawal */
              $_db_mt5_w_res = $_db_mt5_w->LINK->reap_async_query();

              /* Res - Deposit */
              $_db_mt5_d_res = $_db_mt5_d->LINK->reap_async_query();

              /* Res - Correction */
              $_db_mt5_c_res = $_db_mt5_c->LINK->reap_async_query();

              /* Res - Zeroing */
              $_db_mt5_z_res = $_db_mt5_z->LINK->reap_async_query();

              /* Res - Login Groups  */
              $_db_mt5_groups_res = $_db_mt5_groups->LINK->reap_async_query();

              $max_row_mt5 = max(array(
                  $_db_mt5_ps_res->num_rows,
                  $_db_mt5_bins_res->num_rows,
                  $_db_mt5_bout_res->num_rows,
                  $_db_mt5_w_res->num_rows,
                  $_db_mt5_d_res->num_rows,
                  $_db_mt5_c_res->num_rows,
                  $_db_mt5_z_res->num_rows
              ));

              $tps_mt5 = array();
              for ($i = 1; $i <= $max_row_mt5; $i++) {

                  /* Profit & Swap */
                  $ps = $_db_mt5_ps_res->fetch_row();
                  if ($ps[0]) {
                      $tps_mt5[$ps[0]]['profit'] = $ps[1];
                      $tps_mt5[$ps[0]]['swap'] = $ps[2];
                      $tps_mt5[$ps[0]]['trades_count'] = $ps[3];
                  }

                  /* Bonus In */
                  $bin = $_db_mt5_bins_res->fetch_row();
                  if ($bin[0]) {
                      $tps_mt5[$bin[0]]['bonus_in'] = $bin[1];
                      $tps_mt5[$bin[0]]['bonus_in_count'] = $bin[2];
                  }

                  /* Bonus Out */
                  $bout = $_db_mt5_bout_res->fetch_row();
                  if ($bout[0]) {
                      $tps_mt5[$bout[0]]['bonus_out'] = $bout[1];
                      $tps_mt5[$bout[0]]['bonus_out_count'] = $bout[2];
                  }

                  /* Withdrawal */
                  $w = $_db_mt5_w_res->fetch_row();
                  if ($w[0]) {
                      $tps_mt5[$w[0]]['withdrawal'] = $w[1];
                      $tps_mt5[$w[0]]['withdrawal_count'] = $w[2];
                  }

                  /* Deposit */
                  $d = $_db_mt5_d_res->fetch_row();
                  if ($d[0]) {
                      $tps_mt5[$d[0]]['deposit'] = $d[1];
                      $tps_mt5[$d[0]]['deposit_count'] = $d[2];
                  }

                  /* Correction */
                  $c = $_db_mt5_c_res->fetch_row();
                  if ($c[0]) {
                      $tps_mt5[$c[0]]['correction'] = $c[1];
                      $tps_mt5[$c[0]]['correction_count'] = $c[2];
                  }

                  /* Zeroing */
                  $z = $_db_mt5_z_res->fetch_row();
                  if ($z[0]) {
                      $tps_mt5[$z[0]]['zeroing'] = $z[1];
                      $tps_mt5[$z[0]]['zeroing_count'] = $z[2];
                  }

              }

              $list_login_mt5 = implode(',', array_keys($tps_mt5));
              if ($list_login_mt5 ?? false) {
                  $where = 'group_id=2 AND login IN (' . $list_login_mt5 . ')';
                  $user_tps_mt5 = $db->select('tp', $where);
              }
              $login_groups_mt5 = array_column($_db_mt5_groups_res->fetch_all(), 1, 0);

              if ($user_tps_mt5 ?? false) foreach ($user_tps_mt5 as $user_tp) {

                  $tps_mt5[$user_tp['login']]['retention_id'] = $user_tp['retention'];
                  $tps_mt5[$user_tp['login']]['conversion_id'] = $user_tp['conversion'];

                  if (date("Y-m-d", strtotime($user_tp['ftd'])) == $today) {
                      $tps_mt5[$user_tp['login']]['ret_amount'] = $tps_mt5[$user_tp['login']]['deposit'] - $user_tp['ftd_amount'];
                      $tps_mt5[$user_tp['login']]['ftd_amount'] = $user_tp['ftd_amount'];
                  } else {
                      $tps_mt5[$user_tp['login']]['ret_amount'] = $tps_mt5[$user_tp['login']]['deposit'];
                      $tps_mt5[$user_tp['login']]['ftd_amount'] = 0;
                  }

                  $tps_mt5[$user_tp['login']]['ib_id'] = $user_tp['ib'];

                  $tps_mt5[$user_tp['login']]['user_id'] = $user_tp['user_id'];
                  $user_data = $userManager->getCustom($user_tp['user_id'], 'email,unit');
                  $tps_mt5[$user_tp['login']]['email'] = $user_data['email'];
                  $tps_mt5[$user_tp['login']]['unit'] = $user_data['unit'];
                  $tps_mt5[$user_tp['login']]['login'] = $user_tp['login'];
                  $tps_mt5[$user_tp['login']]['day'] = $today;

                  /*
                   *  ALTER TABLE `tp_report_mt5` ADD UNIQUE `login_day`(`login`, `day`);
                   */
                  if ($login_groups_mt5[$user_tp['login']] ?? false) {
                      if (in_array($login_groups_mt5[$user_tp['login']], $crm_groups)) {
                          $tps_mt5[$user_tp['login']]['mt5_group'] = $login_groups_mt5[$user_tp['login']];
                          $db->insert('tp_report_mt5', $tps_mt5[$user_tp['login']], 1);
                          $mt5_count++;
                      } else {
                          $mt5_skip++;
                      }
                  }
              }

              $output['count'] = $mt5_count;
              $output['skip'] = $mt5_skip;
              $this->_end(__FUNCTION__, $log_id, json_encode($output));


          }
      }

      // Func - Database Backup
      public function databaseBackup()
      {
          global $db;
          $this::updateStatus(__FUNCTION__, 'Running');
          $log_id = $this->_addLog(__FUNCTION__);

          $time_break = strtotime('now') - (60 * 60 * 12);
          $dbhost = DB_admin['hostname'];
          $dbuser = DB_admin['username'];
          $dbpass = DB_admin['password'];
          $dbname = DB_admin['name'];
          $path = str_replace('/_sessions', '', Broker['session_path']) . '/backup/';
          $sql = "SELECT `TABLE_NAME`, `UPDATE_TIME` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '" . DB_admin['name'] . "'";
          $tables = $db->run($sql);
          if ($tables) foreach ($tables as $table) {
              $table_name = $table['TABLE_NAME'];
              $db_time = strtotime($table['UPDATE_TIME']);
              if ($time_break < $db_time) {
                  $sql_backup_file = date("YmdHis") . '_' . $table_name . '.sql.gz';
                  $file = $path . $sql_backup_file;
                  $dump = "mysqldump --opt -h $dbhost --no-tablespaces -u $dbuser -p$dbpass $dbname $table_name | gzip > $file";
                  exec("($dump) 2>&1", $stdout, $result);
                  $output['run'][] = array(
                      //'result' => $result,
                      //'stdOut' => $stdout,
                      'fileName' => $sql_backup_file
                  );
              } else {
                  $output['skipped'][] = $table;
              }
              $files = glob($path . '*_' . $table_name . '.sql.gz');
              if (count($files) > 15) {
                  usort($files, function ($x, $y) {
                      return filemtime($x) < filemtime($y);
                  });
                  foreach ($files as $k => $file) {
                      if ((!is_file($file)) || ($k < 15)) continue;
                      $output['removed'][] = $file;
                      unlink($file);
                  }
              }
          }
          /* Remove Older then 7 Day
          $expire = strtotime('-7 DAYS');
          $files = glob($path . '*.sql.gz');
          if($files) foreach($files as $file) {
              if ((!is_file($file)) || (filemtime($file) > $expire)) continue;
              $output['removed'][] = $file;
              unlink($file);
          }
        */
          $this->_end(__FUNCTION__, $log_id, json_encode($output));
      }

  }
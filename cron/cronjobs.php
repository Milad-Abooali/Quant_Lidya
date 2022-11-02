    <?php

    /**
    * Cron Jobs
    * 11:48 AM Tuesday, November 3, 2020 | M.Abooali
    */

    $is_cron = 1;
    require_once "../config.php";
    require_once "./cron_funcs.php";
    global $db;
    $cron_functions = new cronJobs();
    $cron_jobs = $db->selectAll('cronjobs');
    $func = $job = null;
    foreach ($cron_jobs as $job) {
        $func = $job['name'];
        if($job['avoid']==1) {
            $cron_functions->updateStatus($func,'Avoided');
        } else {
            if (method_exists($cron_functions, $func)) {
                if($job['force_run']==1) {
                    $update['force_run'] = 0;
                    $db->updateId('cronjobs', $job['id'], $update);
                    $cron_functions->updateStatus($func,'Forced');
                    $cron_functions->$func();
                } else {
                    $last_job = ($job['last_log_id']) ? $db->selectId('cronjobs_logs', $job['last_log_id']) : false;
                    if ($last_job==false) {
                        $cron_functions->updateStatus($func,'Need Force');
                    } else if ($last_job['status']==1) {
                        $cron_functions->updateStatus($func,'Done');
                        $time_now = date('Y-m-d H:i');
                        $time_run = new DateTime($last_job['end_time']);
                        $time_run->add(new DateInterval('PT' . $job['cycle'] . 'M'));
                        $time_run = $time_run->format('Y-m-d H:i');
                        if (strtotime($time_now) > strtotime($time_run)) {
                            $cron_functions->updateStatus($func,'Started');
                            $cron_functions->$func();
                        }
                    } else if ($last_job['status']==0) {
                        $cron_functions->updateStatus($func,'Running');
                    }
                }
            } else {
                $cron_functions->updateStatus($func,'No Func');
            }
        }
    }
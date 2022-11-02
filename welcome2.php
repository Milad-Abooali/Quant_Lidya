<?php
######################################################################
#  M | 12:48 PM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################



// LangMan
global $_L;

include('includes/head.php'); ?>

        <link rel="stylesheet" href="assets/plugins/morris/morris.css">
        <link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.css">

<?php include('includes/css.php'); ?>

    <body>

        <!-- Begin page -->
        <div id="wrapper">

<?php 
    include('includes/topbar.php');
    include('includes/sidebar.php');

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    if (isset($_GET['startTime']) )
    {
        $startTime = $_GET['startTime'];
        $endTime = $_GET['endTime'];
        $endTime = date("Y-m-d H:i:s", strtotime($endTime) - 1);
    } else {
        $startTime = date('Y-m-01');
        $endTime = date('Y-m-t');
    }
    
    if($_SESSION["type"] == "Retention Agent"){
        $sqlMtTP = 'SELECT GROUP_CONCAT(tp.login) as login FROM user_extra LEFT JOIN tp ON user_extra.user_id = tp.user_id WHERE user_extra.retention = "'.$_SESSION["id"].'" AND tp.group_id != "1"';
        $mttp = $DB_admin->query($sqlMtTP);
        while($rowTP = $mttp->fetch_assoc()) {
            $mttps = $rowTP['login'];
        }
        $array2 = str_replace(",", '","', $mttps);
        $array2r = str_replace('","', ",", $array2);
        //echo $sqlMtTP;
    } else if($_SESSION["type"] == "Sales Agent"){
        $sqlMtTP = 'SELECT GROUP_CONCAT(tp.login) as login FROM user_extra LEFT JOIN tp ON user_extra.user_id = tp.user_id WHERE user_extra.conversion = "'.$_SESSION["id"].'" AND tp.group_id != "1"';
        //echo $sqlMtTP;
        $mttp = $DB_admin->query($sqlMtTP);
        while($rowTP = $mttp->fetch_assoc()) {
            $mttps = $rowTP['login'];
        }
        $array2 = str_replace(",", '","', $mttps);
        $array2r = str_replace('","', ",", $array2);
        //echo $array2r;
        //echo $array2r;
    } else if($_SESSION["type"] == "Trader" OR $_SESSION["type"] == "Leads"){
        $sqlMtTP = 'SELECT GROUP_CONCAT(tp.login) as login FROM user_extra LEFT JOIN tp ON user_extra.user_id = tp.user_id WHERE user_extra.user_id = "'.$_SESSION["id"].'" AND tp.group_id != "1"';
        //echo $sqlMtTP;
        $mttp = $DB_admin->query($sqlMtTP);
        while($rowTP = $mttp->fetch_assoc()) {
            $mttps = $rowTP['login'];
        }
        $array2 = str_replace(",", '","', $mttps);
        $array2r = str_replace('","', ",", $array2);
        //echo $array2;
        //echo $array2r;
    } else if($_SESSION["type"] == "IB"){
        $sqlMtTP = 'SELECT GROUP_CONCAT(tp.login) as login FROM user_marketing LEFT JOIN tp ON user_marketing.user_id = tp.user_id WHERE user_marketing.affiliate = "'.$_SESSION["id"].'" AND tp.group_id != "1"';
        $mttp = $DB_admin->query($sqlMtTP);
        while($rowTP = $mttp->fetch_assoc()) {
            $mttps = $rowTP['login'];
        }
        $array2 = str_replace(",", '","', $mttps);
        $array2r = str_replace('","', ",", $array2);
        //echo $array2r;
    } else if($_SESSION["type"] == "Manager"){
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "'.$_SESSION["unitn"].'"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        $array = str_replace(",", '","', $unitn);
        $prefixed_array = str_replace(",", '","real\\\\', $unitn);
    } else {
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        $array = str_replace(",", '","', $unitn);
        $prefixed_array = str_replace(",", '","real\\\\', $unitn);
    }
    
    
    //echo $array2;
    if($_SESSION["type"] == "Admin"){
        $unit = "All";
        $sql1 = 'SELECT 
                MT4_TRADES.COMMISSION       as COMMISSION, 
                MT4_TRADES.SWAPS            as SWAPS, 
                MT4_TRADES.VOLUME           as VOLUME, 
                MT4_TRADES.TICKET           as TICKET, 
                MT4_TRADES.COMMENT          as COMMENT, 
                MT4_TRADES.CMD              as CMD,
                "15"                        as CMD5, 
                0                           as ENTRY,
                MT4_TRADES.OPEN_TIME        as OPEN_TIME, 
                FORMAT(MT4_USERS.EQUITY, 2) as EQUITY, 
                MT4_USERS.LOGIN             as LOGIN, 
                MT4_USERS.NAME              as NAME, 
                MT4_TRADES.PROFIT           AS PROFIT 
        FROM    lidyapar_mt4.MT4_TRADES 
                JOIN lidyapar_mt4.MT4_USERS 
                    ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN 
        WHERE   MT4_USERS.AGENT_ACCOUNT <> "1" 
                AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
        UNION ALL 
        SELECT  mt5_deals.Commission      as COMMISSION, 
                mt5_deals.Storage         as SWAPS, 
                mt5_deals.Volume          as VOLUME, 
                mt5_deals.Deal            as TICKET, 
                mt5_deals.Comment         as COMMENT,
                "15"                           as CMD,
                mt5_deals.Action          as CMD5,
                mt5_deals.Entry           as ENTRY, 
                mt5_deals.Time            as OPEN_TIME, 
                FORMAT(mt5_accounts.Equity, 2) as EQUITY, 
                mt5_users.Login                as LOGIN, 
                mt5_users.Name                 as NAME, 
                mt5_deals.Profit          AS PROFIT 
        FROM    lidyapar_mt5.mt5_deals 
                LEFT JOIN lidyapar_mt5.mt5_users 
                    ON mt5_deals.Login = mt5_users.Login 
                LEFT JOIN lidyapar_mt5.mt5_accounts 
                    ON mt5_accounts.Login = mt5_users.Login 
        WHERE   mt5_users.Group IN ( "'.$prefixed_array.'" )
                AND mt5_deals.Time BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        //$sql1 = 'SELECT MT4_TRADES.COMMISSION as COMMISSION, MT4_TRADES.SWAPS as SWAPS, MT4_TRADES.VOLUME as VOLUME,MT4_TRADES.TICKET as TICKET, MT4_TRADES.COMMENT as COMMENT, MT4_TRADES.CMD as CMD,MT4_TRADES.OPEN_TIME as OPEN_TIME, FORMAT(MT4_USERS.EQUITY,2) as EQUITY, MT4_USERS.LOGIN as LOGIN, MT4_USERS.NAME as NAME, MT4_TRADES.PROFIT AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $DB_mt4->query($sql1);
        //$sqlreport= 'SELECT MONTH(MT4_TRADES.CLOSE_TIME) as MONTH, YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM lidyapar_mt4.MT4_TRADES JOIN lidyapar_mt4.MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_USERS.AGENT_ACCOUNT <> "1" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME),MONTH(MT4_TRADES.CLOSE_TIME)
        //             UNION ALL
        //             SELECT MONTH(mt5_deals.Time) as MONTH, YEAR(mt5_deals.Time) as YEAR, SUM(mt5_deals.Storage) as SWAPS, SUM(mt5_deals.Volume)/10000 as VOLUME, SUM(mt5_deals.Profit) AS PROFIT FROM lidyapar_mt5.mt5_deals JOIN lidyapar_mt5.mt5_users ON mt5_users.Login = mt5_deals.Login WHERE mt5_deals.Action < 2 AND mt5_deals.Entry > 0 AND mt5_users.Group IN ( "'.$prefixed_array.'" ) GROUP BY YEAR(mt5_deals.Time),MONTH(mt5_deals.Time)';
        //$sqlreport= 'SELECT MONTH(MT4_TRADES.CLOSE_TIME) as MONTH, YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_USERS.AGENT_ACCOUNT <> "1" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME),MONTH(MT4_TRADES.CLOSE_TIME) ORDER BY YEAR';
        //$resultreport = $DB_mt4->query($sqlreport);

        //$sqlreport4= 'SELECT YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM lidyapar_mt4.MT4_TRADES JOIN lidyapar_mt4.MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_USERS.AGENT_ACCOUNT <> "1" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME)
        //              UNION ALL
        //              SELECT YEAR(mt5_deals.Time) as YEAR, SUM(mt5_deals.Storage) as SWAPS, SUM(mt5_deals.Volume)/10000 as VOLUME, SUM(mt5_deals.Profit) AS PROFIT FROM lidyapar_mt5.mt5_deals JOIN lidyapar_mt5.mt5_users ON mt5_users.Login = mt5_deals.Login WHERE mt5_deals.Action < 2 AND mt5_deals.Entry > 0 AND mt5_users.Group IN ( "'.$prefixed_array.'" ) GROUP BY YEAR(mt5_deals.Time)';
        //$resultreport4 = $DB_mt4->query($sqlreport4);
        
        //$data_array = array();
        //while ($data = $resultreport->fetch_assoc()) {
        //    $data_array[] = $data;
        //}
        //echo $sqlreport;
    } else if($_SESSION["type"] == "Manager") {
        $unit = $_SESSION["unit"];
        $sql1 = 'SELECT 
                MT4_TRADES.COMMISSION       as COMMISSION, 
                MT4_TRADES.SWAPS            as SWAPS, 
                MT4_TRADES.VOLUME           as VOLUME, 
                MT4_TRADES.TICKET           as TICKET, 
                MT4_TRADES.COMMENT          as COMMENT, 
                MT4_TRADES.CMD              as CMD,
                "15"                        as CMD5,
                "0"                         as ENTRY,
                MT4_TRADES.OPEN_TIME        as OPEN_TIME, 
                FORMAT(MT4_USERS.EQUITY, 2) as EQUITY, 
                MT4_USERS.LOGIN             as LOGIN, 
                MT4_USERS.NAME              as NAME, 
                MT4_TRADES.PROFIT           AS PROFIT 
        FROM    lidyapar_mt4.MT4_TRADES 
                JOIN lidyapar_mt4.MT4_USERS 
                    ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN 
        WHERE   MT4_USERS.GROUP IN ( "'.$array.'" ) 
                AND MT4_USERS.AGENT_ACCOUNT <> "1" 
                AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
        UNION ALL 
        SELECT  mt5_deals.Commission      as COMMISSION, 
                mt5_deals.Storage         as SWAPS, 
                mt5_deals.Volume          as VOLUME, 
                mt5_deals.Deal            as TICKET, 
                mt5_deals.Comment         as COMMENT,
                "15"                           as CMD,
                mt5_deals.Action          as CMD5,
                mt5_deals.Entry           as ENTRY,
                mt5_deals.Time            as OPEN_TIME, 
                FORMAT(mt5_accounts.Equity, 2) as EQUITY, 
                mt5_users.Login                as LOGIN, 
                mt5_users.Name                 as NAME, 
                mt5_deals.Profit          AS PROFIT 
        FROM    lidyapar_mt5.mt5_deals 
                LEFT JOIN lidyapar_mt5.mt5_users 
                    ON mt5_deals.Login = mt5_users.Login 
                LEFT JOIN lidyapar_mt5.mt5_accounts 
                    ON mt5_accounts.Login = mt5_users.Login 
        WHERE   mt5_users.Group IN ( "'.$prefixed_array.'" ) 
                AND mt5_deals.Time BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $DB_mt4->query($sql1);
        
        $sqlreport= 'SELECT MONTH(MT4_TRADES.CLOSE_TIME) as MONTH, YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM lidyapar_mt4.MT4_TRADES JOIN lidyapar_mt4.MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_USERS.AGENT_ACCOUNT <> "1" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME),MONTH(MT4_TRADES.CLOSE_TIME)
                     UNION ALL
                     SELECT MONTH(mt5_deals.Time) as MONTH, YEAR(mt5_deals.Time) as YEAR, SUM(mt5_deals.Storage) as SWAPS, SUM(mt5_deals.Volume)/10000 as VOLUME, SUM(mt5_deals.Profit) AS PROFIT FROM lidyapar_mt5.mt5_deals JOIN lidyapar_mt5.mt5_users ON mt5_users.Login = mt5_deals.Login WHERE mt5_deals.Action < 2 AND mt5_deals.Entry > 0 AND mt5_users.Group IN ( "'.$prefixed_array.'" ) GROUP BY YEAR(mt5_deals.Time),MONTH(mt5_deals.Time)';
        $resultreport = $DB_mt4->query($sqlreport);
        //echo $sqlreport;
        $sqlreport4= 'SELECT YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM lidyapar_mt4.MT4_TRADES JOIN lidyapar_mt4.MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_USERS.AGENT_ACCOUNT <> "1" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME)
                      UNION ALL
                      SELECT YEAR(mt5_deals.Time) as YEAR, SUM(mt5_deals.Storage) as SWAPS, SUM(mt5_deals.Volume)/10000 as VOLUME, SUM(mt5_deals.Profit) AS PROFIT FROM lidyapar_mt5.mt5_deals JOIN lidyapar_mt5.mt5_users ON mt5_users.Login = mt5_deals.Login WHERE mt5_deals.Action < 2 AND mt5_deals.Entry > 0 AND mt5_users.Group IN ( "'.$prefixed_array.'" ) GROUP BY YEAR(mt5_deals.Time)';
        $resultreport4 = $DB_mt4->query($sqlreport4);
        
        $data_array = array();
        while ($data = $resultreport->fetch_assoc()) {
            $data_array[] = $data;
        }
    } else if($_SESSION["type"] == "Trader" OR $_SESSION["type"] == "Leads") {
        $sql1 = 'SELECT 
                MT4_TRADES.COMMISSION       as COMMISSION, 
                MT4_TRADES.SWAPS            as SWAPS, 
                MT4_TRADES.VOLUME           as VOLUME, 
                MT4_TRADES.TICKET           as TICKET, 
                MT4_TRADES.COMMENT          as COMMENT, 
                MT4_TRADES.CMD              as CMD,
                "15"                        as CMD5,
                "0"                         as ENTRY,
                MT4_TRADES.OPEN_TIME        as OPEN_TIME, 
                FORMAT(MT4_USERS.EQUITY, 2) as EQUITY, 
                MT4_USERS.LOGIN             as LOGIN, 
                MT4_USERS.NAME              as NAME, 
                MT4_TRADES.PROFIT           AS PROFIT 
        FROM    lidyapar_mt4.MT4_TRADES 
                JOIN lidyapar_mt4.MT4_USERS 
                    ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN 
        WHERE   MT4_USERS.LOGIN IN ( "'.$array2.'" ) 
                AND MT4_USERS.AGENT_ACCOUNT <> "1" 
                AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
        UNION ALL 
        SELECT  mt5_deals.Commission      as COMMISSION, 
                mt5_deals.Storage         as SWAPS, 
                mt5_deals.Volume          as VOLUME, 
                mt5_deals.Deal            as TICKET, 
                mt5_deals.Comment         as COMMENT,
                "15"                           as CMD,
                mt5_deals.Action          as CMD5,
                mt5_deals.Entry           as ENTRY,
                mt5_deals.Time            as OPEN_TIME, 
                FORMAT(mt5_accounts.Equity, 2) as EQUITY, 
                mt5_users.Login                as LOGIN, 
                mt5_users.Name                 as NAME, 
                mt5_deals.Profit          AS PROFIT 
        FROM    lidyapar_mt5.mt5_deals 
                LEFT JOIN lidyapar_mt5.mt5_users 
                    ON mt5_deals.Login = mt5_users.Login 
                LEFT JOIN lidyapar_mt5.mt5_accounts 
                    ON mt5_accounts.Login = mt5_users.Login 
        WHERE   mt5_users.Login IN ( "'.$array2.'" ) 
                AND mt5_deals.Time BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $DB_mt4->query($sql1);
        //echo $sql1;
        
        $sqlreport= 'SELECT MONTH(MT4_TRADES.CLOSE_TIME) as MONTH, YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM lidyapar_mt4.MT4_TRADES JOIN lidyapar_mt4.MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.LOGIN IN ("'.$array2.'") AND MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_USERS.AGENT_ACCOUNT <> "1" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME),MONTH(MT4_TRADES.CLOSE_TIME)
                     UNION ALL
                     SELECT MONTH(mt5_deals.Time) as MONTH, YEAR(mt5_deals.Time) as YEAR, SUM(mt5_deals.Storage) as SWAPS, SUM(mt5_deals.Volume)/10000 as VOLUME, SUM(mt5_deals.Profit) AS PROFIT FROM lidyapar_mt5.mt5_deals JOIN lidyapar_mt5.mt5_users ON mt5_users.Login = mt5_deals.Login WHERE mt5_deals.Action < 2 AND mt5_deals.Entry > 0 AND mt5_users.Login IN ( "'.$array2.'" ) GROUP BY YEAR(mt5_deals.Time),MONTH(mt5_deals.Time)';
        $resultreport = $DB_mt4->query($sqlreport);
        //echo $sqlreport;
        $sqlreport4= 'SELECT YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM lidyapar_mt4.MT4_TRADES JOIN lidyapar_mt4.MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.LOGIN IN ("'.$array2.'") AND MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_USERS.AGENT_ACCOUNT <> "1" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME)
                      UNION ALL
                      SELECT YEAR(mt5_deals.Time) as YEAR, SUM(mt5_deals.Storage) as SWAPS, SUM(mt5_deals.Volume)/10000 as VOLUME, SUM(mt5_deals.Profit) AS PROFIT FROM lidyapar_mt5.mt5_deals JOIN lidyapar_mt5.mt5_users ON mt5_users.Login = mt5_deals.Login WHERE mt5_deals.Action < 2 AND mt5_deals.Entry > 0 AND mt5_users.Login IN ( "'.$array2.'" ) GROUP BY YEAR(mt5_deals.Time)';
        $resultreport4 = $DB_mt4->query($sqlreport4);
        
        $data_array = array();
        while ($data = $resultreport->fetch_assoc()) {
            $data_array[] = $data;
        }
        
        $sqltp = 'SELECT login FROM tp WHERE user_id = "'.$_SESSION["id"].'" AND group_id = "2"';
        $resulttp = $DB_admin->query($sqltp);
    } else if($_SESSION["type"] == "IB") {
        $sql1 = 'SELECT 
                MT4_TRADES.COMMISSION       as COMMISSION, 
                MT4_TRADES.SWAPS            as SWAPS, 
                MT4_TRADES.VOLUME           as VOLUME, 
                MT4_TRADES.TICKET           as TICKET, 
                MT4_TRADES.COMMENT          as COMMENT, 
                MT4_TRADES.CMD              as CMD,
                "15"                        as CMD5,
                "0"                         as ENTRY,
                MT4_TRADES.OPEN_TIME        as OPEN_TIME, 
                FORMAT(MT4_USERS.EQUITY, 2) as EQUITY, 
                MT4_USERS.LOGIN             as LOGIN, 
                MT4_USERS.NAME              as NAME, 
                MT4_TRADES.PROFIT           AS PROFIT 
        FROM    lidyapar_mt4.MT4_TRADES 
                JOIN lidyapar_mt4.MT4_USERS 
                    ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN 
        WHERE   MT4_USERS.LOGIN IN ( "'.$array2.'" ) 
                AND MT4_USERS.AGENT_ACCOUNT <> "1" 
                AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
        UNION ALL 
        SELECT  mt5_deals.Commission      as COMMISSION, 
                mt5_deals.Storage         as SWAPS, 
                mt5_deals.Volume          as VOLUME, 
                mt5_deals.Deal            as TICKET, 
                mt5_deals.Comment         as COMMENT,
                "15"                           as CMD,
                mt5_deals.Action          as CMD5,
                mt5_deals.Entry           as ENTRY,
                mt5_deals.Time            as OPEN_TIME, 
                FORMAT(mt5_accounts.Equity, 2) as EQUITY, 
                mt5_users.Login                as LOGIN, 
                mt5_users.Name                 as NAME, 
                mt5_deals.Profit          AS PROFIT 
        FROM    lidyapar_mt5.mt5_deals 
                LEFT JOIN lidyapar_mt5.mt5_users 
                    ON mt5_deals.Login = mt5_users.Login 
                LEFT JOIN lidyapar_mt5.mt5_accounts 
                    ON mt5_accounts.Login = mt5_users.Login 
        WHERE   mt5_users.Login IN ( "'.$array2.'" ) 
                AND mt5_deals.Time BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $DB_mt4->query($sql1);
        
        $sqlreport= 'SELECT MONTH(MT4_TRADES.CLOSE_TIME) as MONTH, YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.EMAIL = "'.$_SESSION["username"].'" AND MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME),MONTH(MT4_TRADES.CLOSE_TIME) ORDER BY YEAR';
        $resultreport = $DB_mt4->query($sqlreport);

        $sqlreport4= 'SELECT YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.EMAIL = "'.$_SESSION["username"].'" AND MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME) ORDER BY YEAR';
        $resultreport4 = $DB_mt4->query($sqlreport4);
        
        $data_array = array();
        while ($data = $resultreport->fetch_assoc()) {
            $data_array[] = $data;
        }
        
        $sqltp = 'SELECT login FROM tp WHERE user_id = "'.$_SESSION["id"].'" AND group_id = "2"';
        $resulttp = $DB_admin->query($sqltp);
    } else if($_SESSION["type"] == "Retention Agent") {
        $unit = $_SESSION["unit"];
        $sql1 = 'SELECT 
                MT4_TRADES.COMMISSION       as COMMISSION, 
                MT4_TRADES.SWAPS            as SWAPS, 
                MT4_TRADES.VOLUME           as VOLUME, 
                MT4_TRADES.TICKET           as TICKET, 
                MT4_TRADES.COMMENT          as COMMENT, 
                MT4_TRADES.CMD              as CMD,
                "15"                        as CMD5,
                "0"                         as ENTRY,
                MT4_TRADES.OPEN_TIME        as OPEN_TIME, 
                FORMAT(MT4_USERS.EQUITY, 2) as EQUITY, 
                MT4_USERS.LOGIN             as LOGIN, 
                MT4_USERS.NAME              as NAME, 
                MT4_TRADES.PROFIT           AS PROFIT 
        FROM    lidyapar_mt4.MT4_TRADES 
                JOIN lidyapar_mt4.MT4_USERS 
                    ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN 
        WHERE   MT4_USERS.LOGIN IN ( "'.$array2.'" ) 
                AND MT4_USERS.AGENT_ACCOUNT <> "1" 
                AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
        UNION ALL 
        SELECT  mt5_deals.Commission      as COMMISSION, 
                mt5_deals.Storage         as SWAPS, 
                mt5_deals.Volume          as VOLUME, 
                mt5_deals.Deal            as TICKET, 
                mt5_deals.Comment         as COMMENT,
                "15"                           as CMD,
                mt5_deals.Action          as CMD5,
                mt5_deals.Entry           as ENTRY,
                mt5_deals.Time            as OPEN_TIME, 
                FORMAT(mt5_accounts.Equity, 2) as EQUITY, 
                mt5_users.Login                as LOGIN, 
                mt5_users.Name                 as NAME, 
                mt5_deals.Profit          AS PROFIT 
        FROM    lidyapar_mt5.mt5_deals 
                LEFT JOIN lidyapar_mt5.mt5_users 
                    ON mt5_deals.Login = mt5_users.Login 
                LEFT JOIN lidyapar_mt5.mt5_accounts 
                    ON mt5_accounts.Login = mt5_users.Login 
        WHERE   mt5_users.Login IN ( "'.$array2.'" ) 
                AND mt5_deals.Time BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $DB_mt4->query($sql1);
        //echo $sql1;
        $sqlreport= 'SELECT MONTH(MT4_TRADES.CLOSE_TIME) as MONTH, YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.LOGIN IN ("'.$array2.'") AND MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME),MONTH(MT4_TRADES.CLOSE_TIME) ORDER BY YEAR';
        $resultreport = $DB_mt4->query($sqlreport);
        
        $sqlreport4= 'SELECT YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.LOGIN IN ("'.$array2.'") AND MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME) ORDER BY YEAR';
        $resultreport4 = $DB_mt4->query($sqlreport4);
        
        $data_array = array();
        while ($data = $resultreport->fetch_assoc()) {
            $data_array[] = $data;
        }
    } else if($_SESSION["type"] == "Sales Agent") {
        $unit = $_SESSION["unit"];
        $sql1 = 'SELECT 
                MT4_TRADES.COMMISSION       as COMMISSION, 
                MT4_TRADES.SWAPS            as SWAPS, 
                MT4_TRADES.VOLUME           as VOLUME, 
                MT4_TRADES.TICKET           as TICKET, 
                MT4_TRADES.COMMENT          as COMMENT, 
                MT4_TRADES.CMD              as CMD,
                "15"                        as CMD5,
                "0"                         as ENTRY,
                MT4_TRADES.OPEN_TIME        as OPEN_TIME, 
                FORMAT(MT4_USERS.EQUITY, 2) as EQUITY, 
                MT4_USERS.LOGIN             as LOGIN, 
                MT4_USERS.NAME              as NAME, 
                MT4_TRADES.PROFIT           AS PROFIT 
        FROM    lidyapar_mt4.MT4_TRADES 
                JOIN lidyapar_mt4.MT4_USERS 
                    ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN 
        WHERE   MT4_USERS.LOGIN IN ( "'.$array2.'" ) 
                AND MT4_USERS.AGENT_ACCOUNT <> "1" 
                AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
        UNION ALL 
        SELECT  mt5_deals.Commission      as COMMISSION, 
                mt5_deals.Storage         as SWAPS, 
                mt5_deals.Volume          as VOLUME, 
                mt5_deals.Deal            as TICKET, 
                mt5_deals.Comment         as COMMENT,
                "15"                           as CMD,
                mt5_deals.Action          as CMD5, 
                mt5_deals.Entry           as ENTRY,
                mt5_deals.Time            as OPEN_TIME, 
                FORMAT(mt5_accounts.Equity, 2) as EQUITY, 
                mt5_users.Login                as LOGIN, 
                mt5_users.Name                 as NAME, 
                mt5_deals.Profit          AS PROFIT 
        FROM    lidyapar_mt5.mt5_deals 
                LEFT JOIN lidyapar_mt5.mt5_users 
                    ON mt5_deals.Login = mt5_users.Login 
                LEFT JOIN lidyapar_mt5.mt5_accounts 
                    ON mt5_accounts.Login = mt5_users.Login 
        WHERE   mt5_users.Login IN ( "'.$array2.'" ) 
                AND mt5_deals.Time BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $DB_mt4->query($sql1);
        //echo $sql1;
        $sqlreport= 'SELECT MONTH(MT4_TRADES.CLOSE_TIME) as MONTH, YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.LOGIN IN ("'.$array2.'") AND MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME),MONTH(MT4_TRADES.CLOSE_TIME) ORDER BY YEAR';
        $resultreport = $DB_mt4->query($sqlreport);

        $sqlreport4= 'SELECT YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.LOGIN IN ("'.$array2.'") AND MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME) ORDER BY YEAR';
        $resultreport4 = $DB_mt4->query($sqlreport4);
        
        $data_array = array();
        while ($data = $resultreport->fetch_assoc()) {
            $data_array[] = $data;
        }
    } else if($_SESSION["type"] == "Sales Manager") {
        $unit = $_SESSION["unit"];
        $sql1 = 'SELECT MT4_TRADES.COMMISSION as COMMISSION, MT4_TRADES.SWAPS as SWAPS, MT4_TRADES.VOLUME as VOLUME,MT4_TRADES.TICKET as TICKET, MT4_TRADES.COMMENT as COMMENT, MT4_TRADES.CMD as CMD,MT4_TRADES.OPEN_TIME as OPEN_TIME, FORMAT(MT4_USERS.EQUITY,2) as EQUITY, MT4_USERS.LOGIN as LOGIN, MT4_USERS.NAME as NAME, MT4_TRADES.PROFIT AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.GROUP IN ("'.$array.'") AND MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $DB_mt4->query($sql1);
    } else if($_SESSION["type"] == "Retention Manager") {
        $unit = $_SESSION["unit"];
        $sql1 = 'SELECT MT4_TRADES.COMMISSION as COMMISSION, MT4_TRADES.SWAPS as SWAPS, MT4_TRADES.VOLUME as VOLUME,MT4_TRADES.TICKET as TICKET, MT4_TRADES.COMMENT as COMMENT, MT4_TRADES.CMD as CMD,MT4_TRADES.OPEN_TIME as OPEN_TIME, FORMAT(MT4_USERS.EQUITY,2) as EQUITY, MT4_USERS.LOGIN as LOGIN, MT4_USERS.NAME as NAME, MT4_TRADES.PROFIT AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.GROUP IN ("'.$array.'") AND MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $DB_mt4->query($sql1);
    }
    $sqlQuotes = 'SELECT * FROM success_quotes ORDER BY RAND() LIMIT 1';
    $squotes = $DB_admin->query($sqlQuotes);
?>
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="page-title-box">
                                    <h4 class="page-title"><?= $_L->T('Dashboard','sidebar') ?> <?php echo $_SESSION["type"]; ?></h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active">
                                            <?= $_L->T('Welcome_User_Broker','general', array(htmlspecialchars($_SESSION["username"]), Broker['title'])) ?>
                                        </li>
                                    </ol>
                                    <div class="state-information d-none d-sm-block">
                                        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                            <i class="fa fa-calendar"></i>&nbsp;
                                            <span><?php echo $startTime." - ".$endTime; ?></span> <i class="fa fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            $userManager = new userManager();
                            $cid = $userManager->getCustom($_SESSION['id'],'cid')['cid'];
                            if (!in_array($cid, array(null,"0000-00-00 00:00:00"))) {
                        ?>
                        <div class="card mb-3 col-lg-12" style="padding: 0px;">
                            <div class="row no-gutters">
                                <div class="col-sm-12 col-md-4 bg-dark">
                                    <a href="https://clientzone.lidyaportal.com/LidyaFX_Forex_E-Book_v1.2.pdf"><img src="assets/e-book/LidyaFX_Forex_E-Book_600-200.png" style="width: 100%;"/></a>
                                </div>
                                <div class="col-sm-12 col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">Yatirim Rehberi</h5>
                                        <p class="card-text">Forex piyasalarını daha iyi tanımanız ve olumsuz sonuçlanan deneyimlerden uzaklaşmanızı sağlamak adına bilmeniz gereken temel bilgileri sizin için eğitim kitapçığı haline getirdik.
                                        </br></br>Siz'de bu kitapçığı indirme hakkına sahip olan seçili yatırımcılarımızdan birisiniz.</p>
                                        <p class="card-text"><a href="<?= DOWNLOAD_LINK['Forex_E-Book'] ?>" class="btn bg-gradient-primary text-white">Şimdi İndirin</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            } 
                        ?>
                        <!-- end row -->
                        <?php 
                            $amountDP = 0;
                            $amountZE = 0;
                            $amountCDP = 0;
                            $amountBO = 0;
                            
                            $amountWT = 0;
                            $amountWBO = 0;
                            $amountCWT = 0;
                            $totalorders = 0;
                            $totalswaps = 0;
                            $totalvolume = 0;
                            $totalcommisions = 0;
                            $pnl = 0;
                            $winning = 0;
                            $losing = 0;
                            
                            $dp = array("Deposit", "DEPOSIT", "DEPOSIT WIRE TRANSFER", "Deposit Wire Transfer", "Deposit Credit Card", "DEPOSIT CREDIT CARD", "Wire In", "wire in", "WIRE IN");
                            $wd = array("Withdrawal", "WITHDRAWAL", "WITHDRAWAL WIRE TRANSFER", "Withdrawal Wire Transfer", "Withdrawal Credit Card", "WITHDRAWAL CREDIT CARD", "Wire Out", "wire out", "WIRE OUT", "Withdraw", "WITHDRAW", "Account Transfer");
                            $accounts = "";
                            if($rowTRADES) while($rowTRADES = $result1->fetch_assoc()) {
                                if($rowTRADES['CMD'] == 6){
                                    if($rowTRADES['PROFIT'] >= 0){
                                        if(!in_array($rowTRADES['COMMENT'], $dp)){
                                            $amountBO += $rowTRADES['PROFIT'];
                                        } else {
                                            $amountDP += $rowTRADES['PROFIT'];
                                        }
                            	    } else if($rowTRADES['PROFIT'] <= 0) { 
                            	        if(!in_array($rowTRADES['COMMENT'], $wd)){
                                            $amountWBO += $rowTRADES['PROFIT'];
                                        } else if(preg_match('/\btradecorrection\b/',$rowTRADES['COMMENT'])) {
                                            $amountCWT += $rowTRADES['PROFIT'];
                                        } else {
                                            $amountWT += $rowTRADES['PROFIT'];
                                        } 
                            	    }
                                } else if ($rowTRADES['CMD5'] == 2) {
                                    if($rowTRADES['PROFIT'] >= 0 AND $rowTRADES['COMMENT'] == "Zeroing"){
                                        $amountZE += $rowTRADES['PROFIT'];
                                    } else if($rowTRADES['PROFIT'] >= 0 AND $rowTRADES['COMMENT'] !== "Zeroing") {
                                        $amountDP += $rowTRADES['PROFIT'];
                                    } else if($rowTRADES['PROFIT'] <= 0) { 
                            	        $amountWT += $rowTRADES['PROFIT'];
                                    }
                                } else if ($rowTRADES['CMD5'] > 2 AND $rowTRADES['CMD5'] < 7) {
                                    if($rowTRADES['PROFIT'] >= 0){
                                        $amountBO += $rowTRADES['PROFIT'];
                            	    } else if($rowTRADES['PROFIT'] <= 0) { 
                            	        $amountWBO += $rowTRADES['PROFIT'];
                            	    }
                                }
                                if($rowTRADES['CMD'] < 2) {
                                    $pnl += $rowTRADES['PROFIT'];
                                    $swaps += $rowTRADES['SWAPS'];
                                    $commission += $rowTRADES['COMMISSION'];
                                    $totalvolume += $rowTRADES['VOLUME']/100;
                                    $totalorders = $totalorders+1;
                                    
                                    if($rowTRADES['PROFIT']+$rowTRADES['SWAPS'] >= 0){
                                        $winning++;
                                    } else {
                                        $losing++;
                                    }
                                } else if($rowTRADES['CMD5'] < 2) {
                                    $pnl += $rowTRADES['PROFIT'];
                                    $swaps += $rowTRADES['SWAPS'];
                                    $commission += $rowTRADES['COMMISSION'];
                                    if($rowTRADES['ENTRY'] > 0){
                                        $totalvolume += $rowTRADES['VOLUME']/10000;
                                        $totalorders = $totalorders+1;
                                    }
                                    
                                    if($rowTRADES['PROFIT']+$rowTRADES['SWAPS'] >= 0){
                                        $winning++;
                                    } else {
                                        $losing++;
                                    }
                                }
                                $totalrate = $winning+$losing;
                                if($totalrate > 0){
                                    $winningrate = ( $winning / $totalrate ) * 100;
                                    $losingrate = ( $losing / $totalrate ) * 100;
                                }
                                $equity = $rowTRADES['EQUITY'];
                                $balance = $rowTRADES['BALANCE'];
                                $margin_level = $rowTRADES['MARGIN_LEVEL'];
                                $used_margin = $rowTRADES['MARGIN'];
                                $free_margin = $rowTRADES['MARGIN_FREE'];
                            }
                        ?>
                        <div class="row">
                                <div class="col-xl-3 col-md-6">
                                    <div class="card mini-stat bg-gradient-primary">
                                        <div class="card-body mini-stat-img">
                                            <div class="mini-stat-icon">
                                                <i class="mdi mdi-buffer float-right"></i>
                                            </div>
                                            <div class="text-white">
                                                <h6 class="text-uppercase mb-3"><?= $_L->T('Traded_Volume','trade') ?></h6>
                                                <h4 class="mb-4"><?= $_L->T('Lot','trade', number_format($totalvolume, 2, '.', ',')) ?></h4>
                                                <span class="badge badge-info"> +11% </span> <span class="ml-2"><?= $_L->T('From_Previous_Period','dashboard') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div class="card mini-stat bg-gradient-info">
                                        <div class="card-body mini-stat-img">
                                            <div class="mini-stat-icon">
                                                <i class="mdi mdi-cash-100 float-right"></i>
                                            </div>
                                            <div class="text-white">
                                                <h6 class="text-uppercase mb-3"><?= $_L->T('Profit_Loss','trade') ?></h6>
                                                <h4 class="mb-4"><?php echo number_format(($pnl+$swaps+($amountBO+$amountWBO))+$amountZE, 2, '.', ','); ?>$</h4>
                                                <span><?= $_L->T('Raw_PNL','trade') ?>: <?php echo number_format($pnl+$swaps, 2, '.', ','); ?>$</span> /
                                                <span><?= $_L->T('Bonus','trade') ?>: <?php echo number_format($amountBO+$amountWBO, 2, '.', ','); ?>$</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div class="card mini-stat bg-gradient-success">
                                        <div class="card-body mini-stat-img">
                                            <div class="mini-stat-icon">
                                                <i class="mdi mdi-debug-step-into float-right"></i>
                                            </div>
                                            <div class="text-white">
                                                <h6 class="text-uppercase mb-3"><?= $_L->T('Total_Deposits','trade') ?></h6>
                                                <h4 class="mb-4"><?php echo number_format($amountDP, 2, '.', ','); ?>$</h4>
                                                <span><?= $_L->T('Given_Bonus','trade') ?>: <?php echo number_format($amountBO, 2, '.', ','); ?>$</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div class="card mini-stat bg-gradient-danger">
                                        <div class="card-body mini-stat-img">
                                            <div class="mini-stat-icon">
                                                <i class="mdi mdi-debug-step-out float-right"></i>
                                            </div>
                                            <div class="text-white">
                                                <h6 class="text-uppercase mb-3"><?= $_L->T('Total_Withdrawals','trade') ?></h6>
                                                <h4 class="mb-4"><?php echo number_format($amountWT, 2, '.', ','); ?>$</h4>
                                                <span><?= $_L->T('Released_Bonus','trade') ?>: <?php echo number_format($amountWBO, 2, '.', ','); ?>$</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
            
                            <div class="row">
            
                                <div class="col-xl-4">
                                    <div class="card m-b-20">
                                        <div class="card-body">
                                            <h4 class="mt-0 header-title"><?= $_L->T('Monthly_Earning','trade') ?></h4>
            
                                            <div class="row text-center m-t-20">
                                                <?php
                                                    $repvolume2 = 0;
                                                    $repswaps2 = 0;
                                                    $repprofit2 = 0;
                                                    if  ($rowReport) foreach($data_array as $rowReport) {
                                                        if($rowReport['YEAR'] == date('Y') AND $rowReport['MONTH'] == date('m')){
                                                            $repvolume2 += $rowReport['VOLUME'];
                                                            $repswaps2 += $rowReport['SWAPS'];
                                                            $repprofit2 += $rowReport['PROFIT']; 
                                                        }
                                                    }
                                                ?>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo number_format($repvolume2, 2, '.', ','); ?> Lot</h6>
                                                    <p class="text-muted"><?= $_L->T('Volume','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo number_format($repswaps2, 2, '.', ','); ?>$</h6>
                                                    <p class="text-muted"><?= $_L->T('Swap','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo number_format($repprofit2, 2, '.', ','); ?>$</h6>
                                                    <p class="text-muted"><?= $_L->T('Profit','trade') ?></p>
                                                </div>
                                            </div>
                                            
                                            <div id="earningm" class="dashboard-charts morris-charts"></div>
                                        </div>
                                    </div>
                                </div>
            
                                <div class="col-xl-4">
                                    <div class="card m-b-20">
                                        <div class="card-body">
                                            <h4 class="mt-0 header-title"><?= $_L->T('Yearly_Earning','trade') ?></h4>
            
                                            <div class="row text-center m-t-20">
                                                <?php
                                                    $repvolume3 = 0;
                                                    $repswaps3 = 0;
                                                    $repprofit3 = 0;
                                                    if($rowReport) foreach($data_array as $rowReport) {
                                                        if($rowReport['YEAR'] == date('Y')){
                                                            $repvolume3 += $rowReport['VOLUME'];
                                                            $repswaps3 += $rowReport['SWAPS'];
                                                            $repprofit3 += $rowReport['PROFIT'];
                                                        }
                                                    }
                                                ?>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo number_format($repvolume3, 2, '.', ','); ?> Lot</h6>
                                                    <p class="text-muted"><?= $_L->T('Volume','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo number_format($repswaps3, 2, '.', ','); ?>$</h6>
                                                    <p class="text-muted"><?= $_L->T('Swap','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo number_format($repprofit3, 2, '.', ','); ?>$</h6>
                                                    <p class="text-muted"><?= $_L->T('Profit','trade') ?></p>
                                                </div>
                                            </div>
            
                                            <div id="earningy" class="dashboard-charts morris-charts"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php if($_SESSION["type"] == "Admin"){ ?>
                                <div class="col-xl-4">
                                    <div class="card m-b-20">
                                        <div class="card-body">
                                            <h4 class="mt-0 header-title">Monthly Volume</h4>
            
                                            <div class="row text-center m-t-20">
                                                <div class="col-4">
                                                    <h6 class="">2567 Lot</h6>
                                                    <p class="text-muted">Turkey</p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="">1834 Lot</h6>
                                                    <p class="text-muted">Iran</p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="">452 Lot</h6>
                                                    <p class="text-muted">STP</p>
                                                </div>
                                            </div>
            
                                            <div id="morris-bar-stacked" class="dashboard-charts morris-charts"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php } else if($_SESSION["type"] == "Manager"){ ?>
                                    <div class="col-xl-4">
                                    <div class="card m-b-20">
                                        <div class="card-body">
                                            <h4 class="mt-0 header-title">Statistics</h4>
            
                                            <div class="row text-center m-t-20">
                                                <div class="col-4">
                                                    <h6 class="" id="ftd">0 FTD</h6>
                                                    <p class="text-muted">#FTD</p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="" id="ret">0 Ret</h6>
                                                    <p class="text-muted">#Retention</p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="" id="leads">0 Leads</h6>
                                                    <p class="text-muted">#Leads</p>
                                                </div>
                                            </div>
            
                                            <div id="morris-bar-stackedxx" class="dashboard-charts morris-charts"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php } else if($_SESSION["type"] == "Sales Agent"){ ?>
                                    <div class="col-xl-4">
                                    <div class="card m-b-20">
                                        <div class="card-body">
                                            <h4 class="mt-0 header-title">Statistics</h4>
            
                                            <div class="row text-center m-t-20">
                                                <div class="col-4">
                                                    <h6 class="" id="ftd">0 FTD</h6>
                                                    <p class="text-muted">#FTD</p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="" id="ret">0 Ret</h6>
                                                    <p class="text-muted">#Retention</p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="" id="leads">0 Leads</h6>
                                                    <p class="text-muted">#Leads</p>
                                                </div>
                                            </div>
            
                                            <div id="morris-bar-stackedxx" class="dashboard-charts morris-charts"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php } else if($_SESSION["type"] == "Trader" OR $_SESSION["type"] == "Leads"){ ?>
                                <div class="col-xl-4">
                                    <div class="card m-b-20">
                                        <div class="card-body">
                                            <h4 class="mt-0 header-title"><?= $_L->T('Total_Statistics','trade') ?></h4>
            
                                            <div class="row text-center m-t-20">
                                                <div class="col-4">
                                                    <h6 class=""><?php echo number_format($winningrate, 2, '.', '.'); ?>%</h6>
                                                    <p class="text-muted"><?= $_L->T('Winning_Rate','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo number_format($losingrate, 2, '.', '.'); ?>%</h6>
                                                    <p class="text-muted"><?= $_L->T('Losing_Rate','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo $totalorders; ?></h6>
                                                    <p class="text-muted"><?= $_L->T('Total_Orders','trade') ?></p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row text-center m-t-20">
                                                <div class="col-4">
                                                    <h6 class=""><?php echo $equity; ?>$</h6>
                                                    <p class="text-muted"><?= $_L->T('Equity','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo $margin_level; ?>%</h6>
                                                    <p class="text-muted"><?= $_L->T('Margin_Level','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo $balance; ?>$</h6>
                                                    <p class="text-muted"><?= $_L->T('Balance','trade') ?></p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row text-center m-t-20">
                                                <div class="col-4">
                                                    <h6 class=""><?php echo $used_margin; ?>$</h6>
                                                    <p class="text-muted"><?= $_L->T('Used_Margin','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo $free_margin; ?>$</h6>
                                                    <p class="text-muted"><?= $_L->T('Free_Margin','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class=""><?php echo $s; ?>0$</h6>
                                                    <p class="text-muted"><?= $_L->T('Credit','trade') ?></p>
                                                </div>
                                            </div>
                                            <hr>
                                            <span class="badge badge-info"> <?= $_L->T('Note','note') ?> </span><span> <?= $_L->T('Total_Statistics_note','dashboard') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php } else if($_SESSION["type"] == "Retention Manager" OR $_SESSION["type"] == "Retention Agent"){ ?>
                                <div class="col-xl-4">
                                    <div class="card m-b-20">
                                        <div class="card-body">
                                            <h4 class="mt-0 header-title">Margin Calls</h4>
            
                                            <div class="row text-center m-t-20">
                                                
                                            </div>
                                            <hr>
                                            <span class="badge badge-info"> Note </span><span> The total statistic is for all your real accounts.</span>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <!-- end row -->
            
                            <div class="row">
                                
                                <div class="col-xl-4 col-lg-6">
                                    <div class="card m-b-20">
                                        <div class="card-body">
                                            <h4 class="mt-0 header-title mb-3"><?= $_L->T('Inbox','pm') ?></h4>
                                            <button type="button" class="btn btn-sm bg-gradient-primary text-white waves-effect waves-light" id="new_pm"><?= $_L->T('New_PM','pm') ?></button>
                                            <div id="inboxmessages">
                                                
                                            </div>
                                        </div>
                                    </div>
            
                                </div>
                                <div class="col-xl-4 col-lg-6">
                                    <div class="card m-b-20">
                                        <div class="card-body">
                                            <h4 class="mt-0 header-title mb-4"><?= $_L->T('Company_News_Promotions','dashboard') ?></h4>
            
                                            <ol class="activity-feed mb-0 pt-0">
                                                <li class="feed-item">
                                                    <div class="feed-item-list">
                                                        <span class="date">Jun 10</span>
                                                        <span class="activity-text"><?= $_L->T('Company_News_Promotion_1','dashboard') ?></span>
                                                    </div>
                                                </li>
                                                <li class="feed-item">
                                                    <div class="feed-item-list">
                                                        <span class="date">Jun 10</span>
                                                        <span class="activity-text"><?= $_L->T('Company_News_Promotion_2','dashboard') ?></span>
                                                    </div>
                                                </li>
                                                <li class="feed-item">
                                                    <div class="feed-item-list">
                                                        <span class="date">Jun 10</span>
                                                        <span class="activity-text"><?= $_L->T('Company_News_Promotion_3','dashboard') ?></span>
                                                    </div>
                                                </li>
                                                <li class="feed-item">
                                                    <div class="feed-item-list">
                                                        <span class="date">Jun 10</span>
                                                        <span class="activity-text"><?= $_L->T('Company_News_Promotion_4','dashboard') ?></span>
                                                    </div>
                                                </li>
                                            </ol>
            
                                            <div class="text-center">
                                                <a href="#" class="btn btn-sm bg-gradient-primary text-white"><?= $_L->T('Load_More','general') ?></a>
                                            </div>
                                        </div>
                                    </div>
            
                                </div>
                                <div class="col-xl-4">
                                    <div class="card widget-user m-b-20">
                                        <?php
                                            while($rowQuote = $squotes->fetch_assoc()) {
                                        ?>
                                        <div class="widget-user-desc p-4 text-center bg-gradient-primary position-relative">
                                            <i class="fas fa-quote-left h3 text-white-50"></i>
                                            <p class="text-white mb-0"><?php echo $rowQuote['quotes']; ?></p>
                                        </div>
                                        <div class="p-4">
                                            <div class="float-left mt-2 mr-3">
                                                <img src="assets/images/users/user-2.jpg" alt="" class="rounded-circle thumb-md">
                                            </div>
                                            <h6 class="mb-1"><?php echo $rowQuote['qowner']; ?></h6>
                                            <p class="text-muted mb-0"><?php echo $rowQuote['title']; ?></p>
                                        </div>
                                        <?php
                                            } 
                                        ?>
                                    </div>
                                    <div class="card m-b-20">
                                        <div class="card-body">
                                            <h4 class="mt-0 header-title"><?= $_L->T('Yearly_Sales','dashboard') ?></h4>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div>
                                                        <h4>52,345</h4>
                                                        <p class="text-muted"><?= $_L->T('languages_s_text','dashboard') ?></p>
                                                        <a href="#" class="text-primary"><?= $_L->T('Load_More','general') ?> <i class="mdi mdi-chevron-double-right"></i></a>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 text-right">
                                                    <div id="sparkline"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
            
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row">
                                <div class="col-xl-8">
                                    <div class="card m-b-20">
                                        <div class="card-body">
                                            <h4 class="mt-0 m-b-30 header-title float-left"><?= $_L->T('Latest_Transactions','transactions') ?></h4>
                                            <span class="float-right">
                                                <button type="button" class="btn btn-success btn-sm refresh"><i class="fas fa-sync"></i></button>
                                                <button type="button" class="btn bg-gradient-primary text-white btn-sm" id="bf-all"><?= $_L->T('All','general') ?></button>
                                                <button type="button" class="btn btn-outline-dark btn-sm" id="bf-ftd"><?= $_L->T('FTD','general') ?></button>
                                                <button type="button" class="btn btn-outline-info btn-sm" id="bf-red"><?= $_L->T('RED','general') ?></button>
                                                <button type="button" class="btn btn-outline-warning btn-sm" id="bf-bonus"><?= $_L->T('Bonus','trade') ?></button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="bf-rwd"><?= $_L->T('R_WD','general') ?></button>
                                            </span>
                                            <div class="clearfix">&nbsp;</div>
                                            <table id="trans-table" class="table table-hover table-vertical mb-1" width="100%">  
                                                <thead>  
                                                    <tr>
                            							<th><?= $_L->T('Bonus','trade') ?></th>
                            							<th><?= $_L->T('Login','trade') ?></th>
                            							<th><?= $_L->T('Name','general') ?></th>
                            							<th><?= $_L->T('Type','general') ?></th>
                            							<th><?= $_L->T('Amount','trade') ?></th>
                            							<th><?= $_L->T('Comment','note') ?></th>
                            							<th><?= $_L->T('Date','general') ?></th>
                            							<th><?= $_L->T('OWNER','general') ?></th>
                            							<th><?= $_L->T('FTD','general') ?></th>
                                                    </tr>
                            					</thead>
                            					<tbody>
                                                    
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="9"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
            
                                <div class="col-xl-4">
                                    <div class="card m-b-20">
                                        <div class="card-body">

                                            <div id="wg-transaction" data-autoload="true" data-wg="transaction.php" class="widget row">
                                                <div class="widget-header col-12 d-flex justify-content-between">
                                                    <h6 class="mt-0 m-b-30 header-title float-left"><?= $_L->T('Deposit_Withdrawal','transactions') ?></h6>
                                                    <span class="float-right">
                                                        <span class="datetime text-black-50" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?= $_L->T('Updated_Time','general') ?>"> </span>
                                                        <span class="px-2">|</span>
                                                        <i class="reload fas fa-sync fa-spin" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?= $_L->T('Reload_Widget','general') ?>"></i>
                                                    </span>
                                                </div>
                                                <div class="widget-body col-12"></div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->

                    </div> <!-- container-fluid -->

                </div> <!-- content -->

<?php include('includes/footer.php'); ?>

            </div>


            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->


        </div>
        <!-- END wrapper -->
<?php include('includes/script.php'); ?>

        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>

        <!--Morris Chart-->
        <script src="assets/plugins/morris/morris.min.js"></script>
        <script src="assets/plugins/raphael/raphael-min.js"></script>
        <script src="assets/pages/dashboard.js"></script>
        <script src="assets/plugins/summernote/summernote-bs4.min.js"></script>
        
        <script>
            $(document).ready( function () {
                $("#inboxmessages").load("readpm.php");
                //setInterval(function(){
                //    $("#inboxmessages").load("readpm.php");
                //}, 10000000);
                $("#new_pm").click(function() {
                    var valuser = '<?php echo htmlspecialchars($_SESSION["id"]); ?>'; 
                    var url = "sendpm.php?user="+valuser;
                    $('#myModal .modal-body').load(url,function(result){
                        $("#myModal .modal-title").html('Send Private Message');
                        $('.summernote').summernote({
                            height: 100,                 // set editor height
                            minHeight: null,             // set minimum height of editor
                            maxHeight: null,             // set maximum height of editor
                            focus: false                 // set focus to editable area after initializing summernote
                        });
                        $('#myModal').modal({show:true});
                    });
                });
                
                $("body").on('click','.pmid', function() {
                    var valuser = $(this).attr('id'); 
                    var url = "readpm.php?id="+valuser;
                    $('#myModal .modal-body').load(url,function(result){
                        $("#myModal .modal-title").html('Private Message');
                        $('#myModal').modal({show:true});
                    });
                });
                
                $("body").on('click','.sendrepbtn', function() {
                    var pmusers = $( "#pmusers" ).val();
                    var pmsubject = $( "#pmsubject" ).val();
                    var pmtext = $( "#pmtext" ).val();
                    var pmid = $(this).attr('id');
            
                    $.ajax({
                        method: 'post',
                        url: 'sendpm.php?pid='+pmid,
                        data: {
                            'pmusers': pmusers,
                            'pmsubject': pmsubject,
                            'pmtext': pmtext
                        },
                        success: function(data) {
                            $('#myModal').modal('hide');
                            toastr.success("PM has been sent", "New PM has been Sent");
                        }
                    });
                });
                
                $("body").on('click','#sendpmbtn', function() {
                    var pmusers = $( "#pmusers" ).val();
                    var pmsubject = $( "#pmsubject" ).val();
                    var pmtext = $( "#pmtext" ).val();
                    $.ajax({
                        method: 'post',
                        url: 'sendpm.php',
                        data: {
                            'pmusers': pmusers,
                            'pmsubject': pmsubject,
                            'pmtext': pmtext
                        },
                        success: function(data) {
                            $('#myModal').modal('hide');
                            $("#inboxmessages").load("readpm.php");
                            toastr.success("PM has been sent", "New PM has been Sent");
                        }
                    });
                });
                
                $('#myModal').on('hide.bs.modal', function (e) {
                  $('#myModal .modal-body').html("");
                })
                

                var transfertype = $('#transferType').val();
                if(transfertype == "1"){
                    $("#reciept").show();
                    $("#bank").hide();
                } else if(transfertype == "2"){
                    $("#reciept").hide();
                    $("#bank").show();
                } else {
                  //  block of code to be executed if the condition1 is false and condition2 is false
                }
                
                $("#transferType").change(function() {
                    var transfertype2 = $('#transferType').val();
                    if(transfertype2 == "1"){
                        $("#reciept").show();
                        $("#bank").hide();
                    } else if(transfertype2 == "2"){
                        $("#reciept").hide();
                        $("#bank").show();
                    } else {
                      //  block of code to be executed if the condition1 is false and condition2 is false
                    }
                });

                <?php if($_SESSION["type"] == "Trader" OR $_SESSION["type"] == "Leads") { ?>
            	    var tp = "<?php echo $array2r; ?>";
            	    var url = "trader_trans.php";
            	    var unit = "";
            	<?php } else if($_SESSION["type"] == "Retention Agent") { ?>
            	    var tp = "<?php echo $array2r; ?>";
            	    var url = "trader_trans.php";
            	    var unit = "";
            	<?php } else if($_SESSION["type"] == "Sales Agent") { ?>
            	    var tp = "<?php echo $array2r; ?>";
            	    var url = "trader_ftd.php";
            	    var unit = "";
            	<?php } else if($_SESSION["type"] == "IB") { ?>
            	    var tp = "<?php echo $array2r; ?>";
            	    var url = "trader_ftd.php";
            	    var unit = "";
            	<?php } else { ?>
            	    var unit = "<?php echo $unit; ?>";
            	    var url = "server_processing.php";
            	    var tp = "";
            	<?php } ?>
                var transactions = $('#trans-table').DataTable( {
                	"serverSide": false,
                	"ajax": {
                        "url": url,
                        "type": 'POST',
                        "data": {
                            "startTime": "<?php echo $startTime; ?>",
                            "endTime": "<?php echo $endTime; ?>",
                            "unit": unit,
                            "tp": tp
                        }
                    },
                	"processing": true,
                    "deferRender": false,
                    "responsive": true,
                    "order": [ 6, 'desc' ],
                	"lengthMenu": [ [ 5, 10, 25, 50, -1], [ 5, 10, 25, 50, "All"] ],
                	"columnDefs" :[  
                        {
                            "targets": 3,
                            "render": function(a) {
            					var type = a;
            					var badge = "secondary";
            					if (type == "Deposit"){
            						badge = "success";
            					} else if (type == "Withdraw"){
            						badge = "danger";
            					}
                                return '<span class="badge badge-'+badge+'">'+type+'</span>';
            			    }
            			},
            			{
                            "targets": 4,
                            "render": function(a) {
            					var amount = a;
                                return '$'+amount;
            			    }
            			},
            			{
                            "targets": 8,
                            "render": function(a) {
            					var type = a;
            					var badge = "secondary";
            					if (type == "FTD"){
            						badge = "dark";
            					} else if (type == "RED"){
            						badge = "info";
            					} else if (type == "BONUS"){
            					    badge = "warning";
            					}
                                return '<span class="badge badge-'+badge+'">'+type+'</span>';
            			    }
            			}
            		],
                	"footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;
             
                        // Remove the formatting to get integer data for summation
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };
                        var dptotal = 0;
                        var bototal = 0;
                        var wdtotal = 0;
                        var zerototal = 0;
                        
                        var ftd = 0;
                        var ret = 0;
                        var arr = [0];
                        var arr1 = [0];
                        var arr2 = [0];
                        
                        var arr3 = [0];
                        var arr4 = [0];
                        var arr5 = [0];
                        
                        var arr6 = [0];
                        var arr7 = [0];
                        
                        function getSum(total, num) {
                          return total + num;
                        }
             
                        // Total over all pages
                        totalDeposit = api
                            .column( 4 )
                            .data()
                            .each(function(value, index) {
                                if (api.column(8).data()[index] == "FTD" || api.column(8).data()[index] == "RED") {
                                    arr.push(parseFloat(value));  
                                } else if (api.column(8).data()[index] == "BONUS") {
                                    arr1.push(parseFloat(value));
                                } else if (api.column(8).data()[index] == "ZERO") {
                                    arr6.push(parseFloat(value));
                                } else {
                                    arr2.push(parseFloat(value));
                                }
                            });
             
                        // Total over this page
                        pageTotalDeposit = api
                            .column( 4, { search: 'applied'} )
                            .data()
                            .each(function(value, index) {
                                if (api.column(8, { search: 'applied'}).data()[index] == "FTD" || api.column(8, { search: 'applied'}).data()[index] == "RED") {
                                    arr3.push(parseFloat(value));  
                                } else if (api.column(8, { search: 'applied'}).data()[index] == "BONUS") {
                                    arr4.push(parseFloat(value));
                                } else if (api.column(8, { search: 'applied'}).data()[index] == "ZERO") {
                                    arr7.push(parseFloat(value));
                                } else {
                                    arr5.push(parseFloat(value));
                                }
                            });
                            
                            dptotal = arr.reduce(getSum);
                            bototal = arr1.reduce(getSum);
                            wdtotal = arr2.reduce(getSum);
                            
                            dptotal2 = arr3.reduce(getSum);
                            bototal2 = arr4.reduce(getSum);
                            wdtotal2 = arr5.reduce(getSum);
                            
                            zerototal = arr6.reduce(getSum);
                            zerototal2 = arr7.reduce(getSum);
                            //console.log(test);
                            //.reduce( function (a, b) {
                            //    return intVal(a) + intVal(b);
                            //}, 0 );
                        
                        // Total over this page
                        pageTotalFTD = api
                            .column( 8, { search: 'applied'} )
                            .data()
                            .reduce(function (a, b) {
                                var cur_index = api.column(8).data().indexOf(b);
                                if (api.column(8).data()[cur_index] == "FTD") {
                                    //alert(a);
                                    //return intVal(a) + intVal(b);
                                    ftd++;
                                    $('#ftd').html(ftd+" FTD");
                                    return ftd;
                                }
                            }, 0 );
                        
                        pageTotalRET = api
                            .column( 8, { search: 'applied'} )
                            .data()
                            .reduce(function (a, b) {
                                var cur_index = api.column(8).data().indexOf(b);
                                if (api.column(8).data()[cur_index] == "RED") {
                                    ret++;
                                    $('#ret').html(ret+" Ret");
                                    return ret;
                                }
                            }, 0 );
             
                        // Update footer
                        $( api.column( 0 ).footer() ).html(
                            '<div class="row text-center"><div class="col-md-3">Filtered Deposit: $'+dptotal2.toFixed(2)+'</div><div class="col-md-3">Filtered Withdrawal: $'+wdtotal2.toFixed(2)+'</div><div class="col-md-3">Filtered Bonus: $'+bototal2.toFixed(2)+'</div><div class="col-md-3">Filtered Zero: $'+zerototal.toFixed(2)+'</div></div><hr><div class="row text-center"><div class="col-md-3">Total Deposit: $'+dptotal.toFixed(2)+'</div><div class="col-md-3">Total Withdrawal: $'+wdtotal.toFixed(2)+'</div><div class="col-md-3">Total Bonus: $'+bototal.toFixed(2)+'</div><div class="col-md-3">Total Zero: $'+zerototal.toFixed(2)+'</div></div>'
                        );
                    }
                });
                $( "#bf-all" ).click(function() {
                    transactions.columns(8).search('').draw();
                });
                $( "#bf-ftd" ).click(function() {
                    transactions.columns(8).search("^("+"FTD"+")$",true,false,false).draw();
                });
                $( "#bf-red" ).click(function() {
                    transactions.columns(8).search("^("+"RED"+")$",true,false,false).draw();
                });
                $( "#bf-bonus" ).click(function() {
                    transactions.columns(8).search("^("+"BONUS"+")$",true,false,false).draw();
                });
                $( "#bf-rwd" ).click(function() {
                    transactions.columns(8).search("^("+"R-WD"+")$",true,false,false).draw();
                });
                
                $('.refresh').click(function(e){
                    transactions.ajax.reload( null, false ); // user paging is not reset on reload
                });
            	
                $(function() {;
                    <?php
                    $stime = strtotime($startTime);
                    $etime = strtotime($endTime);
                    ?>
                    var start = <?php echo date('dd/mm/Y', $stime); ?>;
                    var end = <?php echo date('dd/mm/Y', $estime); ?>;
                
                    $('#reportrange').daterangepicker({
                        startDate: start,
                        endDate: end,
                        opens: 'left',
                        "showDropdowns": true,
                        "timePicker": true,
                        ranges: {
                           'Today': [moment(), moment().add(1, 'days')],
                           'Yesterday': [moment().subtract(1, 'days'), moment()],
                           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                           'This Month': [moment().startOf('month'), moment().endOf('month')],
                           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                        }
                    }, function(start, end, label){
                        $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                        
                        window.location.href = "welcome.php?startTime="+start.format('YYYY-MM-DD')+"&endTime="+end.format('YYYY-MM-DD');  
                    });
                
                    //cb(start, end);
                
                });
            } );
        </script>

<?php include('includes/script-bottom.php'); ?>

</body>

</html>
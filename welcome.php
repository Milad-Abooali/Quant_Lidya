<?php
######################################################################
#  M | 12:48 PM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

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
        $sqlreport= 'SELECT MONTH(MT4_TRADES.CLOSE_TIME) as MONTH, YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM lidyapar_mt4.MT4_TRADES JOIN lidyapar_mt4.MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_USERS.AGENT_ACCOUNT <> "1" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME),MONTH(MT4_TRADES.CLOSE_TIME)
                     UNION ALL
                     SELECT MONTH(mt5_deals.Time) as MONTH, YEAR(mt5_deals.Time) as YEAR, SUM(mt5_deals.Storage) as SWAPS, SUM(mt5_deals.Volume)/10000 as VOLUME, SUM(mt5_deals.Profit) AS PROFIT FROM lidyapar_mt5.mt5_deals JOIN lidyapar_mt5.mt5_users ON mt5_users.Login = mt5_deals.Login WHERE mt5_deals.Action < 2 AND mt5_deals.Entry > 0 AND mt5_users.Group IN ( "'.$prefixed_array.'" ) GROUP BY YEAR(mt5_deals.Time),MONTH(mt5_deals.Time)';
        //$sqlreport= 'SELECT MONTH(MT4_TRADES.CLOSE_TIME) as MONTH, YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_USERS.AGENT_ACCOUNT <> "1" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME),MONTH(MT4_TRADES.CLOSE_TIME) ORDER BY YEAR';
        $resultreport = $DB_mt4->query($sqlreport);

        $sqlreport4= 'SELECT YEAR(MT4_TRADES.CLOSE_TIME) as YEAR, SUM(MT4_TRADES.SWAPS) as SWAPS, SUM(MT4_TRADES.VOLUME)/100 as VOLUME, SUM(MT4_TRADES.PROFIT) AS PROFIT FROM lidyapar_mt4.MT4_TRADES JOIN lidyapar_mt4.MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_TRADES.CMD < 2 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_USERS.AGENT_ACCOUNT <> "1" GROUP BY YEAR(MT4_TRADES.CLOSE_TIME)
                      UNION ALL
                      SELECT YEAR(mt5_deals.Time) as YEAR, SUM(mt5_deals.Storage) as SWAPS, SUM(mt5_deals.Volume)/10000 as VOLUME, SUM(mt5_deals.Profit) AS PROFIT FROM lidyapar_mt5.mt5_deals JOIN lidyapar_mt5.mt5_users ON mt5_users.Login = mt5_deals.Login WHERE mt5_deals.Action < 2 AND mt5_deals.Entry > 0 AND mt5_users.Group IN ( "'.$prefixed_array.'" ) GROUP BY YEAR(mt5_deals.Time)';
        $resultreport4 = $DB_mt4->query($sqlreport4);
        
        $data_array = array();
        while ($data = $resultreport->fetch_assoc()) {
            $data_array[] = $data;
        }
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
                                    <h4 class="page-title"><?= $_L->T('Dashboard','sidebar') ?></h4>
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

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert alert-warning" role="alert">
                                    Credit card payment will be closed for a while due to the system update. ( Sistem
                                    güncellemesinden dolayı bir süre kapalı kalacaktır.)
                                </div>
                            </div>
                        </div>

                        <!--
                         <div class="row">
                            <div class="col-sm-12">
                                <div class="alert alert-warning" role="alert">
                                    Dear valued clients,<br>
                                    Our POS system is temporarily closed due to system update and maintenance by our provider and will be up asap. <br>
                                    Best Regards,<br>
                                    4inFX
                                </div>
                            </div>
                        </div>
                        -->


                        <div class="row">
                            <!-- Start EBook -->
                            <div class="card mb-3 col-lg-8" style="padding: 0px;">
                                <div class="row no-gutters">
                                    <div class="col-sm-12 col-md-4 bg-dark">
                                        <a href="https://clientzone.fourinfx.com/assets/e-book/E-Book-v1.4-4inFX.pdf"><img
                                                    src="assets/e-book/4inFX_E-Book-01-01.png"
                                                    style="width: 100%;"/></a>
                                    </div>
                                    <div class="col-sm-12 col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title">Yatirim Rehberi</h5>
                                            <p class="card-text">Forex piyasalarını daha iyi tanımanız ve olumsuz
                                                sonuçlanan deneyimlerden uzaklaşmanızı sağlamak adına bilmeniz gereken
                                                temel bilgileri sizin için eğitim kitapçığı haline getirdik.
                                                </br></br>Siz'de bu kitapçığı indirme hakkına sahip olan seçili
                                                yatırımcılarımızdan birisiniz.</p>
                                            <p class="card-text"><a
                                                        href="https://clientzone.fourinfx.com/assets/e-book/E-Book-v1.4-4inFX.pdf"
                                                        class="btn bg-gradient-primary text-white">Şimdi İndirin</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end EBook -->
                            <!-- Start Video -->
                            <div class="card mb-3 col-lg-4" style="padding: 0px;">
                                <video style="width: 100%;height: auto;object-fit: cover;" controls>
                                    <source src="assets/media/4inFX_Üyelik_ParaTransfer.mp4" type="video/mp4">
                                    Error Message
                                </video>
                            </div>

                            <!-- end Video -->
                        </div>

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
                            while($rowTRADES = $result1->fetch_assoc()) {
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
                                <?php } else if($_SESSION["type"] == "Trader" OR $_SESSION["type"] == "Leads" OR $_SESSION["type"] == "IB" ){ ?>
                                <div class="col-xl-4">
                                    <div class="card m-b-20">
                                        <div class="card-body" id="stats">
                                            <h4 class="mt-0 m-b-30 header-title float-left"><?= $_L->T('Total_Statistics', 'trade') ?>
                                                <span class="btn btn-sm bg-gradient-primary text-white waves-effect waves-light openLogin"
                                                      data-login=""><?= $_L->T('Open_Positions', 'trade') ?></span></h4>
                                            <span class="float-right">
                                                <select class="form-control float-right" id="LoginList">
                                                        
                                                </select>
                                            </span>
                                            <div class="clearfix"></div>
                                            <div class="float-left">
                                                <b class="stateLogin" style="font-size:1rem;"></b>
                                            </div>
                                            <div class="float-right">
                                                <span class="text-muted"><?= $_L->T('Open_Profit', 'trade') ?>:</span>
                                                <b class="floating" style="font-size:1rem;"></b>
                                            </div>
                                            <div class="clearfix"></div>
                                            <hr>
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
                                                    <h6 class="equity"></h6>
                                                    <p class="text-muted"><?= $_L->T('Equity','trade') ?></p>
                                                </div>
                                                <div class="col-4 ml-box">
                                                    <h6 class="margin_level"></h6>
                                                    <p class="text-muted"><?= $_L->T('Margin_Level','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="balance"></h6>
                                                    <p class="text-muted"><?= $_L->T('Balance','trade') ?></p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row text-center m-t-20">
                                                <div class="col-4">
                                                    <h6 class="margin_used"></h6>
                                                    <p class="text-muted"><?= $_L->T('Used_Margin','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="margin_free"></h6>
                                                    <p class="text-muted"><?= $_L->T('Free_Margin','trade') ?></p>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="credit"></h6>
                                                    <p class="text-muted"><?= $_L->T('Credit','trade') ?></p>
                                                </div>
                                            </div>
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
                                                        <span class="datetime text-black-50" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?= $_L->T('Updated_Time','general') ?>">2021-5-1 11:21:54</span>
                                                        <span class="px-2">|</span>
                                                        <i class="reload fas fa-sync fa-spin" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?= $_L->T('Reload_Widget','general') ?>"></i>
                                                    </span>
                                                </div>
                                                <div class="widget-body col-12"></div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card" id="divOpen" style="display:none;">
                                    <div class="card-body">
                                        <table id="open-positions" class="table table-hover table-vertical mb-1" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Login</th>
                                                    <th>Position</th>
                                                    <th>Symbol</th>
                                                    <th>Action</th>
                                                    <th>Open Time</th>
                                                    <th>Volume</th>
                                                    <th>Open Price</th>
                                                    <th>SL</th>
                                                    <th>TP</th>
                                                    <th>Current Price</th>
                                                    <th>Swap</th>
                                                    <th>Profit</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                        </table>
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
            
<div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="display: none;" aria-hidden="true" id="pmmodal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<?php include('includes/script.php'); ?>

        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>

        <!--Morris Chart-->
        <script src="assets/plugins/morris/morris.min.js"></script>
        <script src="assets/plugins/raphael/raphael-min.js"></script>
        <script src="assets/pages/dashboard.js"></script>
        <script src="assets/plugins/summernote/summernote-bs4.min.js"></script>
        
        <script>
            $(document).ready( function () {
                function toCommas(value) {
                    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
                
                let dataLogin = {
                	'logins': '<?php echo $array2r; ?>'
                }
                
                let AccountDefault = 0;
                ajaxCall ('statistics', 'stats',dataLogin, function(response){
                    let resObj = JSON.parse(response);
                    if (resObj.e) { // ERROR
                        toastr.error(resObj.e);
                    } else if (resObj.res) { // SUCCESS
                        let length =  resObj.res.length;
                        for(let i = 0; i < length; i++){
                            if(i == AccountDefault){
                                $("#LoginList").append("<option value='"+i+"' selected>"+resObj.res[i].Login+"</option>"); 
                            } else {
                                $("#LoginList").append("<option value='"+i+"'>"+resObj.res[i].Login+"</option>"); 
                            }
                            
                        }
                        $(".openLogin").attr( "data-login",resObj.res[AccountDefault].Login);
                        $(".equity").html(toCommas(resObj.res[AccountDefault].Equity)+"$");
                        $(".balance").html(toCommas(resObj.res[AccountDefault].Balance)+"$");
                        $(".margin_free").html(toCommas(resObj.res[AccountDefault].Margin_Free)+"$");
                        $(".margin_used").html(toCommas(resObj.res[AccountDefault].Margin_Used)+"$");
                        $(".credit").html(toCommas(resObj.res[AccountDefault].Credit)+"$");
                        $(".margin_level").html(toCommas(resObj.res[AccountDefault].Margin_Level)+"%");
                        $(".stateLogin").html(resObj.res[AccountDefault].Name);
                        $(".floating").html(toCommas(resObj.res[AccountDefault].Floating)+"$");
                        if(resObj.res[AccountDefault].Margin_Level < 100){
                            $(".ml-box").addClass("rounded text-white blink");
                            $(".ml-box p").addClass("text-white");
                        } else {
                            $(".ml-box").removeClass("rounded text-white blink");
                            $(".ml-box p").removeClass("text-white");
                        }
                        //console.log(resObj.res);
                    }
                });
                
                setInterval( function () {
                    ajaxCall ('statistics', 'stats',dataLogin, function(response){
                        let resObj = JSON.parse(response);
                        if (resObj.e) { // ERROR
                            toastr.error(resObj.e);
                        } else if (resObj.res) { // SUCCESS
                            $(".openLogin").attr( "data-login",resObj.res[AccountDefault].Login);
                            $(".equity").html(toCommas(resObj.res[AccountDefault].Equity)+"$");
                            $(".balance").html(toCommas(resObj.res[AccountDefault].Balance)+"$");
                            $(".margin_free").html(toCommas(resObj.res[AccountDefault].Margin_Free)+"$");
                            $(".margin_used").html(toCommas(resObj.res[AccountDefault].Margin_Used)+"$");
                            $(".credit").html(toCommas(resObj.res[AccountDefault].Credit)+"$");
                            $(".margin_level").html(toCommas(resObj.res[AccountDefault].Margin_Level)+"%");
                            $(".stateLogin").html(resObj.res[AccountDefault].Name);
                            $(".floating").html(toCommas(resObj.res[AccountDefault].Floating)+"$");
                            if(resObj.res[AccountDefault].Margin_Level < 100){
                                $(".ml-box").addClass("rounded text-white blink");
                                $(".ml-box p").addClass("text-white");
                            } else {
                                $(".ml-box").removeClass("rounded text-white blink");
                                $(".ml-box p").removeClass("text-white");
                            }
                            //console.log(resObj.res);
                        }
                    });
                }, 1000);
                
                $( "#LoginList" ).change(function() {
                    AccountDefault = $( "#LoginList" ).val();
                });
                $('#modalMain').on('hidden.bs.modal', function () {
                    $('#divOpen').hide();
                })
                $("body").on('click','.openLogin', function() {
                    $('#divOpen').show();
                    let body = $('#divOpen').html();
                    let footer = "";
                    makeModal('Open Positions', body,'xl',footer);
                    let openLogin = {
                    	'logins': $(this).attr("data-login")
                    }
                    var openTable = $('#open-positions').DataTable({
                        rowId: "position",
                        stateSave: true,
                        columns: [
                            { data: 'login', title: "Login", className: "poslogin" },
                            { data: 'position', title: "Position", className: "posposition" },
                            { data: 'symbol', title: "Symbol", className: "possymbol" },
                            { data: 'action', title: "Action", className: "posaction" },
                            { data: 'open_time', title: "Open Time", className: "posopentime" },
                            { data: 'volume', title: "Volume", className: "posvolume" },
                            { data: 'open_price', title: "Open Price", className: "posopenprice" },
                            { data: 'sl', title: "SL", className: "possl" },
                            { data: 'tp', title: "TP", className: "postp" },
                            { data: 'current_price', title: "Current Price", className: "poscurrentprice" },
                            { data: 'swap', title: "Swap", className: "posswap" },
                            { data: 'profit', title: "Profit", className: "posprofit" },
                            { data: 'actions', title: "Actions", className: "posactions" },
                        ]
                    });
                    ajaxCall ('statistics', 'openPositions',openLogin, function(response){
                        let resObj = JSON.parse(response);
                        if (resObj.e) { // ERROR
                            toastr.error(resObj.e);
                        } else if (resObj.res) { // SUCCESS
                            let length =  resObj.res.length;
                            for(let i = 0; i < length; i++){
                                if(resObj.res[i].Action == "0"){
                                    var PosAction = "Buy";
                                } else if (resObj.res[i].Action == "1"){
                                    var PosAction = "Sell";
                                }
                                openTable.row.add( {
                                    "login":        resObj.res[i].Login,
                                    "position":     resObj.res[i].Position,
                                    "symbol":       resObj.res[i].Symbol,
                                    "action":       PosAction,
                                    "open_time":    resObj.res[i].TimeCreate,
                                    "volume":       resObj.res[i].Volume,
                                    "open_price":   resObj.res[i].PriceOpen,
                                    "sl":           resObj.res[i].PriceSL,
                                    "tp":           resObj.res[i].PriceTP,
                                    "current_price":resObj.res[i].PriceCurrent,
                                    "swap":         resObj.res[i].Storage,
                                    "profit":       resObj.res[i].Profit,
                                    "actions":      '<a href="javascript:;" class="bg-gradient-danger text-white btn-sm edit col-md-12 col-sm-6 text-center closePosition" data-position="'+resObj.res[i].Position+'" data-login="'+resObj.res[i].Login+'"><i class="fas fa-times"></i></a>',
                                } ).draw();
                            }
                        }
                    });
                    
                    setInterval( function () {
                        ajaxCall ('statistics', 'openPositions',openLogin, function(response){
                            let tabledata;
                            let tablendata = [];
                            tabledata = openTable.columns(1).data().toArray();
                            let resObj = JSON.parse(response);
                            if (resObj.e) { // ERROR
                                toastr.error(resObj.e);
                            } else if (resObj.res) { // SUCCESS
                                let length =  resObj.res.length;
                                for(let i = 0; i < length; i++){
                                    if(resObj.res[i].Action == "0"){
                                        var PosAction = "Buy";
                                    } else if (resObj.res[i].Action == "1"){
                                        var PosAction = "Sell";
                                    }
                                    tablendata.push(resObj.res[i].Position);
                                    $("#"+resObj.res[i].Position+" .poslogin").html(resObj.res[i].Login);
                                    $("#"+resObj.res[i].Position+" .posposition").html(resObj.res[i].Position);
                                    $("#"+resObj.res[i].Position+" .possymbol").html(resObj.res[i].Symbol);
                                    $("#"+resObj.res[i].Position+" .posprofit").html(PosAction);
                                    $("#"+resObj.res[i].Position+" .posopentime").html(resObj.res[i].TimeCreate);
                                    $("#"+resObj.res[i].Position+" .posvolume").html(resObj.res[i].Volume);
                                    $("#"+resObj.res[i].Position+" .pospriceopen").html(resObj.res[i].PriceOpen);
                                    $("#"+resObj.res[i].Position+" .possl").html(resObj.res[i].PriceSL);
                                    $("#"+resObj.res[i].Position+" .postp").html(resObj.res[i].PriceTP);
                                    $("#"+resObj.res[i].Position+" .poscurrentprice").html(resObj.res[i].PriceCurrent);
                                    $("#"+resObj.res[i].Position+" .posswap").html(resObj.res[i].Storage);
                                    $("#"+resObj.res[i].Position+" .posprofit").html(resObj.res[i].Profit);
                                    //$("#"+resObj.res[i].Position+" .posactions").html('<a href="javascript:;" class="bg-gradient-danger text-white btn-sm edit col-md-12 col-sm-6 text-center closePosition" data-position="'+resObj.res[i].Position+'" data-login="'+resObj.res[i].Login+'"><i class="fas fa-times"></i></a>');
                                    var valueToFind = resObj.res[i].Position;
                                    var duplicateCheck = openTable.column(1).data().filter(function (value, index) {
                                        return value ==  valueToFind ? true : false;
                                    }).length;
                                    
                                    if(duplicateCheck == 0){
                                        openTable.row.add( {
                                            "login":        resObj.res[i].Login,
                                            "position":     resObj.res[i].Position,
                                            "symbol":       resObj.res[i].Symbol,
                                            "action":       PosAction,
                                            "open_time":    resObj.res[i].TimeCreate,
                                            "volume":       resObj.res[i].Volume,
                                            "open_price":   resObj.res[i].PriceOpen,
                                            "sl":           resObj.res[i].PriceSL,
                                            "tp":           resObj.res[i].PriceTP,
                                            "current_price":resObj.res[i].PriceCurrent,
                                            "swap":         resObj.res[i].Storage,
                                            "profit":       resObj.res[i].Profit,
                                            "actions":      '<a href="javascript:;" class="bg-gradient-danger text-white btn-sm edit col-md-12 col-sm-6 text-center closePosition" data-position="'+resObj.res[i].Position+'" data-login="'+resObj.res[i].Login+'"><i class="fas fa-times"></i></a>',
                                        } ).draw(false);
                                    }
                                }
                                var newArr = [];
                                for(var i = 0; i < tabledata.length; i++)
                                {
                                    newArr = newArr.concat(tabledata[i]);
                                }
                                var difference = $(newArr).not(tablendata).get();
                                openTable.row("#"+difference[0]).remove().draw(false);
                            }
                        });
                    }, 1000);

                    //let openLogin = $(this).attr('data-login');
                    //makeModal('Open Positions', body,'md',footer);
                });
                $("body").on('click','.closePosition', function() {
                    let closeLogin = {
                    	'logins': $(this).attr("data-login"),
                    	'position' : $(this).attr("data-position")
                    }
                    
                    ajaxCall ('statistics', 'closePosition',closeLogin, function(response){
                        let resObj = JSON.parse(response);
                        if (resObj.e) { // ERROR
                            toastr.error(resObj.e);
                        } else if (resObj.res) { // SUCCESS
                            console.log(resObj.res);
                        }
                    });
                });
                
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
            
                var data = [
                    <?php
                        foreach($data_array as $rowReport) {
                            $month_num = $rowReport['MONTH'];
                            $month_name = date("F", mktime(0, 0, 0, $month_num, 10));
                            if($_SESSION["type"] == "Trader" OR $_SESSION["type"] == "Leads") {
                                $repprofit = $rowReport['PROFIT']+$rowReport['SWAPS'];
                            } else {
                                if($rowReport['PROFIT']+$rowReport['SWAPS'] < 0){
                                    $repprofit = abs($rowReport['PROFIT']+$rowReport['SWAPS']);
                                } else {
                                    $repprofit = ($rowReport['PROFIT']+$rowReport['SWAPS']) * -1;
                                }
                            }
                            echo "{ y: '".$month_name."', a: ".$repprofit."},";
                        }
                    ?>
                ]
                
                Morris.Bar({
                    element: 'earningm',
                    data: data,
                    xkey: 'y',
                    ykeys: ['a'],
                    labels: ['PNL'],
                    stacked: true,
                    hideHover: 'auto',
                    resize: true,
                    gridLineColor: '#eee',
                    barColors: function (row, series, type) {
                        if (row.y < 0)
                            return "#ec536c";
                        return "#7a6fbe";
                    }
                    //barColors:['#7a6fbe', '#28bbe3', '#ccc']
                });
                
                var data2 = [
                    <?php
                        while($rowReport4 = $resultreport4->fetch_assoc()) {
                            if($_SESSION["type"] == "Trader" OR $_SESSION["type"] == "Leads") {
                                $repprofit4 = $rowReport4['PROFIT']+$rowReport4['SWAPS'];
                            } else {
                                if($rowReport4['PROFIT']+$rowReport4['SWAPS'] < 0){
                                    $repprofit4 = abs($rowReport4['PROFIT']+$rowReport4['SWAPS']);
                                } else {
                                    $repprofit4 = ($rowReport4['PROFIT']+$rowReport4['SWAPS']) * -1;
                                }
                            }
                            echo "{ y: '".$rowReport4['YEAR']."', a: ".$repprofit4.", b: ".$rowReport4['VOLUME']."},";
                        }
                    ?>
                ]
                
                Morris.Area({
                    element: 'earningy',
                    pointSize: 0,
                    lineWidth: 1,
                    data: data2,
                    xkey: 'y',
                    ykeys: ['b','a'],
                    labels: ['VOLUME', 'PNL'],
                    hideHover: 'auto',
                    resize: true,
                    gridLineColor: '#eee',
                    fillOpacity: 0.7,
                    behaveLikeLine: false,
                    lineColors:['#28bbe3', '#7a6fbe', '#ccc']
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
                            '<?= $_L->T('Today', 'general') ?>': [moment(), moment().add(1, 'days')],
                            '<?= $_L->T('Yesterday', 'general') ?>': [moment().subtract(1, 'days'), moment()],
                            '<?= $_L->T('Last_7_Days', 'general') ?>': [moment().subtract(6, 'days'), moment()],
                            '<?= $_L->T('Last_30_Days', 'general') ?>': [moment().subtract(29, 'days'), moment()],
                            '<?= $_L->T('This_Month', 'general') ?>': [moment().startOf('month'), moment().endOf('month')],
                            '<?= $_L->T('Last_Month', 'general') ?>': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
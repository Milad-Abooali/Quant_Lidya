<?php
######################################################################
#  M | 10:27 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

include('includes/head.php'); 
?>
    <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
<?php 

include('includes/css.php'); 

?>

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

    $unit = $_POST['unit'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $startTime2 = $_POST['startTime2'];
    $endTime2 = $_POST['endTime2'];
    $startTime3 = $_POST['startTime3'];
    $endTime3 = $_POST['endTime3'];
    if($_POST['endTime']) $endTime = date("Y-m-d H:i:s", strtotime($endTime) - 1);
    $usunit = intval($_POST['unit']);
    $userAgent = $_POST['agent'];
    $userType = $_POST['type'];
    $userAgent4 = $_POST['agent4'];
    $userType4 = $_POST['type4'];
    
    
    //echo $startTime;
    
    $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "'.$_SESSION["unitn"].'"';
    $mtgroups = $DB_admin->query($sqlMtGroups);
    while($rowGroups = $mtgroups->fetch_assoc()) {
        $unitn = $rowGroups['name'];
    }
    $array = str_replace(",", '","', $unitn);
    
    $fake = "1";
    if($_SESSION["type"] == "Manager"){
        $sqlTotal = "SELECT lidyapar_mt4.MT4_TRADES.TICKET   AS Ticket, 
               lidyapar_mt4.MT4_USERS.LOGIN AS Login, 
               lidyapar_mt4.MT4_USERS.NAME AS Name, 
               lidyapar_mt4.MT4_TRADES.PROFIT AS Amount, 
               lidyapar_mt4.MT4_TRADES.COMMENT AS Comment, 
               lidyapar_mt4.MT4_TRADES.CLOSE_TIME AS Time, 
               CASE 
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND fourinfx_admin.tp.ftd = lidyapar_mt4.MT4_TRADES.CLOSE_TIME 
        				AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
        				AND lidyapar_mt4.MT4_TRADES.COMMENT IN ( 
        					'Deposit Wire Transfer',
                			'Deposit Credit Card',
                			'Deposit'
                        ) THEN 'FTD' 
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND fourinfx_admin.tp.ftd != lidyapar_mt4.MT4_TRADES.CLOSE_TIME
        				AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
        				AND lidyapar_mt4.MT4_TRADES.COMMENT IN (
                        	'Deposit Wire Transfer',
                			'Deposit Credit Card',
                			'Deposit'
                        ) THEN 'RET' 
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND fourinfx_admin.tp.ftd != lidyapar_mt4.MT4_TRADES.CLOSE_TIME
        				AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
        				AND lidyapar_mt4.MT4_TRADES.COMMENT IN (
                        	'zeroutbonus',
                			'zerooutbonus',
                			'Zerout'
                        ) THEN 'ZEROING' 
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND lidyapar_mt4.MT4_TRADES.PROFIT < 0
                        AND lidyapar_mt4.MT4_TRADES.COMMENT IN(
                        	'Withdrawal Wire Transfer',
                			'Withdrawal Credit Card',
                			'Withdrawal',
                			'Account Transfer'
                        ) THEN 'WD'
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
                        AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN (
                        	'Deposit Wire Transfer',
                			'Deposit Credit Card',
                			'Deposit',
                			'zeroutbonus',
                			'zerooutbonus',
                			'Zerout'
                        ) THEN 'BONUS IN' 
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND lidyapar_mt4.MT4_TRADES.PROFIT < 0 
                        AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN (
                        	'Withdrawal Wire Transfer',
                			'Withdrawal Credit Card',
                			'Withdrawal',
                			'Account Transfer'
                        ) THEN 'BONUS OUT' 
        			ELSE 'NTC' 
               END                   AS Type, 
               fourinfx_admin.tp.user_id            AS `User`, 
               fourinfx_admin.tp.retention          AS Retention, 
               fourinfx_admin.tp.conversion         AS Conversion 
        FROM   lidyapar_mt4.MT4_USERS 
               LEFT JOIN lidyapar_mt4.MT4_TRADES 
                      ON lidyapar_mt4.MT4_USERS.LOGIN = lidyapar_mt4.MT4_TRADES.LOGIN
               LEFT JOIN fourinfx_admin.tp 
                      ON fourinfx_admin.tp.login = lidyapar_mt4.MT4_TRADES.LOGIN
        WHERE  lidyapar_mt4.MT4_USERS.AGENT_ACCOUNT != 1
               AND lidyapar_mt4.MT4_TRADES.CMD IN ( '6' )
               AND lidyapar_mt4.MT4_USERS.GROUP IN (\"" . $array . "\")
               AND lidyapar_mt4.MT4_TRADES.CLOSE_TIME BETWEEN '" . $startTime . "' AND '" . $endTime . "'";
    } else if ($_SESSION["type"] == "Admin") {
        $sqlTotal = "SELECT lidyapar_mt4.MT4_TRADES.TICKET AS Ticket, 
               lidyapar_mt4.MT4_USERS.LOGIN AS Login, 
               lidyapar_mt4.MT4_USERS.NAME AS Name, 
               lidyapar_mt4.MT4_TRADES.PROFIT AS Amount, 
               lidyapar_mt4.MT4_TRADES.COMMENT AS Comment, 
               lidyapar_mt4.MT4_TRADES.CLOSE_TIME AS Time, 
               CASE 
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND fourinfx_admin.tp.ftd = lidyapar_mt4.MT4_TRADES.CLOSE_TIME 
        				AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
        				AND lidyapar_mt4.MT4_TRADES.COMMENT IN ( 
        					'Deposit Wire Transfer',
                			'Deposit Credit Card',
                			'Deposit'
                        ) THEN 'FTD' 
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND fourinfx_admin.tp.ftd != lidyapar_mt4.MT4_TRADES.CLOSE_TIME
        				AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
        				AND lidyapar_mt4.MT4_TRADES.COMMENT IN (
                        	'Deposit Wire Transfer',
                			'Deposit Credit Card',
                			'Deposit'
                        ) THEN 'RET' 
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND fourinfx_admin.tp.ftd != lidyapar_mt4.MT4_TRADES.CLOSE_TIME
        				AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
        				AND lidyapar_mt4.MT4_TRADES.COMMENT IN (
                        	'zeroutbonus',
                			'zerooutbonus',
                			'Zerout'
                        ) THEN 'ZEROING' 
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND lidyapar_mt4.MT4_TRADES.PROFIT < 0
                        AND lidyapar_mt4.MT4_TRADES.COMMENT IN(
                        	'Withdrawal Wire Transfer',
                			'Withdrawal Credit Card',
                			'Withdrawal',
                			'Account Transfer'
                        ) THEN 'WD'
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
                        AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN (
                        	'Deposit Wire Transfer',
                			'Deposit Credit Card',
                			'Deposit',
                			'zeroutbonus',
                			'zerooutbonus',
                			'Zerout'
                        ) THEN 'BONUS IN' 
        			WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        				AND lidyapar_mt4.MT4_TRADES.PROFIT < 0 
                        AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN (
                        	'Withdrawal Wire Transfer',
                			'Withdrawal Credit Card',
                			'Withdrawal',
                			'Account Transfer'
                        ) THEN 'BONUS OUT' 
        			ELSE 'NTC' 
               END                   AS Type, 
               fourinfx_admin.tp.user_id            AS `User`, 
               fourinfx_admin.tp.retention          AS Retention, 
               fourinfx_admin.tp.conversion         AS Conversion 
        FROM   lidyapar_mt4.MT4_USERS 
               LEFT JOIN lidyapar_mt4.MT4_TRADES 
                      ON lidyapar_mt4.MT4_USERS.LOGIN = lidyapar_mt4.MT4_TRADES.LOGIN
               LEFT JOIN fourinfx_admin.tp 
                      ON fourinfx_admin.tp.login = lidyapar_mt4.MT4_TRADES.LOGIN
        WHERE  lidyapar_mt4.MT4_USERS.AGENT_ACCOUNT != 1
               AND lidyapar_mt4.MT4_TRADES.CMD IN ( '6' ) 
               AND lidyapar_mt4.MT4_TRADES.CLOSE_TIME BETWEEN '" . $startTime . "' AND '" . $endTime . "'";
    }
    
    //echo $sqlTotal;
$resultTotal = $DB_admin->query($sqlTotal);

    //---------------------------------------------------------------------------------//

    if($_SESSION["unitn"] == "1"){
        $g = "TUR4";
    } else if($_SESSION["unitn"] == "3"){
        $g = "PERS";
    } else if($_SESSION["unitn"] == "6"){
        $g = "STPL";
    } else if($_SESSION["unitn"] == "4"){
        $g = "ARAB";
    } else if($_SESSION["unitn"] == "8"){
        $g = "PER2";
    } else if($_SESSION["unitn"] == "5"){
        $g = "SPEC";
    }
    if($_SESSION["type"] == "Manager"){
        $sqlTotal2 = "
        SELECT 
        lidyapar_mt5.mt5_deals.Deal AS Ticket, 
        lidyapar_mt5.mt5_users.Login, 
        lidyapar_mt5.mt5_users.Name, 
        lidyapar_mt5.mt5_deals.Profit AS Amount,
        lidyapar_mt5.mt5_deals.Comment, 
        lidyapar_mt5.mt5_deals.Time,
        CASE 
           WHEN lidyapar_mt5.mt5_deals.Action = 2 
                AND fourinfx_admin.tp.ftd = lidyapar_mt5.mt5_deals.Time 
                AND lidyapar_mt5.mt5_deals.Profit > 0 
                AND lidyapar_mt5.mt5_deals.Comment NOT IN ('Carried Balance From MT4','Zeroing') THEN 'FTD' 
            WHEN lidyapar_mt5.mt5_deals.Action = 2
                AND lidyapar_mt5.mt5_deals.Profit > 0 
                AND lidyapar_mt5.mt5_deals.Comment = 'Carried Balance From MT4' THEN 'RET' 
            WHEN lidyapar_mt5.mt5_deals.Action = 2 
                AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
                AND lidyapar_mt5.mt5_deals.Profit > 0 
                AND lidyapar_mt5.mt5_deals.Comment != 'Zeroing' THEN 'RET'
            WHEN lidyapar_mt5.mt5_deals.Action = 2 
                AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
                AND lidyapar_mt5.mt5_deals.Profit > 0 
                AND lidyapar_mt5.mt5_deals.Comment = 'Zeroing' THEN 'ZEROING'
            WHEN lidyapar_mt5.mt5_deals.Action = 2 
                AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'WD' 
            WHEN lidyapar_mt5.mt5_deals.Action = 6 
                AND lidyapar_mt5.mt5_deals.Profit > 0 THEN 'BONUS IN' 
            WHEN lidyapar_mt5.mt5_deals.Action = 6 
                AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'BONUS OUT' 
            ELSE 'NTC' 
        END AS Type,
        fourinfx_admin.tp.user_id AS `User`,
        fourinfx_admin.tp.retention AS Retention,
        fourinfx_admin.tp.conversion AS Conversion
        FROM lidyapar_mt5.mt5_users 
       	LEFT JOIN lidyapar_mt5.mt5_deals 
           		ON lidyapar_mt5.mt5_users.Login = lidyapar_mt5.mt5_deals.Login
        LEFT JOIN fourinfx_admin.tp 
           		ON fourinfx_admin.tp.login = lidyapar_mt5.mt5_deals.Login
        WHERE  lidyapar_mt5.mt5_users.Group LIKE 'real\\\\\\\\%" . $g . "%' 
        AND lidyapar_mt5.mt5_deals.Action IN ( '2', '6' )
        AND lidyapar_mt5.mt5_deals.Time BETWEEN '" . $startTime . "' AND '" . $endTime . "'";
    } else if($_SESSION["type"] == "Admin"){
        $sqlTotal2 = "
        SELECT 
        lidyapar_mt5.mt5_deals.Deal AS Ticket, 
        lidyapar_mt5.mt5_users.Login, 
        lidyapar_mt5.mt5_users.Name, 
        lidyapar_mt5.mt5_deals.Profit AS Amount, 
        lidyapar_mt5.mt5_deals.Comment, 
        lidyapar_mt5.mt5_deals.Time, 
        CASE 
            WHEN lidyapar_mt5.mt5_deals.Action = 2 
                AND fourinfx_admin.tp.ftd = lidyapar_mt5.mt5_deals.Time 
                AND lidyapar_mt5.mt5_deals.Profit > 0 
                AND lidyapar_mt5.mt5_deals.Comment NOT IN ('Carried Balance From MT4','Zeroing') THEN 'FTD' 
            WHEN lidyapar_mt5.mt5_deals.Action = 2
                AND lidyapar_mt5.mt5_deals.Profit > 0 
                AND lidyapar_mt5.mt5_deals.Comment = 'Carried Balance From MT4' THEN 'RET' 
            WHEN lidyapar_mt5.mt5_deals.Action = 2 
                AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
                AND lidyapar_mt5.mt5_deals.Profit > 0 
                AND lidyapar_mt5.mt5_deals.Comment != 'Zeroing' THEN 'RET'
            WHEN lidyapar_mt5.mt5_deals.Action = 2 
                AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
                AND lidyapar_mt5.mt5_deals.Profit > 0 
                AND lidyapar_mt5.mt5_deals.Comment = 'Zeroing' THEN 'ZEROING'
            WHEN lidyapar_mt5.mt5_deals.Action = 2 
                AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'WD' 
            WHEN lidyapar_mt5.mt5_deals.Action = 6 
                AND lidyapar_mt5.mt5_deals.Profit > 0 THEN 'BONUS IN' 
            WHEN lidyapar_mt5.mt5_deals.Action = 6 
                AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'BONUS OUT' 
            ELSE 'NTC' 
        END AS Type,
        fourinfx_admin.tp.user_id AS `User`,
        fourinfx_admin.tp.retention AS Retention,
        fourinfx_admin.tp.conversion AS Conversion
        FROM lidyapar_mt5.mt5_users 
       	LEFT JOIN lidyapar_mt5.mt5_deals 
           		ON lidyapar_mt5.mt5_users.Login = lidyapar_mt5.mt5_deals.Login
        LEFT JOIN fourinfx_admin.tp 
           		ON fourinfx_admin.tp.login = lidyapar_mt5.mt5_deals.Login 
        WHERE  lidyapar_mt5.mt5_users.Group LIKE 'real%4%' 
        AND lidyapar_mt5.mt5_deals.Action IN ( '2', '6' )
        AND lidyapar_mt5.mt5_deals.Time BETWEEN '" . $startTime . "' AND '" . $endTime . "'";
    }
$resultTotal2 = $DB_admin->query($sqlTotal2);
    //echo $sqlTotal2;

    
    if($_SESSION["type"] == "Manager"){
        $sqlTotal3 = "
        SELECT lidyapar_mt4.MT4_TRADES.TICKET AS Ticket, 
           lidyapar_mt4.MT4_USERS.LOGIN AS Login, 
           lidyapar_mt4.MT4_USERS.NAME AS Name, 
           lidyapar_mt4.MT4_TRADES.PROFIT AS Amount, 
           lidyapar_mt4.MT4_TRADES.COMMENT AS Comment, 
           lidyapar_mt4.MT4_TRADES.CLOSE_TIME AS Time, 
           CASE 
        		WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        			AND fourinfx_admin.tp.ftd = lidyapar_mt4.MT4_TRADES.CLOSE_TIME 
        			AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
        			AND lidyapar_mt4.MT4_TRADES.COMMENT IN ( 
        				'Deposit Wire Transfer',
        				'Deposit Credit Card',
        				'Deposit'
        			) THEN 'FTD' 
        		WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        			AND fourinfx_admin.tp.ftd != lidyapar_mt4.MT4_TRADES.CLOSE_TIME
        			AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
        			AND lidyapar_mt4.MT4_TRADES.COMMENT IN (
        				'Deposit Wire Transfer',
        				'Deposit Credit Card',
        				'Deposit'
        			) THEN 'RET' 
        		WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        			AND fourinfx_admin.tp.ftd != lidyapar_mt4.MT4_TRADES.CLOSE_TIME
        			AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
        			AND lidyapar_mt4.MT4_TRADES.COMMENT IN (
        				'zeroutbonus',
        				'zerooutbonus',
        				'Zerout'
        			) THEN 'ZEROING' 
        		WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        			AND lidyapar_mt4.MT4_TRADES.PROFIT < 0
        			AND lidyapar_mt4.MT4_TRADES.COMMENT IN(
        				'Withdrawal Wire Transfer',
        				'Withdrawal Credit Card',
        				'Withdrawal',
        				'Account Transfer'
        			) THEN 'WD'
        		WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        			AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
        			AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN (
        				'Deposit Wire Transfer',
        				'Deposit Credit Card',
        				'Deposit',
        				'zeroutbonus',
        				'zerooutbonus',
        				'Zerout'
        			) THEN 'BONUS IN' 
        		WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
        			AND lidyapar_mt4.MT4_TRADES.PROFIT < 0 
        			AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN (
        				'Withdrawal Wire Transfer',
        				'Withdrawal Credit Card',
        				'Withdrawal',
        				'Account Transfer'
        			) THEN 'BONUS OUT' 
        		ELSE 'NTC' 
           END                   AS Type, 
           fourinfx_admin.tp.user_id            AS `User`, 
           fourinfx_admin.tp.retention          AS Retention, 
           fourinfx_admin.tp.conversion         AS Conversion 
        FROM lidyapar_mt4.lidyapar_mt4.MT4_USERS 
           LEFT JOIN lidyapar_mt4.lidyapar_mt4.MT4_TRADES 
        		  ON lidyapar_mt4.MT4_USERS.LOGIN = lidyapar_mt4.MT4_TRADES.LOGIN
           LEFT JOIN fourinfx_admin.tp 
        		  ON fourinfx_admin.tp.login = lidyapar_mt4.MT4_TRADES.LOGIN
        WHERE  lidyapar_mt4.MT4_USERS.AGENT_ACCOUNT != 1
           AND lidyapar_mt4.MT4_TRADES.CMD IN ( '6' ) 
           AND lidyapar_mt4.MT4_USERS.GROUP IN (\"" . $array . "\")
           AND lidyapar_mt4.MT4_TRADES.CLOSE_TIME BETWEEN '" . $startTime . "' AND '" . $endTime . "'
        UNION ALL
        SELECT 
        lidyapar_mt5.mt5_deals.Deal AS Ticket, 
        lidyapar_mt5.mt5_users.Login, 
        lidyapar_mt5.mt5_users.Name, 
        lidyapar_mt5.mt5_deals.Profit AS Amount, 
        lidyapar_mt5.mt5_deals.Comment, 
        lidyapar_mt5.mt5_deals.Time,
        CASE 
           WHEN lidyapar_mt5.mt5_deals.Action = 2 
        		AND fourinfx_admin.tp.ftd = lidyapar_mt5.mt5_deals.Time 
        		AND lidyapar_mt5.mt5_deals.Profit > 0 
        		AND lidyapar_mt5.mt5_deals.Comment NOT IN ('Carried Balance From MT4','Zeroing') THEN 'FTD' 
        	WHEN lidyapar_mt5.mt5_deals.Action = 2
        		AND lidyapar_mt5.mt5_deals.Profit > 0 
        		AND lidyapar_mt5.mt5_deals.Comment = 'Carried Balance From MT4' THEN 'RET' 
        	WHEN lidyapar_mt5.mt5_deals.Action = 2 
        		AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
        		AND lidyapar_mt5.mt5_deals.Profit > 0 
        		AND lidyapar_mt5.mt5_deals.Comment != 'Zeroing' THEN 'RET'
        	WHEN lidyapar_mt5.mt5_deals.Action = 2 
        		AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
        		AND lidyapar_mt5.mt5_deals.Profit > 0 
        		AND lidyapar_mt5.mt5_deals.Comment = 'Zeroing' THEN 'ZEROING'
        	WHEN lidyapar_mt5.mt5_deals.Action = 2 
        		AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'WD' 
        	WHEN lidyapar_mt5.mt5_deals.Action = 6 
        		AND lidyapar_mt5.mt5_deals.Profit > 0 THEN 'BONUS IN' 
        	WHEN lidyapar_mt5.mt5_deals.Action = 6 
        		AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'BONUS OUT' 
        	ELSE 'NTC' 
        END AS Type,
        fourinfx_admin.tp.user_id AS `User`,
        fourinfx_admin.tp.retention AS Retention,
        fourinfx_admin.tp.conversion AS Conversion
        FROM lidyapar_mt5.mt5_users 
        LEFT JOIN lidyapar_mt5.mt5_deals 
        		ON lidyapar_mt5.mt5_users.Login = lidyapar_mt5.mt5_deals.Login
        LEFT JOIN fourinfx_admin.tp 
        		ON fourinfx_admin.tp.login = lidyapar_mt5.mt5_deals.Login
        WHERE  lidyapar_mt5.mt5_users.Group LIKE 'real\\\\\\\\%" . $g . "%' 
        AND lidyapar_mt5.mt5_deals.Action IN ( '2', '6' )
        AND lidyapar_mt5.mt5_deals.Time BETWEEN '" . $startTime . "' AND '" . $endTime . "'";
    } else if($_SESSION["type"] == "Admin"){
        $sqlTotal3 = "
        SELECT 
        lidyapar_mt5.mt5_deals.Deal AS Ticket, 
        lidyapar_mt5.mt5_users.Login, 
        lidyapar_mt5.mt5_users.Name, 
        lidyapar_mt5.mt5_deals.Profit AS Amount, 
        lidyapar_mt5.mt5_deals.Comment, 
        lidyapar_mt5.mt5_deals.Time, 
        CASE 
        	WHEN lidyapar_mt5.mt5_deals.Action = 2 
        		AND fourinfx_admin.tp.ftd = lidyapar_mt5.mt5_deals.Time 
        		AND lidyapar_mt5.mt5_deals.Profit > 0 
        		AND lidyapar_mt5.mt5_deals.Comment NOT IN ('Carried Balance From MT4','Zeroing') THEN 'FTD' 
        	WHEN lidyapar_mt5.mt5_deals.Action = 2
        		AND lidyapar_mt5.mt5_deals.Profit > 0 
        		AND lidyapar_mt5.mt5_deals.Comment = 'Carried Balance From MT4' THEN 'RET' 
        	WHEN lidyapar_mt5.mt5_deals.Action = 2 
        		AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
        		AND lidyapar_mt5.mt5_deals.Profit > 0 
        		AND lidyapar_mt5.mt5_deals.Comment != 'Zeroing' THEN 'RET'
        	WHEN lidyapar_mt5.mt5_deals.Action = 2 
        		AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
        		AND lidyapar_mt5.mt5_deals.Profit > 0 
        		AND lidyapar_mt5.mt5_deals.Comment = 'Zeroing' THEN 'ZEROING'
        	WHEN lidyapar_mt5.mt5_deals.Action = 2 
        		AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'WD' 
        	WHEN lidyapar_mt5.mt5_deals.Action = 6 
        		AND lidyapar_mt5.mt5_deals.Profit > 0 THEN 'BONUS IN' 
        	WHEN lidyapar_mt5.mt5_deals.Action = 6 
        		AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'BONUS OUT' 
        	ELSE 'NTC' 
        END AS Type,
        fourinfx_admin.tp.user_id AS `User`,
        fourinfx_admin.tp.retention AS Retention,
        fourinfx_admin.tp.conversion AS Conversion
        FROM lidyapar_mt5.mt5_users 
        LEFT JOIN lidyapar_mt5.mt5_deals 
        		ON lidyapar_mt5.mt5_users.Login = lidyapar_mt5.mt5_deals.Login
        LEFT JOIN fourinfx_admin.tp 
        		ON fourinfx_admin.tp.login = lidyapar_mt5.mt5_deals.Login 
        WHERE  lidyapar_mt5.mt5_users.Group LIKE 'real%4%' 
        AND lidyapar_mt5.mt5_deals.Action IN ( '2', '6' )
        AND lidyapar_mt5.mt5_deals.Time BETWEEN '" . $startTime . "' AND '" . $endTime . "'";
    }
$resultTotal3 = $DB_admin->query($sqlTotal3);
    //echo $sqlTotal3;

    if($_SESSION["unitn"] == "1"){
        $g = "TUR4";
    } else if($_SESSION["unitn"] == "3"){
        $g = "PERS";
    } else if($_SESSION["unitn"] == "6"){
        $g = "STPL";
    } else if($_SESSION["unitn"] == "4"){
        $g = "ARAB";
    } else if($_SESSION["unitn"] == "8"){
        $g = "PER2";
    } else if($_SESSION["unitn"] == "5"){
        $g = "SPEC";
    }
    
    if($_SESSION["type"] == "Manager"){
        $sqlAgent = "SELECT 
            lidyapar_mt5.mt5_deals.Deal AS Ticket, 
            lidyapar_mt5.mt5_users.Login, 
            lidyapar_mt5.mt5_users.Name, 
            lidyapar_mt5.mt5_deals.Profit AS Amount, 
            lidyapar_mt5.mt5_deals.Comment AS Comment, 
            lidyapar_mt5.mt5_deals.Time AS Time,
            lidyapar_mt5.mt5_deals.Storage AS Swap, 
            CASE 
                WHEN lidyapar_mt5.mt5_deals.Action = 2 
                    AND fourinfx_admin.tp.ftd = lidyapar_mt5.mt5_deals.Time 
                    AND lidyapar_mt5.mt5_deals.Profit > 0 
                    AND lidyapar_mt5.mt5_deals.Comment NOT IN ('Carried Balance From MT4','Zeroing') THEN 'FTD' 
                WHEN lidyapar_mt5.mt5_deals.Action = 2
                    AND lidyapar_mt5.mt5_deals.Profit > 0 
                    AND lidyapar_mt5.mt5_deals.Comment = 'Carried Balance From MT4' THEN 'RET' 
                WHEN lidyapar_mt5.mt5_deals.Action = 2 
                    AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
                    AND lidyapar_mt5.mt5_deals.Profit > 0 
                    AND lidyapar_mt5.mt5_deals.Comment != 'Zeroing' THEN 'RET'
                WHEN lidyapar_mt5.mt5_deals.Action = 2 
                    AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
                    AND lidyapar_mt5.mt5_deals.Profit > 0 
                    AND lidyapar_mt5.mt5_deals.Comment = 'Zeroing' THEN 'ZEROING'
                WHEN lidyapar_mt5.mt5_deals.Action = 2 
                    AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'WD' 
                WHEN lidyapar_mt5.mt5_deals.Action = 6 
                    AND lidyapar_mt5.mt5_deals.Profit > 0 THEN 'BONUS IN' 
                WHEN lidyapar_mt5.mt5_deals.Action = 6 
                    AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'BONUS OUT'
                WHEN lidyapar_mt5.mt5_deals.Action = 0
                    THEN 'BUY'
                WHEN lidyapar_mt5.mt5_deals.Action = 1
                    THEN 'SELL'
                ELSE 'NTC' 
            END AS Type,
            fourinfx_admin.tp.user_id AS `User`,
            fourinfx_admin.tp.retention AS Retention
        FROM lidyapar_mt5.mt5_users 
           	LEFT JOIN lidyapar_mt5.mt5_deals 
               		ON lidyapar_mt5.mt5_users.Login = lidyapar_mt5.mt5_deals.Login
            LEFT JOIN fourinfx_admin.tp 
               		ON fourinfx_admin.tp.login = lidyapar_mt5.mt5_deals.Login 
        WHERE  lidyapar_mt5.mt5_users.Group LIKE 'real\\\\\\\\%" . $g . "%'
            AND lidyapar_mt5.mt5_deals.Action IN ( '0', '1', '2', '6' )
            AND lidyapar_mt5.mt5_deals.Time BETWEEN '" . $startTime3 . "' AND '" . $endTime3 . "'
            AND fourinfx_admin.tp.retention = " . $userAgent . "";
    } else if($_SESSION["type"] == "Admin"){
        $sqlAgent = "SELECT 
            lidyapar_mt5.mt5_deals.Deal AS Ticket, 
            lidyapar_mt5.mt5_users.Login, 
            lidyapar_mt5.mt5_users.Name, 
            lidyapar_mt5.mt5_deals.Profit AS Amount, 
            lidyapar_mt5.mt5_deals.Comment AS Comment, 
            lidyapar_mt5.mt5_deals.Time AS Time,
            lidyapar_mt5.mt5_deals.Storage AS Swap, 
            CASE 
                WHEN lidyapar_mt5.mt5_deals.Action = 2 
                    AND fourinfx_admin.tp.ftd = lidyapar_mt5.mt5_deals.Time 
                    AND lidyapar_mt5.mt5_deals.Profit > 0 
                    AND lidyapar_mt5.mt5_deals.Comment NOT IN ('Carried Balance From MT4','Zeroing') THEN 'FTD' 
                WHEN lidyapar_mt5.mt5_deals.Action = 2
                    AND lidyapar_mt5.mt5_deals.Profit > 0 
                    AND lidyapar_mt5.mt5_deals.Comment = 'Carried Balance From MT4' THEN 'RET' 
                WHEN lidyapar_mt5.mt5_deals.Action = 2 
                    AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
                    AND lidyapar_mt5.mt5_deals.Profit > 0 
                    AND lidyapar_mt5.mt5_deals.Comment != 'Zeroing' THEN 'RET'
                WHEN lidyapar_mt5.mt5_deals.Action = 2 
                    AND fourinfx_admin.tp.ftd != lidyapar_mt5.mt5_deals.Time 
                    AND lidyapar_mt5.mt5_deals.Profit > 0 
                    AND lidyapar_mt5.mt5_deals.Comment = 'Zeroing' THEN 'ZEROING'
                WHEN lidyapar_mt5.mt5_deals.Action = 2 
                    AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'WD' 
                WHEN lidyapar_mt5.mt5_deals.Action = 6 
                    AND lidyapar_mt5.mt5_deals.Profit > 0 THEN 'BONUS IN' 
                WHEN lidyapar_mt5.mt5_deals.Action = 6 
                    AND lidyapar_mt5.mt5_deals.Profit < 0 THEN 'BONUS OUT'
                WHEN lidyapar_mt5.mt5_deals.Action = 0
                    THEN 'BUY'
                WHEN lidyapar_mt5.mt5_deals.Action = 1
                    THEN 'SELL'
                ELSE 'NTC' 
            END AS Type,
            fourinfx_admin.tp.user_id AS `User`,
            fourinfx_admin.tp.retention AS Retention
        FROM lidyapar_mt5.mt5_users 
           	LEFT JOIN lidyapar_mt5.mt5_deals 
               		ON lidyapar_mt5.mt5_users.Login = lidyapar_mt5.mt5_deals.Login
            LEFT JOIN fourinfx_admin.tp 
               		ON fourinfx_admin.tp.login = lidyapar_mt5.mt5_deals.Login 
        WHERE  lidyapar_mt5.mt5_users.Group LIKE 'real%4%' 
            AND lidyapar_mt5.mt5_deals.Action IN ( '0', '1', '2', '6' )
            AND lidyapar_mt5.mt5_deals.Time BETWEEN '" . $startTime3 . "' AND '" . $endTime3 . "'
            AND fourinfx_admin.tp.retention = " . $userAgent . "";
    }
$resultAgent = $DB_admin->query($sqlAgent);

    if($_SESSION["type"] == "Manager"){
        $sqlAgent1 = "SELECT 
            lidyapar_mt4.MT4_TRADES.TICKET AS Ticket, 
            lidyapar_mt4.MT4_USERS.LOGIN, 
            lidyapar_mt4.MT4_USERS.NAME, 
            lidyapar_mt4.MT4_TRADES.PROFIT AS Amount, 
            lidyapar_mt4.MT4_TRADES.COMMENT AS COMMENT, 
            lidyapar_mt4.MT4_TRADES.CLOSE_TIME AS Time,
            lidyapar_mt4.MT4_TRADES.SWAPS AS Swap, 
            CASE 
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND fourinfx_admin.tp.ftd = lidyapar_mt4.MT4_TRADES.CLOSE_TIME 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN ('Withdrawal', 'WITHDRAWAL', 'WITHDRAWAL WIRE TRANSFER', 'Withdrawal Wire Transfer', 'Withdrawal Credit Card', 'WITHDRAWAL CREDIT CARD', 'Wire Out', 'wire out', 'WIRE OUT', 'Withdraw', 'WITHDRAW', 'Account Transfer') THEN 'FTD'  
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND fourinfx_admin.tp.ftd != lidyapar_mt4.MT4_TRADES.CLOSE_TIME 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN ('Withdrawal', 'WITHDRAWAL', 'WITHDRAWAL WIRE TRANSFER', 'Withdrawal Wire Transfer', 'Withdrawal Credit Card', 'WITHDRAWAL CREDIT CARD', 'Wire Out', 'wire out', 'WIRE OUT', 'Withdraw', 'WITHDRAW', 'Account Transfer', 'Zeroing', 'Deposit Internal') THEN 'RET'
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND fourinfx_admin.tp.ftd != lidyapar_mt4.MT4_TRADES.CLOSE_TIME 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT = 'Zeroing' THEN 'ZEROING'
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT < 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN ('Deposit', 'DEPOSIT', 'DEPOSIT WIRE TRANSFER', 'Deposit Wire Transfer', 'Deposit Credit Card', 'DEPOSIT CREDIT CARD', 'Wire In', 'wire in', 'WIRE IN', 'Deposit Internal', 'Withdrawal Internal') THEN 'WD' 
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT IN ('Deposit Internal') THEN 'BONUS IN' 
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT < 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT IN ('Withdrawal Internal') THEN 'BONUS OUT'
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 0
                    THEN 'BUY'
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 1
                    THEN 'SELL'
                ELSE 'NTC' 
            END AS Type,
            fourinfx_admin.tp.user_id AS `User`,
            fourinfx_admin.tp.retention AS Retention
        FROM lidyapar_mt4.MT4_USERS 
           	LEFT JOIN lidyapar_mt4.MT4_TRADES 
               		ON lidyapar_mt4.MT4_USERS.LOGIN = lidyapar_mt4.MT4_TRADES.LOGIN
            LEFT JOIN fourinfx_admin.tp 
               		ON fourinfx_admin.tp.LOGIN = lidyapar_mt4.MT4_TRADES.LOGIN 
        WHERE lidyapar_mt4.MT4_USERS.AGENT_ACCOUNT != '" . $fake . "'
            AND lidyapar_mt4.MT4_USERS.GROUP IN (\"" . $array . "\")
            AND lidyapar_mt4.MT4_TRADES.CMD IN ( '0', '1', '6' )
            AND lidyapar_mt4.MT4_TRADES.CLOSE_TIME BETWEEN '" . $startTime2 . "' AND '" . $endTime2 . "'
            AND fourinfx_admin.tp.retention = " . $userAgent4 . "";
    } else if($_SESSION["type"] == "Admin"){
        $sqlAgent1 = "SELECT 
            lidyapar_mt4.MT4_TRADES.TICKET AS Ticket, 
            lidyapar_mt4.MT4_USERS.LOGIN, 
            lidyapar_mt4.MT4_USERS.NAME, 
            lidyapar_mt4.MT4_TRADES.PROFIT AS Amount, 
            lidyapar_mt4.MT4_TRADES.COMMENT AS COMMENT, 
            lidyapar_mt4.MT4_TRADES.CLOSE_TIME AS Time,
            lidyapar_mt4.MT4_TRADES.SWAPS AS Swap, 
            CASE 
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND fourinfx_admin.tp.ftd = lidyapar_mt4.MT4_TRADES.CLOSE_TIME 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN ('Withdrawal', 'WITHDRAWAL', 'WITHDRAWAL WIRE TRANSFER', 'Withdrawal Wire Transfer', 'Withdrawal Credit Card', 'WITHDRAWAL CREDIT CARD', 'Wire Out', 'wire out', 'WIRE OUT', 'Withdraw', 'WITHDRAW', 'Account Transfer') THEN 'FTD'  
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND fourinfx_admin.tp.ftd != lidyapar_mt4.MT4_TRADES.CLOSE_TIME 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN ('Withdrawal', 'WITHDRAWAL', 'WITHDRAWAL WIRE TRANSFER', 'Withdrawal Wire Transfer', 'Withdrawal Credit Card', 'WITHDRAWAL CREDIT CARD', 'Wire Out', 'wire out', 'WIRE OUT', 'Withdraw', 'WITHDRAW', 'Account Transfer', 'Zeroing', 'Deposit Internal') THEN 'RET'
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND fourinfx_admin.tp.ftd != lidyapar_mt4.MT4_TRADES.CLOSE_TIME 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT = 'Zeroing' THEN 'ZEROING'
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT < 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT NOT IN ('Deposit', 'DEPOSIT', 'DEPOSIT WIRE TRANSFER', 'Deposit Wire Transfer', 'Deposit Credit Card', 'DEPOSIT CREDIT CARD', 'Wire In', 'wire in', 'WIRE IN', 'Deposit Internal', 'Withdrawal Internal') THEN 'WD' 
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT > 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT IN ('Deposit Internal') THEN 'BONUS IN' 
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 6 
                    AND lidyapar_mt4.MT4_TRADES.PROFIT < 0 
                    AND lidyapar_mt4.MT4_TRADES.COMMENT IN ('Withdrawal Internal') THEN 'BONUS OUT'
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 0
                    THEN 'BUY'
                WHEN lidyapar_mt4.MT4_TRADES.CMD = 1
                    THEN 'SELL'
                ELSE 'NTC' 
            END AS Type,
            fourinfx_admin.tp.user_id AS `User`,
            fourinfx_admin.tp.retention AS Retention
        FROM lidyapar_mt4.MT4_USERS 
           	LEFT JOIN lidyapar_mt4.MT4_TRADES 
               		ON lidyapar_mt4.MT4_USERS.LOGIN = lidyapar_mt4.MT4_TRADES.LOGIN
            LEFT JOIN fourinfx_admin.tp 
               		ON fourinfx_admin.tp.LOGIN = lidyapar_mt4.MT4_TRADES.LOGIN 
        WHERE lidyapar_mt4.MT4_USERS.AGENT_ACCOUNT <> '1'
            AND lidyapar_mt4.MT4_TRADES.CMD IN ( '0', '1', '6' )
            AND lidyapar_mt4.MT4_TRADES.CLOSE_TIME BETWEEN '" . $startTime2 . "' AND '" . $endTime2 . "'
            AND fourinfx_admin.tp.retention = " . $userAgent4 . "";
    }
    //echo $sqlAgent1;
$resultAgent1 = $DB_admin->query($sqlAgent1);
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
                        <h4 class="page-title">Dashboard</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active">
                                Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to <?= Broker['title'] ?>
                            </li>
                        </ol>
                        <div class="state-information d-none d-sm-block">
                            <div class="state-graph">
                                <div id="header-chart-1"></div>
                                <div class="info">Leads 1500</div>
                            </div>
                            <div class="state-graph">
                                <div id="header-chart-2"></div>
                                <div class="info">Converted 40</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    		<div class="row">
    			<div class="col-sm-12">
    			    <div class="card pmd-card">
    					<div class="card-body">
            			    <form action="" method="post" autocomplete="off">
                            	<div class="form-row">
                            		<!--<div class="form-group col-md-3">
                            			<label for="inputuserId">Unit</label>
                        			    <input type="text" class="form-control" id="unit" name="unit" value="<?php echo $unit; ?>" required>
                                    </div>-->
                            		<div class="form-group col-md-4 date">
                            			<label for="inputstartTime">Start Time</label>
                            			<div class="input-group">
                                			<input type="text" class="form-control" id="startTime" name="startTime" value="<?php echo $startTime; ?>" required>
                                			<div class="input-group-append">
                                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                            </div>
                                        </div>
                            		</div>
                            		<div class="form-group col-md-4 date">
                            			<label for="inputendTime">End Time</label><i class="mdi mdi-update"></i>
                            			<div class="input-group">
                            			    <input type="text" class="form-control" id="endTime" name="endTime" value="<?php echo $endTime; ?>" required>
                            			    <div class="input-group-append">
                                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                            </div>
                                        </div>
                            		</div>
                            		<div class="form-group col-md-4">
                            		    <label for="input">&nbsp;</label>
                    			        <input class="btn btn-primary form-control" type="submit" name="submit" value="Submit" />
                    			    </div>
                    			</div>
                            </form>
                        </div>
            		</div>
    				<div class="card pmd-card">
    					<div class="card-body">
        				    <div class="row">
            				    <div class="col-md-6">
            				        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="pills-general-tab" data-toggle="pill" href="#pills-general" role="tab" aria-controls="pills-general" aria-selected="true">General Report</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-2-tab" data-toggle="pill" href="#pills-mt4" role="tab" aria-controls="pills-mt4" aria-selected="false">MT4</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-3-tab" data-toggle="pill" href="#pills-mt5" role="tab" aria-controls="pills-mt5" aria-selected="false">MT5</a>
                                        </li>
                                    </ul>
            				    </div>
                            </div>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                </div>
                                <div class="alert alert-danger alert-dismissible" id="delete" style="display:none;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                </div>
                                <div class="tab-pane fade show active" id="pills-general" role="tabpanel" aria-labelledby="pills-general-tab">
                                    <table id="data-table-general" class="table table-hover" style="width: 100%;">
                                        <thead>  
                                            <tr>
                                                <th>Unit</th>
                                                <th>Owner</th>
                    							<th>Login</th>
                                                <th>Name</th>
                                                <th>Time</th>
                                                <th>Comment</th>
                    							<th>Amount</th>
                    							<th>Type</th>
                    							<th>Retention</th>
                    							<th>Conversion</th>
                                            </tr>
                    					</thead>
                    					<tbody>
                    					    <?php
                                               if($resultTotal3) while ($rowMTG = mysqli_fetch_array($resultTotal3)) {
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php 
                                                            if($rowMTG['User'] == ""){
                                                                echo 'Null';
                                                            } else {
                                                                $sqlUSERS2 = 'SELECT unit FROM user_extra WHERE user_id = "'.$rowMTG['User'].'"';
                                                                $users2 = $DB_admin->query($sqlUSERS2);
                                                                if ($users2) while ($rowUSERS2 = mysqli_fetch_array($users2)) {
                                                                    $sqlUSERS = 'SELECT name FROM units WHERE id = "'.$rowUSERS2['unit'].'"';
                                                                    $users = $DB_admin->query($sqlUSERS);
                                                                    if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                                        echo $rowUSERS['name'];
                                                                    }
                                                                    
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            if($rowMTG['Type'] == "FTD"){
                                                                if($rowMTG['Conversion'] == "") {
                                                                    echo "Null";
                                                                } else {
                                                                    $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowMTG['Conversion'].'"';
                                                                    $users = $DB_admin->query($sqlUSERS);
                                                                    if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                                        echo $rowUSERS['username'];
                                                                    }
                                                                    //echo $rowMT5['Conversion'];
                                                                }
                                                            } else if($rowMTG['Type'] == "") {
                                                                echo "Null";
                                                            } else {
                                                                if($rowMTG['Retention'] == "") {
                                                                    echo "Null";
                                                                } else {
                                                                    $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowMTG['Retention'].'"';
                                                                    $users = $DB_admin->query($sqlUSERS);
                                                                    if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                                        echo $rowUSERS['username'];
                                                                    }
                                                                    //echo $rowMT5['Retention'];
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                    <td><?php echo $rowMTG['Login']; ?></td>
                                                    <td><?php echo $rowMTG['Name']; ?></td>
                                                    <td><?php echo $rowMTG['Time']; ?></td>
                                                    <td><?php echo $rowMTG['Comment']; ?></td>
                                                    <td><?php echo number_format($rowMTG['Amount'], 2, '.', ''); ?></td>
                                                    <td><?php echo $rowMTG['Type']; ?></td>
                                                    <td>
                                                        <?php 
                                                            if($rowMTG['Retention'] == ""){
                                                                echo 'Null';
                                                            } else {
                                                                echo $rowMTG['Retention'];
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            if($rowMTG['Conversion'] == ""){
                                                                echo 'Null';
                                                            } else {
                                                                echo $rowMTG['Conversion'];
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php
                                                }
                                            ?>
                    					</tbody>
                    					<tfoot>
                                            <tr>
                                                <th colspan="10"></th>
                                            </tr>
                                        </tfoot>
                    				</table>
                                </div>
                                <div class="tab-pane fade" id="pills-mt4" role="tabpanel" aria-labelledby="pills-2-tab">
                                    <table id="data-table-mt4" class="table table-hover" style="width: 100%;">  
                                        <thead>  
                                            <tr>
                                                <th>Unit</th>
                                                <th>Owner</th>
                                                <th>Login</th>
                                                <th>Name</th>
                                                <th>Time</th>
                                                <th>Comment</th>
                                                <th>Amount</th>
                                                <th>Type</th>
                                                <th>Retention</th>
                                                <th>Conversion</th>
                                            </tr>
                    					</thead>
                    					<tbody>
                    					    <?php
                                                if($resultTotal) while ($rowMT4 = mysqli_fetch_array($resultTotal)) {
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php 
                                                            if($rowMT4['User'] == ""){
                                                                echo 'Null';
                                                            } else {
                                                                $sqlUSERS2 = 'SELECT unit FROM user_extra WHERE user_id = "'.$rowMT4['User'].'"';
                                                                $users2 = $DB_admin->query($sqlUSERS2);
                                                                if($users2) while ($rowUSERS2 = mysqli_fetch_array($users2)) {
                                                                    $sqlUSERS = 'SELECT name FROM units WHERE id = "'.$rowUSERS2['unit'].'"';
                                                                    $users = $DB_admin->query($sqlUSERS);
                                                                    if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                                        echo $rowUSERS['name'];
                                                                    }
                                                                    
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            if($rowMT4['Type'] == "FTD"){
                                                                if($rowMT4['Conversion'] == "") {
                                                                    echo "Null";
                                                                } else {
                                                                    $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowMT4['Conversion'].'"';
                                                                    $users = $DB_admin->query($sqlUSERS);
                                                                    if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                                        echo $rowUSERS['username'];
                                                                    }
                                                                    //echo $rowMT5['Conversion'];
                                                                }
                                                            } else if($rowMT4['Type'] == "") {
                                                                echo "Null";
                                                            } else {
                                                                if($rowMT4['Retention'] == "") {
                                                                    echo "Null";
                                                                } else {
                                                                    $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowMT4['Retention'].'"';
                                                                    $users = $DB_admin->query($sqlUSERS);
                                                                    if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                                        echo $rowUSERS['username'];
                                                                    }
                                                                    //echo $rowMT5['Retention'];
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                    <td><?php echo $rowMT4['Login']; ?></td>
                                                    <td><?php echo $rowMT4['Name']; ?></td>
                                                    <td><?php echo $rowMT4['Time']; ?></td>
                                                    <td><?php echo $rowMT4['Comment']; ?></td>
                                                    <td><?php echo number_format($rowMT4['Amount'], 2, '.', ''); ?></td>
                                                    <td><?php echo $rowMT4['Type']; ?></td>
                                                    <td>
                                                        <?php 
                                                            if($rowMT4['Retention'] == ""){
                                                                echo 'Null';
                                                            } else {
                                                                echo $rowMT4['Retention'];
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            if($rowMT4['Conversion'] == ""){
                                                                echo 'Null';
                                                            } else {
                                                                echo $rowMT4['Conversion'];
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php                                                }
                                            ?>
                    					</tbody>
                    					<tfoot>
                                            <tr>
                                                <th colspan="10"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <hr>
                                    <form action="" method="post" autocomplete="off">
                            	        <div class="form-row">
                                            <div class="form-group col-md-3">
                                    			<label for="inputuserId">Retention Performance</label>
                                				<select class="form-control" id="agent4" name="agent4" required>
                                    			    <option value="">All</option>
                                    			    <?php
                                                        if($_SESSION["type"] == "Admin"){
                                                            $sqlUSERS = 'SELECT id, username FROM users WHERE type IN ("Retention Agent")';
                                                        } else {
                                                            $sqlUSERS = 'SELECT id, username FROM users WHERE unit = "'.$_SESSION["unit"].'" AND type IN ("Retention Agent")';
                                                        }
                                                        $users = $DB_admin->query($sqlUSERS);
                                                        if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                            echo "<option value='".$rowUSERS['id']."' ";
                                                            if($userAgent4 == $rowUSERS['id']) { 
                                                                echo 'selected';
                                                            }
                                                            echo ">".$rowUSERS['username']."</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                    			<label for="inputuserId">Agent Type</label>
                                				<select class="form-control" id="type4" name="type4" required>
                                    			    <!--<option value="All">All</option>-->
                                    			    <option value="Retention">Retention</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3 date">
                                    			<label for="inputstartTime">Start Time</label>
                                    			<div class="input-group">
                                        			<input type="text" class="form-control" id="startTime2" name="startTime2" value="<?php echo $startTime2; ?>" required>
                                        			<div class="input-group-append">
                                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                                    </div>
                                                </div>
                                    		</div>
                                    		<div class="form-group col-md-3 date">
                                    			<label for="inputendTime">End Time</label><i class="mdi mdi-update"></i>
                                    			<div class="input-group">
                                    			    <input type="text" class="form-control" id="endTime2" name="endTime2" value="<?php echo $endTime2; ?>" required>
                                    			    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                                    </div>
                                                </div>
                                    		</div>
                                    		<div class="form-group col-md-1">
                                    		    <label for="input">&nbsp;</label>
                            			        <input class="btn btn-primary form-control" type="submit" name="submit" value="Submit" />
                            			    </div>
                            			</div>
                            		</form>
                        			<?php
                        			    $nftd = 0;
                                        $aftd = 0;
                                        $nret = 0;
                                        $aret = 0;
                                        $awd = 0;
                                        $nbi = 0;
                                        $nbo = 0;
                                        $abi = 0;
                                        $abo = 0;
                                        $ze = 0;
                                        $pl = 0;
                                        $nbuy = 0;
                                        $nsell = 0;
                                        $swap = 0;
                                        if($resultAgent1) while ($rowAgent = mysqli_fetch_array($resultAgent1)) {
                                            if($rowAgent['Type'] == "FTD"){
                                                $nftd++;
                                                $aftd += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "RET"){
                                                $nret++;
                                                $aret += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "WD"){
                                                $nwd++;
                                                $awd += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "BONUS IN"){
                                                $nbi++;
                                                $abi += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "BONUS OUT"){
                                                $nbo++;
                                                $abo += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "ZEROING"){
                                                $ze += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "ZEROING"){
                                                $ze += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "BUY"){
                                                $pl += $rowAgent['Amount'];
                                                $swap += $rowAgent['Swap'];
                                                $nbuy++;
                                            } else if($rowAgent['Type'] == "SELL"){
                                                $pl += $rowAgent['Amount'];
                                                $swap += $rowAgent['Swap'];
                                                $nsell++;
                                            }
                                        }
                                    ?> 
        					        <?php if($userType !== "Retention"){ ?> 
        					        <div>FTD#: <?php echo $nftd; ?></div>
        					        <div>FTD$: <?php echo $aftd; ?></div>
        					        <?php } ?>
        					        <div>RET#: <?php echo $nret; ?></div>
        					        <div>RET$: <?php echo $aret; ?></div>
        					        <div>Withdrawal$: <?php echo $awd; ?></div>
        					        <div>Bonus In/Out#: <?php echo $nbi."/".$nbo; ?></div>
        					        <div>Bonus In/Out$: <?php echo $abi."/".$abo; ?></div>
        					        <div>Net Bonus$: <?php echo $abi+$abo; ?></div>
        					        <hr>
        					        <?php if($userType4 == "Retention"){ ?> 
        					        <div>Net D/W$: <?php echo ($aret)+$awd; ?></div>
        					        <?php }else{ ?>
        					        <div>Net D/W$: <?php echo ($aret+$aftd)+$awd; ?></div>
        					        <?php }?>
        					        <hr>
        					        <div>Zeroing$: <?php echo $ze; ?></div>
        					        <div>Swap$: <?php echo $swap; ?></div>
        					        <div>RAW PNL$: <?php echo $pl; ?></div>
        					        <hr>
        					        <div>Total PNL$: <?php echo number_format(($pl+$swap+($abi+$abo))+$ze, 2, '.', ''); ?></div>
        					        <hr>
        					        <div>Buy/Sell: <?php echo $nbuy."/".$nsell; ?></div>
                                </div>
                                <div class="tab-pane fade" id="pills-mt5" role="tabpanel" aria-labelledby="pills-3-tab">
                                    <table id="data-table-mt5" class="table table-hover" style="width: 100%;">  
                                        <thead>  
                                            <tr>
                                                <th>Unit</th>
                                                <th>Owner</th>
                    							<th>Login</th>
                                                <th>Name</th>
                                                <th>Time</th>
                                                <th>Comment</th>
                    							<th>Amount</th>
                    							<th>Type</th>
                    							<th>Retention</th>
                    							<th>Conversion</th>
                                            </tr>
                    					</thead>
                    					<tbody>
                    					    <?php
                                                if($resultTotal2) while ($rowMT5 = mysqli_fetch_array($resultTotal2)) {
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php 
                                                            if($rowMT5['User'] == ""){
                                                                echo 'Null';
                                                            } else {
                                                                $sqlUSERS2 = 'SELECT unit FROM user_extra WHERE user_id = "'.$rowMT5['User'].'"';
                                                                $users2 = $DB_admin->query($sqlUSERS2);
                                                                if($users2) while ($rowUSERS2 = mysqli_fetch_array($users2)) {
                                                                    $sqlUSERS = 'SELECT name FROM units WHERE id = "'.$rowUSERS2['unit'].'"';
                                                                    $users = $DB_admin->query($sqlUSERS);
                                                                    if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                                        echo $rowUSERS['name'];
                                                                    }
                                                                    
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            if($rowMT5['Type'] == "FTD"){
                                                                if($rowMT5['Conversion'] == "") {
                                                                    echo "Null";
                                                                } else {
                                                                    $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowMT5['Conversion'].'"';
                                                                    $users = $DB_admin->query($sqlUSERS);
                                                                    if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                                        echo $rowUSERS['username'];
                                                                    }
                                                                    //echo $rowMT5['Conversion'];
                                                                }
                                                            } else if($rowMT5['Type'] == "") {
                                                                echo "Null";
                                                            } else {
                                                                if($rowMT5['Retention'] == "") {
                                                                    echo "Null";
                                                                } else {
                                                                    $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowMT5['Retention'].'"';
                                                                    $users = $DB_admin->query($sqlUSERS);
                                                                    if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                                        echo $rowUSERS['username'];
                                                                    }
                                                                    //echo $rowMT5['Retention'];
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                    <td><?php echo $rowMT5['Login']; ?></td>
                                                    <td><?php echo $rowMT5['Name']; ?></td>
                                                    <td><?php echo $rowMT5['Time']; ?></td>
                                                    <td><?php echo $rowMT5['Comment']; ?></td>
                                                    <td><?php echo number_format($rowMT5['Amount'], 2, '.', ''); ?></td>
                                                    <td><?php echo $rowMT5['Type']; ?></td>
                                                    <td>
                                                        <?php 
                                                            if($rowMT5['Retention'] == ""){
                                                                echo 'Null';
                                                            } else {
                                                                echo $rowMT5['Retention'];
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            if($rowMT5['Conversion'] == ""){
                                                                echo 'Null';
                                                            } else {
                                                                echo $rowMT5['Conversion'];
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php                                                }
                                            ?>
                    					</tbody>
                    					<tfoot>
                                            <tr>
                                                <th colspan="10"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <hr>
                                    <form action="" method="post" autocomplete="off">
                            	        <div class="form-row">
                                            <div class="form-group col-md-3">
                                    			<label for="inputuserId">Retention Performance</label>
                                				<select class="form-control" id="agent" name="agent" required>
                                    			    <option value="">All</option>
                                    			    <?php
                                                        if($_SESSION["type"] == "Admin"){
                                                            $sqlUSERS = 'SELECT id, username FROM users WHERE type IN ("Retention Agent")';
                                                        } else {
                                                            $sqlUSERS = 'SELECT id, username FROM users WHERE unit = "'.$_SESSION["unit"].'" AND type IN ("Retention Agent")';
                                                        }
                                                        $users = $DB_admin->query($sqlUSERS);
                                                        if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                            echo "<option value='".$rowUSERS['id']."' ";
                                                            if($userAgent == $rowUSERS['id']) { 
                                                                echo 'selected';
                                                            }
                                                            echo ">".$rowUSERS['username']."</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                    			<label for="inputuserId">Agent Type</label>
                                				<select class="form-control" id="type" name="type" required>
                                    			    <!--<option value="All">All</option>-->
                                    			    <option value="Retention">Retention</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3 date">
                                    			<label for="inputstartTime">Start Time</label>
                                    			<div class="input-group">
                                        			<input type="text" class="form-control" id="startTime3" name="startTime3" value="<?php echo $startTime3; ?>" required>
                                        			<div class="input-group-append">
                                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                                    </div>
                                                </div>
                                    		</div>
                                    		<div class="form-group col-md-3 date">
                                    			<label for="inputendTime">End Time</label><i class="mdi mdi-update"></i>
                                    			<div class="input-group">
                                    			    <input type="text" class="form-control" id="endTime3" name="endTime3" value="<?php echo $endTime3; ?>" required>
                                    			    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                                    </div>
                                                </div>
                                    		</div>
                                    		<div class="form-group col-md-1">
                                    		    <label for="input">&nbsp;</label>
                            			        <input class="btn btn-primary form-control" type="submit" name="submit" value="Submit" />
                            			    </div>
                            			</div>
                            		</form>
                        			<?php
                        			    $nftd = 0;
                                        $aftd = 0;
                                        $nret = 0;
                                        $aret = 0;
                                        $awd = 0;
                                        $nbi = 0;
                                        $nbo = 0;
                                        $abi = 0;
                                        $abo = 0;
                                        $ze = 0;
                                        $pl = 0;
                                        $nbuy = 0;
                                        $nsell = 0;
                                        $swap = 0;
                                        if($resultAgent) while ($rowAgent = mysqli_fetch_array($resultAgent)) {
                                            if($rowAgent['Type'] == "FTD"){
                                                $nftd++;
                                                $aftd += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "RET"){
                                                $nret++;
                                                $aret += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "WD"){
                                                $nwd++;
                                                $awd += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "BONUS IN"){
                                                $nbi++;
                                                $abi += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "BONUS OUT"){
                                                $nbo++;
                                                $abo += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "ZEROING"){
                                                $ze += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "ZEROING"){
                                                $ze += $rowAgent['Amount'];
                                            } else if($rowAgent['Type'] == "BUY"){
                                                $pl += $rowAgent['Amount'];
                                                $swap += $rowAgent['Swap'];
                                                $nbuy++;
                                            } else if($rowAgent['Type'] == "SELL"){
                                                $pl += $rowAgent['Amount'];
                                                $swap += $rowAgent['Swap'];
                                                $nsell++;
                                            }
                                        }
                                    ?> 
        					        <?php if($userType !== "Retention"){ ?> 
        					        <div>FTD#: <?php echo $nftd; ?></div>
        					        <div>FTD$: <?php echo $aftd; ?></div>
        					        <?php } ?>
        					        <div>RET#: <?php echo $nret; ?></div>
        					        <div>RET$: <?php echo $aret; ?></div>
        					        <div>Withdrawal$: <?php echo $awd; ?></div>
        					        <div>Bonus In/Out#: <?php echo $nbi."/".$nbo; ?></div>
        					        <div>Bonus In/Out$: <?php echo $abi."/".$abo; ?></div>
        					        <div>Net Bonus$: <?php echo $abi+$abo; ?></div>
        					        <hr>
        					        <?php if($userType == "Retention"){ ?> 
        					        <div>Net D/W$: <?php echo ($aret)+$awd; ?></div>
        					        <?php }else{ ?>
        					        <div>Net D/W$: <?php echo ($aret+$aftd)+$awd; ?></div>
        					        <?php }?>
        					        <hr>
        					        <div>Zeroing$: <?php echo $ze; ?></div>
        					        <div>Swap$: <?php echo $swap; ?></div>
        					        <div>RAW PNL$: <?php echo $pl; ?></div>
        					        <hr>
        					        <div>Total PNL$: <?php echo number_format(($pl+$swap+($abi+$abo))+$ze, 2, '.', ''); ?></div>
        					        <hr>
        					        <div>Buy/Sell: <?php echo $nbuy."/".$nsell; ?></div>
        					        
                                </div>
                            </div>
                        </div>
                    </div>
    			</div>
    		</div>
        </div>
        <?php include('includes/footer.php'); ?>
    </div>
</div>
<?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>
$(document).ready( function () {
    
	$(function () {
		$('[data-toggle="tooltip"]').tooltip()
	});
	
	$('#startTime').datepicker({ 
	    uiLibrary: 'bootstrap',
        iconsLibrary: 'fontawesome', 
        format: 'yyyy-mm-dd' 
	});
	
	$('#endTime').datepicker({ 
	    uiLibrary: 'bootstrap',
        iconsLibrary: 'fontawesome', 
        format: 'yyyy-mm-dd' 
	});
	
	$('#startTime2').datepicker({ 
	    uiLibrary: 'bootstrap',
        iconsLibrary: 'fontawesome', 
        format: 'yyyy-mm-dd' 
	});
	
	$('#endTime2').datepicker({ 
	    uiLibrary: 'bootstrap',
        iconsLibrary: 'fontawesome', 
        format: 'yyyy-mm-dd' 
	});
	
	$('#startTime3').datepicker({ 
	    uiLibrary: 'bootstrap',
        iconsLibrary: 'fontawesome', 
        format: 'yyyy-mm-dd' 
	});
	
	$('#endTime3').datepicker({ 
	    uiLibrary: 'bootstrap',
        iconsLibrary: 'fontawesome', 
        format: 'yyyy-mm-dd' 
	});
    
    var groupColumn = 0;
    //////////////////////////////////////////////////////////MT5/////////////////////////////////////////////////////
    var mt5 = $('#data-table-mt5').DataTable({
        "columnDefs": [
            { "visible": false, "targets": 0 },
            { "visible": false, "targets": 1 }
        ],
		"responsive": false,
		"deferRender": true,
		"lengthMenu": [ [-1], ["All"] ],
		"order": [
            [0, 'asc'],
            [1, 'asc'],
            [3, 'asc']
        ],
		rowGroup: {
            startRender: function ( rows, group ) {
                var ftdn = 0;
                var retn = 0;
                var bonusin = 0;
                var bonuson = 0;
                var reta = 0;
                var bonusia = 0;
                var bonusoa = 0;
                var ntcn = 0;
                var wda = 0;
                var wdn = 0;
                var zeron = 0;
                var zeroa = 0;
                var ftdN = rows
                        .data()
                        .pluck(7)
                        .reduce( function (a, b) {
                            if (a == "FTD" || b == "FTD") {
                                ftdn++;
                            }
                            return ftdn;
                        }, 0);
                var ftdA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "FTD") {
                    ftdA += data[6] * 1;
                    }
                } ); 
                var retN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "RET" || b == "RET") {
                            retn++;
                        }
                        return retn;
                    }, 0);
                var retA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "RET") {
                    retA += data[6] * 1;
                    }
                } );
                var wdN = rows
                        .data()
                        .pluck(7)
                        .reduce( function (a, b) {
                            if (a == "WD" || b == "WD") {
                                wdn++;
                            }
                            return wdn;
                        }, 0);
                var wdA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "WD") {
                    wdA += data[6] * 1;
                    }
                } );
                var biN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "BONUS IN" || b == "BONUS IN") {
                            bonusin++;
                        }
                        return bonusin;
                    }, 0);
                var biA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "BONUS IN") {
                    biA += data[6] * 1;
                    }
                } ); 
                var boN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "BONUS OUT" || b == "BONUS OUT") {
                            bonuson++;
                        }
                        return bonuson;
                    }, 0);
                var boA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "BONUS OUT") {
                    boA += data[6] * 1;
                    }
                } ); 
                var zeA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "ZEROING") {
                    zeA += data[6] * 1;
                    }
                } ); 
                var ntcN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "NTC" || b == "NTC") {
                            ntcn++;
                        }
                        return ntcn;
                    }, 0);
                
                return $('<tr/>')
                    .append( '<td colspan="1"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> '+group+'</td>' )
                    .append( '<td colspan="1">FTD#: '+ftdN+'</td>' )
                    .append( '<td colspan="1">FTD$: '+ftdA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">RET#: '+retN+'</td>' )
                    .append( '<td colspan="1">RET$: '+retA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">WD#: '+wdN+'</td>' )
                    .append( '<td colspan="1">WD$: '+wdA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">Bonus#: '+biN+'/'+boN+'</td>' )
                    .append( '<td colspan="1">Bonus In$: '+biA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">Bonus Out$: '+boA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">Zeroing$: '+zeA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">NTC#: '+ntcN+'</td>' );
            },
            dataSrc: [0, 1, 3],
        },
        "drawCallback": function( settings ) {
            $(this).nextUntil('.dtrg-level-0').hide();
            $(this).addClass('hidel2');
            $(this).removeClass('showl2');
        },
        
    });
    
    $('.dtrg-level-1').hide();
    $('.dtrg-level-2').hide();
    $('[role=row]').hide();
    
    $('#data-table-mt5 tbody').on( 'click', '.dtrg-level-0', function () {
        $(this).nextUntil('.dtrg-level-0').hide();
        $(this).find('.dtrg-level-2').hide();
        $(this).addClass('hidel2');
        $(this).removeClass('showl2');
    } );
    $('#data-table-mt5 tbody').on( 'click', '.hidel2', function () {
        $(this).nextUntil('.dtrg-level-0').not('[role=row]').show();
        $(this).addClass('showl2');
        $(this).removeClass('hidel2');
    } );
    $('#data-table-mt5 tbody').on( 'click', '.dtrg-level-1', function () {
        $(this).nextUntil('.dtrg-level-1').hide();
        $(this).addClass('hidel1');
        $(this).removeClass('showl1');
    } );
    $('#data-table-mt5 tbody').on( 'click', '.hidel1', function () {
        $(this).nextUntil('.dtrg-level-1').not('[role=row]').show();
        $(this).addClass('showl1');
        $(this).removeClass('hidel1');
    } );
     $('#data-table-mt5 tbody').on( 'click', '.dtrg-level-2', function () {
        $(this).nextUntil('.dtrg-level-2').not('.dtrg-level-2').not('.dtrg-level-1').slideToggle();
        $(this).find('[role=row]').slideToggle();
    } );
    
    
    $('#data-table-mt5').on('search.dt', function (e, settings) {
        
    });
    
    //////////////////////////////////////////////////////////MT4/////////////////////////////////////////////////////
    
    var mt4 = $('#data-table-mt4').DataTable({
        "columnDefs": [
            { "visible": false, "targets": 0 },
            { "visible": false, "targets": 1 }
        ],
		"responsive": false,
		"deferRender": true,
		"lengthMenu": [ [-1], ["All"] ],
		"order": [
            [0, 'asc'],
            [1, 'asc'],
            [3, 'asc']
        ],
		rowGroup: {
            startRender: function ( rows, group ) {
                var ftdn = 0;
                var retn = 0;
                var bonusin = 0;
                var bonuson = 0;
                var reta = 0;
                var bonusia = 0;
                var bonusoa = 0;
                var ntcn = 0;
                var wda = 0;
                var wdn = 0;
                var zeron = 0;
                var zeroa = 0;
                var ftdN = rows
                        .data()
                        .pluck(7)
                        .reduce( function (a, b) {
                            if (a == "FTD" || b == "FTD") {
                                ftdn++;
                            }
                            return ftdn;
                        }, 0);
                var ftdA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "FTD") {
                    ftdA += data[6] * 1;
                    }
                } ); 
                var retN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "RET" || b == "RET") {
                            retn++;
                        }
                        return retn;
                    }, 0);
                var retA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "RET") {
                    retA += data[6] * 1;
                    }
                } );
                var wdN = rows
                        .data()
                        .pluck(7)
                        .reduce( function (a, b) {
                            if (a == "WD" || b == "WD") {
                                wdn++;
                            }
                            return wdn;
                        }, 0);
                var wdA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "WD") {
                    wdA += data[6] * 1;
                    }
                } );
                var biN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "BONUS IN" || b == "BONUS IN") {
                            bonusin++;
                        }
                        return bonusin;
                    }, 0);
                var biA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "BONUS IN") {
                    biA += data[6] * 1;
                    }
                } ); 
                var boN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "BONUS OUT" || b == "BONUS OUT") {
                            bonuson++;
                        }
                        return bonuson;
                    }, 0);
                var boA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "BONUS OUT") {
                    boA += data[6] * 1;
                    }
                } ); 
                var zeA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "ZEROING") {
                    zeA += data[6] * 1;
                    }
                } ); 
                var ntcN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "NTC" || b == "NTC") {
                            ntcn++;
                        }
                        return ntcn;
                    }, 0);
                
                return $('<tr/>')
                    .append( '<td colspan="1"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> '+group+'</td>' )
                    .append( '<td colspan="1">FTD#: '+ftdN+'</td>' )
                    .append( '<td colspan="1">FTD$: '+ftdA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">RET#: '+retN+'</td>' )
                    .append( '<td colspan="1">RET$: '+retA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">WD#: '+wdN+'</td>' )
                    .append( '<td colspan="1">WD$: '+wdA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">Bonus#: '+biN+'/'+boN+'</td>' )
                    .append( '<td colspan="1">Bonus In$: '+biA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">Bonus Out$: '+boA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">Zeroing$: '+zeA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">NTC#: '+ntcN+'</td>' );
            },
            dataSrc: [0, 1, 3],
        },
        "drawCallback": function( settings ) {
            $(this).nextUntil('.dtrg-level-0').hide();
            $(this).addClass('hidel2');
            $(this).removeClass('showl2');
        },
        
    });
    
    $('.dtrg-level-1').hide();
    $('.dtrg-level-2').hide();
    $('[role=row]').hide();
    
    $('#data-table-mt4 tbody').on( 'click', '.dtrg-level-0', function () {
        $(this).nextUntil('.dtrg-level-0').hide();
        $(this).find('.dtrg-level-2').hide();
        $(this).addClass('hidel2');
        $(this).removeClass('showl2');
    } );
    $('#data-table-mt4 tbody').on( 'click', '.hidel2', function () {
        $(this).nextUntil('.dtrg-level-0').not('[role=row]').show();
        $(this).addClass('showl2');
        $(this).removeClass('hidel2');
    } );
    $('#data-table-mt4 tbody').on( 'click', '.dtrg-level-1', function () {
        $(this).nextUntil('.dtrg-level-1').hide();
        $(this).addClass('hidel1');
        $(this).removeClass('showl1');
    } );
    $('#data-table-mt4 tbody').on( 'click', '.hidel1', function () {
        $(this).nextUntil('.dtrg-level-1').not('[role=row]').show();
        $(this).addClass('showl1');
        $(this).removeClass('hidel1');
    } );
     $('#data-table-mt4 tbody').on( 'click', '.dtrg-level-2', function () {
        $(this).nextUntil('.dtrg-level-2').not('.dtrg-level-2').not('.dtrg-level-1').slideToggle();
        $(this).find('[role=row]').slideToggle();
    } );
    
    
    $('#data-table-mt4').on('search.dt', function (e, settings) {
        
    });
    
    //////////////////////////////////////////////////////////GENERAL/////////////////////////////////////////////////////
    
    var general = $('#data-table-general').DataTable({
        "columnDefs": [
            { "visible": false, "targets": 0 },
            { "visible": false, "targets": 1 }
        ],
        "buttons": [
            'excelHtml5'
        ],
		"responsive": false,
		"deferRender": true,
		"lengthMenu": [ [-1], ["All"] ],
		"order": [
            [0, 'asc'],
            [1, 'asc'],
            [3, 'asc']
        ],
		rowGroup: {
            startRender: function ( rows, group ) {
                var ftdn = 0;
                var retn = 0;
                var bonusin = 0;
                var bonuson = 0;
                var reta = 0;
                var bonusia = 0;
                var bonusoa = 0;
                var ntcn = 0;
                var wda = 0;
                var wdn = 0;
                var zeron = 0;
                var zeroa = 0;
                var ftdN = rows
                        .data()
                        .pluck(7)
                        .reduce( function (a, b) {
                            if (a == "FTD" || b == "FTD") {
                                ftdn++;
                            }
                            return ftdn;
                        }, 0);
                var ftdA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "FTD") {
                    ftdA += data[6] * 1;
                    }
                } ); 
                var retN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "RET" || b == "RET") {
                            retn++;
                        }
                        return retn;
                    }, 0);
                var retA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "RET") {
                    retA += data[6] * 1;
                    }
                } );
                var wdN = rows
                        .data()
                        .pluck(7)
                        .reduce( function (a, b) {
                            if (a == "WD" || b == "WD") {
                                wdn++;
                            }
                            return wdn;
                        }, 0);
                var wdA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "WD") {
                    wdA += data[6] * 1;
                    }
                } );
                var biN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "BONUS IN" || b == "BONUS IN") {
                            bonusin++;
                        }
                        return bonusin;
                    }, 0);
                var biA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "BONUS IN") {
                    biA += data[6] * 1;
                    }
                } ); 
                var boN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "BONUS OUT" || b == "BONUS OUT") {
                            bonuson++;
                        }
                        return bonuson;
                    }, 0);
                var boA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "BONUS OUT") {
                    boA += data[6] * 1;
                    }
                } ); 
                var zeA = 0;
                rows.every( function ( rowIdx, tableLoop, rowLoop ) {
                    var data = this.data();
                
                    if (data[7] === "ZEROING") {
                    zeA += data[6] * 1;
                    }
                } ); 
                var ntcN = rows
                    .data()
                    .pluck(7)
                    .reduce( function (a, b) {
                        if (a == "NTC" || b == "NTC") {
                            ntcn++;
                        }
                        return ntcn;
                    }, 0);
                
                return $('<tr/>')
                    .append( '<td colspan="1"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> '+group+'</td>' )
                    .append( '<td colspan="1">FTD#: '+ftdN+'</td>' )
                    .append( '<td colspan="1">FTD$: '+ftdA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">RET#: '+retN+'</td>' )
                    .append( '<td colspan="1">RET$: '+retA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">WD#: '+wdN+'</td>' )
                    .append( '<td colspan="1">WD$: '+wdA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">Bonus#: '+biN+'/'+boN+'</td>' )
                    .append( '<td colspan="1">Bonus In$: '+biA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">Bonus Out$: '+boA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">Zeroing$: '+zeA.toFixed(2)+'</td>' )
                    .append( '<td colspan="1">NTC#: '+ntcN+'</td>' );
            },
            dataSrc: [0, 1, 3],
        },
        "drawCallback": function( settings ) {
            $(this).nextUntil('.dtrg-level-0').hide();
            $(this).addClass('hidel2');
            $(this).removeClass('showl2');
        },
        
    });
    
    var buttons = new $.fn.dataTable.Buttons(general, {
        buttons: [
            {
                extend: 'excelHtml5',
                autoFilter: true,
                className: 'btn-sm'
            }
        ]
    }).container().appendTo($('#data-table-general_filter'));
    
    $('.dtrg-level-0').addClass('hidel2');
    $('.dtrg-level-1').hide();
    $('.dtrg-level-2').hide();
    $('[role=row]').hide();
    
    $('#data-table-general tbody').on( 'click', '.dtrg-level-0', function () {
        $(this).nextUntil('.dtrg-level-0').hide();
        $(this).find('.dtrg-level-2').hide();
        $(this).addClass('hidel2');
        $(this).removeClass('showl2');
    } );
    $('#data-table-general tbody').on( 'click', '.hidel2', function () {
        $(this).nextUntil('.dtrg-level-0').not('.dtrg-level-2').not('[role=row]').show();
        $(this).addClass('showl2');
        $(this).removeClass('hidel2');
    } );
    $('#data-table-general tbody').on( 'click', '.dtrg-level-1', function () {
        $(this).nextUntil('.dtrg-level-1').not('.dtrg-level-0').hide();
        $(this).addClass('hidel1');
        $(this).removeClass('showl1');
    } );
    $('#data-table-general tbody').on( 'click', '.hidel1', function () {
        $(this).nextUntil('.dtrg-level-1').not('[role=row]').show();
        $(this).addClass('showl1');
        $(this).removeClass('hidel1');
    } );
     $('#data-table-general tbody').on( 'click', '.dtrg-level-2', function () {
        $(this).nextUntil('.dtrg-level-2').not('.dtrg-level-2').not('.dtrg-level-1').not('.dtrg-level-0').slideToggle();
        $(this).find('[role=row]').slideToggle();
    } );
    
    
    $('#data-table-general').on('search.dt', function (e, settings) {
        
    });
});
</script>

<?php include('includes/script-bottom.php'); ?>

</body>

</html>
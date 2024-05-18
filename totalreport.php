<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

require_once "config.php";

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
	if($_POST['endTime']) $endTime = date("Y-m-d H:i:s", strtotime($endTime) - 1);
    $usunit = intval($_POST['unit']);
    
    //echo $startTime;
    
    $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "'.$_SESSION["unitn"].'"';
    $mtgroups = $DB_admin->query($sqlMtGroups);
    while($rowGroups = $mtgroups->fetch_assoc()) {
        $unitn = $rowGroups['name'];
    }
    $array = str_replace(",", '","', $unitn);
    
    $fake = "1";
    if($_SESSION["type"] == "Manager"){
        $sqlTotal = "SELECT
        /* C  DEPOSIT */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS DEPOSIT,
        
        /* C  ZEROING */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS ZEROING,
        
        /* C  WITHDRAWAL */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0, PROFIT,0.0)) AS WITHDRAWAL,
        
        /* C  NET_DW */ 
        (SUM(IF(MT4_TRADES.COMMENT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) +
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0, PROFIT,0.0))) AS NET_DW,
        
        /* C  BONUS_IN */ 
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit',
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS BONUS_IN,
        
        /* C  BONUS_OUT */
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0 AND CMD = 6 , PROFIT,0.0)) AS BONUS_OUT,
        
        /* C  NET_Bonus */ 
        (SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit',
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0))+
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0 AND CMD = 6, PROFIT,0.0))) AS NET_BONUS,
        
        /* C  PROFIT */
        SUM(IF(CMD < 2, PROFIT, 0.0)) AS PROFIT,
        
        /* C  SWAPS */
        SUM(IF(CMD < 2, SWAPS, 0.0)) AS SWAPS,
        
        /* C  DAY(CLOSE_TIME) */
        DAY(CLOSE_TIME) AS DAY,
        
        (CASE
        WHEN MT4_USERS.GROUP LIKE 'KUVVAR%'  THEN 'Turkish'
        WHEN MT4_USERS.GROUP LIKE 'KUV2VAR%' THEN 'Turkish'
        WHEN MT4_USERS.GROUP LIKE 'KUV3VAR%' THEN 'STPL'
        WHEN MT4_USERS.GROUP LIKE 'KUVIA%' THEN 'Farsi'
        WHEN MT4_USERS.GROUP LIKE 'KUVIST%' THEN 'Arabic'
        ELSE 'English'
        END) AS UNIT
        
        /* T  MT4_TRADES ?+ MT4_USERS */
        FROM MT4_TRADES LEFT JOIN MT4_USERS USING(LOGIN)
        
        /* F  W */
        WHERE 
            MT4_TRADES.CLOSE_TIME BETWEEN '".$startTime."' AND '".$endTime."'
            AND MT4_USERS.AGENT_ACCOUNT != '".$fake."'
            AND MT4_USERS.GROUP IN (\"".$array."\")
        
        /* O  G */
        GROUP BY DAY(CLOSE_TIME),UNIT";
    } else if ($_SESSION["type"] == "Admin" || $_SESSION["type"] == "Lawyer") {
        $sqlTotal = "SELECT
        /* C  DEPOSIT */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS DEPOSIT,
        
        /* C  ZEROING */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS ZEROING,
        
        /* C  WITHDRAWAL */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0, PROFIT,0.0)) AS WITHDRAWAL,
        
        /* C  NET_DW */ 
        (SUM(IF(MT4_TRADES.COMMENT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) +
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0, PROFIT,0.0))) AS NET_DW,
        
        /* C  BONUS_IN */ 
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit',
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS BONUS_IN,
        
        /* C  BONUS_OUT */
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0 AND CMD = 6 , PROFIT,0.0)) AS BONUS_OUT,
        
        /* C  NET_Bonus */ 
        (SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit',
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0))+
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0 AND CMD = 6, PROFIT,0.0))) AS NET_BONUS,
        
        /* C  PROFIT */
        SUM(IF(CMD < 2, PROFIT, 0.0)) AS PROFIT,
        
        /* C  SWAPS */
        SUM(IF(CMD < 2, SWAPS, 0.0)) AS SWAPS,
        
        /* C  DAY(CLOSE_TIME) */
        DAY(CLOSE_TIME) AS DAY,
        
        (CASE
        WHEN MT4_USERS.GROUP LIKE 'KUVVAR%'  THEN 'Turkish'
        WHEN MT4_USERS.GROUP LIKE 'KUV2VAR%' THEN 'Turkish'
        WHEN MT4_USERS.GROUP LIKE 'KUV3VAR%' THEN 'STPL'
        WHEN MT4_USERS.GROUP LIKE 'KUVIA%' THEN 'Farsi'
        WHEN MT4_USERS.GROUP LIKE 'KUVIST%' THEN 'Arabic'
        ELSE 'English'
        END) AS UNIT
        
        /* T  MT4_TRADES ?+ MT4_USERS */
        FROM MT4_TRADES LEFT JOIN MT4_USERS USING(LOGIN)
        
        /* F  W */
        WHERE MT4_TRADES.CLOSE_TIME BETWEEN '".$startTime."' AND '".$endTime."'
        AND MT4_USERS.AGENT_ACCOUNT != '".$fake."'
        
        /* O  G */
        GROUP BY DAY(CLOSE_TIME),UNIT";
    }
    
    //echo $sqlTotal;
    $resultTotal = $DB_mt4->query($sqlTotal);
    
    //-----------------------------------MT4Report----------------------------------------------//
  
    if($_SESSION["unitn"] == "1"){
        $g = "TUR";
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
        /* C  DEPOSIT */ 
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) AS DEPOSIT,
        
        /* C  ZEROING */ 
        SUM(IF(mt5_deals.Comment IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) AS ZEROING,
        
        /* C  WITHDRAWAL */ 
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit < 0 AND Action = 2, Profit,0.0)) AS WITHDRAWAL,
        
        /* C  NET_DW */ 
        (SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) +
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit < 0 AND Action = 2, Profit,0.0))) AS NET_DW,
        
        /* C  BONUS_IN */ 
        SUM(IF(Profit > 0 AND Action = '6', Profit,0.0)) AS BONUS_IN,
        
        /* C  BONUS_OUT */
        SUM(IF(Profit < 0 AND Action = '6', Profit,0.0)) AS BONUS_OUT,
        
        /* C  NET_Bonus */ 
        (SUM(IF(Profit > 0 AND Action = '6', Profit,0.0))+
        SUM(IF(Profit < 0 AND Action = '6', Profit,0.0))) AS NET_BONUS,
        
        /* C  Profit */
        SUM(IF(Action < 2, Profit, 0.0)) AS PROFIT,
        
        /* C  Storage */
        SUM(IF(Action < 2, Storage, 0.0)) AS SWAPS,
        
        /* C  DAY(CLOSE_TIME) */
        DAY(mt5_deals.Time) AS DAY,
        
        (CASE WHEN mt5_users.Group LIKE 'real%4%' THEN 
         CASE 
        WHEN mt5_users.Group LIKE '%TUR4%'  THEN 'Turkish'
        WHEN mt5_users.Group LIKE '%STPL%' THEN 'STPL'
        WHEN mt5_users.Group LIKE '%PERS%' THEN 'Farsi'
        WHEN mt5_users.Group LIKE '%ARAB%' THEN 'Arabic'
        WHEN mt5_users.Group LIKE '%PER2%' THEN 'Farsi 2'
        WHEN mt5_users.Group LIKE '%PROF%' THEN 'Demo'
        WHEN mt5_users.Group LIKE '%treasury%' THEN 'Demo'
        ELSE 'English'
        END ELSE 'Demo' END) AS UNIT
        
        /* T  MT4_TRADES ?+ MT4_USERS */
        FROM mt5_deals LEFT JOIN mt5_users USING(Login)
        
        /* F  W */
        WHERE 
            mt5_deals.Time BETWEEN '".$startTime."' AND '".$endTime."'
            AND mt5_users.Group LIKE 'real\\\\\\\\".$g."%'
        
        /* O  G */
        GROUP BY DAY(mt5_deals.Time),UNIT";
    } else if($_SESSION["type"] == "Admin" || $_SESSION["type"] == "Lawyer"){
        $sqlTotal2 = "
        SELECT
        /* C  DEPOSIT */ 
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) AS DEPOSIT,
        
        /* C  ZEROING */ 
        SUM(IF(mt5_deals.Comment IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) AS ZEROING,
        
        /* C  WITHDRAWAL */ 
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit < 0 AND Action = 2, Profit,0.0)) AS WITHDRAWAL,
        
        /* C  NET_DW */ 
        (SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) +
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit < 0 AND Action = 2, Profit,0.0))) AS NET_DW,
        
        /* C  BONUS_IN */ 
        SUM(IF(Profit > 0 AND Action = '6', Profit,0.0)) AS BONUS_IN,
        
        /* C  BONUS_OUT */
        SUM(IF(Profit < 0 AND Action = '6', Profit,0.0)) AS BONUS_OUT,
        
        /* C  NET_Bonus */ 
        (SUM(IF(Profit > 0 AND Action = '6', Profit,0.0))+
        SUM(IF(Profit < 0 AND Action = '6', Profit,0.0))) AS NET_BONUS,
        
        /* C  Profit */
        SUM(IF(Action < 2, Profit, 0.0)) AS PROFIT,
        
        /* C  Storage */
        SUM(IF(Action < 2, Storage, 0.0)) AS SWAPS,
        
        /* C  DAY(CLOSE_TIME) */
        DAY(mt5_deals.Time) AS DAY,
        
        (CASE WHEN mt5_users.Group LIKE 'real%4%' THEN 
         CASE 
        WHEN mt5_users.Group LIKE '%TUR4%' THEN 'Turkish'
        WHEN mt5_users.Group LIKE '%STPL%' THEN 'STPL'
        WHEN mt5_users.Group LIKE '%PERS%' THEN 'Farsi'
        WHEN mt5_users.Group LIKE '%ARAB%' THEN 'Arabic'
        WHEN mt5_users.Group LIKE '%PER2%' THEN 'Farsi 2'
        WHEN mt5_users.Group LIKE '%PROF%' THEN 'Demo'
        WHEN mt5_users.Group LIKE '%treasury%' THEN 'Demo'
        ELSE 'English'
        END ELSE 'Demo' END) AS UNIT
        
        /* T  MT4_TRADES ?+ MT4_USERS */
        FROM mt5_deals LEFT JOIN mt5_users USING(Login)
        
        /* F  W */
        WHERE mt5_deals.Time BETWEEN '".$startTime."' AND '".$endTime."'
        
        /* O  G */
        GROUP BY DAY(mt5_deals.Time),UNIT";
    }
    
    $resultTotal2 = $DB_mt5->query($sqlTotal2);
    //-----------------------------------MT5Report----------------------------------------------//
    if($_SESSION["type"] == "Manager"){
        $sqlTotal3 = "SELECT
        /* C  DEPOSIT */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS DEPOSIT,
        
        /* C  ZEROING */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS ZEROING,
        
        /* C  WITHDRAWAL */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0, PROFIT,0.0)) AS WITHDRAWAL,
        
        /* C  NET_DW */ 
        (SUM(IF(MT4_TRADES.COMMENT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) +
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0, PROFIT,0.0))) AS NET_DW,
        
        /* C  BONUS_IN */ 
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit',
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS BONUS_IN,
        
        /* C  BONUS_OUT */
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0 AND CMD = 6 , PROFIT,0.0)) AS BONUS_OUT,
        
        /* C  NET_Bonus */ 
        (SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit',
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0))+
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0 AND CMD = 6, PROFIT,0.0))) AS NET_BONUS,
        
        /* C  PROFIT */
        SUM(IF(CMD < 2, PROFIT, 0.0)) AS PROFIT,
        
        /* C  SWAPS */
        SUM(IF(CMD < 2, SWAPS, 0.0)) AS SWAPS,
        
        /* C  DAY(CLOSE_TIME) */
        DAY(CLOSE_TIME) AS DAY,
        
        (CASE
        WHEN MT4_USERS.GROUP LIKE 'KUVVAR%'  THEN 'Turkish'
        WHEN MT4_USERS.GROUP LIKE 'KUV2VAR%' THEN 'Turkish'
        WHEN MT4_USERS.GROUP LIKE 'KUV3VAR%' THEN 'STPL'
        WHEN MT4_USERS.GROUP LIKE 'KUVIA%' THEN 'Farsi'
        WHEN MT4_USERS.GROUP LIKE 'KUVIST%' THEN 'Arabic'
        ELSE 'English'
        END) AS UNIT
        
        /* T  MT4_TRADES ?+ MT4_USERS */
        FROM lidyapar_mt4.MT4_TRADES LEFT JOIN lidyapar_mt4.MT4_USERS USING(LOGIN)
        
        /* F  W */
        WHERE 
            MT4_TRADES.CLOSE_TIME BETWEEN '".$startTime."' AND '".$endTime."'
            AND MT4_USERS.AGENT_ACCOUNT != '".$fake."'
            AND MT4_USERS.GROUP IN (\"".$array."\")
        
        /* O  G */
        GROUP BY DAY(CLOSE_TIME),UNIT
        UNION ALL
        SELECT
        /* C  DEPOSIT */ 
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) AS DEPOSIT,
        
        /* C  ZEROING */ 
        SUM(IF(mt5_deals.Comment IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) AS ZEROING,
        
        /* C  WITHDRAWAL */ 
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit < 0 AND Action = 2, Profit,0.0)) AS WITHDRAWAL,
        
        /* C  NET_DW */ 
        (SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) +
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit < 0 AND Action = 2, Profit,0.0))) AS NET_DW,
        
        /* C  BONUS_IN */ 
        SUM(IF(Profit > 0 AND Action = '6', Profit,0.0)) AS BONUS_IN,
        
        /* C  BONUS_OUT */
        SUM(IF(Profit < 0 AND Action = '6', Profit,0.0)) AS BONUS_OUT,
        
        /* C  NET_Bonus */ 
        (SUM(IF(Profit > 0 AND Action = '6', Profit,0.0))+
        SUM(IF(Profit < 0 AND Action = '6', Profit,0.0))) AS NET_BONUS,
        
        /* C  Profit */
        SUM(IF(Action < 2, Profit, 0.0)) AS PROFIT,
        
        /* C  Storage */
        SUM(IF(Action < 2, Storage, 0.0)) AS SWAPS,
        
        /* C  DAY(CLOSE_TIME) */
        DAY(mt5_deals.Time) AS DAY,
        
        (CASE WHEN mt5_users.Group LIKE 'real%4%' THEN 
         CASE 
        WHEN mt5_users.Group LIKE '%TUR4%'  THEN 'Turkish'
        WHEN mt5_users.Group LIKE '%STPL%' THEN 'STPL'
        WHEN mt5_users.Group LIKE '%PERS%' THEN 'Farsi'
        WHEN mt5_users.Group LIKE '%ARAB%' THEN 'Arabic'
        WHEN mt5_users.Group LIKE '%PER2%' THEN 'Farsi 2'
        WHEN mt5_users.Group LIKE '%PROF%' THEN 'Demo'
        WHEN mt5_users.Group LIKE '%treasury%' THEN 'Demo'
        ELSE 'English'
        END ELSE 'Demo' END) AS UNIT
        
        /* T  MT4_TRADES ?+ MT4_USERS */
        FROM lidyapar_mt5.mt5_deals LEFT JOIN lidyapar_mt5.mt5_users USING(Login)
        
        /* F  W */
        WHERE 
            mt5_deals.Time BETWEEN '".$startTime."' AND '".$endTime."'
            AND mt5_users.Group LIKE 'real\\\\\\\\".$g."%'
        
        /* O  G */
        GROUP BY DAY(mt5_deals.Time),UNIT";
    } else if ($_SESSION["type"] == "Admin" || $_SESSION["type"] == "Lawyer") {
        $sqlTotal3 = "SELECT
        /* C  DEPOSIT */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS DEPOSIT,
        
        /* C  ZEROING */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS ZEROING,
        
        /* C  WITHDRAWAL */ 
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0, PROFIT,0.0)) AS WITHDRAWAL,
        
        /* C  NET_DW */ 
        (SUM(IF(MT4_TRADES.COMMENT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) +
        SUM(IF(MT4_TRADES.COMMENT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0, PROFIT,0.0))) AS NET_DW,
        
        /* C  BONUS_IN */ 
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit',
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0)) AS BONUS_IN,
        
        /* C  BONUS_OUT */
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0 AND CMD = 6 , PROFIT,0.0)) AS BONUS_OUT,
        
        /* C  NET_Bonus */ 
        (SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Deposit Wire Transfer',
        'Deposit Credit Card',
        'Deposit',
        'zeroutbonus',
        'zerooutbonus',
        'Zerout'
        ) AND PROFIT > 0 AND CMD = 6, PROFIT,0.0))+
        SUM(IF(MT4_TRADES.COMMENT NOT IN(
        'Withdrawal Wire Transfer',
        'Withdrawal Credit Card',
        'Withdrawal',
        'Account Transfer'
        ) AND PROFIT < 0 AND CMD = 6, PROFIT,0.0))) AS NET_BONUS,
        
        /* C  PROFIT */
        SUM(IF(CMD < 2, PROFIT, 0.0)) AS PROFIT,
        
        /* C  SWAPS */
        SUM(IF(CMD < 2, SWAPS, 0.0)) AS SWAPS,
        
        /* C  DAY(CLOSE_TIME) */
        DAY(CLOSE_TIME) AS DAY,
        
        (CASE
        WHEN MT4_USERS.GROUP LIKE 'KUVVAR%'  THEN 'Turkish'
        WHEN MT4_USERS.GROUP LIKE 'KUV2VAR%' THEN 'Turkish'
        WHEN MT4_USERS.GROUP LIKE 'KUV3VAR%' THEN 'STPL'
        WHEN MT4_USERS.GROUP LIKE 'KUVIA%' THEN 'Farsi'
        WHEN MT4_USERS.GROUP LIKE 'KUVIST%' THEN 'Arabic'
        ELSE 'English'
        END) AS UNIT
        
        /* T  MT4_TRADES ?+ MT4_USERS */
        FROM lidyapar_mt4.MT4_TRADES LEFT JOIN lidyapar_mt4.MT4_USERS USING(LOGIN)
        
        /* F  W */
        WHERE MT4_TRADES.CLOSE_TIME BETWEEN '".$startTime."' AND '".$endTime."'
        AND MT4_USERS.AGENT_ACCOUNT != '".$fake."'
        
        /* O  G */
        GROUP BY DAY(CLOSE_TIME),UNIT
        UNION ALL
        SELECT
        /* C  DEPOSIT */ 
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) AS DEPOSIT,
        
        /* C  ZEROING */ 
        SUM(IF(mt5_deals.Comment IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) AS ZEROING,
        
        /* C  WITHDRAWAL */ 
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit < 0 AND Action = 2, Profit,0.0)) AS WITHDRAWAL,
        
        /* C  NET_DW */ 
        (SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit > 0 AND Action = 2, Profit,0.0)) +
        SUM(IF(mt5_deals.Comment NOT IN(
        'Zeroing'
        ) AND Profit < 0 AND Action = 2, Profit,0.0))) AS NET_DW,
        
        /* C  BONUS_IN */ 
        SUM(IF(Profit > 0 AND Action = '6', Profit,0.0)) AS BONUS_IN,
        
        /* C  BONUS_OUT */
        SUM(IF(Profit < 0 AND Action = '6', Profit,0.0)) AS BONUS_OUT,
        
        /* C  NET_Bonus */ 
        (SUM(IF(Profit > 0 AND Action = '6', Profit,0.0))+
        SUM(IF(Profit < 0 AND Action = '6', Profit,0.0))) AS NET_BONUS,
        
        /* C  Profit */
        SUM(IF(Action < 2, Profit, 0.0)) AS PROFIT,
        
        /* C  Storage */
        SUM(IF(Action < 2, Storage, 0.0)) AS SWAPS,
        
        /* C  DAY(CLOSE_TIME) */
        DAY(mt5_deals.Time) AS DAY,
        
        (CASE WHEN mt5_users.Group LIKE 'real%4%' THEN 
         CASE 
        WHEN mt5_users.Group LIKE '%TUR4%' THEN 'Turkish'
        WHEN mt5_users.Group LIKE '%STPL%' THEN 'STPL'
        WHEN mt5_users.Group LIKE '%PERS%' THEN 'Farsi'
        WHEN mt5_users.Group LIKE '%ARAB%' THEN 'Arabic'
        WHEN mt5_users.Group LIKE '%PER2%' THEN 'Farsi 2'
        WHEN mt5_users.Group LIKE '%PROF%' THEN 'Demo'
        WHEN mt5_users.Group LIKE '%treasury%' THEN 'Demo'
        ELSE 'English'
        END ELSE 'Demo' END) AS UNIT
        
        /* T  MT4_TRADES ?+ MT4_USERS */
        FROM lidyapar_mt5.mt5_deals LEFT JOIN lidyapar_mt5.mt5_users USING(Login)
        
        /* F  W */
        WHERE mt5_deals.Time BETWEEN '".$startTime."' AND '".$endTime."'
        
        /* O  G */
        GROUP BY DAY(mt5_deals.Time),UNIT";
    }
    $resultTotal3 = $DB_mt4->query($sqlTotal3);
    //echo $sqlTotal2;
    
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
                                Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to <?php echo Broker['title']; ?>
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
                                                <th>DAY</th>
                    							<th>Deposit</th>
                                                <th>Withdrawal</th>
                                                <th>Net DW</th>
                                                <th>Bonus In</th>
                    							<th>Bonus Out</th>
                    							<th>Net Bonus</th>
                    							<th>Swap</th>
                    							<th>PL</th>
                    							<th>Zeroing</th>
                    							<th>Total PL</th>
                                            </tr>
                    					</thead>
                    					<tbody>
                    					    <?php
                                                if($resultTotal3) while ($rowTotal = mysqli_fetch_array($resultTotal3)) {
                                                    if($rowTotal['UNIT'] != "Demo"){
                                            ?>
                                                <tr>
                                                    <td><?php echo $rowTotal['UNIT']; ?></td>
                                                    <td><?php echo $rowTotal['DAY']; ?></td>
                                                    <td><?php echo number_format($rowTotal['DEPOSIT'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowTotal['WITHDRAWAL'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowTotal['NET_DW'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowTotal['BONUS_IN'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowTotal['BONUS_OUT'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowTotal['NET_BONUS'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowTotal['SWAPS'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowTotal['PROFIT'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowTotal['ZEROING'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format(($rowTotal['PROFIT']+$rowTotal['SWAPS']+$rowTotal['NET_BONUS'])+$rowTotal['ZEROING'], 2, '.', ''); ?></td>
                                                </tr>
                                            <?php
                                                } }
                                            ?>
                    					</tbody>
                    					<tfoot>
                                            <tr>
                                                <th colspan="12"></th>
                                            </tr>
                                        </tfoot>
                    				</table>
                                </div>
                                <div class="tab-pane fade" id="pills-mt4" role="tabpanel" aria-labelledby="pills-2-tab">
                                    <table id="data-table-mt4" class="table table-hover" style="width: 100%;">  
                                        <thead>  
                                            <tr>
                                                <th>Unit</th>
                                                <th>DAY</th>
                    							<th>Deposit</th>
                                                <th>Withdrawal</th>
                                                <th>Net DW</th>
                                                <th>Bonus In</th>
                    							<th>Bonus Out</th>
                    							<th>Net Bonus</th>
                    							<th>Swap</th>
                    							<th>PL</th>
                    							<th>Zeroing</th>
                    							<th>Total PL</th>
                                            </tr>
                    					</thead>
                    					<tbody>
                    					    <?php
                                                if($resultTotal) while ($rowMT4 = mysqli_fetch_array($resultTotal)) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $rowMT4['UNIT']; ?></td>
                                                    <td><?php echo $rowMT4['DAY']; ?></td>
                                                    <td><?php echo number_format($rowMT4['DEPOSIT'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT4['WITHDRAWAL'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT4['NET_DW'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT4['BONUS_IN'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT4['BONUS_OUT'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT4['NET_BONUS'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT4['SWAPS'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT4['PROFIT'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT4['ZEROING'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format(($rowMT4['PROFIT']+$rowMT4['SWAPS']+$rowMT4['NET_BONUS'])+$rowMT4['ZEROING'], 2, '.', ''); ?></td>
                                                </tr>
                                            <?php
                                                }
                                            ?>
                    					</tbody>
                    					<tfoot>
                                            <tr>
                                                <th colspan="12"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="pills-mt5" role="tabpanel" aria-labelledby="pills-3-tab">
                                    <table id="data-table-mt5" class="table table-hover" style="width: 100%;">  
                                        <thead>  
                                            <tr>
                                                <th>Unit</th>
                                                <th>DAY</th>
                    							<th>Deposit</th>
                                                <th>Withdrawal</th>
                                                <th>Net DW</th>
                                                <th>Bonus In</th>
                    							<th>Bonus Out</th>
                    							<th>Net Bonus</th>
                    							<th>Swap</th>
                    							<th>PL</th>
                    							<th>Zeroing</th>
                    							<th>Total PL</th>
                                            </tr>
                    					</thead>
                    					<tbody>
                    					    <?php
                                                if($resultTotal2) while ($rowMT5 = mysqli_fetch_array($resultTotal2)) {
                                                    if($rowMT5['UNIT'] != "Demo"){
                                            ?>
                                                <tr>
                                                    <td><?php echo $rowMT5['UNIT']; ?></td>
                                                    <td><?php echo $rowMT5['DAY']; ?></td>
                                                    <td><?php echo number_format($rowMT5['DEPOSIT'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT5['WITHDRAWAL'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT5['NET_DW'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT5['BONUS_IN'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT5['BONUS_OUT'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT5['NET_BONUS'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT5['SWAPS'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT5['PROFIT'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format($rowMT5['ZEROING'], 2, '.', ''); ?></td>
                                                    <td><?php echo number_format(($rowMT5['PROFIT']+$rowMT5['SWAPS']+$rowMT5['NET_BONUS'])+$rowMT5['ZEROING'], 2, '.', ''); ?></td>
                                                </tr>
                                            <?php
                                                } }
                                            ?>
                    					</tbody>
                    					<tfoot>
                                            <tr>
                                                <th colspan="12"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
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
    
    var groupColumn = 0;
    var mt4 = $('#data-table-mt4').DataTable({
        "columnDefs": [
            { "visible": false, "targets": groupColumn }
        ],
		"responsive": false,
		"deferRender": true,
		"lengthMenu": [ [-1], ["All"] ],
		"order": [[ groupColumn, 'asc' ]],
		rowGroup: {
            //startRender: null,
            startRender: function ( rows, group ) {
                var deposit = rows
                    .data()
                    .pluck(2)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    deposit = $.fn.dataTable.render.number( ',', '.', 2).display( deposit );
                
                
                var withdrawal = rows
                    .data()
                    .pluck(3)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    withdrawal = $.fn.dataTable.render.number( ',', '.', 2).display( withdrawal );
                    
                var netdw = rows
                    .data()
                    .pluck(4)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    netdw = $.fn.dataTable.render.number( ',', '.', 2).display( netdw );
                    
                var bonusin = rows
                    .data()
                    .pluck(5)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    bonusin = $.fn.dataTable.render.number( ',', '.', 2).display( bonusin );
                    
                var bonusout = rows
                    .data()
                    .pluck(6)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    bonusout = $.fn.dataTable.render.number( ',', '.', 2).display( bonusout );
                    
                var netbonus = rows
                    .data()
                    .pluck(7)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    netbonus = $.fn.dataTable.render.number( ',', '.', 2).display( netbonus );
                    
                var swap = rows
                    .data()
                    .pluck(8)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    swap = $.fn.dataTable.render.number( ',', '.', 2).display( swap );
                    
                var pl = rows
                    .data()
                    .pluck(9)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    pl = $.fn.dataTable.render.number( ',', '.', 2).display( pl );
                    
                var zero = rows
                    .data()
                    .pluck(10)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    zero = $.fn.dataTable.render.number( ',', '.', 2).display( zero );
                    
                var tpl = rows
                    .data()
                    .pluck(11)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    tpl = $.fn.dataTable.render.number( ',', '.', 2).display( tpl );
                
 
                return $('<tr/>')
                    .append( '<td>'+group+'</td>' )
                    .append( '<td>'+deposit+'</td>' )
                    .append( '<td>'+withdrawal+'</td>' )
                    .append( '<td>'+netdw+'</td>' )
                    .append( '<td>'+bonusin+'</td>' )
                    .append( '<td>'+bonusout+'</td>' )
                    .append( '<td>'+netbonus+'</td>' )
                    .append( '<td>'+swap+'</td>' )
                    .append( '<td>'+pl+'</td>' )
                    .append( '<td>'+zero+'</td>' )
                    .append( '<td>'+tpl+'</td>' );
            },
            dataSrc: 0
        },
    	"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            var tdeposit = api
                .column( 2 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tdeposit = $.fn.dataTable.render.number( ',', '.', 2).display( tdeposit );
            
            
            var twithdrawal = api
                .column( 3 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                twithdrawal = $.fn.dataTable.render.number( ',', '.', 2).display( twithdrawal );
                
            var tnetdw = api
                .column( 4 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tnetdw = $.fn.dataTable.render.number( ',', '.', 2).display( tnetdw );
                
            var tbonusin = api
                .column( 5 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tbonusin = $.fn.dataTable.render.number( ',', '.', 2).display( tbonusin );
                
            var tbonusout = api
                .column( 6 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tbonusout = $.fn.dataTable.render.number( ',', '.', 2).display( tbonusout );
                
            var tnetbonus = api
                .column( 7 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tnetbonus = $.fn.dataTable.render.number( ',', '.', 2).display( tnetbonus );
                
            var tswap = api
                .column( 8 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tswap = $.fn.dataTable.render.number( ',', '.', 2).display( tswap );
                
            var tpl = api
                .column( 9 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tpl = $.fn.dataTable.render.number( ',', '.', 2).display( tpl );
                
            var ttpl = api
                .column( 11 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                ttpl = $.fn.dataTable.render.number( ',', '.', 2).display( ttpl );
            
 
            // Update footer
            $( api.column( 0 ).footer() ).html(
                '<div class="row text-center"><div class="col-md-2">Deposit: $'+tdeposit+'</div><div class="col-md-2">Withdrawal: $'+twithdrawal+'</div><div class="col-md-2">Net In/Out: $'+tnetdw+'</div><div class="col-md-2">Bonus In: $'+tbonusin+'</div><div class="col-md-2">Bonus Out: $'+tbonusout+'</div><div class="col-md-2">Bonus In/Out: $'+tnetbonus+'</div></div><hr><div class="row text-center"><div class="col-md-4">Swap: $'+tswap+'</div><div class="col-md-4">P&L: $'+tpl+'</div><div class="col-md-4"><?= $_L->T('Total_P_L','statistics') ?>: $'+ttpl+'</div></div>'
            );
        },
        "drawCallback": function( settings ) {
            $(this).find('[role=row]').slideToggle();
            $("#data-table-mt4 thead").find('[role=row]').slideToggle();
        },
        
    });
    
    $('#data-table-mt4 tbody').on( 'click', '.dtrg-level-0', function () {
        $(this).nextUntil('.dtrg-level-0').not('.dtrg-level-1').slideToggle();
    } );
    
    $('#data-table-mt4').on('search.dt', function (e, settings) {
        $(this).find('[role=row]').slideToggle();
        $("#data-table-mt4 thead").find('[role=row]').slideToggle();
    });
    
    
    
    var mt5 = $('#data-table-mt5').DataTable({
        "columnDefs": [
            { "visible": false, "targets": groupColumn }
        ],
		"responsive": false,
		"deferRender": true,
		"lengthMenu": [ [-1], ["All"] ],
		"order": [[ groupColumn, 'asc' ]],
		rowGroup: {
            //startRender: null,
            startRender: function ( rows, group ) {
                var deposit = rows
                    .data()
                    .pluck(2)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    deposit = $.fn.dataTable.render.number( ',', '.', 2).display( deposit );
                
                
                var withdrawal = rows
                    .data()
                    .pluck(3)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    withdrawal = $.fn.dataTable.render.number( ',', '.', 2).display( withdrawal );
                    
                var netdw = rows
                    .data()
                    .pluck(4)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    netdw = $.fn.dataTable.render.number( ',', '.', 2).display( netdw );
                    
                var bonusin = rows
                    .data()
                    .pluck(5)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    bonusin = $.fn.dataTable.render.number( ',', '.', 2).display( bonusin );
                    
                var bonusout = rows
                    .data()
                    .pluck(6)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    bonusout = $.fn.dataTable.render.number( ',', '.', 2).display( bonusout );
                    
                var netbonus = rows
                    .data()
                    .pluck(7)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    netbonus = $.fn.dataTable.render.number( ',', '.', 2).display( netbonus );
                    
                var swap = rows
                    .data()
                    .pluck(8)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    swap = $.fn.dataTable.render.number( ',', '.', 2).display( swap );
                    
                var pl = rows
                    .data()
                    .pluck(9)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    pl = $.fn.dataTable.render.number( ',', '.', 2).display( pl );
                
                var zero = rows
                    .data()
                    .pluck(10)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    zero = $.fn.dataTable.render.number( ',', '.', 2).display( zero );
                    
                var tpl = rows
                    .data()
                    .pluck(11)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    tpl = $.fn.dataTable.render.number( ',', '.', 2).display( tpl );
                
 
                return $('<tr/>')
                    .append( '<td>'+group+'</td>' )
                    .append( '<td>'+deposit+'</td>' )
                    .append( '<td>'+withdrawal+'</td>' )
                    .append( '<td>'+netdw+'</td>' )
                    .append( '<td>'+bonusin+'</td>' )
                    .append( '<td>'+bonusout+'</td>' )
                    .append( '<td>'+netbonus+'</td>' )
                    .append( '<td>'+swap+'</td>' )
                    .append( '<td>'+pl+'</td>' )
                    .append( '<td>'+zero+'</td>' )
                    .append( '<td>'+tpl+'</td>' );
            },
            dataSrc: 0
        },
    	"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            var tdeposit = api
                .column( 2 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tdeposit = $.fn.dataTable.render.number( ',', '.', 2).display( tdeposit );
            
            
            var twithdrawal = api
                .column( 3 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                twithdrawal = $.fn.dataTable.render.number( ',', '.', 2).display( twithdrawal );
                
            var tnetdw = api
                .column( 4 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tnetdw = $.fn.dataTable.render.number( ',', '.', 2).display( tnetdw );
                
            var tbonusin = api
                .column( 5 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tbonusin = $.fn.dataTable.render.number( ',', '.', 2).display( tbonusin );
                
            var tbonusout = api
                .column( 6 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tbonusout = $.fn.dataTable.render.number( ',', '.', 2).display( tbonusout );
                
            var tnetbonus = api
                .column( 7 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tnetbonus = $.fn.dataTable.render.number( ',', '.', 2).display( tnetbonus );
                
            var tswap = api
                .column( 8 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tswap = $.fn.dataTable.render.number( ',', '.', 2).display( tswap );
                
            var tpl = api
                .column( 9 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tpl = $.fn.dataTable.render.number( ',', '.', 2).display( tpl );
                
            var ttpl = api
                .column( 11 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                ttpl = $.fn.dataTable.render.number( ',', '.', 2).display( ttpl );
            
 
            // Update footer
            $( api.column( 0 ).footer() ).html(
                '<div class="row text-center"><div class="col-md-2">Deposit: $'+tdeposit+'</div><div class="col-md-2">Withdrawal: $'+twithdrawal+'</div><div class="col-md-2">Net In/Out: $'+tnetdw+'</div><div class="col-md-2">Bonus In: $'+tbonusin+'</div><div class="col-md-2">Bonus Out: $'+tbonusout+'</div><div class="col-md-2">Bonus In/Out: $'+tnetbonus+'</div></div><hr><div class="row text-center"><div class="col-md-4">Swap: $'+tswap+'</div><div class="col-md-4">P&L: $'+tpl+'</div><div class="col-md-4"><?= $_L->T('Total_P_L','statistics') ?>: $'+ttpl+'</div></div>'
            );
        },
        "drawCallback": function( settings ) {
            $(this).find('[role=row]').slideToggle();
            $("#data-table-mt5 thead").find('[role=row]').slideToggle();
        },
        
    });
    
    $('#data-table-mt5 tbody').on( 'click', '.dtrg-level-0', function () {
        $(this).nextUntil('.dtrg-level-0').not('.dtrg-level-1').slideToggle();
    } );
    
    $('#data-table-mt5').on('search.dt', function (e, settings) {
        $(this).find('[role=row]').slideToggle();
        $("#data-table-mt5 thead").find('[role=row]').slideToggle();
    });
    
    var general = $('#data-table-general').DataTable({
        "columnDefs": [
            { "visible": false, "targets": groupColumn }
        ],
		"responsive": true,
		"deferRender": true,
		"lengthMenu": [ [-1], ["All"] ],
		"order": [[ groupColumn, 'asc' ]],
		rowGroup: {
            //startRender: null,
            startRender: function ( rows, group ) {
                var deposit = rows
                    .data()
                    .pluck(2)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    deposit = $.fn.dataTable.render.number( ',', '.', 2).display( deposit );
                
                
                var withdrawal = rows
                    .data()
                    .pluck(3)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    withdrawal = $.fn.dataTable.render.number( ',', '.', 2).display( withdrawal );
                    
                var netdw = rows
                    .data()
                    .pluck(4)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    netdw = $.fn.dataTable.render.number( ',', '.', 2).display( netdw );
                    
                var bonusin = rows
                    .data()
                    .pluck(5)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    bonusin = $.fn.dataTable.render.number( ',', '.', 2).display( bonusin );
                    
                var bonusout = rows
                    .data()
                    .pluck(6)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    bonusout = $.fn.dataTable.render.number( ',', '.', 2).display( bonusout );
                    
                var netbonus = rows
                    .data()
                    .pluck(7)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    netbonus = $.fn.dataTable.render.number( ',', '.', 2).display( netbonus );
                    
                var swap = rows
                    .data()
                    .pluck(8)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    swap = $.fn.dataTable.render.number( ',', '.', 2).display( swap );
                    
                var pl = rows
                    .data()
                    .pluck(9)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    pl = $.fn.dataTable.render.number( ',', '.', 2).display( pl );
                    
                var zero = rows
                    .data()
                    .pluck(10)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    zero = $.fn.dataTable.render.number( ',', '.', 2).display( zero );
                    
                var tpl = rows
                    .data()
                    .pluck(11)
                    .reduce(function(a,b) { return a + b*1; }, 0);
                    tpl = $.fn.dataTable.render.number( ',', '.', 2).display( tpl );
                
 
                return $('<tr/>')
                    .append( '<td>'+group+'</td>' )
                    .append( '<td>'+deposit+'</td>' )
                    .append( '<td>'+withdrawal+'</td>' )
                    .append( '<td>'+netdw+'</td>' )
                    .append( '<td>'+bonusin+'</td>' )
                    .append( '<td>'+bonusout+'</td>' )
                    .append( '<td>'+netbonus+'</td>' )
                    .append( '<td>'+swap+'</td>' )
                    .append( '<td>'+pl+'</td>' )
                    .append( '<td>'+zero+'</td>' )
                    .append( '<td>'+tpl+'</td>' );
            },
            dataSrc: 0
        },
    	"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            var tdeposit = api
                .column( 2 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tdeposit = $.fn.dataTable.render.number( ',', '.', 2).display( tdeposit );
            
            
            var twithdrawal = api
                .column( 3 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                twithdrawal = $.fn.dataTable.render.number( ',', '.', 2).display( twithdrawal );
                
            var tnetdw = api
                .column( 4 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tnetdw = $.fn.dataTable.render.number( ',', '.', 2).display( tnetdw );
                
            var tbonusin = api
                .column( 5 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tbonusin = $.fn.dataTable.render.number( ',', '.', 2).display( tbonusin );
                
            var tbonusout = api
                .column( 6 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tbonusout = $.fn.dataTable.render.number( ',', '.', 2).display( tbonusout );
                
            var tnetbonus = api
                .column( 7 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tnetbonus = $.fn.dataTable.render.number( ',', '.', 2).display( tnetbonus );
                
            var tswap = api
                .column( 8 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tswap = $.fn.dataTable.render.number( ',', '.', 2).display( tswap );
                
            var tpl = api
                .column( 9 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                tpl = $.fn.dataTable.render.number( ',', '.', 2).display( tpl );
                
            var ttpl = api
                .column( 11 )
                .data()
                .reduce(function(a,b) { return a + b*1; }, 0);
                ttpl = $.fn.dataTable.render.number( ',', '.', 2).display( ttpl );
            
 
            // Update footer
            $( api.column( 0 ).footer() ).html(
                '<div class="row text-center"><div class="col-md-2">Deposit: $'+tdeposit+'</div><div class="col-md-2">Withdrawal: $'+twithdrawal+'</div><div class="col-md-2">Net In/Out: $'+tnetdw+'</div><div class="col-md-2">Bonus In: $'+tbonusin+'</div><div class="col-md-2">Bonus Out: $'+tbonusout+'</div><div class="col-md-2">Bonus In/Out: $'+tnetbonus+'</div></div><hr><div class="row text-center"><div class="col-md-4">Swap: $'+tswap+'</div><div class="col-md-4">P&L: $'+tpl+'</div><div class="col-md-4"><?= $_L->T('Total_P_L','statistics') ?>: $'+ttpl+'</div></div>'
            );
        },
        "drawCallback": function( settings ) {
            $(this).find('[role=row]').slideToggle();
            $("#data-table-general thead").find('[role=row]').slideToggle();
        },
        
    });
    
    $('#data-table-general tbody').on( 'click', '.dtrg-level-0', function () {
        $(this).nextUntil('.dtrg-level-0').not('.dtrg-level-1').slideToggle();
    } );
    
    $('#data-table-general').on('search.dt', function (e, settings) {
        $(this).find('[role=row]').slideToggle();
        $("#data-table-general thead").find('[role=row]').slideToggle();
    });
});
</script>

<?php include('includes/script-bottom.php'); ?>

</body>

</html>
<?php
    $DB_mt4->close();
?>
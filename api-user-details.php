<?php
######################################################################
#  M | 10:27 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

	$userId = $_POST['userId'];
	$startTime = $_POST['startTime'];
	$endTime = $_POST['endTime'];
	$groupName = $_POST['groupName'];
}


require('LxApi.php');

$lxApiOptions = array(
    'login'=>'lidyafxProd',
    'password'=>'lidG$#d33',
    'debug'=>true
);

$LxApi = new LxApi($lxApiOptions);

$request = array(
    'userID'=> $userId,
);

$request2 = array(
	'userID'=> $userId,
    'startTime'=> $startTime,
    'endTime'=> $endTime
);

$verifyUserResponse = $LxApi->VerifyUser($requestUser);

$loginUser = json_encode($verifyUserResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT);

$login = json_decode($loginUser);

$UserActivityData = $LxApi->DoesUserHaveAnyPositions($request2);
$userBalanceResponse = $LxApi->GetAccountBalance($request);
$userDepositResponse = $LxApi->GetAccountDepositsForUser($request2);
$getUserResponse = $LxApi->GetUser($request);
$closedPositionsForUserResponse = $LxApi->GetClosedPositionsForUser($request2);

$closedPositions = json_encode($closedPositionsForUserResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT);

$positions = json_decode($closedPositions);

$users = json_encode($getUserResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT);

$user = json_decode($users);

$balances = json_encode($userBalanceResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT);

$balance = json_decode($balances);

$deposits = json_encode($userDepositResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT);

$deposit = json_decode($deposits);

$cp = $positions->Data->GetClosedPositionsForUserResult->ClosedPosition;

$cpDP = $deposit->Data->GetAccountDepositsForUserResult->AccountDeposit;

$arrays = (array) $cp;

$arrayDP = (array) $cpDP;

$arrayWT = (array) $cpWT;

$pnl = 0;
$swap = 0;
$commission = 0;

$amountDP = 0;
$commentDP = 0;
$typeDP = 0;

$amountWT = 0;
$commentWT = 0;
$typeWT = 0;

for ($i = 0; $i < count($arrays); $i++) {
    $pnl += $arrays[$i]->ProfitInAccountCurrency;
    $swap += $arrays[$i]->RolloverInAccountCurrency;
    $commission += $arrays[$i]->CommissionInAccountCurrency;
    
}

for ($iDP = 0; $iDP < count($arrayDP); $iDP++) {
    $amount = $arrayDP[$iDP]->Amount;
    if($amount >= '0'){
        $amountDP += $arrayDP[$iDP]->Amount;
        $commentDP += $arrayDP[$iDP]->Comment;
        $typeDP += $arrayDP[$iDP]->Type;
    } else {
        $amountWT += $arrayDP[$iDP]->Amount;
        $commentWT += $arrayDP[$iDP]->Comment;
        $typeWT += $arrayDP[$iDP]->Type;
    
    }
    
}
?>
<form action="api-user-details.php" method= "POST">
    Account ID: <input type = "text" name = "userId" />
    Start Time: <input type = "text" name = "startTime" />
    End Time: <input type = "text" name = "endTime" />
    <input type = "submit" />
</form>
<?php
$equity = $balance->Data->GetAccountBalanceResult->Equity;
$marginUsed = $balance->Data->GetAccountBalanceResult->Margin;

$marginLevel = ($equity/$marginUsed) * 100;
$totalpl = $pnl+$swap+$commission;
$totalplG = $pnlG+$swapG+$commissionG;
echo 'Login: '.$user->Data->GetUserResult->UserID;
echo "</br>";
echo 'Full Name: '.$user->Data->GetUserResult->FullName;
echo "</br>";
echo 'Balance: '.$user->Data->GetUserResult->Balance;
echo "</br>";
echo 'Equity: '.number_format($equity, 2, '.', '');
echo "</br>";
echo 'Margin: '.number_format($marginUsed, 2, '.', '');
echo "</br>";
echo 'Margin Level: '.number_format($marginLevel, 2, '.', '');
echo "</br>";
echo 'Leverage: '.$user->Data->GetUserResult->Leverage;
echo "</br>";
echo 'Deposit: '.$amountDP;
echo "</br>";
echo 'Withdrawal: '.$amountWT;
echo "</br>";
echo $_L->T('Total_Swap','statistics').": ".number_format($swap, 2, '.', '').' '.$_L->T('Total_Commissions','statistics').": ".number_format($commission, 2, '.', '')." Total P/L: ".number_format($pnl, 2, '.', '')." Total P/L + Swaps and Commissions: ".number_format($totalpl, 2, '.', '');
?>
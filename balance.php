<?php
    require_once "config.php";
    $sqlCredit = 'SELECT sum(amount) as Total FROM payment_orders WHERE status = "1"';
    $Credit = $DB_admin->query($sqlCredit);
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <title>Balance</title>
  </head>
  <body>
     <!-- no additional media querie or css is required -->
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height:100vh">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php while ($rowCredit = mysqli_fetch_array($Credit)) {
                            $TRY = ($rowCredit['Total']) - (38331.97 + 89659.59 + 51510.96 + 87940 + 58580 + 113305 + 36499 + 64870 + 58100 + 81550 + 84661 + 145040.16 + 83790 + 139245 + 40116 + 43600);
                        ?>
                            <div><h5>Total Credit Card:</h5> <?= $TRY; ?> TRY</div>
                            <div><h5>Total Webmoney (346353150377):</h5> 1508.44 USD</div>
                            <div><h5>Total Webmoney (530473471195):</h5> 30.92 USD</div>
                            <div><h5>Total Bitcoin:</h5> 0.08037704 BTC</div>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
  </body>
</html>
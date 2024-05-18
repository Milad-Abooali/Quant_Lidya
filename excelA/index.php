<?php

    require_once "../config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    /**
     * Read CSV
     * @param $file
     * @return array
     */
    function getCSV ($file) {
        return array_map('str_getcsv', file($file));
    }

    /**
     * Read XLSX
     * @param $file
     * @return array|bool
     */
    function getXLSX ($file) {
        include 'simpleExlsx.php';
        $xlsx = @(new SimpleXLSX($file));
        return $xlsx->rows();
    }

?>
<!DOCTYPE html>
<html>
<head>
    <style>
        .column {
            max-height: 700px;
            overflow-y: scroll;
            border: 1px solid #a59f9f;
            float: left;
            width: 33.33%;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</head>
<body>
<?php if (!isset($_POST["submit"])): ?>
    <form action="index.php" method="post" enctype="multipart/form-data">
        Select "CSV" or "XLSX" file to upload:
        <br>
        <hr>
        <input type="file" name="file" id="file">
        <input type="submit" value="Upload" name="submit">
    </form>
<?php else: ?>
    <form method="get" action="./index.php">
        <button type="submit">Upload New File</button>
    </form><br>
    <span>Total:</span><strong id="trows" style="color:Blue;"></strong>
    | <span>Current Row:</span><strong id="crow" style="color:#B1139E;"></strong>
    | <span>Success:</span><strong id="count_s" style="color:green;"></strong>
    | <span>Error:</span><strong id="count_e" style="color:red;"></strong>
    | <span>Units:</span><strong id="count_units" style="color:orange;"></strong>
    <hr>
    <div id="result-1" class="column"></div>
    <div id="result-2" class="column"></div>
    <div id="result-3" class="column"></div>
<?php endif; ?>
</body>
</html>

<?php
    
    if (isset($_POST["submit"])) {
        $file=$error=false;
        $target_file = "./files/" . basename($_FILES["file"]["name"]);
        $FileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($_FILES["file"]["size"] > 500000000) $error = 'File Size';
        if ($FileType != "csv" && $FileType != "xlsx") $error = 'File Type must be "csv" or "xlsx"';
        if ($error) {
            echo "Sorry, your file was not uploaded. \n $error \n";
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $file = basename( $_FILES["file"]["name"]);
            } else {
                echo "Sorry, there was an error uploading your file! \n";
            }
        }
        $data=array();
        if ($file) {
            echo "<h5>Type is:  $FileType <hr></h5>";
            $data = (in_array($FileType, ['csv', 'CSV'])) ? getCSV($target_file) : getXLSX($target_file);
        } else {
            echo "Sorry, there was an error to loading your file! \n";
        }
        $header = array_shift($data);

        $rows = array();

        foreach ($data as $k => $row) {
            foreach ($row as $c => $v) $rows[$k][$header[$c]] = $v;
        }
        $total = 0;
        $chunkedRows = array();
        if ($rows) {
            $total = count($rows);
            $chunkLength = intdiv($total, 3) + 3;
            $chunkedRows = array_chunk($rows, $chunkLength);
        }
        ?>
<script>
    const chunks = [
        '',
        JSON.parse('<?= json_encode($chunkedRows[0]) ?>'),
        JSON.parse('<?= json_encode($chunkedRows[1]) ?>'),
        JSON.parse('<?= json_encode($chunkedRows[2]) ?>')
    ];
    const countRows = <?= ($total) ? $total : 0 ?>;
    $("#trows").html(countRows);
    let i = 0, count_s = 0, count_e = 0;
    var item_unit;
    var units = {};
    const url = '../lead_add.php';

    async function ajaxCallHere(url, data, callback) {
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            cache: false,
            global: false,
            async: false,
            success: callback,
            error: function (request, status, error) {
                console.log(status);
            }
        });
    }

    async function ajaxCall(callClass, callFunction, data = null, callback) {
        $.ajax({
            type: "POST",
            url: "../lib/ajax.php?c=" + callClass + '&f=' + callFunction + "&t=<?= TOKEN ?>",
            data: data,
            cache: false,
            global: false,
            async: true,
            success: callback,
            error: function (request, status, error) {
                console.log(error);
            }
        });
    }

    async function callNotify() {
        ajaxCall('notify', 'addLeads', units, function (response) {
            let resObj = JSON.parse(response);
            console.log(resObj.e);
        });
        return true;
    }

    function chunksLoop(chunkId) {
        let rows = chunks[chunkId];
        $.each(rows, function (index, item) {
            let text = '';
            if (item?.email?.length < 1) {
                item.email = item.phone + '@email.temp';
                text += `<br>Temp Email Generated: <strong>${item.email}</strong><br>`;
            }
            setTimeout(() => {
                ajaxCallHere(url, item, function (response) {
                    let color = '';
                    if (response != '{"statusCode":200}') {
                        count_e++
                        color = 'red';
                    } else {
                        count_s++;
                        i++;
                        color = 'blue';
                    }
                    text += `Part:${chunkId} Row:${index} | <span style="color: ${color}">Response:${response}</span>'`
                    $(`#result-${chunkId}`).prepend('<hr>' + text);
                    $("#count_e").html(count_e);
                    $("#count_s").html(count_s);
                    $("#count_units").html(JSON.stringify(units));
                    if (countRows === i) callNotify();
                    $("#crows").html(i);
                });
            }, 1 + i);
        });
    }

    (async () => {
        chunksLoop(1);
        chunksLoop(2);
        chunksLoop(3);
    })();

</script>

<?php } ?>



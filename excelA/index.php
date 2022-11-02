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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</head>
<body>
<?php if (!isset($_POST["submit"])): ?>
<form action="index.php" method="post" enctype="multipart/form-data">
    Select "CSV" or "XLSX" file to upload:
    <br><hr>
    <input type="file" name="file" id="file">
    <input type="submit" value="Upload" name="submit">
</form>
<?php else: ?>
- <a href="./index.php">Upload New File</a>
| <span>Total:</span><strong id="trows" style="color:Blue;"></strong>
| <span>Current Row:</span><strong id="crow" style="color:#B1139E;"></strong>
| <span>Success:</span><strong id="count_s" style="color:green;"></strong>
| <span>Error:</span><strong id="count_e" style="color:red;"></strong>
| <span>Units:</span><strong id="count_units" style="color:orange;"></strong>
<?php endif; ?>
<hr>
<div id="result" style="max-height: 700px;overflow-y: scroll;border: 1px solid #a59f9f;"></div>
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
        if ($file) $data = ($FileType == 'csv') ? getCSV($target_file) : getXLSX($target_file);
        $header = array_shift($data);
        $rows = array();

        foreach ($data as $k => $row) {
            foreach ($row as $c => $v) $rows[$k][$header[$c]] = $v;
        }
        ?>
<script>
    var item_unit;
    var units = {};
    // Ajax Call- Core
    function ajaxCall (callClass, callFunction, data=null, callback) {
      $.ajax({
        type: "POST",
        url: "../lib/ajax.php?c="+callClass+'&f='+callFunction+"&t=<?= TOKEN ?>",
        data: data,
        cache: false,
        global: false,
        async: true,
        success: callback,
        error: function(request, status, error) {
          console.log(error);
        }
      });
    }
    const o_rows = '<?= json_encode($rows) ?>';
    const url = '../lead_add.php';
    function ajaxCallHere (url, data, callback) {
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            cache: false,
            global: false,
            async: false,
            success: callback,
            error: function(request, status, error) {
                console.log(status);
            }
        });
    }
    let rows = JSON.parse(o_rows);
    var countRows = Object.keys(rows).length;
    $("#trows").html(countRows);
    let i = 0, count_s = 0, count_e = 0, text = null;
    $.each(rows, async function(index, item) {
        setTimeout(function() {
                $("#crow").html(i);
                console.log(item); 
                text = '<h4> ID . . . '+i+'</h4>'; 
                ajaxCallHere (url, item,function(response) {
                    const color = (response!='{"statusCode":200}') ? 'red' : 'blue';
                    (response!='{"statusCode":200}') ? count_e++ : count_s++;
                    if(response=='{"statusCode":200}') units[item.userunit] = (units[item.userunit]+1) || 1 ;
                    let res = 'Row: '+index+' | <span  style="color: '+color+'">Response:'+response+'</span>'
                    text += res;
                });
                $('#result').prepend('<hr><div>'+text+'</div>');
                i++;
                $("#count_e").html(count_e);
                $("#count_s").html(count_s);
                $("#count_units").html(JSON.stringify(units));
                if (countRows == i) callNotify();
        }, 1);
    });
     
    function callNotify() {
        ajaxCall ('notify', 'addLeads',units, function(response){
            let resObj = JSON.parse(response);
            console.log(resObj.e);
        });
        return true;
    }
  
</script>

<?php } ?>



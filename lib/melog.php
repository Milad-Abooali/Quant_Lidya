<?php

    $delete = $_GET['del'] ?? false;

?>


<style>
body{font-family:tahoma;margin:45px 15px;background-color: #000;color: #999;}
.bar a {color: #fff;}
.bar a.red {color: #f00;float:right;}
.bar {
    position: fixed;
    top: 0;
    background-color: #11315f;
    left: 0;
    right: 0;
    color: #777;
    padding: 5px;
}
</style>
<?php define("WEB_ROOT",substr(dirname(__FILE__),0,strlen(dirname(__FILE__))-3)); ?>
<body>
    <div style="overflow-y: scroll;height:45%">
        <h6>$ root</h6>
      <?php
          $root = WEB_ROOT.'error_log';
          $lines_root=array();
          if (file_exists($root)) {
            if($delete) {
                unlink ($root);
            } else {
                $lines_root = file($root, FILE_IGNORE_NEW_LINES);
                if ($lines_root) foreach($lines_root as $k=>$line) echo "<div id='$k'><small>$line</small></div>";
            }
          } else {
              echo "<small>No File</small>";
          }
      ?>
    </div>
    <hr>
    <div style="overflow-y: scroll;height:24%">
     <h6># root/lib</h6>
      <?php
          $lib = WEB_ROOT.'lib/error_log';
          $lines_lib=array();
          if (file_exists($lib)) {
            if($delete) {
                unlink ($lib);
            } else {
                $lines_lib = file($lib, FILE_IGNORE_NEW_LINES);
                if ($lines_lib) foreach($lines_lib as $k=>$line) echo "<div id='$k'><small>$line</small></div>";
            }
          } else {
              echo "<small>No File</small>";
          }
      ?>
    </div>
    <hr>
    <div style="overflow-y: scroll;height:24%">
     <h6># root/cron</h6>
      <?php
          $cron = WEB_ROOT.'cron/error_log';
          $lines_cron=array();
          if (file_exists($cron)) {
            if($delete) {
                unlink ($cron);
            } else {
                $lines_cron = file($cron, FILE_IGNORE_NEW_LINES);
                if ($lines_cron) foreach($lines_cron as $k=>$line) echo "<div id='$k'><small>$line</small></div>";
            }
          } else {
              echo "<small>No File</small>";
          }
      ?>
    </div>
  <div class="bar">
    root: <?= count($lines_root ?? array()) ?> |
    lib: <?= count($lines_lib ?? array()) ?> |
    cron: <?= count($lines_cron ?? array()) ?> |
    <a href="?laod=1" > Reload </a> |
    <a href="?del=1" class="red" > Delete ! </a>
  </div>
  
</body>
<script>
setTimeout(function() {
  location.reload();
}, 5000);
<?php if(($_GET['del']) ?? false) echo "window.location.href = 'melog.php';" ?>
</script>
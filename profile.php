<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

// LangMan
global $_L;

    include('includes/head.php'); ?>

        <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
        <link href="https://rawgit.com/enyo/dropzone/master/dist/dropzone.css" rel="stylesheet">

<?php include('includes/css.php'); ?>

    <body>

        <!-- Begin page -->
        <div id="wrapper">

<?php 
    include('includes/topbar.php');
    include('includes/sidebar.php');
    
    $sql = 'SELECT users.id, users.username, users.email, user_extra.fname, user_extra.lname, user_extra.phone, user_extra.retention, user_extra.status FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE user_extra.type = 8';
    $result = $DB_admin->query($sql);
?>
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
    		<div class="row">
    			<div class="col-lg-12">
    				<div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <h4 class="page-title"><?= $_L->T('Profile','profile') ?><button type="button" class="btn btn-primary btn-sm" id="edit_profile"><?= $_L->T('Edit_Profile','profile') ?></button><button type="button" class="btn btn-primary btn-sm" id="detail_profile" style="display:none;"><?= $_L->T('Edit_Details','profile') ?></button></h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">
                                        <?= $_L->T('Welcome_User_Broker','general', array(htmlspecialchars($_SESSION["username"]), Broker['title'])) ?>
                                    </li>
                                </ol>
                                <div class="state-information d-none d-sm-block">
                                    <div class="state-graph">
                                        <div id="header-chart-1"></div>
                                        <div class="info"><?= $_L->T('Leads','sidebar') ?> 1500</div>
                                    </div>
                                    <div class="state-graph">
                                        <div id="header-chart-2"></div>
                                        <div class="info"><?= $_L->T('Converted','general') ?> 40</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="card pmd-card" style="padding: 0;">
            					<div class="card-body" id="load-user-details" style="padding: 15px 0;">
            					</div>
                    		</div>
                		</div>
                		<div class="col-sm-4">
                    		<div class="card pmd-card">
            					<div class="card-body">
            					    <div class="row">
            					        <div class="col-md-4 text-center">
                					        <img src="media/<?php echo $avatars; ?>" alt="user" class="avatar_img rounded-circle" style="max-width: 100px !important;">
                					        <form method="post" action="" enctype="multipart/form-data" id="avatar">
                					            <label class="btn btn-primary btn-sm btn-file mt-2">
                                                    <i class="fa fa-upload" aria-hidden="true"></i> <?= $_L->T('Upload_Avatar','profile') ?>
                                                    <input type="file" id="avatarfile" name="avatarfile" style="display: none;">
                                                </label>
                					        </form>
                					    </div>
                					    <div class="col-md-8">
                					        <label><?= $_L->T('Your_Profile_Completion','profile') ?> %</label>
                					        <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: <?= GF::profileRate($_SESSION["id"]) ?>%;" aria-valuenow="<?= GF::profileRate($_SESSION["id"]) ?>" aria-valuemin="0" aria-valuemax="100"><?= GF::profileRate($_SESSION["id"]) ?>%</div>
                                            </div>
                                            </br>
                                            <small><?= $_L->T('Profile_Completion_note','profile') ?></small>
                					    </div>
                					    <hr style="width: 100%">
                					    <div class="col-md-12">
                					        <label><?= $_L->T('Upload_Valid_ID','profile') ?>:</label>
                					        <span><?= $_L->T('valid_ID_note','profile') ?></span>
                					        <ul>
                					            <li><?= $_L->T('Passport_Copy','profile') ?></li>
                					            <li><?= $_L->T('National_ID','profile') ?></li>
                					            <li><?= $_L->T('Driving_License','profile') ?></li>
                					        </ul>
                					        <div class="container2">
                					        <img id="idc_img" src="media/<?php echo $id; ?>" alt="user" class="rounded-circle" style="width:80px;height:80px;float: right;margin: -100px -5px 0px 0;">
                    					        <div class="overlay <?php if($id_verify == "1"){ echo "verify"; } ?>">
                                                    <a href="#" class="icon" title="<?= $_L->T('User_Profile','profile') ?>">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                </div>
                                            </div>
                					        <form method="post" action="" enctype="multipart/form-data" id="id">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="idfile" name="idfile">
                                                    <label class="custom-file-label" for="customFile"><?= $_L->T('Choose_your_ID','profile') ?></label>
                                                </div>
                                            </form>
                					    </div>
                					    <hr style="width: 100%">
                					    <div class="col-md-12">
                					        <label><?= $_L->T('Upload_Valid_Residence','profile') ?>:</label>
                					        <span><?= $_L->T('valid_Residence_note','profile') ?></span>
                					        <ul>
                					            <li><?= $_L->T('Electricity_Bill','profile') ?></li>
                					            <li><?= $_L->T('Gas_Bill','profile') ?></li>
                					            <li><?= $_L->T('Water_Bill','profile') ?></li>
                					            <li><?= $_L->T('Mobile_Bill','profile') ?></li>
                					        </ul>
                					        <div class="container2">
                					        <img id="poa_img" src="media/<?php echo $poa; ?>" alt="user" class="rounded-circle" style="width:80px;height:80px;float: right;margin: -100px -5px 0px 0;">
                    					        <div class="overlay <?php if($poa_verify == "1"){ echo "verify"; } ?>">
                                                    <a href="#" class="icon" title="User Profile">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                </div>
                                            </div>
                					        <form method="post" action="" enctype="multipart/form-data" id="poa">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="poafile" name="poafile">
                                                    <label class="custom-file-label" for="customFile"><?= $_L->T('Choose_your_Bill','profile') ?></label>
                                                </div>
                                            </form>
                					    </div>
                                        <?php if($_SESSION['type']==='IB') { ?>
                                        <hr style="width: 100%">
                                        <div class="col-md-12">
                                            <h5>IB Contract</h5>
                                            <p><button data-dismiss="modal" data-uid="<?= $_SESSION['id'] ?>" type="button" class="doM-contract btn btn-sm btn-info"><?= $_L->T('Show_My_Contracts','profile') ?></button></p>
                                        </div>
                                        <?php } ?>
            					    </div>
            					</div>
                    		</div>
                		</div>
                	</div>
    			</div>
    		</div>
        </div>
        <?php include('includes/footer.php'); ?>
    </div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->
</div>
<!-- END wrapper -->

<?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script src="https://rawgit.com/enyo/dropzone/master/dist/dropzone.js"></script>
        <script>
        $(document).ready( function () {

            /*  Show Contracts Modal */
            $("body").on("click",'.doM-contract', function() {
                const id = $(this).data('uid');
                ajaxCall ('ib', 'myContracts', {id:id}, function(response) {
                    makeModal('Contracts', response, 'xl');
                });
            });


            $(".custom-file-input").on("change", function() {
                var fileName = $(this).val().split("\\").pop();
              $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            });
            var pval = '<?php echo htmlspecialchars($_SESSION["id"]); ?>';
            var pvaluser = '<?php echo htmlspecialchars($_SESSION["username"]); ?>';
            <?php if($_SESSION["type"] !== "IB"){ ?>
            let url = "user-details.php?code="+pval;
            <?php } else { ?>
            let url = "user-details.php?code="+pval+"&type=profile";
            <?php } ?>
            $('#load-user-details').load(url,function(result){});
        	$(function () {
        		$('[data-toggle="tooltip"]').tooltip()
        	})

            $("#detail_profile").on('click', function(){
                var pval = '<?php echo htmlspecialchars($_SESSION["id"]); ?>';
                var pvaluser = '<?php echo htmlspecialchars($_SESSION["username"]); ?>';
                <?php if($_SESSION["type"] !== "IB"){ ?>
                var url = "user-details.php?code="+pval;
                <?php } else { ?>
                var url = "user-details.php?code="+pval+"&type=profile";
                <?php } ?>
                $('#load-user-details').load(url,function(result){})
                $("#edit_profile").show();
                $("#detail_profile").hide();
            });
            $("#edit_profile").on('click', function(){
                var pval2 = '<?php echo htmlspecialchars($_SESSION["id"]); ?>';
                var pvaluser2 = '<?php echo htmlspecialchars($_SESSION["username"]); ?>'; 
                var url = "user-edit.php?code="+pval2;
                $('#load-user-details').load(url,function(result){$("#load-user-details").append( $( '<div class="col-md-12"><button type="button" class="btn btn-primary" id="saveUser" disabled><?= $_L->T('Save','general') ?></button><div>' ) );});
                $("#edit_profile").hide();
                $("#detail_profile").show();
            });
            
            $("#idfile").change(function(){
                var fd = new FormData();
                var files = $('#idfile')[0].files[0];
                fd.append('file',files);
                fd.append('cat', 'ID');
        
                $.ajax({
                    url: 'upload.php',
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if(response == 1){
                            $('#idc_img').attr('src','media/'+files.name)
                            alert('success');
                        } else {
                            alert('file not uploaded');
                        }
                    },
                });
            });

            $("#poafile").change(function(){
                var fd = new FormData();
                var files = $('#poafile')[0].files[0];
                fd.append('file',files);
                fd.append('cat', 'Bill');
        
                $.ajax({
                    url: 'upload.php',
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if(response != 0){
                            $('#poa_img').attr('src','media/'+files.name)
                            alert('success');
                        }else{
                            alert('file not uploaded');
                        }
                    },
                });
            });
            
            $("#avatarfile").change(function(){
                var fd = new FormData();
                var files = $('#avatarfile')[0].files[0];
                fd.append('file',files);
                fd.append('cat', 'avatar');
        
                $.ajax({
                    url: 'upload.php',
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if(response != 0){
                            $('.avatar_img').attr('src','media/'+files.name)
                            alert('success');
                        }else{
                            alert('file not uploaded');
                        }
                    },
                });
            });

        } );
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>

</html>
<?php
    $DB_admin->close();
?>
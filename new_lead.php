<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    $userID = intval($_GET['code']);    
    $page = $_GET['page'];
    
?>
 
<!DOCTYPE html>
<html lang="en">
<body>
    <!-- Page Content -->
    <div class="container">
		<div class="row">
			<div class="col-lg-12">
				<?php
					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Sales Agent"){
				?>
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-1-tab" data-toggle="pill" href="#pills-1" role="tab" aria-controls="pills-1" aria-selected="true">General Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-2-tab" data-toggle="pill" href="#pills-2" role="tab" aria-controls="pills-2" aria-selected="false">Past Experiences</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-3-tab" data-toggle="pill" href="#pills-3" role="tab" aria-controls="pills-3" aria-selected="false">Marketing</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div id="notifications">
                            <div class="alert alert-danger alert-dismissible" id="delete" style="display:none;">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            </div>
                        </div>
                        <div class="tab-pane fade show active" id="pills-1" role="tabpanel" aria-labelledby="pills-1-tab">
                            <form class="was-validated" novalidate>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="fname">First Name:</label>
                                        <input type="text" class="form-control form-control-sm" name="fname" id="fname" placeholder="First Name" required>
                                        <div class="invalid-feedback">
                                            Please enter a valid First Name.
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="lname">Last Name:</label>
                                        <input type="text" class="form-control form-control-sm" name="lname" id="lname" placeholder="Last Name" required>
                                        <div class="invalid-feedback">
                                            Please enter a valid Last Name.
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="email">Email:</label>
                                        <input type="email" class="form-control form-control-sm" name="email" id="email" placeholder="example@gmail.com" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required>
                                        <div class="invalid-feedback">
                                            Please enter a valid Email Address.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="phone">Phone:</label>
                                        <input type="number" class="form-control form-control-sm" name="phone" id="phone" placeholder="00123456789" required />
                                    </div>
                                    <div class="col-md-4 mb-3">    
                                        <label for="country">Country:</label>
                                        <select class="custom-select custom-select-sm" name="country" id="country" placeholder="Country of Residence">
                                        <?php
                                            $sqlCU = 'SELECT country_name FROM countries';
                                            $countries = $DB_admin->query($sqlCU);
                                            while ($rowCU = mysqli_fetch_array($countries)) {
                                                echo "<option value='".$rowCU['country_name']."'>".$rowCU['country_name']."</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="city">City:</label>
                                        <input type="text" class="form-control form-control-sm" name="city" id="city" placeholder="City" />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="address">Address:</label>
                                        <input type="text" class="form-control form-control-sm" name="address" id="address" placeholder="1 No., 26 St., 1234 Florida, United State of America" />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="interests">Interests:</label>
                                        <input type="text" class="form-control form-control-sm" name="interests" id="interests" placeholder="Finanance, Sports, Cars, etc." />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="hobbies">Hobbies:</label>
                                        <input type="text" class="form-control form-control-sm" name="hobbies" id="hobbies" placeholder="Playing Golf, Investing, etc." />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="unit">Business Unit:</label>
                                        <select class="custom-select custom-select-sm" name="userunit" id="userunit" placeholder="Business Unit">
                                        <?php
                        					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager"){
                                                $sqlUNIT = 'SELECT id, name FROM units';
                                                $units = $DB_admin->query($sqlUNIT);
                                                while ($rowUNIT = mysqli_fetch_array($units)) {
                                                    echo "<option value='".$rowUNIT['id']."'>".$rowUNIT['name']."</option>";
                                                }
                        					} else {
                        					    echo '<option value='.$_SESSION["unitn"].'>'.$_SESSION["unit"].'</option>';
                        					}
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <?php if($page != "staff"){ ?>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="retention">Retention:</label>
                                        <select class="custom-select custom-select-sm" name="retention" id="retention" placeholder="Retention Agent">
                                        <?php
                                            $sqlUSERS = 'SELECT id, username FROM users WHERE unit = "'.$_SESSION["unit"].'" AND type = "Retention Agent"';
                                            $users = $DB_admin->query($sqlUSERS);
                                            while ($rowUSERS = mysqli_fetch_array($users)) {
                                                echo "<option value='".$rowUSERS['id']."'>".$rowUSERS['username']."</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="conversion">Conversion:</label>
                                        <select class="custom-select custom-select-sm" name="conversion" id="conversion"  placeholder="Sales Agent">
                                        <?php
                                            if($_SESSION["type"] == "Sales Agent") {
                                                $sqlUSERS = 'SELECT id, username FROM users WHERE unit = "'.$_SESSION["unit"].'" AND id = "'.$_SESSION["id"].'" AND type IN ("Sales Agent", "Manager")';
                                                $users = $DB_admin->query($sqlUSERS);
                                                while ($rowUSERS = mysqli_fetch_array($users)) {
                                                    echo "<option value='".$rowUSERS['id']."'>".$rowUSERS['username']."</option>";
                                                }
                                            } else {
                                                $sqlUSERS = 'SELECT id, username FROM users WHERE unit = "'.$_SESSION["unit"].'" AND type IN ("Sales Agent", "Manager")';
                                                $users = $DB_admin->query($sqlUSERS);
                                                while ($rowUSERS = mysqli_fetch_array($users)) {
                                                    echo "<option value='".$rowUSERS['id']."'>".$rowUSERS['username']."</option>";
                                                }
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="status">Status:</label>
                                        <select class="custom-select custom-select-sm" name="status" id="status" placeholder="Status">
                                        <?php
                                            $sqlSTATUS = 'SELECT id, status FROM status WHERE cat = "Leads" ORDER BY id';
                                            $status = $DB_admin->query($sqlSTATUS);
                                            while ($rowSTATUS = mysqli_fetch_array($status)) {
                                                echo "<option value='".$rowSTATUS['id']."'>".$rowSTATUS['status']."</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="type">Type:</label>
                                        <select class="custom-select custom-select-sm" name="type" id="type" placeholder="Type">
                                        <?php
                                            if($page == "staff"){
                                                $sqlTYPE = 'SELECT id, name FROM type WHERE name NOT IN ("IB", "Trader", "Leads", "Admin") ORDER BY id';
                                                $types = $DB_admin->query($sqlTYPE);
                                                while ($rowTYPE = mysqli_fetch_array($types)) {
                                                    echo "<option value='".$rowTYPE['id']."'>".$rowTYPE['name']."</option>";
                                                }
                                            } else if($_SESSION["type"] == "Sales Agent") {
                                                $sqlTYPE = 'SELECT id, name FROM type WHERE id < 4 ORDER BY id';
                                                $types = $DB_admin->query($sqlTYPE);
                                                while ($rowTYPE = mysqli_fetch_array($types)) {
                                                    echo "<option value='".$rowTYPE['id']."'>".$rowTYPE['name']."</option>";
                                                }
                                            } else {
                                                $sqlTYPE = 'SELECT id, name FROM type ORDER BY id';
                                                $types = $DB_admin->query($sqlTYPE);
                                                while ($rowTYPE = mysqli_fetch_array($types)) {
                                                    echo "<option value='".$rowTYPE['id']."'>".$rowTYPE['name']."</option>";
                                                }
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="followup">Follow-Up:</label>
                                        <input type="text" class="form-control form-control-sm" name="followup" id="followup" placeholder="Last Follow-Up Date" />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="ip">IP:</label>
                                        <input type="text" class="form-control-plaintext form-control-sm" name="ip" id="ip" disabled/>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="pills-2" role="tabpanel" aria-labelledby="pills-2-tab">
                            <form class="was-validated" novalidate>
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="job_cat">Job Category:</label>
                                        <select class="custom-select custom-select-sm" name="job_cat" id="job_cat" placeholder="Job Category">
                                        <?php
                                            $sqlJOBC = 'SELECT id, name FROM job_category';
                                            $job_cat = $DB_admin->query($sqlJOBC);
                                            while ($rowJOBC = mysqli_fetch_array($job_cat)) {
                                                echo "<option value='".$rowJOBC['id']."'>".$rowJOBC['name']."</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="job_title">Job Title:</label>
                                        <input type="text" class="form-control form-control-sm" name="job_title" id="job_title" placeholder="Job Title, Exampl: CEO, CTO, etc." />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-3 mb-3">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input custom-control-sm" name="exp_fx" id="exp_fx" placeholder="Experience in FX" />
                                            <label class="custom-control-label" for="exp_fx">Experience in FX</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select class="custom-select custom-select-sm" name="exp_fx_year" id="exp_fx_year" placeholder="Years of Experience in FX">
                                        <?php
                                            $sqlEXYEAR = 'SELECT id, name FROM experience';
                                            $exyear = $DB_admin->query($sqlEXYEAR);
                                            while ($rowEXYEAR = mysqli_fetch_array($exyear)) {
                                                echo "<option value='".$rowEXYEAR['id']."'>".$rowEXYEAR['name']."</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input custom-control-sm" name="exp_cfd" id="exp_cfd" placeholder="Experience in CFD" />
                                            <label class="custom-control-label" for="exp_cfd">Experience in CFD</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select class="custom-select custom-select-sm" name="exp_cfd_year" id="exp_cfd_year" placeholder="Years of Experience in CFD">
                                        <?php
                                            $sqlEXYEAR1 = 'SELECT id, name FROM experience';
                                            $exyear1 = $DB_admin->query($sqlEXYEAR1);
                                            while ($rowEXYEAR1 = mysqli_fetch_array($exyear1)) {
                                                echo "<option value='".$rowEXYEAR1['id']."'>".$rowEXYEAR1['name']."</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="income">Income:</label>
                                        <select class="custom-select custom-select-sm" name="income" id="income" placeholder="Income">
                                        <?php
                                            $sqlINCOME = 'SELECT id, name FROM income';
                                            $income = $DB_admin->query($sqlINCOME);
                                            while ($rowINCOME = mysqli_fetch_array($income)) {
                                                echo "<option value='".$rowINCOME['id']."'>".$rowINCOME['name']."</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="investment">Planned Investment Amount:</label>
                                        <select class="custom-select custom-select-sm" name="investment" id="investment" placeholder="Planned Investment Amount">
                                        <?php
                                            $sqlINVEST = 'SELECT id, name FROM investment';
                                            $investment = $DB_admin->query($sqlINVEST);
                                            while ($rowINVEST = mysqli_fetch_array($investment)) {
                                                echo "<option value='".$rowINVEST['id']."'>".$rowINVEST['name']."</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="strategy">Trading Strategy:</label>
                                        <input type="text" class="form-control form-control-sm" name="strategy" id="strategy"  placeholder="Scalper, Day Trader, Long Term Trader, etc."/>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="pills-3" role="tabpanel" aria-labelledby="pills-3-tab">
                            <form class="was-validated" novalidate>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="bd">Date of Birth:</label>
                                        <input type="text" class="form-control form-control-sm" name="bd" id="bd" value="1900-01-01" />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="whatsapp">WhatsApp:</label>
                                        <input type="text" class="form-control form-control-sm" name="whatsapp" id="whatsapp" />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="telegram">Telegram:</label>
                                        <input type="text" class="form-control form-control-sm" name="telegram" id="telegram" />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="facebook">Facebook:</label>
                                        <input type="text" class="form-control form-control-sm" name="facebook" id="facebook" />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="instagram">Instagram:</label>
                                        <input type="text" class="form-control form-control-sm" name="instagram" id="instagram" />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="twitter">Twitter:</label>
                                        <input type="text" class="form-control form-control-sm" name="twitter" id="twitter" />
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <form class="was-validated" novalidate>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="lead_src">Source:</label>
                                        <select class="form-control" name="lead_src" id="lead_src">
                                            <option value=''>Choose One Source</option>
                                            <?php
                                                if($_SESSION["unit"] == "Turkish"){
                                                    echo "<option value='TurkNew-FB Ref.'>TurkNew-FB Ref.</option>";
                                                } else if($_SESSION["unit"] == "Arabic"){
                                                    echo "<option value='ArabicNew-FB Ref.'>ArabicNew-FB Ref.</option>";
                                                } else if($_SESSION["unit"] == "Farsi"){
                                                    echo "<option value='FarsiNew-FB Ref.'>FarsiNew-FB Ref.</option>";
                                                } else if($_SESSION["unit"] == "Farsi2"){
                                                    echo "<option value='Farsi2New-FB Ref.'>Farsi2New-FB Ref.</option>";
                                                } else if($_SESSION["unit"] == "Farsi"){
                                                    echo "<option value='STPLNew-FB Ref.'>STPLNew-FB Ref.</option>";
                                                }
                                            ?>
                                            <option value='Telegram'>Telegram</option>
                                            <option value='WhatsApp'>WhatsApp</option>
                                            <option value='Instagram'>Instagram</option>
                                            <option value='Reference'>Reference</option>
                                            <option value='Other - '>Other</option>
                                        </select>
                                        <input type="text" class="form-control" style="display: none;" name="lead_src2" id="lead_src2" />
                                        <!--<input type="text" class="form-control form-control-sm" name="lead_src" id="lead_src" />-->
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="lead_camp">Campaign:</label>
                                        <input type="text" class="form-control form-control-sm" name="lead_camp" id="lead_camp" />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="affiliate">Affiliate:</label>
                                        <input type="text" class="form-control form-control-sm" name="affiliate" id="affiliate" value="0" />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
				<?php
					}
				?>
			</div>
		</div>
    </div>
<script>
$(document).ready( function () {
    window.addEventListener('load', () => {
        // Grab all the forms
        var forms = document.getElementsByClassName('needs-validation');
        // Iterate over each one
        for (let form of forms) {
            // Add a 'submit' event listener on each one
            form.addEventListener('#addUser', (evt) => {
                // check if the form input elements have the 'required' attribute  
                if (!form.checkValidity()) {
                    evt.preventDefault();
                    evt.stopPropagation();
                    console.log('Bootstrap will handle incomplete form fields');
                } else {
                    // Since form is now valid, prevent default behavior..
                    evt.preventDefault();
                    console.info('All form fields are now valid...');
                }
                form.classList.add('was-validated');
            });
        }
    });
    
	$("#lead_src").change(function () {
        var ls = $('#lead_src').val();
	    if(ls == "Other - "){
		    $("#lead_src2").show();
		} else {
		    $("#lead_src2").hide();
		}
    });
    
	$(":input").change(function () {
        $("#addUser").prop("disabled", false);
    });
    
    $('#addUser').on('click', function() {
		$("#addUser").attr("disabled", "disabled");
		var fname = $('#fname').val();
		var lname = $('#lname').val();
		var email = $('#email').val();
		var phone = $('#phone').val();
		var country = $('#country').val();
		var city = $('#city').val();
		var address = $('#address').val();
		var interests = $('#interests').val();
		var hobbies = $('#hobbies').val();
		var userunit = $('#userunit').val();
		var retention = $('#retention').val();
		var conversion = $('#conversion').val();
		var status = $('#status').val();
		var type = $('#type').val();
		var followup = $('#followup').val();
		
		var job_cat = $('#job_cat').val();
		var job_title = $('#job_title').val();
		var exp_fx = $('#exp_fx').val();
		var exp_fx_year = $('#exp_fx_year').val();
		var exp_cfd = $('#exp_cfd').val();
		var exp_cfd_year = $('#exp_cfd_year').val();
		var income = $('#income').val();
		var investment = $('#investment').val();
		var strategy = $('#strategy').val();
		
		var bd = $('#bd').val();
		var whatsapp = $('#whatsapp').val();
		var telegram = $('#telegram').val();
		var facebook = $('#facebook').val();
		var instagram = $('#instagram').val();
		var twitter = $('#twitter').val();
		
		var source = $('#lead_src').val();
		var campaign = $('#lead_camp').val();
		var affiliate = $('#affiliate').val();
		
		
		if(fname!="" && lname!="" && phone!="" && type!="" && conversion!="" && userunit!=""){
			$.ajax({
				url: "lead_add.php",
				type: "POST",
				data: {
					fname: fname,
                    lname: lname,
                    email: email,
                    phone: phone,
                    country: country,
                    city: city,
                    address: address,
                    interests: interests,
                    hobbies: hobbies,
                    userunit: userunit,
                    retention: retention,
                    conversion: conversion,
                    status: status,
                    type: type,
                    followup: followup,
                    job_cat: job_cat,
                    job_title: job_title,
                    exp_fx: exp_fx,
                    exp_fx_year: exp_fx_year,
                    exp_cfd: exp_cfd,
                    exp_cfd_year: exp_cfd_year,
                    income: income,
                    investment: investment,
                    strategy: strategy,
                    bd: bd,
                    whatsapp: whatsapp,
                    telegram: telegram,
                    facebook: facebook,
                    instagram: instagram,
                    twitter: twitter,
                    source: source,
                    campaign: campaign,
                    affiliate: affiliate,
					user_id: "<?php echo $userID; ?>"
				},
				cache: false,
				success: function(dataResult){
				    console.log(dataResult);
					var dataResult = JSON.parse(dataResult);
					if(dataResult.statusCode==200){
						$("#butsave").removeAttr("disabled");
						$('#fupForm').find('input:text').val('');
						$('#notifications').html('<div class="alert alert-success alert-dismissible" id="success">Lead details updated successfully! <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a></div>');
					}
					else if(dataResult.statusCode==201){
					   alert(dataResult.statusCode);
					}
					
				}
			});
		}
		else{
			alert('Please fill all the field!');
		}
	});
});
</script>
<?php
    $DB_admin->close();
?>
</body>
</html>
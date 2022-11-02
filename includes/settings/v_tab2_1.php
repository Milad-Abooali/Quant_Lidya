<?php

    // 4:10 PM Monday, November 16, 2020

    $_sys_Users_Completion = json_decode($_sys['Users_Completion']);
    
?>

<h6 class="text-center">Profile Completion Setting</h6>
<form id="Users_Completion">
<table id="t1-1-table" class="table table-striped">
    <thead>
        <tr>
            <th>Item</th>
            <th>Rate %</th>
            <th>Descrybtion</th>
        </tr>
    </thead>
    <tr>
        <td><label for="t1-1-email">Email Address</label></td>
        <td><input class="t1-1" type="number" id="t1-1-email" name="email" min="1" max="100" value="<?= $_sys_Users_Completion->email ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-fname">First Name</label></td>
        <td><input class="t1-1" type="number" id="t1-1-fname" name="fname" min="1" max="100" value="<?= $_sys_Users_Completion->fname ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-lname">Last Name</label></td>
        <td><input class="t1-1" type="number" id="t1-1-lname" name="lname" min="1" max="100" value="<?= $_sys_Users_Completion->lname ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-phone">Phone</label></td>
        <td><input class="t1-1" type="number" id="t1-1-phone" name="phone" min="1" max="100" value="<?= $_sys_Users_Completion->phone ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-country">Country</label></td>
        <td><input class="t1-1" type="number" id="t1-1-country" name="country" min="1" max="100" value="<?= $_sys_Users_Completion->country ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-city">City</label></td>
        <td><input class="t1-1" type="number" id="t1-1-city" name="city" min="1" max="100" value="<?= $_sys_Users_Completion->city ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-address">Address</label></td>
        <td><input class="t1-1" type="number" id="t1-1-address" name="address" min="1" max="100" value="<?= $_sys_Users_Completion->address ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-hobbies">Hobbies</label></td>
        <td><input class="t1-1" type="number" id="t1-1-hobbies" name="hobbies" min="1" max="100" value="<?= $_sys_Users_Completion->hobbies ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-interests">Interests</label></td>
        <td><input class="t1-1" type="number" id="t1-1-interests" name="interests" min="1" max="100" value="<?= $_sys_Users_Completion->interests ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-jobcat">Job Category</label></td>
        <td><input class="t1-1" type="number" id="t1-1-jobcat" name="jobcat" min="1" max="100" value="<?= $_sys_Users_Completion->jobcat ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-jobtitle">Job Title</label></td>
        <td><input class="t1-1" type="number" id="t1-1-jobtitle" name="jobtitle" min="1" max="100" value="<?= $_sys_Users_Completion->jobtitle ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-exFX">Experience in FX</label></td>
        <td><input class="t1-1" type="number" id="t1-1-exFX" name="exFX" min="1" max="100" value="<?= $_sys_Users_Completion->exFX ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-exCFD">Experience in CFD</label></td>
        <td><input class="t1-1" type="number" id="t1-1-exCFD" name="exCFD" min="1" max="100" value="<?= $_sys_Users_Completion->exCFD ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-income">Income</label></td>
        <td><input class="t1-1" type="number" id="t1-1-income" name="income" min="1" max="100" value="<?= $_sys_Users_Completion->income ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-planinvest">Planned Investment Amount</label></td>
        <td><input class="t1-1" type="number" id="t1-1-planinvest" name="planinvest" min="1" max="100" value="<?= $_sys_Users_Completion->planinvest ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-tstrategy">Trading Strategy</label></td>
        <td><input class="t1-1" type="number" id="t1-1-tstrategy" name="tstrategy" min="1" max="100" value="<?= $_sys_Users_Completion->tstrategy ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-birthdate">Date of Birth</label></td>
        <td><input class="t1-1" type="number" id="t1-1-birthdate" name="birthdate" min="1" max="100" value="<?= $_sys_Users_Completion->birthdate ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-WhatsApp">WhatsApp</label></td>
        <td><input class="t1-1" type="number" id="t1-1-WhatsApp" name="WhatsApp" min="1" max="100" value="<?= $_sys_Users_Completion->WhatsApp ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-Telegram">Telegram</label></td>
        <td><input class="t1-1" type="number" id="t1-1-Telegram" name="Telegram" min="1" max="100" value="<?= $_sys_Users_Completion->Telegram ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-Telegram">Facebook</label></td>
        <td><input class="t1-1" type="number" id="t1-1-Facebook" name="Facebook" min="1" max="100" value="<?= $_sys_Users_Completion->Facebook ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-Telegram">Instagram</label></td>
        <td><input class="t1-1" type="number" id="t1-1-Instagram" name="Instagram" min="1" max="100" value="<?= $_sys_Users_Completion->Instagram ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-Telegram">Twitter</label></td>
        <td><input class="t1-1" type="number" id="t1-1-Twitter" name="Twitter" min="1" max="100" value="<?= $_sys_Users_Completion->Twitter ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-Telegram">Source</label></td>
        <td><input class="t1-1" type="number" id="t1-1-Source" name="Source" min="1" max="100" value="<?= $_sys_Users_Completion->Source ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td><label for="t1-1-Telegram">Campaign</label></td>
        <td><input class="t1-1" type="number" id="t1-1-Campaign" name="Campaign" min="1" max="100" value="<?= $_sys_Users_Completion->Campaign ?>"></td>
        <td>-</td>
    </tr>
    <tr>
        <td></td>
        <td id="action-sec"></td>
        <td></td>
    </tr>

</table>
</form>
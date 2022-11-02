<?php
    // 4:10 PM Monday, November 16, 2020

    $_sys_Users_Completion = json_decode($_sys['Users_Completion']);
    
?>
<section class="<?= $href ?>">

    <h6 class="text-center">Profile Completion Setting</h6>
    <form id="Users_Completion">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Item</th>
                <th>Rate %</th>
                <th>Decryption</th>
            </tr>
        </thead>
        <tr>
            <td><label for="email">Email Address</label></td>
            <td><input class="t1-1" type="number" id="email" name="email" min="1" max="100" value="<?= $_sys_Users_Completion->email ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="fname">First Name</label></td>
            <td><input class="t1-1" type="number" id="fname" name="fname" min="1" max="100" value="<?= $_sys_Users_Completion->fname ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="lname">Last Name</label></td>
            <td><input class="t1-1" type="number" id="lname" name="lname" min="1" max="100" value="<?= $_sys_Users_Completion->lname ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="phone">Phone</label></td>
            <td><input class="t1-1" type="number" id="phone" name="phone" min="1" max="100" value="<?= $_sys_Users_Completion->phone ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="country">Country</label></td>
            <td><input class="t1-1" type="number" id="country" name="country" min="1" max="100" value="<?= $_sys_Users_Completion->country ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="city">City</label></td>
            <td><input class="t1-1" type="number" id="city" name="city" min="1" max="100" value="<?= $_sys_Users_Completion->city ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="address">Address</label></td>
            <td><input class="t1-1" type="number" id="address" name="address" min="1" max="100" value="<?= $_sys_Users_Completion->address ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="hobbies">Hobbies</label></td>
            <td><input class="t1-1" type="number" id="hobbies" name="hobbies" min="1" max="100" value="<?= $_sys_Users_Completion->hobbies ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="interests">Interests</label></td>
            <td><input class="t1-1" type="number" id="interests" name="interests" min="1" max="100" value="<?= $_sys_Users_Completion->interests ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="jobcat">Job Category</label></td>
            <td><input class="t1-1" type="number" id="jobcat" name="jobcat" min="1" max="100" value="<?= $_sys_Users_Completion->jobcat ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="jobtitle">Job Title</label></td>
            <td><input class="t1-1" type="number" id="jobtitle" name="jobtitle" min="1" max="100" value="<?= $_sys_Users_Completion->jobtitle ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="exFX">Experience in FX</label></td>
            <td><input class="t1-1" type="number" id="exFX" name="exFX" min="1" max="100" value="<?= $_sys_Users_Completion->exFX ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="exCFD">Experience in CFD</label></td>
            <td><input class="t1-1" type="number" id="exCFD" name="exCFD" min="1" max="100" value="<?= $_sys_Users_Completion->exCFD ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="income">Income</label></td>
            <td><input class="t1-1" type="number" id="income" name="income" min="1" max="100" value="<?= $_sys_Users_Completion->income ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="planinvest">Planned Investment Amount</label></td>
            <td><input class="t1-1" type="number" id="planinvest" name="planinvest" min="1" max="100" value="<?= $_sys_Users_Completion->planinvest ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="tstrategy">Trading Strategy</label></td>
            <td><input class="t1-1" type="number" id="tstrategy" name="tstrategy" min="1" max="100" value="<?= $_sys_Users_Completion->tstrategy ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="birthdate">Date of Birth</label></td>
            <td><input class="t1-1" type="number" id="birthdate" name="birthdate" min="1" max="100" value="<?= $_sys_Users_Completion->birthdate ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="WhatsApp">WhatsApp</label></td>
            <td><input class="t1-1" type="number" id="WhatsApp" name="WhatsApp" min="1" max="100" value="<?= $_sys_Users_Completion->WhatsApp ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="Telegram">Telegram</label></td>
            <td><input class="t1-1" type="number" id="Telegram" name="Telegram" min="1" max="100" value="<?= $_sys_Users_Completion->Telegram ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="Telegram">Facebook</label></td>
            <td><input class="t1-1" type="number" id="Facebook" name="Facebook" min="1" max="100" value="<?= $_sys_Users_Completion->Facebook ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="Telegram">Instagram</label></td>
            <td><input class="t1-1" type="number" id="Instagram" name="Instagram" min="1" max="100" value="<?= $_sys_Users_Completion->Instagram ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="Telegram">Twitter</label></td>
            <td><input class="t1-1" type="number" id="Twitter" name="Twitter" min="1" max="100" value="<?= $_sys_Users_Completion->Twitter ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="Telegram">Source</label></td>
            <td><input class="t1-1" type="number" id="Source" name="Source" min="1" max="100" value="<?= $_sys_Users_Completion->Source ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td><label for="Telegram">Campaign</label></td>
            <td><input class="t1-1" type="number" id="Campaign" name="Campaign" min="1" max="100" value="<?= $_sys_Users_Completion->Campaign ?>"></td>
            <td>-</td>
        </tr>
        <tr>
            <td></td>
            <td id="action-sec"></td>
            <td></td>
        </tr>

    </table>
    </form>
</section>
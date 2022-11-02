<?php
    $profile = main::getProfile();
?>
<?php $form_name = 'profile-edit-general'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>">

    <table class="table table-sm table-dark">
        <thead>
            <tr>
                <th colspan="2" class="text-center text-white-50">General Detail</th>
            </tr>
        </thead>
        <tbody>
            <?php $item = 'fname' ?>
            <tr data-item="<?= $item ?>" class="item-row">
                <td>
                    <label for="f-<?= $item ?>" class="form-label">First Name</label>
                </td>
                <td>
                    <input type="text" class="form-control" name="<?= $item ?>" id="f-<?= $item ?>" value="<?= $profile->General[$item] ?>" placeholder="your <?= $item ?>" required>
                </td>
            </tr>
            <?php $item = 'lname' ?>
            <tr data-item="<?= $item ?>" class="item-row">
                <td>
                    <label for="f-<?= $item ?>" class="form-label">Last Name</label>
                </td>
                <td>
                    <input type="text" class="form-control" name="<?= $item ?>" id="f-<?= $item ?>" value="<?= $profile->General[$item] ?>" placeholder="your <?= $item ?>" required>
                </td>
            </tr>
            <?php $item = 'country' ?>
            <tr data-item="<?= $item ?>" class="item-row">
                <td>
                    <label for="f-<?= $item ?>" class="form-label">Location</label>
                </td>
                <td>
                    <input type="hidden" class="form-control" name="<?= $item ?>" id="f-<?= $item ?>" value="<?= $profile->General[$item] ?>" placeholder="your <?= $item ?>" required>
                    <div id="select-country" class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle w-100" type="button" id="countryList" data-bs-toggle="dropdown" aria-expanded="false">
                            Change Country of Residence
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark" id="countries"> </ul>
                    </div>
                </td>
            </tr>
            <?php $item = 'phone' ?>
            <tr data-item="<?= $item ?>" class="item-row">
                <td>
                    <label for="f-<?= $item ?>" class="form-label">Phone</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <strong class="input-group-text" id="phone-plus">+</strong>
                        <input type="number" min="1" max="99999" pattern="[0-9]*" class="form-control text-primary" placeholder="1" id="f-phone-p" name="phone-p" required>
                        <input type="number" maxlength="11" pattern="[0-9]*" class="form-control w-50" placeholder="123xxxxxxx" id="f-phone" name="phone" value="<?= $profile->General[$item] ?>" required>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div id="form-actions" class="text-end">
        <button type="button" class="btn btn-outline-danger me-2" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>


<script>

    /**
     * Load Countries
     */
    socket.emit("listCountries", (res) => {
        if(res)
        {
            countriesLib = res;
            let countryListHtml = '';
            let countryIso = '';
            let currentCountry = $('#profile-edit-general #f-country').val();
            for(let key in countriesLib) {
                if(res[key].country == currentCountry)
                    countryIso = key;
                countryListHtml += `<li><span class="dropdown-item" data-country="${key}">${res[key].flag} ${res[key].country}</span></li>`;
            }
            $('#profile-edit-general #countries').html(countryListHtml);
            $('#profile-edit-general #countryList').html(countriesLib[countryIso].flag+' '+countriesLib[countryIso].country);
            let countryDial = countriesLib[countryIso].dialCode.substring(1);
            $('#profile-edit-general #f-phone-p').val(countryDial);
            let phone = $('#profile-edit-general #f-phone').val().replace(countryDial,'');
            $('#profile-edit-general #f-phone').val(phone);
        }
    });

    try{
        <?= str_replace('-','_', $form_name) ?>;
    }
    catch(e) {
        if(e.name == "ReferenceError") {
            <?= str_replace('-','_', $form_name) ?> = true;
            $.ajax({
                async: false,
                url: "app/assets/js/<?= $form_name ?>.js",
                dataType: "script"
            });
        }
    }
</script>

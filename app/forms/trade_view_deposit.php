<?php
    global $db;

    $mt5api = new mt5API();
    $api_params['login'] = $params['login'];
    $mt5api->get('/api/user/account/get', $api_params);
    $e = $mt5api->Error;
    $api = $mt5api->Response;

    $number_digit = $api->answer->CurrencyDigits;

    // Test
    $api_params['login']  = $params['login'];
    $mt5api->get('/api/position/get_total', $api_params);
    $e_test = $mt5api->Error;
    $api_test = $mt5api->Response;


?>
<?php $form_name = 'trade-view-deposit'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>">

</form>

<script>

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
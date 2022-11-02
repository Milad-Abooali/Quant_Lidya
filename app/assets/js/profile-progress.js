$( document ).ready(function() {

    const val = $('#profile-progress').data('val');

    let color = 'rgb(57,173,15)';
    if(val<99) color = 'rgb(109,196,38)';
    if(val<75) color = 'rgb(26,166,184)';
    if(val<50) color = 'rgb(28,210,227)';
    if(val<25) color = 'rgb(182,34,34)';
    $('#profile-progress').css('--color', color);


    const cpp = new CircleProgress('#profile-progress',{
        value: val,
        min:0,
        max: 100,
        textFormat:'percent'
    });



});
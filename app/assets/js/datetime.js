$(document).ready(function() {

    setInterval(function tick() {
        let time = new Date();
        time = time.toLocaleTimeString("en-US", {timeZone: `Europe/Athens`});
        $(`#server-time .time`).html(time);
    }, 1000);

    $('.data-local-zone').text(Intl.DateTimeFormat().resolvedOptions().timeZone);
    setInterval(() => $('.data-local-time').html(new Date().toLocaleTimeString()),1000);

    const citysTime = [
        "America/New_York",
        "Europe/London",
        "Europe/Berlin",
        "Asia/Tokyo",
        "Asia/Hong_Kong",
        "Australia/Sydney"
    ];
    for (let i = 0; i < citysTime.length; i++) {
        setInterval(function tick() {
            let time = new Date();
            time = time.toLocaleTimeString("en-US", {timeZone: citysTime[i]});
            $(`li[data-zone="${citysTime[i]}"] .time`).html(time);
        }, 1000);
    }

    $('#zone-times').breakingNews({
        effect:'slide-down',
        play: true,
        delayTimer: 2800,
        scrollSpeed: 5,
        stopOnHover: true,

        height: 40,
        fontSize: "default",
        themeColor: "rgba(77,83,101,0.12)",
        background: "rgba(0, 0, 0, 0.05)",
        borderWidth: 2,
        radius: 1,
        zIndex: 99999

    });

});

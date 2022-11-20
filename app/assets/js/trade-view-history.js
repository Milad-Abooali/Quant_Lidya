var hiddenColumn = [
    {target: 1, visible: true},
    {target: 2, visible: true},
    {target: 3, visible: true},
    {target: 4, visible: true},
    {target: 5, visible: true},
    {target: 6, visible: true},
    {target: 7, visible: true},
    {target: 8, visible: true},
    {target: 9, visible: true},
    {target: 10, visible: true},
    {target: 11, visible: true}
];

function updateHistory() {
    if(!startDate) return;
    if(!endDate) return;
    const data = {
        login: selectedLogin,
        from: startDate,
        to: endDate
    }
    socket.emit("getLoginHistory", data, (response) => {
        console.log(response);
        if (response.e){
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
        }
        else{
            dtHistory.clear();
            dtHistory.rows.add(response.data).draw();
            $("form#trade-view-history #update-time").text(rDT().dateTime);
        }
    });
}

$("body").on("change","form#trade-view-history #start-date", function() {
    startDate = $(this).val();
    updateHistory();
});
$("body").on("change","form#trade-view-history #end-date", function() {
    endDate = $(this).val();
    updateHistory();
});
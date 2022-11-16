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

const intervalResLoginHistory = {
    HistoryRun:false
};
function intervalLoginHistory(oneTime=false){
    intervalModalTemp.loginHistory = setInterval(function(){
        console.log(startDate,endDate);
        updateHistory();
        if(oneTime)
            stopInterval(intervalModalTemp.loginHistory);
    }, 5*1000);
}

function updateHistory() {
    if(!startDate) return;
    if(!endDate) return;
    if(intervalResLoginHistory.HistoryRun) return;
    intervalResLoginHistory.HistoryRun = true;
    const data = {
        login: selectedLogin,
        from: startDate,
        to: endDate
    }
    socket.emit("getLoginHistory", data, (response) => {
        console.log(response);
        if (response.e){
            intervalResLoginHistory.History = response.e;
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            stopInterval(intervalModalTemp.loginHistory);
        }
        else{
            intervalResLoginHistory.History = response.data.length;
            dtHistory.clear();
            dtHistory.rows.add(response.data).draw();
            $("form#trade-view-history #update-time").text(rDT().dateTime);
        }
        intervalResLoginHistory.HistoryRun = false;
    });
}

let startDate,endDate;
$("body").on("change","form#trade-view-history #start-date", function() {
    startDate = $(this).val();
});
$("body").on("change","form#trade-view-history #end-date", function() {
    endDate = $(this).val();
});
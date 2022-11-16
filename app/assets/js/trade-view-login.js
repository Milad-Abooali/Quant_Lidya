var hiddenColumn = [
    {target: 0, visible: true},
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

const intervalResLoginPositions = {
    StatisticsRun:false,
    PositionsRun:false
};
function intervalLoginPositions(oneTime=false){
    intervalModalTemp.loginPositions = setInterval(function(){
        updateStatistics();
        updatePositions();
        if(oneTime)
            stopInterval(intervalModalTemp.loginPositions);
    }, 2000);
}

function updateStatistics() {
    if(intervalResLoginPositions.updateStatisticsRun) return;
    intervalResLoginPositions.updateStatisticsRun = true;
    const data = {
        login: selectedLogin,
    }
    socket.emit("getLoginStatistics", data, (response) => {
        //console.log(response);
        if (response.e){
            intervalResLoginPositions.Statistics = response.e;
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            stopInterval(intervalModalTemp.loginPositions);
        }
        else{
            intervalResLoginPositions.Statistics = true;
            $(`#loginStatistics span[data-lable='balance']`).html(response.data.Balance);
            $(`#loginStatistics span[data-lable='equity']`).html(response.data.Equity);
            $(`#loginStatistics span[data-lable='margin']`).html(response.data.Margin);
            $(`#loginStatistics span[data-lable='margin-level']`).html(response.data.MarginLevel);
            $(`#loginStatistics span[data-lable='free-margin']`).html(response.data.MarginFree);
            $(`#loginStatistics span[data-lable='margin-leverage']`).html(response.data.MarginLeverage);
            $(`#loginStatistics span[data-lable='profit']`).html(response.data.Profit);
            $("form#trade-view-login #update-time").text(rDT().dateTime);
        }
        intervalResLoginPositions.updateStatisticsRun = false;
    });
}

function updatePositions() {
    if(intervalResLoginPositions.updatePositionsRun) return;
    intervalResLoginPositions.updatePositionsRun = true;
    const data = {
        login: selectedLogin,
    }
    socket.emit("getLoginPositions", data, (response) => {
        if (response.e){
            intervalResLoginPositions.Positions = response.e;
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            stopInterval(intervalModalTemp.loginPositions);
        }
        else{
            intervalResLoginPositions.Positions = response.data.length;
            dtPosition.clear();
            dtPosition.rows.add(response.data).draw();
            $("form#trade-view-login #update-time").text(rDT().dateTime);
        }
        intervalResLoginPositions.updatePositionsRun = false;
    });
}

$("body").on("change","form#trade-view-login #intervalResLoginPositions", function() {
    ( $(this).is(":checked") )
        ?   intervalLoginPositions()
        :   stopInterval(intervalModalTemp.loginPositions);
});

$("body").on("change","form#trade-view-login .table-hide-column", function() {
    const column = parseInt($(this).attr('data-column'));
    const status = $(this).is(':checked');
    const object = {target: column, visible: status,bVisible: status};
    hiddenColumn[column] = (object);

    const col = dtPosition.column(column);
    col.visible(!col.visible());

});

// Close Position
$("body").on("click",".doA-close-position", function(e) {
    e.preventDefault();
    const data = {
        login: $(this).data('tp'),
        position:  $(this).data('position')
    }
    if (confirm(`Are you sure you want to close position ${data.position}?`)) {
        appAlert('info','<i class="fas fa-spinner fa-spin"></i> In Progress', 'Closing the positions: '+data.position);
        socket.emit("closePosition", data, (response) => {
            //console.log(response);
            if(response.e)
                appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            else{
                appAlert('success','<i class="fas fa-exclamation-triangle"></i> Done', 'Position "'+data.position+'" is closed, your profit is: <strong>'+response.position.answer[0].Profit+'</strong>');
            }
        });
    } else {
        return;
    }
});

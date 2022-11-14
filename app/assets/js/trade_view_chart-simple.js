const intervalResSymbolChart = {
    updateChart:false,
};
function intervalSymbolChart(oneTime=false){
    intervalModalTemp.symbolChart = setInterval(function(){
        updateChart();
        if(oneTime)
            stopInterval(intervalModalTemp.symbolChart);
        //console.log('Interval Res',intervalSymbolChart);
    }, 60*1000);
}

function updateChart() {
    if(intervalResSymbolChart.updateChart) return;
    intervalResSymbolChart.updateChart = true;
    const data = {
        symbol: selectedSymbol
    }
    socket.emit("getSymbolChart", data, (response) => {
        //console.log(response);
        if (response.e){
            intervalResSymbolChart.updateChart = response.e;
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            stopInterval(intervalModalTemp.symbolChart);
        }
        else{
            intervalResSymbolChart.updateChart = true;
            console.log('Chart Updated');
            //console.log(response.chartData);
        }
        intervalResSymbolChart.updateChart = false;
    });
}
console.log('simple chart loaded');
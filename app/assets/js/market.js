const intervalResMarketPrices = {
    updateMarket:false
};

function intervalMarketPrices(oneTime=false){
    intervalScreenTemp.marketPrices = setInterval(function(){
        updateMarket();
        if(oneTime)
            stopInterval(intervalScreenTemp.marketPrices);
        //console.log('Interval Res', intervalResMarketPrices);
    }, 2500);
}

const prices = {};
function updateMarket() {
    if(intervalResMarketPrices.updateMarketRun) return;
      intervalResMarketPrices.updateMarketRun = true;
    const data = {
        login: selectedLogin,
    }
    socket.emit("getMarketPrices", data, (response) => {
        //console.log(response);
        if (response.e){
            intervalResMarketPrices.updateMarket = response.e;
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            stopInterval(intervalScreenTemp.marketPrices);
        }
        else{
            intervalResMarketPrices.updateMarket = true;
            $("#market #update-time").text(rDT().dateTime);
            for(let key in response.symbols){
                const item = response.symbols[key];
                const old = prices[item.Symbol];
                for(let i in item){
                    try{
                        const numColor = numChangeColor(item[i], old[i]);
                        if (numColor)
                            $(`.row-symbol[data-symbol="${item.Symbol}"] .${i}`).html(numColor);
                    }catch(e){}
                }
                prices[item.Symbol] = response.symbols[key];
            }


        }
        intervalResMarketPrices.updateMarketRun = false;
    });
}


$("body").on("change","#market #intervalResMarketPrices", function() {
    intervalTimeout = 1000;
    ( $(this).is(":checked") )
        ?   intervalMarketPrices()
        :   stopInterval(intervalScreenTemp.marketPrices);
});


console.log('market js loaded');

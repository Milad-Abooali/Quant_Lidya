const intervalResMarketPrices = {
    updateMarket:false
};

function intervalMarketPrices(oneTime=false){
    intervalScreenTemp.marketPrices = setInterval(function(){
        updateMarket();
        if(oneTime)
            stopInterval(intervalScreenTemp.marketPrices);
        clearPairView();
        //console.log('Interval Res', intervalResMarketPrices);
    }, 900);
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

if(typeof localStorage.defaultPairs === 'undefined'){
    localStorage.defaultPairs =
        JSON.stringify([
            'EURUSD',
            'GBPUSD',
            'USDJPY',
            'USDTRY',
            'XAUUSD',
            'USDCHF',
            'AUDUSD',
            'USDCAD'
        ]);
}
function clearPairView(){
    if( $('#market-search #pair-input').val().length>2 ) {return;}
    $(`.row-symbol`).hide();
    $(`.row-symbol .remove-pair`).hide();
    const defaultPairs = JSON.parse(localStorage.defaultPairs);
    defaultPairs.forEach((symbol, index)=>{
        $(`.row-symbol[data-symbol="${symbol}"]`).show();
        $(`.row-symbol[data-symbol="${symbol}"] .remove-pair`).show();
        $(`.row-symbol[data-symbol="${symbol}"] .add-pair`).hide();
    });
}
clearPairView();

$("body").on("click","#market .add-pair", function() {
    const symbol = $(this).data('symbol');
    let defaultPairs = JSON.parse(localStorage.defaultPairs);
    defaultPairs.push(symbol);
    localStorage.defaultPairs = JSON.stringify(defaultPairs);
    $(`.row-symbol[data-symbol="${symbol}"] .remove-pair`).show();
    $(`.row-symbol[data-symbol="${symbol}"] .add-pair`).hide();
    clearPairView();
});
$("body").on("click","#market .remove-pair", function() {
    const symbol = $(this).data('symbol');
    let defaultPairs = JSON.parse(localStorage.defaultPairs);
    localStorage.defaultPairs = JSON.stringify(
        defaultPairs.filter(x => x !== symbol)
    );
    $(`.row-symbol[data-symbol="${symbol}"] .remove-pair`).hide();
    $(`.row-symbol[data-symbol="${symbol}"] .add-pair`).show();
    clearPairView();
});

$("body").on("submit","#market #market-search", function() {
    e.preventDefault();
});
$("body").on('change, keyup','#market-search #pair-input', function(e) {
    e.preventDefault();
    const jumpPair = $(this).val().toUpperCase()
    if(jumpPair.length>2){
        $(`.row-symbol`).show();
        Object.keys(prices).forEach((item, index)=>{
            if(item.includes(jumpPair)){
                $(`.row-symbol[data-symbol="${jumpPair}"]`).show();
            } else {
                $(`.row-symbol[data-symbol="${item}"]`).fadeOut();
            }
        });
    } else if (jumpPair.length<2) {
        clearPairView();
    }
});

let pairsDatalist='';
setInterval(function(){
    let datalist='';
    for (var i = 0; i < Object.keys(prices).length; i++) {
        datalist += '<option value="' + Object.keys(prices)[i] + '" />';
    }
    if(datalist.length>1 && (datalist==pairsDatalist) ) {
        return;
    }
    pairsDatalist = datalist;
    $('#pairs-list').html(pairsDatalist);
}, 5000);


console.log('market js loaded');


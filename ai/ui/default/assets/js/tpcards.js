/**
 * Make TP Card
 * @param login
 * @param type
 * @returns {string}
 */
function makeTpCard(login, type) {
    if(type==1){
        typeText = 'Demo';
        type = 'secondary';
    } else if(type==2){
        typeText = 'Real';
        type = 'success';
    }
    return '<li id="tpcard-'+login+'" class="alert alert-'+type+'" data-login="'+login+'"><a href="#"><div class="media"><div class="media-body overflow-hidden"><h5 class="font-16 mt-0 mb-1 text-muted">#'+login+'</h5><div class="row"><p class="mb-0 col-md-6 text-muted">Balance</p><p class="mb-0 col-md-6 text-muted">Equity</p><p class="mb-0 col-md-6 bold text-muted">$<span class="balance"></span></p><p class="mb-0 col-md-6 bold text-muted">$<span class="equity"></span></p></div><hr><div class="row"><p class="mb-0 col-md-6 text-muted">Margin Level</p><p class="mb-0 col-md-6 text-muted">Free Margin</p><p class="mb-0 col-md-6 bold text-muted"><span class="margin-level"></span>%</p><p class="mb-0 col-md-6 bold text-muted">$<span class="free-margin"></span></p></div></div><div class="font-11 border border-success px-2 text-muted" style="position: absolute;right: 10px;">'+typeText+'</div><div class="selected-login d-none font-11 border border-success px-2 py-1 text-'+type+'" style="position: absolute;right: 0;bottom: 0;border-bottom: 0px !important;border-right: 0px !important;border-radius: 5px 0 0 0;background: #fff;border: 1px transparent !important;"><i class="fas fa-check"></i></div></div></a></li>';
}

/**
 * Update TP Card
 * @param login
 * @param item
 * @param value
 */
function updateTpCard(login, item, value) {
    $('li#tpcard-'+login+' .'+item).text(value);
}

/**
 * Select TP Login
 * @param login
 */
function selectTpLogin(login) {
    localStorage.selectedLogin = login;
    $('#seltpaccount').val(login);
    $('#tp-cards .selected-login').addClass('d-none');
    $('#tp-cards #tpcard-'+login+' .selected-login').removeClass('d-none');
    $('.selected-tp-account').html('#'+login);
}

/**
 * Check TP From Server
 * @returns {Promise<void>}
 */
async function checkTps(){
    if(visitor.level==='user') {
        aiAjaxCall('mt5api', 'act=tpData', function(tpData){
            if(tpData.res) {
                    $.each(tpData.res, function(index) {
                    if(this.api) {
                        if(this.api.answer[0]) {
                            let api = this.api.answer[0];
                            if($('#tpcard-'+index).length){
                                updateTpCard(index, 'free-margin', api.MarginFree);
                                updateTpCard(index, 'margin-level', api.MarginLevel);
                                updateTpCard(index, 'balance', api.Balance);
                                updateTpCard(index, 'equity', api.Equity);
                                tpCardsUpdaterLock=false;
                            } else {
                                $('ul#tp-cards').append(makeTpCard(index, this.tp.group_id));
                                updateTpCard(index, 'free-margin', api.MarginFree);
                                updateTpCard(index, 'margin-level', api.MarginLevel);
                                updateTpCard(index, 'balance', api.Balance);
                                updateTpCard(index, 'equity', api.Equity);
                                tpCardsUpdaterLock=false;
                            }
                        } else {
                            //console.warn(index+' - Account was removed');
                            $('ul#tp-cards li#tpcard-'+index).remove();
                            tpCardsUpdaterLock=false;
                        }
                    }
                });
            } else {
                tpCardsUpdaterLock=false;
            }
        });
    }
    else {
        cb.C.warning('login to check TPs');
    }
}

/**
 * Update TP Cards Loop
 * for stop > clearInterval(tpCardsUpdater);
 * @type {number}
 */
var tpCardsUpdaterLock = true;
let tpCardsUpdater = setInterval(() => {
    if(visitor.level==='user'){
        if(tpCardsUpdaterLock==false) {
            tpCardsUpdaterLock=true;
            checkTps();
        } else {
            console.log('tpCardsUpdaterLock');
        }
    }
},1000*8);
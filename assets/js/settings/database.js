
var consoleVisible = false;

$(document).ready(function(){

    $('#db-console').draggable({
        cursor: "move",
        greedy: true
    });
    $("body").on("click", '.db-console-mini', function(event){
        consoleVisible ^= true;
        $('.db-console-content').toggle();
        $(this).html($(this).html() === '<i class="fas fa-caret-down"></i>' ? '<i class="fas fa-caret-up"></i>' : '<i class="fas fa-caret-down"></i>');
    });

    var DT_tables = $('#tables.dataTable').DataTable({
        'pageLength': -1,
        'lengthMenu': [ [50, 100, 250, 500, -1], [50, 100, 250, 500, 'All'] ],
        order: [[1, 'desc']],
        "autoWidth": false,
        columnDefs: [
            { orderable: false, targets: 0 }
        ],
    });

    var selected_tables = [];
    $("body").on("click", '#select-all', function(event){
        selected_tables = [];
        var rows = DT_tables.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
        $('.select-row').each(function() {
            if ($(this).is(":checked")) {
                selected_tables.push($(this).data('table'));
            }
        });
    });
    $("body").on("change", '.select-row', function(event){
        selected_tables = [];
        $('.select-row').each(function() {
            if ($(this).is(":checked")) {
                selected_tables.push($(this).data('table'));
            }
        });
    });

    $("body").on("click", '#do-check', function(event){
        doCheck(selected_tables);
    });
    $("body").on("click", '.doA-check', function(event){
        let table = [$(this).data('table')];
        doCheck(table);
    });

    $("body").on("click", '#do-repair', function(event){
        doRepair(selected_tables);
    });
    $("body").on("click", '.doA-repair', function(event){
        let table = [$(this).data('table')];
        doRepair(table);
    });

    $("body").on("click", '#do-optimize', function(event){
        doOptimize(selected_tables);
    });
    $("body").on("click", '.doA-optimize', function(event){
        let table = [$(this).data('table')];
        doOptimize(table);
    });

    $("body").on("click", '.doA-export', function(event){
        let table = [$(this).data('table')];
        doExport(table);
    });

    $("body").on("click", '.doM-history', function(event){
        if(consoleVisible)
            $('.db-console-mini').trigger('click');
        let table = $(this).data('table');

        ajaxCall ('db', 'listFiles', {table:table}, function(response){
            let resObj = JSON.parse(response);
            if(resObj.e) {
                $('.db-console-content').JSONView(resObj.e);
                $('.db-console-mini').trigger('click');
            } else if(resObj.res) {
                html = '<table class="table table-hover table-sm"><thead><tr><th>Time</th><th>Download</th></tr></thead><tbody>';
                $.each(resObj.res, function(index) {
                    let fileDate = new Date(this[0]*1000);
                    html += '<tr><td>'+fileDate.toLocaleString()+'</td><td><a target="_blank" href="backup/'+this[1]+'">'+this[1]+'</a></td></tr>'
                });
                html += '</tbody></table>';
                makeModal('Table Backups ('+table+')', html);
            }
        });
    });

});

function modalProgress(){
    $('.db-console .db-console-content > div:first-of-type').css('border','');
    $('#modal-progress').modal('show');
}

function doCheck(tables) {
    modalProgress();
    ajaxCall ('db', 'checkTables', {tables:tables}, function(response){
        let resObj = JSON.parse(response);
        $('#modal-progress').modal('hide');
        if(resObj.e) {
            $('.db-console-content').JSONView(resObj.e);
            $('.db-console-mini').trigger('click');
        } else if(resObj.res) {
            $('.db-console-content').prepend('<div class="rounded bg-dark p-1 mb-2"></div><hr>');
            $('.db-console-content > div:first-of-type').JSONView(resObj.res);
            if(!consoleVisible)
                $('.db-console-mini').trigger('click');
        }
    });
}

function doRepair(tables) {
    modalProgress();
    ajaxCall ('db', 'repairTables', {tables:tables}, function(response){
        let resObj = JSON.parse(response);
        if(resObj.e) {
            $('.db-console-content').JSONView(resObj.e);
            $('.db-console-mini').trigger('click');
        } else if(resObj.res) {
            $('.db-console-content').prepend('<div class="rounded bg-dark p-1 mb-2"></div><hr>');
            $('.db-console-content > div:first-of-type').JSONView(resObj.res);
            if(!consoleVisible)
                $('.db-console-mini').trigger('click');
        }
        $('#modal-progress').modal('hide');
    });
}

function doOptimize(tables) {
    modalProgress();
    ajaxCall ('db', 'optimizeTables', {tables:tables}, function(response){
        let resObj = JSON.parse(response);
        if(resObj.e) {
            $('.db-console-content').JSONView(resObj.e);
            $('.db-console-mini').trigger('click');
        } else if(resObj.res) {
            $('.db-console-content').prepend('<div class="rounded bg-dark p-1 mb-2"></div><hr>');
            $('.db-console-content > div:first-of-type').JSONView(resObj.res);
            if(!consoleVisible)
                $('.db-console-mini').trigger('click');
        }
        $('#modal-progress').modal('hide');
    });
}

function doExport(tables) {
    modalProgress();
    ajaxCall ('db', 'exportTable', {tables:tables}, function(response){
        let resObj = JSON.parse(response);
        if(resObj.e) {
            $('.db-console-content').JSONView(resObj.e);
            $('.db-console-mini').trigger('click');
        } else if(resObj.res) {
            $('.db-console-content').prepend('<div class="rounded bg-dark p-1 mb-2"></div><hr>');
            $('.db-console-content > div:first-of-type').JSONView(resObj.res);
            if(!consoleVisible)
                $('.db-console-mini').trigger('click');
            if(resObj.res['result']==0 && resObj.res['filename']){
                window.open('backup/'+resObj.res['filename'], "_blank")
            }
        }
        $('#modal-progress').modal('hide');
    });
}
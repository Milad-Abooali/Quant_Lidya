if(FinalFix===true) {

    // Fix Tooltip / Popover hide
    $('body').on('click','*', function() {
        $('[data-toggle="tooltip"], .tooltip').tooltip("hide");
        $('[data-trigger="focus"]').popover("hide");
    });

    // Fix Popover show
    $('body').on('click','[data-trigger="focus"]', function(e) {
        e.stopPropagation();
        $(this).popover("show");
    });

    // Fix DataTable width on tabs
    $("body").on('shown.bs.tab', 'a[data-toggle="tab"]', function(e){
        $('.tab-content .tab-pane.active .table-DT').each(function (index) {
            if (eval('DT_' + $(this).attr('id'))) {
                eval('DT_' + $(this).attr('id')).columns.adjust().responsive.recalc();
                console.log(this);
            }
        });
    });
    FinalFix = false;
    console.log('Final Fix Loaded');
} else {
    console.log('Final Fix Skipped');
}
/**
 *  IBs
 */
/* New Group */

$( '.ibs_new-group form#new-ib-group.needShow').hide()
$('body').on('click', '.ibs_new-group #prepare-form #doV-form', function() {
    let name = $( '.ibs_new-group #prepare-form #group_name_t' ).val();
    let mt4_list = $( '.ibs_new-group #prepare-form #mt4_list' ).val();
    let mt5_list = $( '.ibs_new-group #prepare-form #mt5_list' ).val();
    alert(name);
    $( '.ibs_new-group form#new-ib-group.needShow #group_name').val(name);

    $(mt4_list).each(function(index) {
        let html = '<div class="col-md-3 mb-2"><label>'+this+'</label><input class="form-control" type="number" step="0.01" name="mt4_v[]" value="1.00" required></div>';
        html += '<input class="form-control" type="hidden" name="mt4_s[]" value="'+this+'" required>';
        $( '.ibs_new-group .needShow #symbols-rate-mt4').append(html);
    });

    $(mt5_list).each(function(index) {
        let html = '<div class="col-md-3 mb-2"><label>'+this+'</label><input class="form-control" type="number" step="0.01" name="mt5_v[]" value="1.00" required></div>';
        html += '<input class="form-control" type="hidden" name="mt5_s[]" value="'+this+'" required>';
        $( '.ibs_new-group .needShow #symbols-rate-mt5').append(html);
    });

    $( '.ibs_new-group #prepare-form').hide();
    $( '.ibs_new-group form#new-ib-group.needShow').fadeIn();

});

$('body').on('click', '.ibs_new-group #doW-go-back', function() {
    location.reload();
});

// Submit Add/Copy
$("body").on("submit", ".ibs_new-group form#new-ib-group", function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    ajaxForm ('ib', 'newGroup', formData, function(response){
        obj = JSON.parse(response);
        if(obj.e) {
            toastr.error(obj.e);
        } else {
            toastr.success("Group successfully created");
        }
    });
});

// Submit Edit
$("body").on("submit", ".ibs_new-group form#edit-ib-group", function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    ajaxForm ('ib', 'editGroup', formData, function(response){
        obj = JSON.parse(response);
        if(obj.e) {
            toastr.error(obj.e);
        } else {
            toastr.success("Group successfully created");
        }
    });
});


/* Groups List */

let groupsListTable = $( '.ibs_groups-list #groups-list' ).DataTable();

$('body').on('click', '.ibs_groups-list .doA-delete', function() {
    let row = $(this).parents('tr');
    let id = $(this).data('id');
    const r = confirm("Are you sure to delete the Group id "+id);
    if (r === true) {
        let data = {groupId:id};
        ajaxCall('ib', 'deleteGroup', data, function(response){
            obj = JSON.parse(response);
            if(obj.e) {
                toastr.error(obj.e);
            } else {
                groupsListTable
                    .row( row )
                    .remove()
                    .draw();
                toastr.success("Group successfully created");

            }
        });
    }
});

$('body').on('click', '.ibs_groups-list .doA-copy', function() {
    let id = $(this).data('id');
    let url = 'sys_settings.php?section=ibs_new-group&a=copy&id='+id;
    window.location.replace(url);
});

$('body').on('click', '.ibs_groups-list .doA-edit', function() {
    let id = $(this).data('id');
    let url = 'sys_settings.php?section=ibs_new-group&a=edit&id='+id;
    window.location.replace(url);
});

/* Group Members */

let groupMembersTable = $( '.ibs_group-members #groups-list' ).DataTable();
let groupIBsTable = $( '.ibs_group-members #group-ibs' ).DataTable();
let brokerIBsTable = $( '.ibs_group-members #list-ibs' ).DataTable();

// Select group
$('body').on('click', '.ibs_group-members .doA-select', function() {
    let id = $(this).data('id');
    let url = 'sys_settings.php?section=ibs_group-members&id='+id;
    window.location.replace(url);
});

// Add IB to group
$('body').on('click', '.ibs_group-members .doA-add', function() {
    let selRow = $(this).parents('tr');
    let data = {
        ib_id:$(this).data('ib'),
        group_id:$(this).data('group')
    }
    let cell = brokerIBsTable.cell( $(this).parents('td') );
    ajaxCall('ib', 'addMember', data, function(response){
        toastr.options = {
            "positionClass": "toast-bottom-left"
        };
        obj = JSON.parse(response);
        if(obj.e) {
            toastr.error(obj.e);
        } else {
            cell.data( cell.data().replace('doA-add btn btn-outline-success fa fa-angle-right', 'doA-remove btn btn-outline-danger fa fa-times-circle') ).draw();
            let moveRow = brokerIBsTable.row( selRow );
            let rowNode = moveRow.node();
            moveRow
                .remove()
                .draw();
            groupIBsTable
                .row
                .add(rowNode)
                .draw();
            toastr.success("IB successfully Added");
        }
    });
});

// Remove IB from group
$('body').on('click', '.ibs_group-members .doA-remove', function() {
    let selRow = $(this).parents('tr');
    let data = {
        ib_id:$(this).data('ib'),
        group_id:$(this).data('group')
    }
    let cell = groupIBsTable.cell( $(this).parents('td') );
    ajaxCall('ib', 'removeMember', data, function(response){
        toastr.options = {
            "positionClass": "toast-bottom-left"
        };
        obj = JSON.parse(response);
        if(obj.e) {
            toastr.error(obj.e);
        } else {
            cell.data( cell.data().replace('doA-remove btn btn-outline-danger fa fa-times-circle', 'doA-add btn btn-outline-success fa fa-angle-right') ).draw();
            let moveRow = groupIBsTable.row( selRow );
            let rowNode = moveRow.node();
            moveRow
                .remove()
                .draw();
            brokerIBsTable
                .row
                .add(rowNode)
                .draw();
            toastr.success("IB successfully Removed");
        }
    });
});

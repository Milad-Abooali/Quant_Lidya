/**
 *  Permissions
 *  Groups Manager
 */
// Add New Group
$(".permissions_groups form#newGroup").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    let name = $('.permissions_groups #newName').val();
    let priority = $('.permissions_groups #newPriority').val();
    ajaxCall ('groups', 'add',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Group added successfully.");
            id = resObj.res;
            let newTR = '<tr id="g-'+id+'"><td>'+id+'</td><td class="gn-'+id+'">'+name+'</td><td>'+priority+'</td><td>0</td><td><button class="btn btn-warning btn-sm form-control doDrop" data-gid="'+id+'"><i class="fa fa-user-alt-slash"></i></button></td><td><button class="btn btn-danger btn-sm form-control doDelete" data-gid="'+id+'"><i class="fa fa-times-circle"></i></button></td></tr>';
            $('.permissions_groups #groupsList tr:last').after(newTR);
            let newOP = '<option class="gn-'+id+'" value="'+id+'">'+name+'</option>';
            $('.permissions_groups .glist').append(newOP);
        }
    });
});
// Copy Group
$(".permissions_groups form#copyGroup").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    let name = $('.permissions_groups #cgName').val();
    let priority = $('.permissions_groups #cgPriority').val();
    ajaxCall ('groups', 'copyFrom',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Group added successfully.");
            id = resObj.res;
            let newTR = '<tr id="g-'+id+'"><td>'+id+'</td><td class="gn-'+id+'">'+name+'</td><td>'+priority+'</td><td>0</td><td><button class="btn btn-warning btn-sm form-control doDrop" data-gid="'+id+'"><i class="fa fa-user-alt-slash"></i></button></td><td><button class="btn btn-danger btn-sm form-control doDelete" data-gid="'+id+'"><i class="fa fa-times-circle"></i></button></td></tr>';
            $('.permissions_groups #groupsList tr:last').after(newTR);
            let newOP = '<option class="gn-'+id+'" value="'+id+'">'+name+'</option>';
            $('.permissions_groups .glist').append(newOP);
        }
    });
});
// Edit Group
$(".permissions_groups #uGid").change(function(){
    let name = $(this).find(':selected').text();
    $('.permissions_groups #changeName').val(name);
    $('.permissions_groups #changePriority').val(0);
});
$(".permissions_groups form#editGroup").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    let gid = $('.permissions_groups #uGid').val();
    let name = $('.permissions_groups #changeName').val();
    let priority = $('.permissions_groups #changePriority').val();
    ajaxCall ('groups', 'update',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Group name has been changed.");
            $('.permissions_groups .gn-'+gid).html(name);
            $('.permissions_groups .gp-'+gid).html(priority);
        }
    });
});
// Delete Group
$("body").on("click", ".permissions_groups .doDelete", function(event){
    event.preventDefault();
    let clicked = $(this);
    let data = {
        gid: $(this).data('gid')
    }
    var r = confirm("Delete Group?");
    if (r == true) {
        ajaxCall('groups', 'delete', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on saving form !");
            } else if (resObj.res) {
                toastr.success("Group deleted.");
                clicked.closest("tr").remove();
            }
        });
    }
});
// Drop Group Users
$("body").on("click", ".permissions_groups .doDrop", function(event){
    event.preventDefault();
    let clicked = $(this);
    let data = {
        gid: $(this).data('gid')
    }
    var r = confirm("Drop Users?");
    if (r == true) {
        ajaxCall ('groups', 'drop',data, function(response){
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on saving form !");
            } else if (resObj.res) {
                toastr.success("Group users drop...");
                clicked.closest('td').prev().html("0");
                clicked.closest('td').prev().html("0");
            }
        });
    }
});

/**
 *  Permissions
 *  Paths Manager
 */
var pStatus = ['<i class="fa fa-ban text-danger"></i>','<i class="fa fa-check-circle text-success"></i>']
// Add New Path
$(".permissions_paths form#newPath").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    let path = $('.permissions_paths #nPath').val();
    let pView = $('.permissions_paths #nView').prop('checked') ? 1 : 0;
    let pNew = $('.permissions_paths #nNew').prop('checked') ? 1 : 0;
    let pEdit = $('.permissions_paths #nEdit').prop('checked') ? 1 : 0;
    let pDel = $('.permissions_paths #nDel').prop('checked') ? 1 : 0;
    ajaxCall ('paths', 'add',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Path added successfully.");
            let id = resObj.res;
            let newTR = '<tr id="p-'+id+'"><td>'+id+'</td><td>'+path+'</td><td>'+pStatus[pView]+'</td><td>'+pStatus[pNew]+'</td><td>'+pStatus[pEdit]+'</td><td>'+pStatus[pDel]+'</td><td><button class="btn btn-danger btn-sm form-control doDelete" data-pid="'+id+'"><i class="fa fa-times-circle"></i></button></td></tr>';
            $('.permissions_paths #pathsList tr:last').after(newTR);
            let newOP = '<option class="pn-'+id+'" value="'+id+'" data-edit="'+pEdit+'" data-del="'+pDel+'" data-new="'+pNew+'" data-view="'+pView+'">'+path+'</option>';
            $('.permissions_paths .plist').append(newOP);
        }
    });
});
// Show Edit Form Part
$(".permissions_paths #editPath-data").hide();
$(".permissions_paths #uPid").change(function(){
    $(this).closest('form').find("input[type=checkbox]").prop( "checked", false);
    $('#uPath').val($(this).find(':selected').text());
    if ($(this).find(':selected').data('new')) $('#uNew').prop( "checked", true);
    if ($(this).find(':selected').data('view')) $('#uView').prop( "checked", true);
    if ($(this).find(':selected').data('edit')) $('#uEdit').prop( "checked", true);
    if ($(this).find(':selected').data('del')) $('#uDel').prop( "checked", true);
    $(".permissions_paths #editPath-data").fadeIn();
});
// Edit Path
$(".permissions_paths form#editPath").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    let pid = $('.permissions_paths #uPid').val();
    let path = $('.permissions_paths #uPath').val();
    let pView = $('.permissions_paths #uView').prop('checked') ? 1 : 0;
    let pNew = $('.permissions_paths #uNew').prop('checked') ? 1 : 0;
    let pEdit = $('.permissions_paths #uEdit').prop('checked') ? 1 : 0;
    let pDel = $('.permissions_paths #uDel').prop('checked') ? 1 : 0;
    ajaxCall ('paths', 'update',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Path has been updated.");
            let newTR = '<td>'+pid+'</td><td>'+path+'</td><td>'+pStatus[pView]+'</td><td>'+pStatus[pNew]+'</td><td>'+pStatus[pEdit]+'</td><td>'+pStatus[pDel]+'</td><td><button class="btn btn-danger btn-sm form-control doDelete" data-pid="'+pid+'"><i class="fa fa-times-circle"></i></button></td>';
            $('.permissions_paths #p-'+pid).html(newTR);
        }
    });
});
// Delete Path
$("body").on("click", ".permissions_paths .doDelete", function(event){
    event.preventDefault();
    let clicked = $(this);
    let data = {
        pid: $(this).data('pid')
    }
    const r = confirm("Delete Path?");
    if (r === true) {
        ajaxCall('paths', 'delete', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on saving form !");
            } else if (resObj.res) {
                toastr.success("Path deleted.");
                clicked.closest("tr").remove();
            }
        });
    }
});

/**
 *  Permissions
 *  Groups Users
 */
// Select All checkbox
$("body").on("click", ".permissions_groups-users .select-all", function(event){
    let status = $(this).prop('checked');
    $('#user-list input[type="checkbox"]').prop( "checked", status );
});
// Select checkbox by click on row
$("body").on("click", ".permissions_groups-users #user-list td", function(event){
    let status = !($(this).closest("tr").find('input[type="checkbox"]').prop('checked'));
    $(this).closest("tr").find('input[type="checkbox"]').prop('checked', status);
});
// Add Users To Groups
$(".permissions_groups-users form#usersGroups").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    ajaxCall ('groups', 'addUsers',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Users has been added.");
            console.log(resObj.res);
        }
    });
});

/**
 *  Permissions
 *  Groups Perms
 */
// Load Group Perms
$(".permissions_groups-perms #uGid").change(function(){
    window.location.href = 'sys_settings.php?section=permissions_groups-perms&id='+$(this).val();
});
// New Perm
$(".permissions_groups-perms form#newPerm").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    ajaxCall ('groups', 'addPerm',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Perm has been seted.");
            DT_group_perms.ajax.reload();
        }
    });
});
// Delete Perm
$("body").on("click", ".permissions_groups-perms .doDelete", function(event){
    event.preventDefault();
    let clicked = $(this);
    let data = {
        gpid: $(this).data('gpid')
    }
    const r = confirm("Delete Perm?");
    if (r === true) {
        ajaxCall('groups', 'delPerm', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Perm deleted.");
                DT_group_perms.ajax.reload();
            }
        });
    }
});
/**
 *  Permissions
 *  Users Perms
 */
// Load Users Perms
$(".permissions_users-perms #uUid").change(function(){
    window.location.href = 'sys_settings.php?section=permissions_users-perms&id='+$(this).val();
});
// New Perm
$(".permissions_users-perms form#newPerm").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    ajaxCall ('groups', 'addUserPerm',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Perm has been seted.");
            DT_user_perms.ajax.reload();
        }
    });
});
// Delete Perm
$("body").on("click", ".permissions_users-perms .doDelete", function(event){
    event.preventDefault();
    let clicked = $(this);
    let data = {
        upid: $(this).data('upid')
    }
    const r = confirm("Delete Perm?");
    if (r === true) {
        ajaxCall('groups', 'delUserPerm', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on saving form !");
            } else if (resObj.res) {
                toastr.success("Perm deleted.");
                DT_user_perms.ajax.reload();
            }
        });
    }
});

/**
 *  Permissions
 *  Path Perms
 */
// Load Users Perms
$(".permissions_path-perms #vPid").change(function(){
    window.location.href = 'sys_settings.php?section=permissions_path-perms&id='+$(this).val();
});
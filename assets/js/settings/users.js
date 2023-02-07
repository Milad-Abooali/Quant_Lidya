/**
 *  Users
 *  profile Completion
 */
var sum;
$(".users_profile-completion input").change(function(){
    sum = 0;
    $('#Users_Completion input').each(function(){
        sum += (parseFloat(this.value)) || 0;
    });
    if(sum<100) {
        let left = 100 - sum;
        $('#action-sec').html('You have to assign '+left+' more Points')
    } else if (sum>100) {
        let overhead = sum - 100;
        $('#action-sec').html('You have to remove '+overhead+' Points')
    } else {
        $('#action-sec').html('<button type="submite" class="btn btn-success btn-block">Save Setting</button>')
    }
});
$(".users_profile-completion form#Users_Completion").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    ajaxCall ('settings', 'userCompletionSave',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Form saved successful.");
        }
    });
});

/**
 * Merge Users
 */
var selected_users = [];
var merged_user = {
    users:{},
    user_extra:{},
    marketing:{},
    gi:{},
    fx:{},
    tp:{}
}
$(".users_merge .main-user-select").click(function(){
    merged_user = {
        users:{},
        user_extra:{},
        marketing:{},
        gi:{},
        fx:{},
        tp:{}
    }
    let uid = $(this).data('uid');
    $('.main-user-select').removeClass('alert-success').addClass('alert-danger');
    $(this).removeClass('alert-danger').addClass('alert-success');
    $('tr.items').removeClass('alert-success');
    $('#u-item-'+uid+' tr.items').each(async function(i, obj) {
        await selectItem(obj);
    });
});

// Update Main User
$(".users_merge #doA-mergeUsers").click(async function(){
    const r = confirm("Do you want to merge users? Other user will be removed!");
    if (r === true) {
        let data = {
            update: merged_user,
            users: selected_users
        }
        ajaxCall('users', 'merge', data, function (Res) {
            $('#modalMain').modal({
                backdrop: 'static',
                keyboard: false
            });
            body = '<div id="merge-detail"></div>';
            footer = '<button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="document.location.reload(true)">Close</button>';
            makeModal('Merge Detail',body,'lg', footer);
            $("#mege-detail").JSONView(Res);
        });
    }
});

// Move Comments
$(".users_merge #doA-moveComments").click(async function(){
    const r = confirm("Do you want to move users comments to selected user?");
    if (r === true) {
        let data = {
            mainUser: merged_user.users.id,
            users: selected_users
        }
        ajaxCall('users', 'moveComments', data, function (Res) {
            $('#modalMain').modal({
                backdrop: 'static',
                keyboard: false
            });
            body = '<div id="merge-detail"></div>';
            footer = '<button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="document.location.reload(true)">Close</button>';
            makeModal('Move Comments',body,'lg', footer);
            $("#merge-detail").JSONView(Res);
        });
    }
});

// Move Emails
$(".users_merge #doA-moveEmails").click(async function(){
    const r = confirm("Do you want to move users Emails to selected user?");
    if (r === true) {
        let data = {
            mainUser: merged_user.users.id,
            users: selected_users
        }
        ajaxCall('users', 'moveEmails', data, function (Res) {
            $('#modalMain').modal({
                backdrop: 'static',
                keyboard: false
            });
            body = '<div id="merge-detail"></div>';
            footer = '<button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="document.location.reload(true)">Close</button>';
            makeModal('Move Emails',body,'lg', footer);
            $("#merge-detail").JSONView(Res);
        });
    }
});

// Move Logs
$(".users_merge #doA-moveLogs").click(async function(){
    const r = confirm("Do you want to move users Emails to selected user?");
    if (r === true) {
        let data = {
            mainUser: merged_user.users.id,
            users: selected_users
        }
        ajaxCall('users', 'moveLogs', data, function (Res) {
            $('#modalMain').modal({
                backdrop: 'static',
                keyboard: false
            });
            body = '<div id="merge-detail"></div>';
            footer = '<button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="document.location.reload(true)">Close</button>';
            makeModal('Move Logs',body,'lg', footer);
            $("#merge-detail").JSONView(Res);
        });
    }
});

// Move Sessions Archive
$(".users_merge #doA-sessionsArchive").click(async function(){
    const r = confirm("Do you want to move users sessions from archive to selected user?");
    if (r === true) {
        let data = {
            mainUser: merged_user.users.id,
            users: selected_users
        }
        ajaxCall('users', 'sessionsArchive', data, function (Res) {
            $('#modalMain').modal({
                backdrop: 'static',
                keyboard: false
            });
            body = '<div id="merge-detail"></div>';
            footer = '<button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="document.location.reload(true)">Close</button>';
            makeModal('Move sessions from archive',body,'lg', footer);
            $("#merge-detail").JSONView(Res);
        });
    }
});

// Remove from merge
$(".users_merge .doA-remove").click(function(){
    let id = $(this).data('id');
    const r = confirm("Do you want to remove user from merge list?");
    if (r === true) {
        ajaxCall('users', 'removeMerge', {"id":id}, function (Res) {
            window.location.replace("sys_settings.php?section=users_merge");
        });
    }
});

$(".users_merge .user-items tr.items").click(async function(){
    await selectItem(this);
});

async function selectItem(elem){
    let table = $(elem).data('table');
    let item  = $(elem).data('item');
    let uid   = $(elem).data('uid');
    $('tr.item-'+item).removeClass('alert-success');
    $(elem).addClass('alert-success');
    merged_user[table][item] = $('#v-'+uid+'-'+item).html();
}

$(document).ready(function () {
    $('.main-user-select').first().trigger('click');
    $('.main-user-select').each(function (){
        selected_users.push($(this).data('uid'));
    });
});

// Empty merge list
$(".users_merge .doA-empty").click(function(){
    let id = $(this).data('id');
    const r = confirm("Do you want to remove all users from merge list?");
    if (r === true) {
        ajaxCall('users', 'removeMergeAll', '', function (Res) {
            window.location.replace("sys_settings.php?section=users_merge");
        });
    }
});

// Duplicates
$('.users_duplicates  #duplicates_email').DataTable();
$('.users_duplicates  #duplicates_phone').DataTable();
$('.users_duplicates .form-select3').selectpicker({
    tickIcon: 'fas fa-check',
    liveSearch: true
});
$('.users_duplicates .do-clear-filters').on('click', function() {
    let target = '#'+$(this).data('target')
    let itar = '#'+$(this).data('itar')
    $(target).selectpicker('deselectAll');
    $(itar).val('');
});

$(".users_duplicates .do-filter").click(function(){
    const filter = {};
    filter.leads        = $('#leads').is(':checked');
    filter.trader       = $('#trader').is(':checked');
    filter.IB           = $('#IB').is(':checked');
    filter.skipArchived = $('#skip_archived').is(':checked');
    filter.units        = $('#filterUnit').val().toString();
    console.log(filter);
    let url = 'manager_panel.php?section=users_duplicates&'
    if(filter.leads)        url += 'leads&';
    if(filter.trader)       url += 'trader&';
    if(filter.IB)           url += 'IB&';
    if(filter.skipArchived) url += 'skip_archived&';
    if(filter.units.length > 0) url += `units=${filter.units}`;
    console.log(url);
    window.location.replace(url);
});

$(".users_duplicates .doM-d-l10").click(function(){
    let item = $(this).data('l10');
    console.log(item);
});




function updatePending() {
    const data = {
        login: selectedLogin
    }
    socket.emit("getLoginPending", data, (response) => {
        //console.log(response);
        if (response.e){
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
        }
        else{
            dtPending.clear();
            dtPending.rows.add(response.data).draw();
            $("form#trade-view-Pending #update-time").text(rDT().dateTime);
        }
    });
}

// Delete Order
$("body").on("click",".doA-delete-order", function(e) {
    e.preventDefault();
    const data = {
        login: selectedLogin,
        order: $(this).data('order'),
        type: $(this).data('type'),
        symbol:  $(this).data('symbol')
    }
    if (confirm(`Are you sure you want to delete order ${data.order}?`)) {
        appAlert('info','<i class="fas fa-spinner fa-spin"></i> In Progress', 'Deleting the order: '+data.order);
        socket.emit("deleteOrder", data, (response) => {
            updatePending();
            //console.log(response);
            if(response.e)
                appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            else{
                appAlert('success','<i class="fas fa-exclamation-triangle"></i> Done', 'Order "'+data.order+'" is deleted');
            }
        });
    } else {
        return;
    }
});


                                    
                                    // Expense Form Mixer
                                    $('body').on( 'click', 'form#expense select#type', function(){
                                        const type = $(this).val();
                                        if(type==='Agent Bonus'){
                                            $('form#expense .pre-payee').after('<select class="form-control pre-payee" id="payee" name="payee" required></select>').remove();
                                            ajaxCall ('global', 'selectorStaff', {}, function(response) {
                                                let resObj = JSON.parse(response);
                                                if (resObj.e) {
                                                    toastr.error(resObj.e);
                                                } else if (resObj.res) {
                                                    $('form#expense select#payee').html(resObj.res);
                                                    $('form#expense .payee-type').html("Agent");
                                                }
                                            });
                                            $('form#expense #o_type').prop("disabled",true).prop('required',false);
                                        }
                                        if(type==='IB Bonus'){
                                            $('form#expense .pre-payee').after('<select class="form-control pre-payee" id="payee" name="payee" required></select>').remove();
                                            ajaxCall ('global', 'selectorByType', {type:'IB'}, function(response) {
                                                let resObj = JSON.parse(response);
                                                if (resObj.e) {
                                                    toastr.error(resObj.e);
                                                } else if (resObj.res) {
                                                    $('form#expense select#payee').html(resObj.res);
                                                    $('form#expense .payee-type').html("IB");
                                                }
                                            });
                                            $('form#expense #o_type').prop("disabled",true).prop('required',false);
                                        }
                                        if(type==='Referral Bonus'){
                                            $('form#expense .pre-payee').after('<input type="text" class="form-control pre-payee" id="payee" name="payee" placeholder="email@example.com" required>').remove();
                                            $('form#expense .payee-type').html("User");
                                            $('form#expense #o_type').prop("disabled",true).prop('required',false);
                                        }
                                        if(type==='Exchange Fee'){
                                            $('form#expense .pre-payee').after('<input type="text" class="form-control pre-payee" id="payee" name="payee" placeholder="Name or Company" required>').remove();
                                            $('form#expense .payee-type').html("Name");
                                            $('form#expense #o_type').prop("disabled",true).prop('required',false);
                                        }
                                        if(type==='Others'){
                                            $('form#expense .pre-payee').after('<input type="text" class="form-control pre-payee" id="payee" name="payee" placeholder="Name or Company" required>').remove();
                                            $('form#expense .payee-type').html("Name");
                                            $('form#expense #o_type').prop("disabled",false).prop('required',true);
                                        }
                                    });
                                    // Expense Form Handler
                                    $("body").on("submit","form#expense", function(e) {
                                        e.preventDefault();
                                        let formData = new FormData(this);
                                        ajaxForm ('expenses', 'add', formData, function(response){
                                            let resObj = JSON.parse(response);
                                            if (resObj.e) {
                                                toastr.error(resObj.e);
                                            }
                                            if (resObj.res) {
                                                const user_id = '<?= $userID ?>';
                                                const startTime = '<?= $startTime ?>';
                                                const endTime = '<?= $endTime ?>';
                                                ajaxCall ('expenses', 'userTotal', {user_id:user_id,startTime:startTime,endTime:endTime}, function(response) {
                                                    let resObj = JSON.parse(response);
                                                    if (resObj.e) {
                                                        toastr.error(resObj.e);
                                                    } else if (resObj.res) {
                                                        $('.wrap-total-expense').html('$'+resObj.res)
                                                        toastr.success("New expense added.");
                                                        DT_user_expenses.ajax.reload();
                                                    }
                                                });

                                            }
                                        });
                                    });
                                    //  Delete expense
                                    $("body").on("click",".remove-expense", function(e) {
                                        e.preventDefault();
                                        const id = $(this).data('id');
                                        const r = confirm("Delete selected expense?");
                                        if (r === true) {
                                            ajaxCall ('expenses', 'delete', {expense_id:id}, function(response) {
                                                let resObj = JSON.parse(response);
                                                if (resObj.e) {
                                                    toastr.error(resObj.e);
                                                } else if (resObj.res) {
                                                    const user_id = '<?= $userID ?>';
                                                    const startTime = '<?= $startTime ?>';
                                                    const endTime = '<?= $endTime ?>';
                                                    ajaxCall ('expenses', 'userTotal', {user_id:user_id,startTime:startTime,endTime:endTime}, function(response) {
                                                        let resObj = JSON.parse(response);
                                                        if (resObj.e) {
                                                            toastr.error(resObj.e);
                                                        } else if (resObj.res) {
                                                            $('.wrap-total-expense').html('$'+resObj.res)
                                                            toastr.success("Expense deleted.");
                                                            DT_user_expenses.ajax.reload();
                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    });
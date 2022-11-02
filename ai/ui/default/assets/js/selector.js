function selectOption(listener, topic='') {
    if(listener==='CommandLogin') {
        commandLogin(topic);
    } else if(listener==='CommandLogout') {
        commandLogout();
    } else if(listener==='CommandRecover') {
        commandRecover(topic);
    } else if(listener==='CommandRegister') {
        commandRegister(topic);
    } else {
        alert('Not available yet!');
    }
    setTimeout(() => {$('#modalMain .modal-body input:first').focus()}, 500);
}

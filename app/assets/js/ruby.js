
/**
 * Open Modal
 */
$("body").on("click",".doM-show-ruby", function(e) {
    e.preventDefault();
    let mBody = `<iframe id="iframeModalWindow" height="99%" width="100%" src="ai/run/app-client" name="iframe_modal"></iframe>`;
    makeFrameModal('AI Assistance', mBody);
});
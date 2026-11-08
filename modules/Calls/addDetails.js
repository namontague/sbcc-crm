/**
 * Retrieves additional details dynamically
 */
function getAdditionalDetailsForCalls(bean, id) {

    go = function() {
        oReturn = function(body, caption, width, theme) {
            var old_div = $("#addDetails");
            if (old_div.length) {
                // If div already exists
                old_div.html(body);
            } else {
                // create new div and append to td
                var parent_cell = $('#parent_id').parent("td");
                parent_cell.append('<div id="addDetails" style="padding: 5px 0 5px 0px;">'+body+'</div>');
            }
        }

        success = function(data) {
            if (data.responseText == "bad data") {
                //ajaxStatus.hideStatus();
                return;
            }
            eval(data.responseText);

            SUGAR.util.additionalDetailsCache[id] = new Array();
            SUGAR.util.additionalDetailsCache[id]['body'] = result['body'];
            SUGAR.util.additionalDetailsCache[id]['caption'] = result['caption'];
            SUGAR.util.additionalDetailsCache[id]['width'] = result['width'];
            SUGAR.util.additionalDetailsCache[id]['theme'] = result['theme'];
            //ajaxStatus.hideStatus();
            return oReturn(SUGAR.util.additionalDetailsCache[id]['body'], SUGAR.util.additionalDetailsCache[id]['caption'], SUGAR.util.additionalDetailsCache[id]['width'], SUGAR.util.additionalDetailsCache[id]['theme']);
        }

        if(typeof SUGAR.util.additionalDetailsCache[id] != 'undefined')
            return oReturn(SUGAR.util.additionalDetailsCache[id]['body'], SUGAR.util.additionalDetailsCache[id]['caption'], SUGAR.util.additionalDetailsCache[id]['width'], SUGAR.util.additionalDetailsCache[id]['theme']);

        if(typeof SUGAR.util.additionalDetailsCalls[id] != 'undefined') // call already in progress
            return;

        //ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING'));
        url = 'index.php?to_pdf=1&module=Home&action=AdditionalDetailsRetrieve&forcalls=1&bean=' + bean + '&id=' + id;

        SUGAR.util.additionalDetailsCalls[id] = YAHOO.util.Connect.asyncRequest('GET', url, {success: success, failure: success});

        return false;
    }
    SUGAR.util.additionalDetailsRpcCall = window.setTimeout('go()', 250);
}

/**
 * This function is fired when the parent field is changed
 */
function updateParentDetails() {
    
    var module = document.getElementById("parent_type").value;
    var id = document.getElementById("parent_id").value;

    if (id.length) {
        getAdditionalDetailsForCalls(module, id);
    } else {
        // If the id is empty
        var old_div = $("#addDetails");            
        if (old_div.length) {
            // Clear div
            old_div.html("");
        }
    }

}


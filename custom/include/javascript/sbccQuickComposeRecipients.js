/*
 * Used by SugarWidgetSubPanelTopComposeEmailButton_SBCC.
 * Opens the quick compose window and adds a button beside the To field that
 * retrieves the email addresses of the subpanel's records on demand.
 */
var SBCCQuickCompose = {

    /**
     * @param {Object} options quick compose options (compose package and full return url)
     * @param {Object} ctx parent_type, parent_id, linked_field, label
     */
    open: function(options, ctx) {
        SUGAR.quickCompose.init(options);
        //the compose window is built asynchronously the first time it is opened
        SUGAR.util.doWhen(
            "document.getElementById('move_to_bcc_span0') && !document.getElementById('sbcc_load_addresses0')",
            function() { SBCCQuickCompose.addButton(ctx); }
        );
    },

    addButton: function(ctx) {
        var button = document.createElement('button');
        button.id = 'sbcc_load_addresses0';
        button.className = 'button';
        button.type = 'button';
        button.style.marginLeft = '9px';
        button.innerHTML = ctx.label;

        var status = document.createElement('span');
        status.id = 'sbcc_load_addresses_status0';
        status.style.paddingLeft = '9px';

        var span = document.getElementById('move_to_bcc_span0');
        YAHOO.util.Dom.insertAfter(button, span);
        YAHOO.util.Dom.insertAfter(status, button);

        button.onclick = function() { SBCCQuickCompose.load(ctx, button, status); };
    },

    load: function(ctx, button, status) {
        button.disabled = true;
        status.innerHTML = SUGAR.language.get('app_strings', 'LBL_LOADING');

        var url = 'index.php?entryPoint=sbccSubpanelEmailAddresses&to_pdf=1' +
            '&parent_type=' + encodeURIComponent(ctx.parent_type) +
            '&parent_id=' + encodeURIComponent(ctx.parent_id) +
            '&linked_field=' + encodeURIComponent(ctx.linked_field);

        var failed = function(message) {
            button.disabled = false;
            status.innerHTML = message || 'Unable to retrieve the email addresses.';
        };

        YAHOO.util.Connect.asyncRequest('GET', url, {
            success: function(o) {
                var data;
                try {
                    data = YAHOO.lang.JSON.parse(o.responseText);
                } catch (e) {
                    return failed();
                }
                if (data.error) {
                    return failed(data.error);
                }

                var to = document.getElementById('addressTO0');
                if (data.addresses) {
                    var current = to.value.replace(/^\s+|[\s;]+$/g, '');
                    to.value = (current ? current + '; ' : '') + data.addresses;
                    SUGAR.email2.composeLayout.showAddressDetails(to);
                }
                status.innerHTML = data.count + ' of ' + data.total + ' added';
            },
            failure: function() { failed(); }
        }, null);
    }
};

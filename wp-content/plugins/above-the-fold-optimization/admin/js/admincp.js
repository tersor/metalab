jQuery(function() {
    if (jQuery('#fullcsspages').length > 0 && typeof jQuery('#fullcsspages').selectize !== 'undefined') {
        jQuery('#fullcsspages').selectize({
            persist         : true,
            placeholder     : "Select a page...",
            plugins         : ['remove_button']
        });
    }

    if (jQuery('#abtfcss').length > 0 && parseInt(jQuery('#abtfcss').data('advanced')) === 1) {
        jQuery(function () {
            window.abtfcss = CodeMirror.fromTextArea(
                jQuery('#abtfcss')[0], {
                lineWrapping: true,
                lineNumbers: true,
                gutters: ["CodeMirror-lint-markers"],
                lint: true
            });

            window.abtfResize = function() {

                var d = jQuery('.CodeMirror').closest('.inside').outerWidth();
                var td = jQuery('.CodeMirror').closest('td').outerWidth();

                var w = (td > d) ? (d - 25) : td;

                jQuery('.CodeMirror').css({width: w + 'px'});
                window.abtfcss.refresh();
            };

            window.abtfResize();

            jQuery( window ).resize(function() {
                window.abtfResize();
            });

            window.abtfcssToggle = function(obj) {
                if (jQuery('.CodeMirror').hasClass('large')) {
                    jQuery(obj).html('[+] Large Editor');
                } else {
                    jQuery(obj).html('[-] Small Editor');
                }

                jQuery('.CodeMirror').toggleClass('large');
                window.abtfcss.refresh();
            };
        });
    }
});
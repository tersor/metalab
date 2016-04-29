/**
 * Above the fold optimization Javascript
 *
 * This javascript handles the CSS delivery optimization.
 *
 * @package    abovethefold
 * @subpackage abovethefold/public
 * @author     Optimalisatie.nl <info@optimalisatie.nl>
 */

window['abovethefold'] = {

    debug: false,
    cnf: {},

    /**
     * Set configuration
     *
     * @param cnf
     */
    config: function(cnf) {
        this.cnf = cnf;
    },

    /**
     * Load CSS asynchronicly
     *
     * @link https://developers.google.com/speed/docs/insights/OptimizeCSSDelivery
     */
    css: function() {

        var m;
        var files = this.cnf.css;

        if (this.debug) {
            if (!files) {
                return;
            } else {
                console.log('abovethefold.css()', files);
            }
        }
        if (!files) {
            return;
        }

        for (i in files) {
            if (typeof files[i] !== 'object') {
                if (this.debug) {
                    console.error('abovethefold.css()','Invalid CSS file configuration',i,files);
                }
                continue;
            }
            m = files[i][0].join(',');
            this.loadCSS(files[i][1],document.getElementById('atfcss'),m);
        }
    },

    /**
     * DomReady
     */
    ready: function(a, b, c) {
        b = document;
        c = 'addEventListener';
        b[c] ? b[c]('DocumentContentLoaded', a) : window.attachEvent('onload', a);
    }
};
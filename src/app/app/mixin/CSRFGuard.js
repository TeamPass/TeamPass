/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to version 3 of the GPL license,
 * that is bundled with this package in the file LICENSE, and is
 * available online at http://www.gnu.org/licenses/gpl.txt
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

Ext.define('TeamPass.mixin.CSRFGuard', {

    mixins: {
        aesEncrypt: 'TeamPass.mixin.Encryption',
        getIdentifierCookie: 'TeamPass.mixin.Session'
    },

    setCSRFToken: function (store) {

        var headers = this.getCSRFTokenHeaderObject();

        store.getProxy().setHeaders(headers);
    },

    getCSRFTokenHeaderObject: function() {
        token = this.generateToken();

        var headers = {};
        headers[TeamPass.Util.Settings.get('CSRFTokenHeader')] = token;

        return headers;
    },

    generateToken: function() {
        var randomKey = this.generateRandomKey(10);

        itentifier = this.getIdentifierCookie();

        return btoa(this.aesEncrypt(randomKey, itentifier));
    },

    generateRandomKey: function(length) {
        return Math.round((Math.pow(36, length + 1) - Math.random() * Math.pow(36, length))).toString(36).slice(1);
    }

});

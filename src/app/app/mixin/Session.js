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

Ext.define('TeamPass.mixin.Session', {

    /**
     * flushes the session storage
     */
    destroySessionData: function () {
        sessionStorage.removeItem('data');
    },

    /**
     * Returns the session data value for given key
     *
     * @param {string} key the data key
     *
     * @returns {*}
     */
    getSessionDataValue: function (key) {
        try {
            var userData = sessionStorage.getItem('data');

            if (!userData) {
                return null;
            }
            var identifier = Ext.util.Cookies.get(TeamPass.Util.Settings.get("IdentifierCookie"));

            if (!identifier) {
                //this.destroySessionData();
                return null;
            }

            userData = GibberishAES.dec(userData, identifier);
            userData = JSON.parse(userData);

            if (userData[key]) {
                return userData[key];
            } else {
                return null;
            }

        } catch (err) {
            // usually a exception is thrown by GibberishAES if the session storage data could not be decrypted
            // by the identifier cookie
            console.log(err);
            console.log("ERROR: sessionStorage invalid -> application shutdown will be initiated");
            this.application.fireEvent("appShutdown");
        }
    },

    /**
     * Sets the initial session data
     *
     * @param {array} data the session data
     *
     * @return void
     */
    persistSessionData: function (data) {

        var userData = {
            userName: data.userName,
            fullName: data.fullName,
            userId: data.userId,
            isAdmin: data.isAdmin,
            pollInterval: data.pollInterval,
            isLocal: data.isLocal,
            presetLanguage: data.presetLanguage
        };

        jsonResult = JSON.stringify(userData);
        var identifier = Ext.util.Cookies.get(TeamPass.Util.Settings.get("IdentifierCookie"));

        userData = GibberishAES.enc(jsonResult, identifier);
        sessionStorage.setItem('data', userData);

        // set the session id value so we can validate that this browser window (tab) has got a valid session
        sessionStorage.setItem('session', identifier);
    },

    /**
     * checks if the identifier cookie has changed
     *
     * @returns void
     */
    checkIdentifier: function() {
        let storedIdentifier = sessionStorage.getItem('session');
        let identifierCookie = Ext.util.Cookies.get(TeamPass.Util.Settings.get("IdentifierCookie"));

        if (storedIdentifier !== identifierCookie) {
            console.log("identifierCookie has changed - appShutdown initiated!");
            this.application.fireEvent("appShutdown");
        }
    },

    /**
     * Updates the session data
     *
     * @param {array} data the session data
     *
     * @returns void
     */
    setSessionDataValue: function (data) {
        var userData = sessionStorage.getItem('data');
        var identifier = Ext.util.Cookies.get(TeamPass.Util.Settings.get("IdentifierCookie"));

        userData = GibberishAES.dec(userData, identifier);
        userData = JSON.parse(userData);
        for (var key in data) {
            userData[key] = data[key];
        }

        jsonResult = JSON.stringify(userData);
        userData = GibberishAES.enc(jsonResult, identifier);

        sessionStorage.setItem('data', userData);
    },

    /**
     * destroys the session cookie
     */
    destroyCookies: function () {
        this.destroySessionCookie();
        this.destroyIdentifierCookie();
    },

    /**
     * destroys the session cookie
     */
    destroySessionCookie: function () {
        Ext.util.Cookies.clear(this.getSessionId());
    },

    /**
     * destroys the identifier cookie
     */
    destroyIdentifierCookie: function () {
        Ext.util.Cookies.clear(TeamPass.Util.Settings.get("IdentifierCookie"));
    },

    /**
     * Returns the identifier cookie value
     *
     * @returns {Object}
     */
    getIdentifierCookie: function() {
        return Ext.util.Cookies.get(TeamPass.Util.Settings.get("IdentifierCookie"));
    }
});

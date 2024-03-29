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

Ext.define('TeamPass.Util.Settings', {

    singleton: true,

    config: {
        resource_path: "/resources",
        base_path: "/app",
        CSRFTokenHeader: "X-CSRF-Token",
        IdentifierCookie: "identifier",
        defaultLocale: "en",
        LdapDefaultPort: 389,
        LdapDefaultSSLPort: 636
    },

    constructor: function(config) {
        this.initConfig(config);

        return this;
    },

    get: function(key) {
        return this.getConfig(key);
    }
});

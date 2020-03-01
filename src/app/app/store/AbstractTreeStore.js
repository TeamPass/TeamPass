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

Ext.define('TeamPass.store.AbstractTreeStore', {
    extend: 'Ext.data.TreeStore',

    requires: [
        'TeamPass.view.notifications.ErrorNotification'
    ],
    constructor : function(config) {
        this.callParent([config]);
        this.proxy.on('exception', this.onProxyException, this);
    },
    onProxyException: function(proxy, response, operation, eOpts) {
        response = Ext.JSON.decode(response.responseText);
        var message = "<no message>";
        if (typeof(response.message) !== "undefinied") {
            message = response.message;
        }

        Ext.create('widget.ErrorNotification',{title: 'Error',html: message}).show();
        TeamPass.app.fireEvent("checkLoginState", response);
    }
});

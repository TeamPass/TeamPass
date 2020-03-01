/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to version 3 of the GPL license,
 * that is bundled with this package in the file LICENSE, and is
 * available online at http://www.gnu.org/licenses/gpl.txt
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2019 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

Ext.define('Ext.state.SessionStorageProvider', {
    extend: 'Ext.state.Provider',
    requires: [
        'Ext.util.LocalStorage'
    ],
    alias: 'state.sessionstorage',

    constructor: function () {
        var me = this;

        me.callParent(arguments);

        me.store = me.getStorageObject();
        if (me.store) {

            me.state = me.readLocalStorage();
        } else {
            me.state = {};
        }
    },

    readLocalStorage: function () {
        var store = this.store,
            data = {},
            keys = store.getKeys(),
            i = keys.length,
            key;

        while (i--) {
            key = keys[i];
            data[key] = this.decodeValue(store.getItem(key));
        }
        return data;
    },

    set: function (name, value) {
        var me = this;

        me.clear(name);
        if (value != null) { // !== undefined && !== null
            me.store.setItem(name, me.encodeValue(value));
            me.callParent(arguments);
        }
    },

    // private
    clear: function (name) {
        this.store.removeItem(name);
        this.callParent(arguments);
    },

    getStorageObject: function () {
        var prefix = this.prefix,
            id = prefix,
            n = id.length - 1;

        if (id.charAt(n) === '-') {
            id = id.substring(0, n);
        }

        return new Ext.util.LocalStorage({
            id: id,
            prefix: prefix,
            session: true
        });
    }
});
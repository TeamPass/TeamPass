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

Ext.define('TeamPass.store.Admin.Permissions', {
    extend: 'TeamPass.store.AbstractDataStore',

    requires: 'TeamPass.model.Admin.Permission',
    model: 'TeamPass.model.Admin.Permission',

    autoLoad: false,
    autoSync: true,

    proxy: {
        type: 'rest',
        url: '/api/v1/admin/permission',
        reader: {
            type: 'json'
        },
        writer: {
            type: 'json',
            writeAllFields: true,
            transform: {
                fn: function(data, request) {
                    return { permission: data };
                },
                scope: this
            }
        }
    },
    remoteSort: false,
    remoteFilter: false
});


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

Ext.define('TeamPass.store.AdminTree', {
    extend: 'Ext.data.TreeStore',

    requires: [
        'TeamPass.model.AdminTree'
    ],

    model: 'TeamPass.model.AdminTree',
    autoLoad: false,
    autoSync: false,
    children: {
        expanded: true,
        text : "Administration"
    },
    root: {
        expanded: true,
        loaded:true
    },
    proxy: {
        type: 'rest',
        url: '/api/v1/admin',
        reader: {
            type: 'json',
            rootProperty: 'children'
        }
    }
});



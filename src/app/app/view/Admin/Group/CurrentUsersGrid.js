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

Ext.define('TeamPass.view.Admin.Group.CurrentUsersGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.admingroupcurrentusersgrid',

    requires:[
        'TeamPass.store.Admin.GroupCurrentUsersGrid'
    ],
    plugins: [{
        ptype: 'filterfield'
    }],
    store: 'Admin.GroupCurrentUsersGrid',
    border:true,
    height: "100%",
    padding: '20 40 20 0',
    columns: [{
        text:  TeamPass.Locales.gettext("ADMIN.GROUP_CURRENT_USERS_TEXT"),
        dataIndex: 'displayName',
        flex:1,
        margin:0,
        menuDisabled: true,
        filter: {
            xtype: 'textfield',
            emptyText: "Search..."
        }
    }],
    viewConfig: {
        plugins: {
            ptype: 'gridviewdragdrop',
            dragGroup: 'current',
            dropGroup: 'available'
        },
        markDirty:false
    },
    initComponent: function() {
        this.callParent();
    }
});

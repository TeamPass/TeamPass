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

Ext.define('TeamPass.view.Admin.Group.AvailableUsersGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.admingroupavailableusersgrid',

    requires:[
        'TeamPass.store.Admin.GroupAvailableUsersGrid'
    ],
    plugins: [{
        ptype: 'filterfield'
    }],
    store: 'Admin.GroupAvailableUsersGrid',
    height: "100%",
    border:true,
    padding: '20 40 20 40',
    columns: [{
        text:  TeamPass.Locales.gettext("ADMIN.GROUP_AVAILABLE_USERS_TEXT"),
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
            dragGroup: 'available',
            dropGroup: 'current'
        },
        markDirty:false
    },
    initComponent: function() {
        this.callParent();
    }
});

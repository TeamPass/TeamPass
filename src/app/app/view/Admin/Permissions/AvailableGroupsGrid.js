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

Ext.define('TeamPass.view.Admin.Permissions.AvailableGroupsGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.adminpermissionsavailablegroupsgrid',

    plugins: [{
        ptype: 'filterfield'
    }],
    store: 'Admin.PermissionGroup',
    height: "50%",
    border: true,
    columns: [{
        text:  TeamPass.Locales.gettext("ADMIN.PERMISSION_AVAILABLE_GROUPS_TEXT"),
        dataIndex: 'groupName',
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
            dragGroup: 'permissiongroups',
        }
    },
    initComponent: function() {
        this.callParent();
    }
});

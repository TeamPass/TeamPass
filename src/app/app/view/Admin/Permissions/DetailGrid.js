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

Ext.define('TeamPass.view.Admin.Permissions.DetailGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.adminpermissionsdetailgrid',

    store: 'Admin.Permissions',
    border:true,
    region:'center',
    columns: [{
        text:  TeamPass.Locales.gettext("ADMIN.PERMISSION_GRID_TEXT"),
        dataIndex: 'groupName',
        flex: 1,
        menuDisabled: true,
        margin:0
    }, {
        xtype:'checkcolumn',
        text:  TeamPass.Locales.gettext("ADMIN.PERMISSION_GRID_READ"),
        dataIndex: 'pRead',
        flex: 1,
        menuDisabled: true,
        margin:0
    }, {
        xtype:'checkcolumn',
        text:  TeamPass.Locales.gettext("ADMIN.PERMISSION_GRID_CREATE"),
        dataIndex: 'pCreate',
        flex: 1,
        menuDisabled: true,
        margin:0
    }, {
        xtype:'checkcolumn',
        text:  TeamPass.Locales.gettext("ADMIN.PERMISSION_GRID_UPDATE"),
        dataIndex: 'pUpdate',
        flex: 1,
        menuDisabled: true,
        margin:0
    }, {
        xtype:'checkcolumn',
        text:  TeamPass.Locales.gettext("ADMIN.PERMISSION_GRID_DELETE"),
        dataIndex: 'pDelete',
        flex: 1,
        menuDisabled: true,
        margin:0
    },{
        xtype:'checkcolumn',
        text:  TeamPass.Locales.gettext("ADMIN.PERMISSION_GRID_INHERITED"),
        dataIndex: 'inherited',
        flex: 1,
        disabled: true,
        menuDisabled: true,
        margin:0
    }, {
        xtype: 'actioncolumn',
        text: TeamPass.Locales.gettext("ADMIN.PERMISSION_GRID_ACTION_TEXT"),
        flex: 1,
        align: 'center',
        items: [
            {
                iconCls: 'x-fa fa-trash-o',
                tooltip: TeamPass.Locales.gettext("ADMIN.PERMISSION_GRID_DELETE_TOOLTIP"),
                handler: function (grid, rowIndex, colIndex) {
                    var record = grid.getStore().getAt(rowIndex);
                    if (!record) {
                        return;
                    }

                    var msgbox = Ext.Msg.confirm(TeamPass.Locales.gettext(
                        "ADMIN.PERMISSION_DELETE_ENTRY_CONFIRMATION_TITLE"),
                        TeamPass.Locales.gettext("ADMIN.PERMISSION_DELETE_ENTRY_CONFIRMATION_TEXT"),
                        function(btn) {
                            if (btn == 'yes') {
                                grid.getStore().remove(record);
                            }
                        }, this);
                }
            }],
    }],
    viewConfig: {
        plugins: {
            ptype: 'gridviewdragdrop',
            enableDrag: false,
            dropGroup: 'permissiongroups'
        },
        listeners: {
            beforedrop: function(node, data, dropRec, dropPosition) {
                var rec = data.records[0];
                var newRec = new TeamPass.model.Admin.Permission();
                newRec.set("groupName",rec.get("groupName"));
                newRec.set("gteId",rec.get("gteId"));
                newRec.set("userGroupId",rec.get("userGroupId"));
                data.records[0] = newRec;
            }
        }
    },
    initComponent: function() {
        this.callParent();
    }
});

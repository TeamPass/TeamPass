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

Ext.define('TeamPass.view.Admin.Group.Grid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.admingroupgrid',


    requires:[
        'Ext.ux.filters.OperatorButton'
    ],


    plugins: [{
        ptype: 'filterfield'
    }],

    store: 'Admin.GroupGrid',
    border:true,
    deferRowRender: false,
    region:'center',
    columns: [{
        text:  TeamPass.Locales.gettext("ADMIN.GROUP_GRID_NAME_TEXT"),
        dataIndex: 'groupName',
        flex: 1,
        menuDisabled: true,
        margin:0,
        filter: {
            xtype: 'textfield'
        }
    }, {
        xtype: 'actioncolumn',
        header: TeamPass.Locales.gettext("ADMIN.GROUP_GRID_ISADMIN_TEXT"),
        dataIndex: 'isAdmin',
        flex: 1,
        stopSelection: false,
        disableSelection: true,
        menuDisabled: true,
        align: 'center',
        getClass: function (value, metadata, record) {
            if (record.get('isAdmin') == true) {
                return 'x-fa fa-check';
                //return 'x-fa fa-check-square-o';
            } else {
                return '';
                return 'x-fa fa-minus-square-o';
            }
        },
        getTip: function(value, metadata, record, row, col, store) {
            if (record.get('isAdmin') == true) {
                return TeamPass.Locales.gettext("ADMIN.GROUP_GRID_ISADMIN_TRUE_TOOLTIP");
            } else {
                return TeamPass.Locales.gettext("ADMIN.GROUP_GRID_ISADMIN_FALSE_TOOLTIP");
            }
        }
    }, {
        xtype: 'actioncolumn',
        width: 50,
        menuDisabled: true,
        items: [{
            iconCls: 'x-fa fa-times',
            tooltip:  TeamPass.Locales.gettext("ADMIN.GROUP_GRID_DELETE_TOOLTIP"),
            handler: function (grid, rowIndex, colIndex) {
                var record = grid.getStore().getAt(rowIndex);
                if (!record) {
                    return;
                }
                this.fireEvent("deleteGroup", record);
            }
        }]
    }],
    dockedItems: [{
        xtype: 'toolbar',
        dock: 'top',
        items: [{
            xtype: 'button',
            itemId: 'newGroupBtn',
            iconCls: 'x-fa fa-plus',
            text:  TeamPass.Locales.gettext("ADMIN.GROUP_GRID_NEW_BTN")
        }]
    }],
    initComponent: function() {
        this.callParent();
    }
});

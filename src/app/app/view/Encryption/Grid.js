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

Ext.define('TeamPass.view.Encryption.Grid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.encryption.grid',

    requires:[
        'TeamPass.store.Encryption.Grid'
    ],
    border:false,
    deferRowRender: false,
    store:'Encryption.Grid',
    hideHeaders: true,
    columns: [{
        dataIndex: 'fullName',
        flex:1
    }, {
        xtype: 'actioncolumn',
        header: 'encrypt',
        width: 50,
        align: 'center',
        items: [{
            iconCls: 'x-fa fa-lock',
            tooltip:  TeamPass.Locales.gettext("ENCRYPTION.START_TASK_TOOLTIP"),
            handler: function (grid, rowIndex, colIndex) {
                var record = grid.getStore().getAt(rowIndex);
                if (!record) {
                    return;
                }
                TeamPass.app.fireEvent("initEncryptionForWorkQueueUser", record);
            },
            scope : this
        }]
    }, {
        xtype : 'actioncolumn',
        header : 'delete',
        width : 50,
        align : 'center',
        items : [{
            iconCls: 'x-fa fa-times',
            tooltip:  TeamPass.Locales.gettext("ENCRYPTION.DELETE_TASK_TOOLTIP"),
            handler: function (grid, rowIndex, colIndex) {
                var record = grid.getStore().getAt(rowIndex);

                if (!record) {
                    return;
                }

                TeamPass.app.fireEvent("deleteUserFromWorkQueue", record);
            },
            scope : this
        }]
    }],
    initComponent: function(){
        Ext.apply(this, {
        });
        this.callParent(arguments);
    }
});

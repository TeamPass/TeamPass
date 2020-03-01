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

Ext.define('TeamPass.view.Admin.Settings.Grid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.adminsettingsgrid',

    requires:[
        'TeamPass.view.CheckColumn',
        'TeamPass.store.Admin.SettingGrid'
    ],

    border:false,
    deferRowRender: false,
    region:'center',

    store:'Admin.SettingGrid',
    initComponent: function() {
        Ext.apply(this, {
                border:false,
                columns: [{
                    text: TeamPass.Locales.gettext("ADMIN.SETTINGS_GRID_KEY_TEXT"),
                    dataIndex: 'settingName',
                    readOnly: true,
                    menuDisabled: true,
                    flex: 1,
                    filter: {
                        xtype: 'textfield'
                    }
                }, {
                    text: TeamPass.Locales.gettext("ADMIN.SETTINGS_GRID_DEFAULT_VALUE_TEXT"),
                    dataIndex: 'defaultValue',
                    readOnly: true,
                    menuDisabled: true,
                    flex: 1,
                    filter: {
                        xtype: 'textfield'
                    }
                }, {
                    text: TeamPass.Locales.gettext("ADMIN.SETTINGS_GRID_CUSTOM_VALUE_TEXT"),
                    dataIndex: 'customValue',
                    menuDisabled: true,
                    flex: 1,
                    filter: {
                        xtype: 'textfield'
                    },
                    editor: {
                        xtype: 'textfield',
                        allowBlank: true
                    }
                }],
                selType: 'cellmodel',
                plugins: [
                    {
                        ptype: 'cellediting',
                        clicksToEdit: 1
                    }, {
                        ptype: 'filterfield'
                    }
                ],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [{
                        xtype: 'button',
                        itemId: 'saveBtn',
                        iconCls: 'x-fa fa-floppy-o',
                        text: TeamPass.Locales.gettext("ADMIN.SETTINGS_GRID_SAVE_BTN")
                    }]
                }]
        });
        this.callParent();
    }
});

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

Ext.define('TeamPass.view.Admin.User.Grid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.adminusergrid',

    requires:[
        'TeamPass.view.CheckColumn',
        'TeamPass.store.Admin.UserGrid',
        'TeamPass.view.Admin.SetupCompletedCheckColumn'
    ],

    plugins: [{
        ptype: 'filterfield'
    }],

    deferRowRender: false,
    region:'center',
    margin: '0',

    store:'Admin.UserGrid',
    initComponent: function() {
        Ext.apply(this, {

                border:true,
                columns: [{
                    text: TeamPass.Locales.gettext("ADMIN.USER_GRID_USERNAME_TEXT"),
                    dataIndex: 'userName',
                    menuDisabled: true,
                    flex: 2,
                    filter: {
                        xtype: 'textfield'
                    },
                    getClass: function (value, metadata, record) {
                        if (record.get('deleted') == true) {
                            return 'strike-through-row';
                        } else {
                            return '';
                        }
                    }
                }, {
                    text: TeamPass.Locales.gettext("ADMIN.USER_GRID_FULLNAME_TEXT"),
                    dataIndex: 'fullName',
                    menuDisabled: true,
                    flex: 2,
                    filter: {
                        xtype: 'textfield'
                    }
                }, {
                    text: TeamPass.Locales.gettext("ADMIN.USER_GRID_EMAIL_TEXT"),
                    dataIndex: 'emailAddress',
                    menuDisabled: true,
                    flex: 2,
                    filter: {
                        xtype: 'textfield'
                    }
                }, {
                    text: TeamPass.Locales.gettext("ADMIN.USER_GRID_GROUPS_TEXT"),
                    dataIndex: 'groups',
                    menuDisabled: true,
                    flex: 3,
                    filter: {
                        xtype: 'textfield'
                    }
                }, {
                    text: TeamPass.Locales.gettext("ADMIN.USER_GRID_DIRECTORY_TEXT"),
                    dataIndex: 'directoryName',
                    menuDisabled: true,
                    flex: 2,
                    filter: {
                        xtype: 'textfield'
                    }
                }, {
                    xtype: 'actioncolumn',
                    header: TeamPass.Locales.gettext("ADMIN.USER_GRID_STATUS_TEXT"),
                    dataIndex: 'enabled',
                    flex: 1,
                    stopSelection: false,
                    disableSelection: true,
                    menuDisabled: true,
                    align: 'center',
                    getClass: function (value, metadata, record) {
                        if (record.get('enabled') == true) {
                            return 'x-fa fa-check';
                        } else {
                            return '';
                        }
                    },
                    getTip: function(value, metadata, record, row, col, store) {
                        if (record.get('enabled') == true) {
                            return TeamPass.Locales.gettext("ADMIN.USER_GRID_ENABLED_TRUE_TOOLTIP");
                        } else {
                            return TeamPass.Locales.gettext("ADMIN.USER_GRID_ENABLED_FALSE_TOOLTIP");
                        }
                    }
                }, {
                    xtype: 'actioncolumn',
                    header: TeamPass.Locales.gettext("ADMIN.USER_GRID_SETUP_COMPLETED_TEXT"),
                    dataIndex: 'setupCompleted',
                    flex: 2,
                    stopSelection: false,
                    disableSelection: true,
                    menuDisabled: true,
                    align: 'center',
                    getClass: function (value, metadata, record) {
                        if (record.get('setupCompleted') == true) {
                            return 'x-fa fa-check';
                        } else {
                            return '';
                        }
                    },
                    getTip: function(value, metadata, record, row, col, store) {
                        if (record.get('setupCompleted') == true) {
                            return TeamPass.Locales.gettext("ADMIN.USER_GRID_SETUP_COMPLETED_TRUE_TOOLTIP");
                        } else {
                            return TeamPass.Locales.gettext("ADMIN.USER_GRID_SETUP_COMPLETED_FALSE_TOOLTIP");
                        }
                    }
                }, {
                    xtype: 'actioncolumn',
                    header: TeamPass.Locales.gettext("ADMIN.USER_GRID_ACTIONS_TEXT"),
                    flex: 1,
                    align: 'center',
                    stopSelection: false,
                    disableSelection: true,
                    menuDisabled: true,
                    items: [{
                        iconCls: 'x-fa fa-map-marker ',
                        tooltip: TeamPass.Locales.gettext("ADMIN.USER_GRID_ADD_TO_WORK_QUEUE_TOOLTIP"),
                        handler: function (grid, rowIndex, colIndex) {
                            var record = grid.getStore().getAt(rowIndex);
                            if (!record) {
                                return;
                            }
                            this.fireEvent("addUserToWorkQueue", record);
                        }
                    },{
                        iconCls: 'x-fa fa-times',
                        //icon: TeamPass.Util.Settings.resource_path + '/cross.png',
                        tooltip: TeamPass.Locales.gettext("ADMIN.USER_GRID_DELETE_TOOLTIP"),
                        handler: function (grid, rowIndex, colIndex) {
                            var record = grid.getStore().getAt(rowIndex);
                            if (!record) {
                                return;
                            }
                            this.fireEvent("deleteUser", record);
                        }
                    }]
                }],
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    xtype: 'button',
                    itemId: 'newUserBtn',
                    iconCls: 'x-fa fa-plus',
                    text: TeamPass.Locales.gettext("ADMIN.USER_GRID_NEW_BTN")
                }, {
                    xtype: 'button',
                    itemId: 'saveBtn',
                    iconCls: 'x-fa fa-floppy-o',
                    text: TeamPass.Locales.gettext("ADMIN.USER_GRID_SAVE_BTN")
                }]
            }],
            viewConfig: {
              getRowClass: function(record) {
                  if (record.get('deleted')) {
                      return 'strike-through-row';
                  }
              }
            }
        });
        this.callParent();
    }
});

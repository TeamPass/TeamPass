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

Ext.define('TeamPass.view.Admin.Directory.Grid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.admindirectorygrid',

    requires:[
        'TeamPass.view.CheckColumn',
        'TeamPass.view.Admin.Directory.EditDirectoryComboBox',
        'TeamPass.store.Admin.DirectoryGrid'
    ],
    initComponent: function() {
        Ext.apply(this, {
            store: 'Admin.DirectoryGrid',
            region: 'center',
            border: false,
            disableSelection: true,
            columns: [{
                text:  TeamPass.Locales.gettext("ADMIN.DIRECTORY_GRID_NAME_TEXT"),
                dataIndex: 'directoryName',
                flex: 2
            }, {
                text:  TeamPass.Locales.gettext("ADMIN.DIRECTORY_GRID_TYPE_TEXT"),
                dataIndex: 'type',
                flex: 2
            }, {
                text:  TeamPass.Locales.gettext("ADMIN.DIRECTORY_GRID_ADAPTER_TEXT"),
                dataIndex: 'adapter',
                flex: 2
            }, {
                text:  TeamPass.Locales.gettext("ADMIN.DIRECTORY_GRID_SORTING_TEXT"),
                xtype: 'actioncolumn',
                align: 'center',
                flex:1,
                items: [{
                    iconCls: 'x-fa fa-arrow-up',
                    tooltip:  TeamPass.Locales.gettext("ADMIN.DIRECTORY_GRID_UP_TOOLTIP"),
                    handler: function (grid, rowIndex, colIndex) {
                        var direction = -1;
                        var record = grid.getStore().getAt(rowIndex);

                        if (!record) {
                            return;
                        }
                        var index = grid.getStore().indexOf(record);
                        if (direction < 0) {
                            index--;
                            if (index < 0) {
                                return;
                            }
                        } else {
                            index++;
                            if (index >= grid.getStore().getCount()) {
                                return;
                            }
                        }
                        this.fireEvent("ChangeDirectoryIndex", record, "up");
                    }
                }, {
                    iconCls: 'x-fa fa-arrow-down',
                    tooltip:  TeamPass.Locales.gettext("ADMIN.DIRECTORY_GRID_DOWN_TOOLTIP"),
                    handler: function (grid, rowIndex, colIndex) {
                        var direction = 1;
                        var record = grid.getStore().getAt(rowIndex);

                        if (!record) {
                            return;
                        }
                        var index = grid.getStore().indexOf(record);
                        if (direction < 0) {
                            index--;
                            if (index < 0) {
                                return;
                            }
                        } else {
                            index++;
                            if (index >= grid.getStore().getCount()) {
                                return;
                            }
                        }
                        this.fireEvent("ChangeDirectoryIndex", record, "down");
                    }
                }]
            }, {
                xtype: 'actioncolumn',
                text:  TeamPass.Locales.gettext("ADMIN.DIRECTORY_GRID_ACTIONS_TEXT"),
                flex:1,
                align: 'center',
                items: [
                    {
                        iconCls: 'x-fa fa-refresh',
                        tooltip:  TeamPass.Locales.gettext("ADMIN.DIRECTORY_GRID_SYNC_TOOLTIP"),
                        isDisabled: function(view, rowIndex, colIndex, item, record) {
                            if (record.get('type') != "internal") {
                                return false;
                            } else {
                                return true;
                            }
                        },
                        handler: function (grid, rowIndex, colIndex) {
                            var record = grid.getStore().getAt(rowIndex);
                            if (!record) {
                                return;
                            }

                            // the internal directory is not sync-able
                            if (record.get('type') == "internal") {
                                return;
                            }

                            this.fireEvent("syncDirectory", record);
                        }
                    }, {
                        iconCls: 'x-fa fa-pencil',
                        tooltip:  TeamPass.Locales.gettext("ADMIN.DIRECTORY_GRID_EDIT_TOOLTIP"),
                        isDisabled: function(view, rowIndex, colIndex, item, record) {
                            if (record.get('type') != "internal") {
                                return false;
                            } else {
                                return true;
                            }
                        },
                        handler: function (grid, rowIndex, colIndex) {
                            var record = grid.getStore().getAt(rowIndex);
                            if (!record) {
                                return;
                            }

                            // the internal directory is not editable
                            if (record.get('type') == "internal") {
                                return;
                            }

                            this.fireEvent("editDirectory", record);
                        }
                    }, {
                        iconCls: 'x-fa fa-trash-o',
                        tooltip:  TeamPass.Locales.gettext("ADMIN.DIRECTORY_GRID_DELETE_TOOLTIP"),
                        isDisabled: function(view, rowIndex, colIndex, item, record) {
                            if (record.get('type') != "internal") {
                                return false;
                            } else {
                                return true;
                            }
                        },
                        handler: function (grid, rowIndex, colIndex) {
                            var record = grid.getStore().getAt(rowIndex);
                            if (!record) {
                                return;
                            }

                            // the internal directory is not editable
                            if (record.get('type') == "internal") {
                                return;
                            }

                            var msgbox = Ext.Msg.confirm(TeamPass.Locales.gettext(
                                "ADMIN.DIRECTORY_DELETE_ENTRY_CONFIRMATION_TITLE"),
                                TeamPass.Locales.gettext("ADMIN.DIRECTORY_DELETE_ENTRY_CONFIRMATION_TEXT"),
                                function(btn) {
                                    if (btn == 'yes') {
                                        this.fireEvent("deleteDirectory", record);
                                    }
                                }, this);
                        }
                    }
                ]
            }],
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    xtype: 'button',
                    itemId: 'newDirectoryBtn',
                    text:  TeamPass.Locales.gettext("ADMIN.DIRECTORY_NEW_BTN"),
                    menu: {
                        items: [
                            '<b class="menu-title">Choose a Type</b>',
                            {
                                text: 'Microsoft Active Directory',
                                implementationClass: 'ActiveDirectoryImplementation',
                                iconCls: 'x-fa fa-sitemap'
                            }, {
                                text: 'OpenLDAP',
                                implementationClass: 'OpenLdapImplementation',
                                iconCls: 'x-fa fa-sitemap'
                            }
                        ]
                    }
                }]
            }]
        });
        this.callParent();
    }
});

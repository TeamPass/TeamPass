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

Ext.define('TeamPass.controller.Admin.Permissions', {
    extend: 'TeamPass.controller.Abstract',

    refs: [{
        selector: 'adminpermissionstreegrid',
        ref: 'AdminPermissionsTreeGrid'
    }, {
        selector: 'adminpermissionscontainer',
        ref: 'AdminPermissionsContainer'
    }, {
        selector: 'adminpermissionsdetailgrid',
        ref: 'AdminPermissionsDetailGrid'
    }],

    models: [
        'Admin.Permission',
        'Admin.GroupTree',
        'Admin.PermissionGroup'
    ],

    stores: [
        'Admin.PermissionGroup',
        'Admin.GroupTree',
        'Admin.Permissions'
    ],

    init: function() {
        this.control({
            'adminpermissionstreegrid': {
                afterrender : this.onPermissionsGridAfterRender,
                itemclick: this.onTreeGridItemClick
            }
        });

        this.listen({
            store: {
                '#Admin.PermissionGroup': {
                    beforeload: this.loadAdminPermissionGroupStore,
                    beforesync: this.syncAdminPermissionGroupStore
                },
                '#Admin.Permissions': {
                    beforeload: this.loadAdminPermissionsStore,
                    beforesync: this.syncAdminPermissionsStore
                },
                '#Admin.GroupTree': {
                    beforeload: this.loadAdminGroupTreeStore
                }
            }
        });
    },

    loadAdminPermissionGroupStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminPermissionGroupStore: function() {
        this.setCSRFToken(this.getAdminPermissionGroupStore());
    },

    loadAdminPermissionsStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminPermissionsStore: function() {
        this.setCSRFToken(this.getAdminPermissionsStore());
    },

    loadAdminGroupTreeStore: function(store) {
        this.setCSRFToken(store);
    },

    onPermissionsGridAfterRender: function() {

        console.log("onPermissionsGridAfterRender");

        this.getAdminGroupTreeStore().load();

        delete this.getAdminPermissionsStore().getProxy().extraParams;
        this.getAdminPermissionsStore().load();

        delete this.getAdminPermissionGroupStore().getProxy().extraParams;
        this.getAdminPermissionGroupStore().load();
    },

    onTreeGridItemClick: function(dv, record) {

        this.getAdminPermissionsStore().getProxy().extraParams = {gteId: record.get('id')};
        this.getAdminPermissionsStore().load();

        this.getAdminPermissionGroupStore().getProxy().extraParams = {gteId: record.get('id')};
        this.getAdminPermissionGroupStore().load();
    },

    onContextMenuClick : function( view, rec, node, index, e ) {
        var position = e.getXY();
        e.stopEvent();
        this.permissinMenu = this.createContextMenu(rec);
        if(this.permissinMenu) {
            this.permissinMenu.showAt(position);
        }
    },

    createContextMenu: function(record){
        if(record.get('type') === "group") {
            this.getAdminPermissionGroupStore().getProxy().extraParams = {groupId: record.get('id')};
            this.cmenu = Ext.create('Ext.menu.Menu', {
                scope: this,
                items: [{
                    scope:this,
                    fieldLabel: 'select Group',
                    xtype: 'combo',
                    store: this.getAdminPermissionGroupStore(),
                    valueField: 'userGroupId',
                    displayField: 'userGroupName',
                    listeners: {
                        select: function(view, rec) {this.onNewPermissionEntry(view, rec, record)},
                        scope:this
                    }
                }]
            });
            return this.cmenu;
        } else {
            this.cmenu = Ext.create('Ext.menu.Menu', {
                scope:this,
                margin: '0 0 10 0',
                items: [{
                    text: 'Delete',
                    iconCls: 'x-fa fa-trash-o',
                    scope: this,
                    handler : Ext.bind(this.deleteUserGroupPermission, this, record, true)
                }]
            });
            return this.cmenu;
        }
    },

    deleteUserGroupPermission: function(view, menuitem, rec) {
        rec.remove();
    },

    /**
     *
     * @param view
     * @param rec the selected record from drop down
     * @param record the selected record in tree panel
     */
    onNewPermissionEntry: function(view, rec, record) {

        request = {
            groupId: record.get('id'),
            userGroupId: rec.get('userGroupId')
        };

        record.insertChild(0, {
            "gteId": record.get('id'),
            "userGroupId": rec.get('userGroupId'),
            "leaf": true
        });

        this.permissinMenu.hide();
    }
});

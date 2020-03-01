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

Ext.define('TeamPass.controller.Admin.Users', {
    extend: 'TeamPass.controller.Abstract',

    requires:[
        'TeamPass.view.Admin.User.Grid'
    ],

    refs: [{
        selector: 'adminusergrid',
        ref: 'AdminUserGrid'
    },{
        selector: 'adminuserdetail',
        ref: 'AdminUserDetail'
    }],

    models: [
        'Admin.UserGrid',
        'Admin.GroupGrid',
        'Admin.UserCurrentGroupsGrid',
        'Admin.UserAvailableGroupsGrid'
    ],

    stores: [
        'Admin.UserGrid',
        'Admin.GroupGrid',
        'Admin.UserCurrentGroupsGrid',
        'Admin.UserAvailableGroupsGrid'
    ],

    init: function() {

        this.control({
            'adminusergrid' : {
                afterrender : this.onUserGridAfterRender,
                selectionchange: this.onUserSelectionChange
            },
            'adminusercontainer' : {
                afterrender : this.onUserContainerAfterRender
            },

            'adminusergrid [itemId=newUserBtn]' : {
                click : this.onNewUserClick
            },
            'adminusergrid [itemId=saveBtn]' : {
                click : this.onUserSaveClick
            },
            'adminusergrid actioncolumn': {
                deleteUser: this.onDeleteUser,
                addUserToWorkQueue: this.onAddUserToWorkQueue
            },
            'adminusercurrentgroupsgrid > gridview' : {
                beforedrop: this.onBeforeDrop,
                drop : this.onUserGroupDrop
            },
            'adminuseravailablegroupsgrid > gridview' : {
                drop : this.onUserGroupDrop
            }
        });

        this.listen({
            store: {
                '#Admin.UserGrid': {
                    beforeload: this.loadAdminUserGridStore,
                    beforesync: this.syncAdminUserGridStore
                },
                '#Admin.GroupGrid': {
                    beforeload: this.loadAdminGroupGridStore,
                    beforesync: this.syncAdminGroupGridStore
                },
                '#Admin.UserCurrentGroupsGrid': {
                    beforeload: this.loadAdminUserCurrentGroupsGridStore,
                    beforesync: this.syncAdminUserCurrentGroupsGridStore
                },
                '#Admin.UserAvailableGroupsGrid': {
                    beforeload: this.loadAdminUserAvailableGroupsGridStore,
                    beforesync: this.syncAdminUserAvailableGroupsGridStore
                }
            }
        });
    },

    loadAdminUserGridStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminUserGridStore: function() {
        this.setCSRFToken(this.getAdminUserGridStore());
    },

    loadAdminGroupGridStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminGroupGridStore: function() {
        this.setCSRFToken(this.getAdminGroupGridStore());
    },

    loadAdminUserCurrentGroupsGridStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminUserCurrentGroupsGridStore: function() {
        this.setCSRFToken(this.getAdminUserCurrentGroupsGridStore());
    },

    loadAdminUserAvailableGroupsGridStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminUserAvailableGroupsGridStore: function() {
        this.setCSRFToken(this.getAdminUserAvailableGroupsGridStore());
    },

    onUserGridAfterRender: function() {
        this.getAdminUserDetail().disable();
    },

    onUserContainerAfterRender: function() {
        // load the grid store
        this.getAdminUserGridStore().load();
        // clear existing filter
        this.getAdminUserGrid().store.clearFilter();

        this.getAdminUserCurrentGroupsGridStore().getProxy().extraParams = {userId: 0};
        this.getAdminUserCurrentGroupsGridStore().load();

        this.getAdminUserAvailableGroupsGridStore().getProxy().extraParams = {userId: 0};
        this.getAdminUserAvailableGroupsGridStore().load();
    },

    onUserSelectionChange: function(view, rec) {
        if (typeof rec[0] !== "undefined") {
            rec = rec[0];

            this.getAdminUserDetail().enable();

            if (rec.get("directoryType") == "internal") {
                this.loadReadWriteForm(this.getAdminUserDetail().down('fieldcontainer'));
            } else {
                this.loadReadOnlyForm(this.getAdminUserDetail().down('fieldcontainer'));
            }

            this.getAdminUserDetail().loadRecord(rec);

            this.getAdminUserCurrentGroupsGridStore().getProxy().extraParams = {userId: rec.get('userId')};
            this.getAdminUserCurrentGroupsGridStore().load();

            this.getAdminUserAvailableGroupsGridStore().getProxy().extraParams = {userId: rec.get('userId')};
            this.getAdminUserAvailableGroupsGridStore().load();
        }
    },

    onNewUserClick: function () {
        store = this.getAdminUserGridStore();
        var username = this.getDummyUsername(store);

        rec = store.add({
            userName: username,
            enabled:false,
            directoryType:'internal',
            directoryName: 'internal'
        });

        this.getAdminUserGrid().getSelectionModel().select(rec, true, true);

        store.sync({
            success: function() {
                this.onUserSelectionChange(null, rec);
            },
            scope: this
        });
    },

    getDummyUsername: function (store) {
        var dummyUserName = "New User";
        return this.generateDummyUsername(store, dummyUserName);
    },

    generateDummyUsername: function (store, username) {
        var user = store.findRecord('userName', username);
        if (user) {
            me = this;
            username = username + "-1";
            return this.generateDummyUsername(store, username);
        }
        return username;
    },

    onAddUserToWorkQueue: function (record) {
        params = {
            userId: record.get('userId')
        };
        Ext.Ajax.request({
            url: '/api/v1/admin/user/addToWorkQueue/' + record.get('userId'),
            method : "POST",
            headers: this.getCSRFTokenHeaderObject(),
            // params : params,
            scope:this,
            success : function(response) {
                response = Ext.JSON.decode(response.responseText);

                Ext.create('widget.SuccessNotification',{title: 'Success', html: response.result}).show();
            },
            failure : function(response) {
                response = Ext.JSON.decode(response.responseText);
                var errorMessage = TeamPass.Locales.gettext("ADMIN.ADD_USER_TO_WORK_QUEUE_MSG_TEXT");
                if (response.message !== undefined) {
                    errorMessage = response.message;
                }
                Ext.create('widget.ErrorNotification',{title: TeamPass.Locales.gettext("ADMIN.ADD_USER_TO_WORK_QUEUE_MSG_TITLE"), html: errorMessage}).show();
            }
        });
    },

    onDeleteUser: function (record) {
        Ext.Msg.show({
            title: TeamPass.Locales.gettext("ADMIN.USER_GRID_DELETE_CONFIRMATION_TITLE"),
            msg: TeamPass.Locales.gettext("ADMIN.USER_GRID_DELETE_CONFIRMATION_TEXT", [record.get('userName')]),
            buttons: Ext.MessageBox.OKCANCEL,
            fn: function(btn, text) {
                if (btn == 'ok'){

                    var oldIndex = this.getAdminUserGridStore().indexOf(record);
                    this.getAdminUserGridStore().remove(record);

                    var newRecord = this.getAdminUserGridStore().getAt(oldIndex);

                    if (typeof newRecord !== "undefined") {
                        this.getAdminUserGrid().getSelectionModel().select(newRecord);
                    } else {
                        this.getAdminUserGrid().getSelectionModel().select(this.getAdminUserGridStore().getCount()-1);
                    }

                    this.getAdminUserGridStore().sync();
                }
            },
            scope:this,
            icon: Ext.MessageBox.ERROR
        })
    },

    onBeforeDrop:function(node, data) {
        for(var i =0; i < data.records.length; i++){
            var rec = data.records[i];
            rec.setDirty(true);
        }
    },

    onUserGroupDrop: function() {
        this.getAdminUserCurrentGroupsGridStore().sync();
    },

    onUserSaveClick: function () {
        this.getAdminUserDetail().updateRecord();
        this.getAdminUserGridStore().sync();
        this.getAdminUserCurrentGroupsGridStore().sync();
    },

    loadReadOnlyForm: function (form) {
        form.remove(0);
        form.insert(0, { xtype: 'adminuserreadonlyfieldcontainerform', flex: 1 });
    },

    loadReadWriteForm: function (form) {
        form.remove(0);
        form.insert(0, { xtype: 'adminuserfieldcontainerform', flex: 1 });
    }
});

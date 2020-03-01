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

Ext.define('TeamPass.controller.Admin.UserGroups', {
    extend: 'TeamPass.controller.Abstract',

    refs: [{
        selector: 'admingroupgrid',
        ref: 'AdminGroupGrid'
    },{
        selector: 'admingroupgrid',
        ref: 'AdminGroupGrid'
    },{
        selector: 'admingroupdetails',
        ref: 'AdminGroupDetails'
    },{
        selector: 'admingroupcurrentusersgrid',
        ref: 'AdminGroupCurrentUsersGrid'
    },
    {
        selector: 'admingroupavailableusersgrid',
        ref: 'AdminGroupAvailableUsersGrid'
    },
    {
        selector: 'admingroupdetailform',
        ref: 'AdminGroupDetailForm'
    }],

    models: [
        'Admin.GroupGrid',
        'Admin.GroupCurrentUsersGrid',
        'Admin.GroupAvailableUsersGrid'
    ],

    stores: [
        'Admin.GroupGrid',
        'Admin.GroupCurrentUsersGrid',
        'Admin.GroupAvailableUsersGrid'
    ],

    init: function() {
        this.control({
            'admingroupgrid' : {
                afterrender : this.onGroupGridAfterRender,
                selectionchange: this.onSelectionChange
            },
            'admingroupcontainer' : {
                afterrender : this.onGroupContainerAfterRender
            },
            'admingroupgrid [itemId=newGroupBtn]' : {
                click : this.onNewGroupClick
            },
            'admingroupdetails [itemId=saveBtn]' : {
                click : this.onGroupSaveClick
            },
            'admingroupgrid actioncolumn': {
                deleteGroup: this.onDeleteGroup
            },
            'admingroupcurrentusersgrid > gridview' : {
                beforedrop: this.onBeforeDrop,
                drop : this.onGroupDrop
            },
            'admingroupavailableusersgrid > gridview' : {
                drop : this.onGroupDrop
            }
        });

        this.listen({
            store: {
                '#Admin.GroupGrid': {
                    beforeload: this.loadAdminGroupGridStore,
                    beforesync: this.syncAdminGroupGridStore
                },
                '#Admin.GroupCurrentUsersGrid': {
                    beforeload: this.loadAdminGroupCurrentUsersGridStore,
                    beforesync: this.syncAdminGroupCurrentUserssGridStore
                },
                '#Admin.GroupAvailableUsersGrid': {
                    beforeload: this.loadAdminGroupAvailableUsersGridStore,
                    beforesync: this.syncAdminGroupAvailableUsersGridStore
                }
            }
        });
    },

    loadAdminGroupGridStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminGroupGridStore: function() {
        this.setCSRFToken(this.getAdminGroupGridStore());
    },

    loadAdminGroupCurrentUsersGridStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminGroupCurrentUserssGridStore: function() {
        this.setCSRFToken(this.getAdminGroupCurrentUsersGridStore());
    },

    loadAdminGroupAvailableUsersGridStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminGroupAvailableUsersGridStore: function() {
        this.setCSRFToken(this.getAdminGroupAvailableUsersGridStore());
    },

    onDeleteGroup: function (record) {
        Ext.Msg.show({
            title: TeamPass.Locales.gettext("ADMIN.GROUP_GRID_DELETE_CONFIRMATION_TITLE"),
            msg: TeamPass.Locales.gettext("ADMIN.GROUP_GRID_DELETE_CONFIRMATION_TEXT", [record.get('groupName')]),
            buttons: Ext.MessageBox.OKCANCEL,
            fn: function(btn, text) {
                if (btn == 'ok'){

                    var oldIndex = this.getAdminGroupGridStore().indexOf(record);
                    this.getAdminGroupGridStore().remove(record);

                    var newRecord = this.getAdminGroupGridStore().getAt(oldIndex);

                    if (typeof newRecord !== "undefined") {
                        this.getAdminGroupGrid().getSelectionModel().select(newRecord);
                    } else {
                        this.getAdminGroupGrid().getSelectionModel().select(this.getAdminGroupGridStore().getCount()-1);
                    }

                    this.getAdminGroupGridStore().sync();
                }
            },
            scope:this,
            icon: Ext.MessageBox.ERROR
        })
    },

    onGroupSaveClick: function () {
        this.getAdminGroupDetailForm().updateRecord();
        this.getAdminGroupGridStore().sync();

        this.getAdminGroupCurrentUsersGridStore().sync();
    },

    onSelectionChange: function(view, rec) {
        if (typeof rec[0] !== "undefined") {
        rec = rec[0];

        this.getAdminGroupDetails().enable();

        this.getAdminGroupDetailForm().loadRecord(rec);

        this.getAdminGroupCurrentUsersGridStore().getProxy().extraParams = {groupId: rec.get('groupId')};
        this.getAdminGroupCurrentUsersGridStore().load();

        this.getAdminGroupAvailableUsersGridStore().getProxy().extraParams = {groupId: rec.get('groupId')};
        this.getAdminGroupAvailableUsersGridStore().load();
        }
    },

    onNewGroupClick: function () {
        store = this.getAdminGroupGridStore();
        var groupName = this.getDummyGroupName(store);

        rec = store.add({
            groupName: groupName,
            isAdmin: false
        });

        this.getAdminGroupGrid().getSelectionModel().select(rec, true, true);

        store.sync({
            success: function() {
                this.onSelectionChange(null, rec);
            },
            scope: this
        });
    },

    getDummyGroupName: function (store) {
        return this.generateDummyGroupName(store, 'New Group');
    },

    generateDummyGroupName: function (store, groupname) {
        var userGroup = store.findRecord('groupName', groupname);
        if (userGroup) {
            groupname = groupname + "-1";
            return this.generateDummyGroupName(store, groupname);
        }
        return groupname;
    },


    onUserGroupDrop: function(a,b,rec,d) {
        var records = this.getAdminUserCurrentGroupsGridStore().getRange();
        for(var i =0; i < records.length; i++){
            rec = records[i];
            rec.setDirty(true);
        }
    },

    onBeforeDrop:function(node, data) {
        for(var i =0; i < data.records.length; i++){
            var rec = data.records[i];
            rec.setDirty(true);
        }
    },

    onGroupDrop: function() {
        this.getAdminGroupCurrentUsersGridStore().sync();
    },

    onGroupGridAfterRender: function() {
        this.getAdminGroupDetails().disable();
    },

    onGroupContainerAfterRender: function() {
        this.getAdminGroupCurrentUsersGridStore().getProxy().extraParams = {groupId: 0};
        this.getAdminGroupCurrentUsersGridStore().load();
        this.getAdminGroupCurrentUsersGrid().store.clearFilter();

        this.getAdminGroupAvailableUsersGridStore().getProxy().extraParams = {groupId: 0};
        this.getAdminGroupAvailableUsersGridStore().load();
        this.getAdminGroupAvailableUsersGrid().store.clearFilter();

        // load the grid store
        this.getAdminGroupGridStore().load();
        // clear existing filter
        this.getAdminGroupGrid().store.clearFilter();
    }
});

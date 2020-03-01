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

Ext.define('TeamPass.controller.Admin.Directories', {
    extend: 'TeamPass.controller.Abstract',

    refs: [{
            selector: 'admindirectorygrid',
            ref: 'AdminDirectoryGrid'
        },{
            selector: 'admindirectorytabpanel',
            ref: 'AdminDirectoryTabPanel'
        }],
    models: [
        'Admin.DirectoryGrid'
    ],
    stores: [
        'Admin.DirectoryGrid'
    ],
    init: function() {
        this.control({
            'admindirectorygrid': {
                afterrender: this.onDirectoryGridAfterRender,
                activate: this.onDirectoryGridAfterRender
            },
            'admindirectorygrid actioncolumn': {
                ChangeDirectoryIndex: this.onDirectoryGridChangeIndex,
                syncDirectory: this.onSyncDirectory,
                editDirectory: this.onEditDirectoryClick,
                deleteDirectory: this.onDeleteDirectory
            },
            'admindirectorygrid [itemId=newDirectoryBtn] > menu' : {
                click : this.onNewDirectoryBtnClick
            }
        });

        this.listen({
            store: {
                '#Admin.DirectoryGrid': {
                    beforeload: this.loadAdminDirectoryGridStore,
                    beforesync: this.syncAdminDirectoryGridStore
                }
            }
        });
    },

    onNewDirectoryBtnClick: function(a, menu, record) {
        this.createDirectoryTab("widget.admindirectoryimplementationcontainer",
            {
                implementationClass: menu.implementationClass,
                id: "createDirectory"
            }
        );
    },

    onEditDirectoryClick: function(record) {
        this.createDirectoryTab("widget.admindirectoryimplementationcontainer",
            {
                implementationClass: record.get('adapter'),
                directoryId: record.get('directoryId'),
                id: "editDirectory"
            }
        );
    },

    loadAdminDirectoryGridStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminDirectoryGridStore: function() {
        this.setCSRFToken(this.getAdminDirectoryGridStore());
    },

    onDirectoryGridAfterRender: function() {
        this.getAdminDirectoryGridStore().load();
    },

    onSyncDirectory: function (record) {
        var request = {
            directoryId : record.get('directoryId')
        };

        this.getAdminDirectoryGrid().setLoading(true);

        Ext.Ajax.request({
            url: '/api/v1/admin/directory/sync/' + record.get('directoryId'),
            method : "POST",
            headers: this.getCSRFTokenHeaderObject(),
            scope:this,
            success : function(response) {
                Ext.create('widget.SuccessNotification', {title: 'Success', html: "success"}).show();

                this.getAdminDirectoryGrid().setLoading(false);
            },
            failure : function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification',{title: 'Error',html: response.message}).show();

                this.getAdminDirectoryGrid().setLoading(false);
            }
        });
    },

    onDirectoryGridChangeIndex: function(record, direction) {

        var directoryId = record.get('directoryId');

        Ext.Ajax.request({
            url: '/api/v1/admin/directory/index/' + directoryId + '/' + direction,
            method: "POST",
            headers: this.getCSRFTokenHeaderObject(),
            scope: this,
            success: function(response) {
                this.getAdminDirectoryGridStore().load();
            },
            failure: function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification', {title: 'Error', html: response.message}).show();
            }
        });
    },

    /**
     * deletes given directory records from store and syncs with backend
     *
     * @param record
     *
     * @return void
     */
    onDeleteDirectory: function(record) {
        this.getAdminDirectoryGridStore().remove(record);
        this.getAdminDirectoryGridStore().sync();
    },

    /**
     * creates a new directory tab
     *
     * @param {string} alias  the widget name
     * @param {object} params tab parameters
     *
     * @return void
     */
    createDirectoryTab: function(alias, params) {
        var tab = this.getAdminDirectoryTabPanel().getComponent(params.id);

        if (tab) {
            this.getAdminDirectoryTabPanel().remove(tab, true);
        }

        var panel = Ext.create(alias, params);
        tab = this.getAdminDirectoryTabPanel().add(panel);
        this.getAdminDirectoryTabPanel().setActiveTab(tab);
    }
});

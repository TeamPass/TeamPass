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

Ext.define('TeamPass.controller.Admin.Directory.Management', {
    extend: 'TeamPass.controller.Abstract',

    refs: [
        {
            selector: 'admindirectorypreview',
            ref: 'AdminDirectoryPreview'
        },
        {
            selector: 'admindirectoryimplementationformldap',
            ref: 'AdminDirectoryImplementationFormLdap'
        },
        {
            selector: 'admindirectoryimplementationpanelldap',
            ref: 'AdminDirectoryImplementationPanelLdap'
        },
        {
            selector: 'admindirectoryimplementationpreviewstatus',
            ref: 'AdminDirectoryImplementationPreviewStatus'
        },
        {
            selector: 'admindirectoryimplementationpreviewgrid',
            ref: 'AdminDirectoryImplementationPreviewGrid'
        },
        {
            selector: 'admindirectoryimplementationcontainer',
            ref: 'AdminDirectoryImplementationContainer'
        },
        {
            selector: 'admindirectoryimplementationpreviews',
            ref: 'AdminDirectoryImplementationPreviews'
        }, {
            selector: 'admindirectorytabpanel',
            ref: 'AdminDirectoryTabPanel'
        }
    ],

    models: [
        'Admin.Directory.Preview',
        'Admin.Directory.ExternalDirectories'
    ],

    stores: [
        'Admin.Directory.Preview',
        'Admin.Directory.ExternalDirectories'
    ],

    init: function() {
        this.control({
            'admindirectoryimplementationformldap': {
                afterrender : this.onLdapFormAfterRender,
                validitychange: this.onLdapFormValidityChange,
                dirtychange: this.onLdapFormDirtyChange
            },
            'admindirectoryimplementationformldap [itemId=submitBtn]': {
                click : this.onSubmitFormBtnClick
            },
            'admindirectoryimplementationpreviewstatus [itemId=connectionTestBtn]': {
                click : this.onTestConnectionBtnClick
            },
            'admindirectoryimplementationcontainer': {
                afterrender : this.onContainerAfterRender,
                activate: this.onPanelActivate
            }
        });
        this.listen({
            store: {
                '#Admin.Directory.ExternalDirectories': {
                    beforeload: this.loadAdminDirectoryExternalDirectoriesStore,
                    beforesync: this.syncAdminDirectoryExternalDirectoriesStore
                }
            }
        });
    },

    loadAdminDirectoryExternalDirectoriesStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminDirectoryExternalDirectoriesStore: function() {
        this.setCSRFToken(this.getAdminDirectoryExternalDirectoriesStore());
    },

    onPanelActivate: function() {
      this.getAdminDirectoryPreviewStore().removeAll();
      this.getAdminDirectoryImplementationPreviewStatus().reset();
    },

    onSelectDirectoryToEdit: function(view, rec) {
        this.application.fireEvent("editDirectory", rec);
    },

    onLdapFormValidityChange: function(form) {
        var formButton = this.getAdminDirectoryImplementationPanelLdap().down("form").down("button");
        var testButton = Ext.ComponentQuery.query('admindirectoryimplementationpreviewstatus [itemId=connectionTestBtn]')[0];

        if (form.isValid()) {
            testButton.enable();
            formButton.enable();
        } else {
            testButton.disable();
            formButton.disable();
        }

        if (form.isDirty()) {
            formButton.enable();
        } else {
            formButton.disable();
        }
    },

    onLdapFormDirtyChange: function(form, dirty) {
        var button = this.getAdminDirectoryImplementationPanelLdap().down("form").down("button");

        if (dirty) {
            button.enable();
        } else {
            button.disable();
        }
    },

    onContainerAfterRender: function() {
        this.getAdminDirectoryImplementationContainer().loadForm();
    },

    /**
     * loads ether a existing node or fetches the presets for given implementation class
     *
     * @param scope
     */
    onLdapFormAfterRender: function(scope) {

        var directoryId = this.getAdminDirectoryImplementationPanelLdap().getDirectoryId();
        var implementationClass = this.getAdminDirectoryImplementationPanelLdap().getImplementationClass();

        // if directoryId exits its a edit task
        if (directoryId) {
            this.fetchNodeAndLoadRecord(directoryId);
        } else {
            this.fetchPresetsAndLoadRecord(implementationClass);
        }
    },

    onTestConnectionBtnClick: function() {
        this.getAdminDirectoryImplementationPreviews().setLoading();

        var ic = this.getAdminDirectoryImplementationPanelLdap().getImplementationClass();
        var values = this.getAdminDirectoryImplementationFormLdap().getForm().getFieldValues();
        var reqObj = {
            backend: {
                name: values.name,
                implementationClass: values.implementationClass,
                directoryId: values.directoryId
            }
        };

        delete values.id;
        delete values.directoryId;
        delete values.implementationClass;
        delete values.name;

        reqObj.backendConfiguration = values;

        Ext.Ajax.request({
            url: '/api/v1/admin/directory/preview/test/' + ic,
            method: "POST",
            headers: this.getCSRFTokenHeaderObject(),
            jsonData: reqObj,
            scope: this,
            success: function(response) {
                response = Ext.JSON.decode(response.responseText);

                this.setStatusText(response.statusText);

                this.getAdminDirectoryImplementationPreviews().setLoading(false);

                this.getAdminDirectoryPreviewStore().setProxy({type: "memory", data: response.data});
                this.getAdminDirectoryPreviewStore().load();

            },
            failure: function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification',{title: 'Error',html: response.message}).show();
            }
        });
    },

    setStatusText: function(text) {
        this.getAdminDirectoryImplementationPreviewStatus().down('textarea').setValue(text);
    },

    onSubmitFormBtnClick: function() {

        this.getAdminDirectoryImplementationFormLdap().setLoading(true);

        this.getAdminDirectoryImplementationFormLdap().updateRecord();
        var values = this.getAdminDirectoryImplementationFormLdap().getRecord().getData();

        // default http method is POST, but if directory id exists, its only a update so we need to change to PUT
        var method = "POST";
        if (values['directoryId']) {
            method = "PUT";
        }

        var reqObj = {
            backend: {
                name: values.name,
                implementationClass: values.implementationClass,
                directoryId: values.directoryId
            }
        };

        delete values.id;
        delete values.directoryId;
        delete values.implementationClass;
        delete values.name;

        reqObj.backendConfiguration = values;

        Ext.Ajax.request({
            url: '/api/v1/admin/directory',
            method: method,
            headers: this.getCSRFTokenHeaderObject(),
            jsonData: reqObj,
            scope: this,
            success: function(response) {
                response = Ext.JSON.decode(response.responseText);

                this.getAdminDirectoryImplementationFormLdap().setLoading(false);

                this.showAdminDirectoryPanel();
            },
            failure: function(response) {
                response = Ext.JSON.decode(response.responseText);

                this.getAdminDirectoryImplementationFormLdap().setLoading(false);

                Ext.create('widget.ErrorNotification',{title: 'Error',html: response.message}).show();
            }
        });
    },

    showAdminDirectoryPanel: function() {
      // first tab is always the directory grid view
      this.getAdminDirectoryTabPanel().setActiveTab(0);
    },

    fetchPresetsAndLoadRecord: function(ic) {
        Ext.Ajax.request({
            url: '/api/v1/admin/directory/node/presets/' + ic,
            method: "GET",
            headers: this.getCSRFTokenHeaderObject(),
            scope: this,
            success: function(response) {
                response = Ext.JSON.decode(response.responseText);

                this.loadRecord(response);
            },
            failure: function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification',{title: 'Error',html: response.message}).show();
            }
        });
    },

    fetchNodeAndLoadRecord: function(directoryId) {
        Ext.Ajax.request({
            url: '/api/v1/admin/directory/' + directoryId,
            method: "GET",
            headers: this.getCSRFTokenHeaderObject(),
            scope: this,
            success: function(response) {
                response = Ext.JSON.decode(response.responseText);

                this.loadRecord(response);
            },
            failure: function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification',{title: 'Error',html: response.message}).show();
            }
        });
    },

    loadRecord: function(presets) {
        var record = Ext.create('TeamPass.model.Admin.Directory.Node');
        var ic = this.getAdminDirectoryImplementationPanelLdap().getImplementationClass();

        record.set(presets);
        record.set({implementationClass: ic});

        this.getAdminDirectoryImplementationFormLdap().loadRecord(record);
    }
});

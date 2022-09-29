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

Ext.define('TeamPass.controller.Admin.Export', {
    extend: 'TeamPass.controller.Abstract',

    refs: [{
        selector: 'adminexporttreegrid',
        ref: 'AdminExportTreeGrid'
    }, {
        selector: 'adminexportdetail',
        ref: 'AdminExportDetail'
    }, {
        selector: '#exportGroupName',
        ref: 'ExportGroupName'
    }, {
        selector: '#exportGroupId',
        ref: 'ExportGroupId'
    },{
        selector: '#exportBtn',
        ref: 'exportBtn'
    },{
        selector: '#exportGroupStatusArea',
        ref: 'exportGroupStatusArea'
    }],

    models: [
        'Admin.GroupTree'
    ],

    stores: [
        'Admin.GroupTree'
    ],

    init: function() {
        this.control({
            'adminexporttreegrid': {
                afterrender : this.onExportGridAfterRender,
                itemclick: this.onSelectGroup
            },
            'adminexportdetail [itemId=exportBtn]': {
                click : this.onExportBtnClick
            },
        });

        this.listen({
            store: {
                '#Admin.GroupTree': {
                    beforeload: this.loadAdminGroupTreeStore
                }
            }
        });
    },

    loadAdminGroupTreeStore: function(store) {
        this.setCSRFToken(store);
    },

    onExportGridAfterRender: function() {
        this.getAdminGroupTreeStore().load();
    },
    updateSelectedPath: function (pathArray) {
        this.getExportGroupName().setText('<strong>' + pathArray.join('/') + '</strong>');
    },
    updateSelectedGroupId: function (id) {
        this.getExportGroupId().setValue(id);
    },
    getSelectedGroupId: function () {
        return this.getExportGroupId().getValue();
    },
    enableExportBtn: function () {
        this.getExportBtn().enable();
    },
    updateStatusText: function (text) {
        this.getExportGroupStatusArea().setValue(text);
    },

    onExportBtnClick: function() {
        this.getAdminExportTreeGrid().setLoading(true);
        var groupId = this.getSelectedGroupId();

        var privateKey = this.getPrivateKey();
        jsonResult = JSON.stringify({privateKey: privateKey});
        var sessionAesKey = this.getSessionAesKey();
        enc = GibberishAES.enc(jsonResult, sessionAesKey);

        Ext.Ajax.request({
            url: '/api/v1/admin/export/' + groupId,
            method: "POST",
            headers: this.getCSRFTokenHeaderObject(),
            scope: this,
            jsonData: {privateKey: {encryptedData: enc}},
            success: function (response) {
                response = Ext.JSON.decode(response.responseText);
                this.updateStatusText(response.result)
                this.getAdminExportTreeGrid().setLoading(false);
            },
            failure: function (response) {
                this.getAdminExportTreeGrid().setLoading(false);
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification', {title: 'Error', html: response.message}).show();
            }
        });

        this.updateStatusText("export started...")
    },

    onSelectGroup: function(dv, record) {
        groupId = record.get('id');

        Ext.Ajax.request({
            url: '/api/v1/admin/export/group/path/' + groupId,
            method: "GET",
            headers: this.getCSRFTokenHeaderObject(),
            scope: this,
            success: function(response) {
                response = Ext.JSON.decode(response.responseText);
                this.updateStatusText("");
                this.updateSelectedPath(response.result.path);
                this.updateSelectedGroupId(response.result.id);
                this.enableExportBtn();
            },
            failure: function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification', {title: 'Error', html: response.message}).show();
            }
        });
    },
});

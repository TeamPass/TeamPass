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

Ext.define('TeamPass.controller.Elements', {
    extend: 'TeamPass.controller.Abstract',

    requires:[
        'TeamPass.view.CenterPanel',
        'TeamPass.model.ElementGrid',
        'TeamPass.view.DefaultElementForm',
        'TeamPass.view.RteElementForm',
        'TeamPass.view.TemplateSelectorComboBox'
    ],

    refs: [
        {
            selector: 'mainpanel',
            ref: 'MainPanel'
        }, {
            selector: 'centerpanel',
            ref: 'CenterPanel'
        }, {
            selector: 'adminpanel',
            ref: 'AdminPanel'
        }, {
            selector: 'gridlist',
            ref: 'GridList'
        }, {
            selector: 'detailpanel',
            ref: 'DetailPanel'
        }, {
            selector: 'templateselectorcombobox',
            ref: 'TemplateSelectorComboBox'
        }, {
            selector: 'detailpanel > toolbar',
            ref: 'DetailToolbar'
        }
    ],

    models: ['ElementGrid', 'TemplateSelector'],
    stores: ['ElementGrids', 'TemplateSelector'],

    init: function() {

        this.control({
            'gridlist': {
                itemcontextmenu: this.onItemContextMenuClick,
                containercontextmenu: this.onContainerContextMenuClick,
                itemclick: this.onGridListItemClick
            },
            'detailpanel > form > fieldcontainer > button[itemId=hidebtn]': {
                click: this.onFormHideButtonClick
            },
            'detailpanel > form > fieldcontainer > button[itemId=copybtn]': {
                click: this.onCopyButtonClick
            },
            'detailpanel > form > fieldcontainer > button[itemId=openurlbtn]': {
                click: this.onOpenUrlButtonClick
            },
            'detailpanel > form > button ': {
                click: this.onFormSaveButtonClick
            },
            'templateselectorcombobox': {
                select: this.onChangeElementTemplate
            }
        });

        this.listen({
            store: {
                '#ElementGrids': {
                    beforeload: this.loadElementGridsStore,
                    beforesync: this.syncElementGridsStore
                },
                '#TemplateSelector': {
                    beforeload: this.loadTemplateSelectorStore,
                    beforesync: this.syncTemplateSelectorStore
                }
            }
        });

        this.application.on({
            resetGroup: this.onResetGroup,
            clearGroup: this.clearGrid,
            openGroup: this.openGroup,
            loadDetailAfterPasswordPrompt: this.onGridListItemClickWrapper,
            appstart: this.onAppStart,
            scope: this
        });
    },

    onAppStart: function() {
        this.getElementGridsStore().load();
    },


    loadTemplateSelectorStore: function(store) {
        this.setCSRFToken(store);
    },

    syncTemplateSelectorStore: function() {
        this.setCSRFToken(this.getTemplateSelectorStore());
    },

    loadElementGridsStore: function(store) {
        this.setCSRFToken(store);
    },

    syncElementGridsStore: function() {
        this.setCSRFToken(this.getElementGridsStore());
    },

    onResetGroup: function() {

        if (this.getAdminPanel()) {
            this.getAdminPanel().setVisible(false);
            this.getAdminPanel().removeAll(true);
        }
        this.getCenterPanel().setVisible(true);
    },

    onChangeElementTemplate: function(view, rec) {

        var internalTemplateName = rec.get('internalName');

        var elementRecord = this.getGridList().getSelectionModel().getSelection()[0];

        this.getElementGridsStore().suspendAutoSync();
        elementRecord.set('template', internalTemplateName);
        this.getElementGridsStore().resumeAutoSync();

        this.onGridListItemClick(view, elementRecord);
    },

    onItemDblClick: function(view, cell, cellIndex, record,row, rowIndex, e) {
        var clickedDataIndex = this.getGridList().headerCt.getHeaderAtIndex(cellIndex).dataIndex;
        var groupId = record.get("groupId");
        this.copy(clickedDataIndex);
    },

    checkDefaultElementForm: function(rec) {

        var password = this.getDetailPanel().down("form").down("textfield[name=password]");
        var username = this.getDetailPanel().down("form").down("textfield[name=username]");
        var url = this.getDetailPanel().down("form").down("textfield[name=url]");
        var content = false;

        if(password.isDirty() || username.isDirty() || url.isDirty()) {
            content = {
                password:password.getValue(),
                username: username.getValue(),
                url: url.getValue()
            };
        }
        return content;
    },

    checkRteElementForm: function(rec) {
        var rtecontent = this.getDetailPanel().down("form").down("htmleditor[name=rtecontent]");
        var content = false;
        if(rtecontent.isDirty()) {
            content = {
                rtecontent: rtecontent.getValue()
            };
        }
        return content;
    },

    onFormSaveButtonClick: function( button, event) {
        var form = button.up('form').getForm();
        var rec = button.up('form').getForm().getRecord();
        var content = false;

        if (rec.get("template") == "DEFAULT_TEMPLATE") {
            content = this.checkDefaultElementForm(rec);
        } else if (rec.get("template") == "RTE") {
            content = this.checkRteElementForm(rec);
        } else {
            console.log("no template found");
        }

        form.updateRecord();
        form.setValues(form.getValues());

        this.disableElementTemplateSelector();

        if (content !== false) {

            jsonContent = JSON.stringify(content);

            var sessionAesKey = this.getSessionAesKey();
            var privateKey = this.getPrivateKey();

            // set var to false, and only if entity is already encrypted and we have got the encrypted aes key
            // we overwrite this var
            var encAesKey = false;

            if (rec.get('isEncrypted') == true) {
                var rsaEncAesKey = rec.get('rsaEncAesKey');
                var crypt = new JSEncrypt({default_key_size: 2048});
                crypt.setPrivateKey(privateKey);
                var clearAesKey = crypt.decrypt(rsaEncAesKey);
                encAesKey = GibberishAES.enc(clearAesKey, sessionAesKey);
            }

            var encryptedContent = GibberishAES.enc(jsonContent, sessionAesKey);
            var elementId = rec.get("elementId");
            var templateName = rec.get("template");

            var request = {
                element: {
                    elementId: elementId,
                    encryptedContent: encryptedContent,
                    encAesKey: encAesKey,
                    template: templateName
                }
            };

            this.getDetailPanel().setLoading(true);

            headers = this.getCSRFTokenHeaderObject();
            headers['Content-Type'] = 'application/json';

            Ext.Ajax.request({
                url: '/api/v1/encryption/element/update',
                method : "POST",
                headers: headers,
                jsonData : request,
                scope:this,
                success : function(response) {

                    response = Ext.JSON.decode(response.responseText);

                    // if encryption was successful we set the isEncrypted field to true and updating the record
                    // with the generated rsa encrypted aes key
                    button.up('form').getForm().getRecord().set("isEncrypted", true);
                    if (response.result.rsaEncAesKey) {
                        button.up('form').getForm().getRecord().set("rsaEncAesKey", response.result.rsaEncAesKey);
                    }

                    this.getElementGridsStore().sync();
                    this.getDetailPanel().setLoading(false);
                },
                failure : function(response) {
                    Ext.Msg.show({
                        title: 'Error',
                        msg: 'Error while encrypting content!',
                        buttons: Ext.MessageBox.OK,
                        closable: false,
                        icon: Ext.MessageBox.ERROR
                    });

                    this.getElementGridsStore().sync();
                    this.getDetailPanel().setLoading(false);
                }
            });
        } else {
            this.getElementGridsStore().sync();
        }
    },

    onFormHideButtonClick: function (button) {
        this.getDetailPanel().down('form').toggleHideBtn(button);
    },

    onSelectionChange: function (view, selections, options) {

        if (typeof selections[0] !== "undefined") {
            var rec = selections[0];
            if (rec.get("template") == "DEFAULT_TEMPLATE") {
                this.getDetailPanel().removeAll();
                this.getDetailPanel().insert(0,{xtype:'defaultelementform'});
                this.getDetailPanel().down("form").loadRecord(rec);

            } else if (rec.get("template") == "RTE") {
                console.log("rte template");
            } else {
                console.log("no template found");
            }
        } else {
            this.getDetailPanel().removeAll();
        }
    },

    onGridListItemClickWrapper: function (rec) {
        this.onGridListItemClick(null, rec);
    },

    disableFormFields: function() {
        var form = this.getDetailPanel().down("form");

        form.down("button[name=submit]").setDisabled(true);
        form.down("textfield[name=password]").setDisabled(true);
        form.down("textfield[name=url]").setDisabled(true);
        form.down("textfield[name=username]").setDisabled(true);
        form.down("textfield[name=title]").setDisabled(true);
        form.down("textarea[name=comment]").setDisabled(true);
    },

    onGridListItemClick: function( view, rec ) {

        // reset the button
        this.resetCopyButton();

        authState = this.getSessionDataValue('state');

        if (authState == "unlocked" ) {

            // select in combo box current template type
            var templateRecord = this.getTemplateSelectorStore().findRecord("internalName", rec.get("template"));
            this.getTemplateSelectorComboBox().select(templateRecord);
            // set toolbar to visible
            this.getDetailToolbar().setVisible(true);

            if (rec.get("template") == "DEFAULT_TEMPLATE") {

                this.getDetailPanel().removeAll();
                this.getDetailPanel().insert(0,{xtype:'defaultelementform'});

                this.loadElementForm(rec);

                this.application.on("onPrepareClipboardFeature", function() {
                    this.copyGridCellContent(grid, td, columnIndex, rec, tr, rowIndex, e, eOpts);
                },this);

            } else if (rec.get("template") == "RTE") {

                this.getDetailPanel().removeAll();
                this.getDetailPanel().insert(0,{xtype:'rteelementform'});

                this.loadElementForm(rec);

            } else {
                console.log("no template found");
            }
        } else {
            console.log("decryption failed");
        }

    },

    loadElementForm: function(rec) {

        this.getDetailPanel().down("form").loadRecord(rec);
        var form = this.getDetailPanel().down("form").getForm();
        form.setValues(form.getValues());

        if (rec.get('isEncrypted') === true) {

            this.disableElementTemplateSelector();

            if (rec.get('rsaEncAesKey') === "false") {

                this.disableFormFields();

                Ext.Msg.show({
                    title: TeamPass.Locales.gettext("ELEMENT.ELEMENT_NOT_ENCRYPTED_ERROR_TITLE"),
                    msg: TeamPass.Locales.gettext("ELEMENT.ELEMENT_NOT_ENCRYPTED_ERROR_MSG"),
                    buttons: Ext.MessageBox.YES,
                    closable:false,
                    buttonText:{
                        yes: "OK"
                    },
                    fn: function() {
                        Ext.Msg.hide();
                    },
                    icon: Ext.MessageBox.ERROR
                });
            } else {

                var sessionAesKey = this.getSessionAesKey();
                var privateKey = this.getPrivateKey();
                var rsaEncAesKey = rec.get('rsaEncAesKey');

                var crypt = new JSEncrypt({default_key_size: 2048});
                crypt.setPrivateKey(privateKey);
                var clearAesKey = crypt.decrypt(rsaEncAesKey);

                var encAesKey = GibberishAES.enc(clearAesKey, sessionAesKey);

                var request = {
                    element: {
                        elementId: rec.get('elementId'),
                        encAesKey: encAesKey
                    }
                };

                Ext.Ajax.request({
                    url: '/api/v1/encryption/element/get',
                    method : "POST",
                    headers: this.getCSRFTokenHeaderObject(),
                    jsonData: request,
                    scope:this,
                    success : function(response) {

                        response = Ext.JSON.decode(response.responseText);

                        response = GibberishAES.dec(response.result.encryptedContent, sessionAesKey);

                        this.fillEncryptedFields(rec, response);

                        this.enableCopyPasswordButton();
                    },
                    failure : function(response) {
                        Ext.Msg.show({
                            title: TeamPass.Locales.gettext("ELEMENT.ELEMENT_NOT_ENCRYPTED_ERROR_TITLE"),
                            msg: TeamPass.Locales.gettext("ELEMENT.ELEMENT_NOT_ENCRYPTED_ERROR_MSG"),
                            buttons: Ext.MessageBox.YES,
                            closable:false,
                            buttonText:{
                                yes: "OK"
                            },
                            fn: function() {
                                Ext.Msg.hide();
                            },
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                });
            }
        } else {
            // if element has no encrypted content, the template selector is enabled
            this.enableElementTemplateSelector();
        }
    },

    enableElementTemplateSelector: function() {
        this.getTemplateSelectorComboBox().enable();
    },

    disableElementTemplateSelector: function() {
        this.getTemplateSelectorComboBox().disable();
    },

    fillEncryptedFields: function(rec, value) {
        obj = JSON.parse(value);
        var form = this.getDetailPanel().down("form");

        if (rec.get("template") == "DEFAULT_TEMPLATE") {

            form.down("textfield[name=password]").setValue(obj.password);
            form.down("textfield[name=password]").resetOriginalValue();
            form.down("textfield[name=url]").setValue(obj.url);
            form.down("textfield[name=url]").resetOriginalValue();
            form.down("textfield[name=username]").setValue(obj.username);
            form.down("textfield[name=username]").resetOriginalValue();

        } else if (rec.get("template") == "RTE") {

            form.down("htmleditor[name=rtecontent]").setValue(obj.rtecontent);
            form.down("htmleditor[name=rtecontent]").resetOriginalValue();
        }
    },

    openGroup: function(rec) {
        this.groupId = rec.id;
        this.getElementGridsStore().getProxy().extraParams = {groupId: rec.id};
        this.getElementGridsStore().load();
        this.getGridList().getSelectionModel().select(0);
        this.getDetailPanel().removeAll();
        this.getDetailToolbar().setVisible(false);
    },

    clearGrid: function() {
        this.groupId = null;
        this.getElementGridsStore().getProxy().extraParams = {groupId: ""};
        this.getElementGridsStore().load();
        this.getDetailPanel().removeAll();
        this.getDetailToolbar().setVisible(false);
    },

    onItemContextMenuClick: function(view, rec, node, index, e) {
        if (typeof this.groupId !== 'undefined' && this.groupId !== null) {
            var position = e.getXY();
            e.stopEvent();
            var menu = this.createItemContextMenu(rec);
            menu.showAt(position);
        } else {
            console.log("not allowed to open context menu, because groupId is undefined");
        }
    },

    createItemContextMenu: function(rec) {

        var disabled = true;
        if (this.getSessionDataValue("elementClipboard")) {
            disabled = false;
        }

        this.cmenu = Ext.create('Ext.menu.Menu', {
            scope:this,
            items: [
                {
                    text: TeamPass.Locales.gettext("ELEMENT.NEW_ENTRY_TEXT"),
                    iconCls: 'x-fa fa-plus',
                    scope: this,
                    handler: this.onNewEntryClick
                }, {
                    text: TeamPass.Locales.gettext("ELEMENT.DELETE_ENTRY_TEXT"),
                    iconCls: 'x-fa fa-trash-o',
                    scope: this,
                    handler: this.onDeleteClick
                }]
        });
        return this.cmenu;
    },

    onContainerContextMenuClick : function( view, e ) {
        if (typeof this.groupId !== 'undefined' && this.groupId !== null) {
            var position = e.getXY();
            e.stopEvent();
            var menu = this.createContainerContextMenu();
            menu.showAt(position);
        } else {
            console.log("not allowed to open context menu, because groupId is undefined");
        }
    },

    createContainerContextMenu: function() {

        var disabled = true;
        if (this.getSessionDataValue("elementClipboard")) {
            disabled = false;
        }

        this.cmenu = Ext.create('Ext.menu.Menu', {
            scope:this,
            margin: '0 0 10 0',
            items: [{
                    text: TeamPass.Locales.gettext("ELEMENT.NEW_ENTRY_TEXT"),
                    iconCls: 'x-fa fa-plus',
                    scope:this,
                    handler:this.onNewEntryClick
                }, {
                    text: TeamPass.Locales.gettext("ELEMENT.PASTE_ENTRY_TEXT"),
                    iconCls: 'x-fa fa-thumb-tack',
                    disabled: disabled,
                    scope: this,
                    handler : Ext.bind(this.onPasteClick, this, rec, true)
                }]
        });
        return this.cmenu;
    },

    onNewEntryClick: function (view, menuitem, record , e, opt) {
        store = this.getElementGridsStore();
        result = store.add({
            text: TeamPass.Locales.gettext("ELEMENT.NEW_ENTRY_TEXT"),
            groupId:this.groupId,
            local: true,
            template: "DEFAULT_TEMPLATE"
        });

        // sync store
        this.getElementGridsStore().sync();

        // simulate a on click by user
        this.getGridList().getSelectionModel().select(result[0]);
        this.onGridListItemClick(null, result[0]);
    },

    onDeleteClick: function () {
        var me = this;
        var selection = this.getGridList().getSelectionModel().getSelection()[0];
        if (selection) {
            var msgbox = Ext.Msg.confirm(TeamPass.Locales.gettext("ELEMENT.DELETE_ENTRY_CONFIRMATION_TITLE"), TeamPass.Locales.gettext("ELEMENT.DELETE_ENTRY_CONFIRMATION_TEXT"), function(btn) {
                if (btn == 'yes') {
                    this.getElementGridsStore().remove(selection);

                    // sync store
                    this.getElementGridsStore().sync();
                }
            }, this);
        }
    },

    getCopyButtonComponent: function() {
        return Ext.ComponentQuery.query('detailpanel > form > fieldcontainer > button[itemId=copybtn]')[0];
    },

    enableCopyPasswordButton: function() {
        var button = this.getCopyButtonComponent();
        if (button) {
            button.enable();
            this.onCopyButtonClick();
        }
    },

    onCopyButtonClick: function() {
        var button = this.getCopyButtonComponent();
        var values = this.getDetailPanel().down('form').getForm().getValues();
        button.getEl().set({
            "data-clipboard-text": values.password
        });

        if (!this.copyButtonClipboard) {
            this.copyButtonClipboard = new Clipboard(this.getCopyButtonComponent().getEl().dom);
            this.copyButtonClipboard.on('success', function (e) {
                var message = Ext.create('widget.SuccessNotification', {title: 'Success', html: "success"}).show();
            });
        } else {
            this.copyButtonClipboard = new Clipboard(this.getCopyButtonComponent().getEl().dom);
        }
    },

    resetCopyButton: function() {
        if (this.copyButtonClipboard) {
            this.copyButtonClipboard.destroy();
            delete this.copyButtonClipboard;
        }
    },

    onOpenUrlButtonClick: function() {
        var values = this.getDetailPanel().down("form").getForm().getValues();

        var prefix = 'https://';
        if (values.url.substr(0, prefix.length) !== prefix)
        {
            values.url = prefix + values.url;
        }

        console.log(values.url);
        window.open(values.url,"_blank");
    }
});

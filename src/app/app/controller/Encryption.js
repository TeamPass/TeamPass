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

Ext.define('TeamPass.controller.Encryption', {
    extend: 'TeamPass.controller.Abstract',

    requires:[
        'TeamPass.view.Encryption.Window',
        'TeamPass.view.Encryption.Grid',
        'TeamPass.store.Encryption.Grid',
        'TeamPass.model.Encryption.Grid',
        'TeamPass.controller.Abstract'
    ],

    refs: [
        {
            selector: 'encryption.window',
            ref: 'Encryption.Window'
        }, {
            selector: '#encryptionBtn',
            ref: 'EncryptionBtn'
        }, {
            selector: '#encryptallbtn',
            ref: 'EncryptAllBtn'
        }, {
            selector: '#statustoolbar',
            ref: 'StatusToolBar'
        }
    ],

    models: ['Encryption.Grid'],
    stores: ['Encryption.Grid'],

    init: function() {

        this.control({
            '#encryptionBtn' : {
                click : this.onEncryptionButtonClick
            },
            '#encryptallbtn' : {
                click : this.onEncryptAllButtonClick
            }
        });

        this.listen({
            store: {
                '#Encryption.Grid': {
                    beforeload: this.loadEncryptionGridStore,
                    beforesync: this.syncEncryptionGridStore
                }
            }
        });

        this.application.on({
            appStart: this.onAppStart,
            initEncryptionForWorkQueueUser: this.onInitEncryptionForWorkQueueUser,
            deleteUserFromWorkQueue: this.onDeleteUserFromWorkQueue,
            scope: this
        });
    },

    onAppStart:function() {

        isAdmin = this.getSessionDataValue('isAdmin');
        if (isAdmin === true) {
            this.getEncryptionBtn().show();
            this.encryptionPoll();
        }
    },

    loadEncryptionGridStore: function(store) {
        this.setCSRFToken(store);
    },

    syncEncryptionGridStore: function() {
        this.setCSRFToken(this.getEncryptionGridStore());
    },

    onInitEncryptionForWorkQueueUser: function(rec) {
        this.encryptForUser(rec);
    },

    encryptForUser: function(rec, data, all = false) {
        if (data) {
            reqObj = {
                massEncryption: {
                    userId: rec.get('userId'),
                    encryptedData: data
                }
            };
        } else {

            this.updateEncryptStatusBarStart(rec);

            reqObj = {
                massEncryption: {
                    userId: rec.get('userId')
                }
            };
        }

        Ext.Ajax.request({
            url: '/api/v1/admin/encrypt',
            method: "POST",
            headers: this.getCSRFTokenHeaderObject(),
            scope: this,
            jsonData: reqObj,
            success: function(response) {
                response = Ext.JSON.decode(response.responseText);

                if (response.finished === false) {
                    encryptionKeys = this.performDecryptionAndEncryption(response.content);
                    this.encryptForUser(rec, encryptionKeys, all);
                } else {

                    this.updateEncryptStatusBarFinished(rec);

                    this.updateEncryptStatusForUserToFinish(rec, all);
                }
            },
            failure: function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification',{title: 'Error',html: response.message}).show();
            }
        });
    },

    updateEncryptStatusBarStart: function(rec) {
        this.getStatusToolBar().setText("encryption for " + rec.get('fullName') + " started...");
    },

    updateEncryptStatusBarFinished: function(rec) {
        this.getStatusToolBar().setText("encryption for " + rec.get('fullName') + " finished...");
    },

    updateEncryptStatusForUserToFinish: function(rec, all) {
        this.getEncryptionGridStore().remove(rec);

        if (all) {
            this.onEncryptAllButtonClick();
        }
    },

    onEncryptAllButtonClick: function() {
        rec = this.getEncryptionGridStore().first();

        if (rec) {
            this.encryptForUser(rec, false,true);
        }
    },

    performDecryptionAndEncryption: function(content) {
        var value = this.aesDecrypt(content);
        value = JSON.parse(value);
        for (var i = 0, len = value.length; i < len; i++) {
            value[i].aesKey = this.rsaDecrypt(value[i].rsaKey);
            delete value[i].rsaKey;
        }

        return this.aesEncrypt({entries: value});
    },

    onDeleteUserFromWorkQueue: function(rec) {

        var workId = rec.get('workId');
        Ext.Ajax.request({
            url: '/api/v1/admin/wq/' + workId,
            method : "DELETE",
            headers: this.getCSRFTokenHeaderObject(),
            scope:this,
            success : function(response) {
                this.getEncryptionGridStore().reload();
            },
            failure : function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification', {title: 'Error', html: response.message}).show();
            }
        });
    },

    encryptionPoll: function() {
        var self = this;
        this.encryptionCheck();
        var intervalTime = this.getIntervalTime();
        var encryptionTimer = setInterval(function() {
            var user = self.getSessionDataValue("userId");
            if (!user) {
                clearInterval(encryptionTimer);
                return;
            } else {
                self.encryptionCheck();
            }
        }, intervalTime * 1000);
    },

    getIntervalTime: function() {
        return this.getSessionDataValue('pollInterval');
    },

    encryptionCheck: function() {
        var self = this;

        Ext.Ajax.request({
            url: '/api/v1/admin/poll',
            headers: this.getCSRFTokenHeaderObject(),
            method: "GET",
            scope: this,
            success: function(response) {
                response = Ext.JSON.decode(response.responseText);
                this.changeEncryptionButton(response.result);
            },
            failure: function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification',{title: 'Error',html: response.message}).show();

                TeamPass.app.fireEvent("checkLoginState", response);
            }
        });
    },

    changeEncryptionButton: function(value) {
        string = Ext.String.format(TeamPass.Locales.gettext("HEADER.ENCRYPTION_BUTTON") + ' ( {0} )', value);
        this.getEncryptionBtn().setText(string);

        if (value === 0) {
            this.getEncryptionBtn().addCls("encryptionBtnNormal");
            this.getEncryptionBtn().removeCls("encryptionBtnCritical");
            this.getEncryptionBtn().setDisabled(true);
        } else {
            this.getEncryptionBtn().addCls("encryptionBtnCritical");
            this.getEncryptionBtn().removeCls("encryptionBtnNormal");
            this.getEncryptionBtn().setDisabled(false);
        }
    },

    onEncryptionButtonClick: function() {
        this.window = Ext.create('widget.encryption.window');
        this.window.show();

        this.getEncryptionGridStore().load();
    }
});

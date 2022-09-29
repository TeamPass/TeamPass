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

Ext.define('TeamPass.controller.Management', {
    extend: 'TeamPass.controller.Abstract',

    requires:[
        'Ext.window.MessageBox',
        'Ext.tip.*',
        'Ext.ux.WebWorker',
        'Ext.ux.WebWorkerManager',
        'TeamPass.view.Viewport',
        'TeamPass.view.AdminTreePanel',
        'TeamPass.view.SetupPasswordWindow',
        'TeamPass.view.SettingsWindow',
        'TeamPass.view.ChangePasswordPanel',
        'TeamPass.view.ChangeLanguagePanel',
        'TeamPass.view.ChangeRSAKeyPanel',
        'TeamPass.view.Settings.MiscPanel',
        'TeamPass.view.notifications.SuccessNotification',
        'TeamPass.view.notifications.ErrorNotification'
    ],

    refs: [
        { selector: 'headertoolbar',
            ref: 'HeaderToolbar'
        },{
            selector: '#adminbtn',
            ref: 'adminBtn'
        },{
            selector: '#settingsBtn',
            ref: 'settingsBtn'
        },{
            selector: '#logoutbtn',
            ref: 'LogoutBtn'
        },{
            selector: '#welcometext',
            ref: 'WelcomeText'
        },{
            selector: 'viewport',
            ref: 'Viewport'
        },{
            selector: 'setuppasswordwindow',
            ref: 'SetupPasswordWindow'
        },{
            selector: 'changepasswordpanel',
            ref: 'ChangePasswordPanel'
        },{
            selector: 'changelanguagepanel',
            ref: 'ChangeLanguagePanel'
        },{
            selector: 'changersakeypanel',
            ref: 'ChangeRSAKeyPanel'
        },{
            selector: 'settingsmiscpanel',
            ref: 'SettingsMiscPanel'
        },
        {
            selector: 'menupanel',
            ref: 'MenuPanel'
        }
    ],

    models: ['Language'],

    stores: ['AllLanguages'],

    rsaPassPhraseComplexity: '',

    init: function() {

        this.control({
            '#settingsBtn' : {
                click : this.onSettingsBtnClick
            },
            '#logoutbtn' : {
                click : this.onLogoutButtonClick
            },
            'setuppasswordwindow > form > button' : {
                click : this.onPasswordProceedButtonClick
            },
            'changepasswordpanel' : {
                afterrender: this.onChangePasswordPanelAfterRender
            },
            'changelanguagepanel' : {
                afterrender: this.onAfterRenderLanguagePanel
            },
            'changersakeypanel' : {
                afterrender: this.onAfterRenderRsaKeyPanel
            },
            'changepasswordpanel > button' : {
                click: this.onChangePasswordButtonClick
            },
            'changelanguagepanel > button' : {
                click: this.onChangeLanguageButtonClick
            },
            'changersakeypanel > button' : {
                click: this.onChangeRSAKeyButtonClick
            },
            'settingsmiscpanel' : {
                afterrender: this.onAfterRenderSettingsMiscPanel
            },
            'settingsmiscpanel > button' : {
                click: this.onClearLocalCacheButtonClick
            },
            'settingsmiscpanel > checkbox[name=usePinkTheme]' : {
                change: this.usePinkThemeCheckBoxToggle
            },
            'settingsmiscpanel > checkbox[name=treeAlphabeticallyOrder]' : {
                change: this.treeAlphabeticallyOrderToggle
            }
        });

        this.application.on({
            PassPhrasePrompt: this.onPassPhrasePrompt,
            initEncryption: this.initEncryption,
            setupRsaKeyPair: this.onSetupRsaKeyPair,
            appStart: this.onAppStart,
            checkLoginState: this.onCheckLoginState,
            scope: this
        });
    },

    treeAlphabeticallyOrderToggle: function(view, state) {
        var params = {alphabeticalOrder: state};
        Ext.Ajax.request({
            url: '/api/v1/management/tree-alphabetical-order',
            method: "POST",
            headers: this.getCSRFTokenHeaderObject(),
            jsonData: {setting: params},
            scope: this,
            success: function(response) {
                var msgbox = Ext.Msg.confirm(TeamPass.Locales.gettext(
                    "SETTINGS.CHANGE_TREE_SORT_ORDER_RELOAD_TITLE"),
                    TeamPass.Locales.gettext("SETTINGS.CHANGE_TREE_SORT_ORDER_RELOAD_TEXT"),
                    function(btn) {
                        if (btn == 'yes') {
                            location.reload();
                        }
                    }, this);

            },
            failure: function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.SuccessNotification', {title: 'Success', html: response.result}).show();
            }
        });
    },

    usePinkThemeCheckBoxToggle: function(view, state) {
      var theme = "teampass";
      if (state == true) {
          theme = "teampasspink";
      }
      var params = {theme: theme};

      Ext.Ajax.request({
          url: '/api/v1/management/theme',
          method: "POST",
          headers: this.getCSRFTokenHeaderObject(),
          jsonData: {setting: params},
          scope: this,
          success: function(response) {
              this.setLocalStorageValue("theme", theme);

              var msgbox = Ext.Msg.confirm(TeamPass.Locales.gettext(
                  "SETTINGS.CHANGE_THEME_RELOAD_TITLE"),
                  TeamPass.Locales.gettext("SETTINGS.CHANGE_THEME_RELOAD_TEXT"),
                  function(btn) {
                      if (btn == 'yes') {
                          location.reload();
                      }
                  }, this);

          },
          failure: function(response) {
              response = Ext.JSON.decode(response.responseText);
              Ext.create('widget.SuccessNotification', {title: 'Success', html: response.result}).show();
          }
      });
    },

    /**
     * clears the local cache
     *
     * @return void
     */
    onClearLocalCacheButtonClick: function() {
        var provider = Ext.state.Manager.getProvider();

        for (var item in provider.state) {
            Ext.state.Manager.clear(item);
        }

        Ext.Msg.show({
            title: TeamPass.Locales.gettext("MANAGEMENT.CLEAR_LOCAL_CACHE_RELOAD_CONFIRMATION_TITLE"),
            msg: TeamPass.Locales.gettext("MANAGEMENT.CLEAR_LOCAL_CACHE_RELOAD_CONFIRMATION_TEXT"),
            buttons: Ext.MessageBox.YES,
            closable: true,
            buttonText: {
                yes: "OK"
            },
            fn: function(btn) {
                if (btn == 'yes') {
                    window.location.reload();
                }
            },
            iconCls: 'x-fa fa-info-circle'
        });
    },

    /**
     * called by a event. Submits the new user password to server-side
     *
     * @return void
     */
    onChangePasswordButtonClick: function() {

        var panel = this.getChangePasswordPanel();
        var form = panel.getForm();

        if (form.isValid()) {
            panel.setLoading();
            var values = form.getValues();
            var enc = this.aesEncrypt(values);

            Ext.Ajax.request({
                url: '/api/v1/management/password',
                method: "PUT",
                headers: this.getCSRFTokenHeaderObject(),
                jsonData: {person: {encryptedData: enc}},
                scope: this,
                success: function(response) {
                    response = Ext.JSON.decode(response.responseText);
                    panel.setLoading(false);
                    form.reset();
                    Ext.create('widget.SuccessNotification', {title: 'Success', html: response.result}).show();
                },
                failure: function(response) {
                    panel.setLoading(false);
                    response = Ext.JSON.decode(response.responseText);
                    form.markInvalid(response.errors);
                }
            });
        }
    },

    /**
     * Loads the current language from server-side and updates the current preset language in displayfield and combo
     *
     * @return void
     */
    onAfterRenderLanguagePanel: function() {
        Ext.Ajax.request({
            url: '/api/v1/management/language',
            method : "GET",
            headers: this.getCSRFTokenHeaderObject(),
            scope: this,
            success : function(response) {
                response = Ext.JSON.decode(response.responseText);
                this.getChangeLanguagePanel().down('displayfield').setValue(response.result.langCode);
                this.getChangeLanguagePanel().down('combo').setValue(response.result.langCode);
            },
            failure : function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification',{title: 'Error',html: response.message}).show();
            }
        });
    },

    /**
     * loads the rsa passphrase complexity regex
     *
     * @return void
     */
    onAfterRenderRsaKeyPanel: function() {
        Ext.Ajax.request({
            url: '/api/v1/management/rsa-passphrase-complexity',
            method : "GET",
            headers: this.getCSRFTokenHeaderObject(),
            scope: this,
            success : function(response) {
                response = Ext.JSON.decode(response.responseText);
                if (response.result.enabled === true) {
                    this.rsaPassPhraseComplexity = response.result.regex
                }
            },
            failure : function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification',{title: 'Error',html: response.message}).show();
            }
        });
    },

    /**
     * Loads the current language from server-side and updates the current preset language in displayfield and combo
     *
     * @return void
     */
    onAfterRenderSettingsMiscPanel: function() {
        Ext.Ajax.request({
            url: '/api/v1/management/theme',
            method : "GET",
            headers: this.getCSRFTokenHeaderObject(),
            scope: this,
            success: function(response) {
                response = Ext.JSON.decode(response.responseText);
                if (response.result.theme == "teampasspink") {
                  this.getSettingsMiscPanel().down('checkbox[name=usePinkTheme]').suspendEvents(false);
                  this.getSettingsMiscPanel().down('checkbox[name=usePinkTheme]').setValue(true);
                  this.getSettingsMiscPanel().down('checkbox[name=usePinkTheme]').resumeEvents();
                }
            },
            failure: function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification',{title: 'Error',html: response.message}).show();
            }
        });

        Ext.Ajax.request({
            url: '/api/v1/management/tree-alphabetical-order',
            method : "GET",
            headers: this.getCSRFTokenHeaderObject(),
            scope: this,
            success: function(response) {
                response = Ext.JSON.decode(response.responseText);
                if (response.result.treeAlphabeticalOrder === true) {
                    this.getSettingsMiscPanel().down('checkbox[name=treeAlphabeticallyOrder]').suspendEvents(false);
                    this.getSettingsMiscPanel().down('checkbox[name=treeAlphabeticallyOrder]').setValue(true);
                    this.getSettingsMiscPanel().down('checkbox[name=treeAlphabeticallyOrder]').resumeEvents();
                }
            },
            failure: function(response) {
                response = Ext.JSON.decode(response.responseText);
                Ext.create('widget.ErrorNotification',{title: 'Error',html: response.message}).show();
            }
        });
    },

    /**
     * change the current preset language for user
     *
     * @return void
     */
    onChangeLanguageButtonClick: function() {

        var panel = this.getChangeLanguagePanel();
        var form = panel.getForm();

        if (form.isValid()) {
            panel.setLoading();
            var values = form.getValues();

            Ext.Ajax.request({
                url: '/api/v1/management/language',
                method : "PUT",
                headers: this.getCSRFTokenHeaderObject(),
                jsonData: { setting: values },
                scope:this,
                success : function(response) {
                    response = Ext.JSON.decode(response.responseText);
                    panel.setLoading(false);
                    var langCode = this.getChangeLanguagePanel().down('combo').getValue();
                    this.getChangeLanguagePanel().down('displayfield').setValue(langCode);

                    TeamPass.Locales.setLanguage(langCode);

                    Ext.Msg.show({
                        title: TeamPass.Locales.gettext("MANAGEMENT.CHANGE_LANGUAGE_RELOAD_CONFIRMATION_TITLE"),
                        msg: TeamPass.Locales.gettext("MANAGEMENT.CHANGE_LANGUAGE_RELOAD_CONFIRMATION_TEXT"),
                        buttons: Ext.MessageBox.YES,
                        closable: true,
                        buttonText: {
                            yes: "OK"
                        },
                        fn: function(btn) {
                            if (btn == 'yes') {
                                window.location.reload();
                            }
                        },
                        iconCls: 'x-fa fa-info-circle'
                    });

                    // Ext.create('widget.SuccessNotification',{title: 'Success',html: response.result}).show();
                },
                failure : function(response) {
                    Ext.create('widget.ErrorNotification',{title: TeamPass.Locales.gettext("MANAGEMENT.SET_LANGUAGE_ERROR_TITLE"),html: TeamPass.Locales.gettext("MANAGEMENT.SET_LANGUAGE_ERROR_TEXT")}).show();
                }
            });
        }
    },

    /**
     * called by event. Enables the password changing panel if user is local
     *
     * @return void
     */
    onChangePasswordPanelAfterRender: function() {
        if (this.getSessionDataValue("isLocal")) {
            this.getChangePasswordPanel().enable();
        }
    },

    /**
     * checks if server response requested an application shutdown
     *
     * @param {object} response a server response object
     *
     * @return void
     */
    onCheckLoginState: function(response) {
        if (response.actions) {
            if (response.actions.action) {
                if (response.actions.action == "logout") {
                    var app = this;
                    app.application.fireEvent("appShutdown");
                }
            }
        }
    },

    /**
     * called by event. Updates the welcome message in header
     *
     * @return void
     */
    onAppStart:function() {
        fullName = this.getSessionDataValue('fullName');
        string = Ext.String.format(TeamPass.Locales.gettext("HEADER.LOGGED_IN_TEXT") + ': <strong>{0}</<strong>', fullName);
        this.getWelcomeText().setText(string);
        this.initEncryption();
    },

    /**
     * creates the settings window and shows it
     *
     * @return void
     */
    onSettingsBtnClick: function() {
        this.window = Ext.create('widget.settingswindow');
        this.window.show();
    },

    /**
     * proceeds the rsa key setup
     *
     * @return void
     */
    onPasswordProceedButtonClick: function() {
        var keyPair = this.getSetupPasswordWindow().getKeyPair();
        var password = this.getSetupPasswordWindow().down('form').down('#pass').getValue();

        encrytedPrivateKey = GibberishAES.enc(keyPair['privateKey'], password);

        var result = {
            privateKey: encrytedPrivateKey,
            publicKey: keyPair['publicKey']
        };

        jsonResult = JSON.stringify(result);
        var sessionAesKey = this.getSessionAesKey();
        enc = GibberishAES.enc(jsonResult, sessionAesKey);

        Ext.Ajax.request({
            url: '/api/v1/encryption/rsa/setup',
            method: "POST",
            headers: this.getCSRFTokenHeaderObject(),
            jsonData: { encrypted: {encryptedData: enc}},
            scope: this,
            success: function(response) {
                this.getSetupPasswordWindow().close();

                this.setSessionDataValue({privateKey: keyPair['privateKey']});
                this.setSessionDataValue({state: "unlocked"});

                Ext.Msg.show({
                    title: TeamPass.Locales.gettext("SETUP.SETUP_RSA_TITLE"),
                    msg: TeamPass.Locales.gettext("SETUP.SETUP_RSA_MSG"),
                    buttons: Ext.MessageBox.OK,
                    closable:false,
                    buttonText:{
                        ok: TeamPass.Locales.gettext("SETUP.SETUP_RSA_BUTTON")
                    },
                    fn: function() {
                        this.close();
                    }
                });
                setTimeout(function(){
                    Ext.Msg.hide();
                },3000);
            },
            failure : function(response) {
                Ext.create('widget.ErrorNotification',{title: 'Error',html: TeamPass.Locales.gettext("SETUP.GENERATE_RSA_KEY_ERROR_MSG")}).show();
            }
        });

    },

    /**
     * shows the password prompt to unlock the rsa key pair
     *
     * @param params
     *
     * @return void
     */
    onPassPhrasePrompt: function(params) {
        var msgbox = Ext.Msg.show({
            title: TeamPass.Locales.gettext("LOGIN.PASSPHRASE_TITLE"),
            msg: TeamPass.Locales.gettext("LOGIN.PASSPHRASE_MSG"),
            closable:false,
            prompt:true,
            buttonText: {
                ok: TeamPass.Locales.gettext("LOGIN.PASSPHRASE_OK_BUTTON")
            },
            fn: function(btn, text) {
                if (btn == 'ok'){
                    try {
                        var privateKey = this.getAesPrivateKey();

                        clearPrivateKey = GibberishAES.dec(privateKey, text);

                        this.setSessionDataValue({privateKey: clearPrivateKey});
                        this.setSessionDataValue({state: 'unlocked'});

                        if (typeof params["callbackEvent"] != 'undefined') {
                            this.application.fireEvent(params["callbackEvent"], params["rec"]);
                        }
                    } catch (err) {

                        Ext.create('widget.ErrorNotification', {title: 'Error', html: TeamPass.Locales.gettext("LOGIN.INVALID_PASSPHRASE_MSG")}).show();
                        this.application.fireEvent("PassPhrasePrompt");
                    }
                }
            },
            scope:this,
            animEl: 'elId'
        });
        msgbox.textField.inputEl.dom.type = 'password';
        msgbox.focus();
    },

    /**
     * initiates the logout
     *
     * @return void
     */
    onLogoutButtonClick: function() {
        Ext.Ajax.request({
            url: '/api/v1/auth/logout',
            method : "GET",
            headers: this.getCSRFTokenHeaderObject(),
            scope:this,
            success : function(response) {
                // trigger the application shutdown. This will flush the session storage
                this.application.fireEvent("appShutdown");
                // delete the session cookie
                this.destroyCookies();
            },
            failure : function(response) {
                console.log("logout error");
            }
        });
    },

    /**
     * generates a rsa key pair for the duration of the session
     *
     * @return void
     */
    initEncryption: function() {
         Ext.MessageBox.show({
             title: TeamPass.Locales.gettext("LOGIN.GENERATE_RSA_KEY_TITLE"),
             msg: TeamPass.Locales.gettext("LOGIN.GENERATE_RSA_KEY_MSG"),
             width: 300,
             wait: true,
             waitConfig: {interval: 100}
        });

        var webworker = Ext.create ('Ext.ux.WebWorker', {
            file: '/libraries/worker.js',
            listeners: {
                message: this.onGeneratedRsaKeyPair,
                scope: this
            }
        });
        webworker.send('start');
    },

    /**
     * generates the rsa key pair for a user
     *
     * @return void
     */
    onSetupRsaKeyPair: function() {
        Ext.MessageBox.show({
            title: TeamPass.Locales.gettext("SETUP.ESTABLISH_ACCOUNT_TITLE"),
            msg: TeamPass.Locales.gettext("SETUP.ESTABLISH_ACCOUNT_MSG"),
            width:300,
            wait:true,
            waitConfig: {interval:100}
        });

        var webworker = Ext.create ('Ext.ux.WebWorker', {
            file: '/libraries/worker.js',
            listeners: {
                message: this.onGeneratedSetupRsaKeyPair,
                scope:this
            }
        });
        webworker.send('start');
    },

    /**
     * called after rsa key pair was generated. show
     *
     * @param view
     * @param result
     *
     * @return void
     */
    onGeneratedSetupRsaKeyPair: function(view, result) {
        Ext.MessageBox.hide();
        var window = Ext.create('TeamPass.view.SetupPasswordWindow',{keyPair: result});
        window.show();
    },

    /**
     * called after the rsa key pair is generated
     *
     * @param view
     * @param result
     *
     * @return void
     */
    onGeneratedRsaKeyPair: function(view, result) {
        var rsaKey = new JSEncrypt({default_key_size: 2048});
        rsaKey.setPublicKey(result['publicKey']);
        rsaKey.setPrivateKey(result['privateKey']);

        Ext.Ajax.request({
            url: '/api/v1/encryption/handshake/start',
            method : "POST",
            headers: this.getCSRFTokenHeaderObject(),
            scope: this,
            jsonData: { handshake: { publicKey: rsaKey.getPublicKeyB64() } },
            success: function(response) {
                response = Ext.JSON.decode(response.responseText);
                var encryptedRandomKey = response.result.encryptedSessionAesKey;
                var randomKey = rsaKey.decrypt(encryptedRandomKey);

                // decrypt handshake token and encrypt it again with the given random key
                var encryptedHandshakeToken = response.result.encryptedHandshakeToken;
                var handshakeToken = rsaKey.decrypt(encryptedHandshakeToken);

                enc = GibberishAES.enc(JSON.stringify({handshakeToken: handshakeToken}), randomKey);

                Ext.Ajax.request({
                    url: '/api/v1/encryption/handshake/ack',
                    method: "POST",
                    headers: this.getCSRFTokenHeaderObject(),
                    scope:this,
                    jsonData: { handshake: { encryptedData: enc } },
                    success: function(response) {
                        response = Ext.JSON.decode(response.responseText);
                        response = GibberishAES.dec(response.result.privateKey, randomKey);
                        this.setSessionDataValue({sessionAesKey: randomKey});

                        obj = JSON.parse(response);

                        if(obj.validRsaState == true) {
                            this.setSessionDataValue({aesPrivateKey: obj.aesPrivateKey});
                            this.application.fireEvent("PassPhrasePrompt");
                        } else {
                            this.application.fireEvent("setupRsaKeyPair");
                        }
                    },
                    failure : function(response) {
                        Ext.Msg.show({
                            title: TeamPass.Locales.gettext("LOGIN.GENERATE_RSA_KEY_ERROR_TITLE"),
                            msg: TeamPass.Locales.gettext("LOGIN.GENERATE_RSA_KEY_ERROR_MSG"),
                            buttons: Ext.MessageBox.YES,
                            closable:false,
                            buttonText:{
                                yes: "Reload"
                            },
                            fn: function() {
                                location.reload();
                            },
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                });
            }
        });
    },

    /**
     * changes the rsa pass phrase. technically we do not use a rsa phrase but instead encrypt the private rsa key with
     * an aes key
     *
     * @return void
     */
    onChangeRSAKeyButtonClick: function() {
        var panel = this.getChangeRSAKeyPanel();
        var form = panel.getForm();

        if (form.isValid()) {
            try {
                panel.setLoading();
                var values = form.getValues();

                var privateKey = this.getAesPrivateKey();

                // throws a exception if pass phrase is wrong
                var clearPrivateKey = GibberishAES.dec(privateKey, values.currentPassphrase);

                // check if password meets the requirements
                this.checkPassword(values.passphrase, values.passphrase2);

                // encrypt existing private key with new Passphrase
                encrytedPrivateKey = GibberishAES.enc(clearPrivateKey, values.passphrase);

                var result = {
                    privateKey: encrytedPrivateKey
                };

                jsonResult = JSON.stringify(result);
                var sessionAesKey = this.getSessionAesKey();
                enc = GibberishAES.enc(jsonResult, sessionAesKey);

                Ext.Ajax.request({
                    url: '/api/v1/encryption/rsa/update',
                    method : "POST",
                    headers: this.getCSRFTokenHeaderObject(),
                    jsonData : { encrypted: {encryptedData: enc }},
                    scope:this,
                    success : function(response) {
                        response = Ext.JSON.decode(response.responseText);
                        this.setSessionDataValue({aesPrivateKey: encrytedPrivateKey});
                        panel.setLoading(false);
                        form.reset();
                        Ext.create('widget.SuccessNotification',{title: 'Success',html: "Password Changed"}).show();
                    },
                    failure : function(response) {

                        response = Ext.JSON.decode(response.responseText);
                        Ext.create('widget.ErrorNotification',{title: 'Error',html: "Error persisting rsa key"}).show();
                    }
                });
            } catch (err) {
                panel.setLoading(false);
                if (err === "Decryption error: Maybe bad key") {
                    form.markInvalid({
                        "currentPassphrase": TeamPass.Locales.gettext("SETTINGS.NEW_RSA_PASSPHRASE_CURRENT_PASSPHRASE_MISMATCH_ERROR")
                    });
                }
                if (err === "new_password_mismatch") {
                    form.markInvalid({
                          "passphrase": TeamPass.Locales.gettext("SETTINGS.NEW_RSA_PASSPHRASE_MISMATCH_ERROR"),
                          "passphrase2": TeamPass.Locales.gettext("SETTINGS.NEW_RSA_PASSPHRASE_MISMATCH_ERROR")
                    });
                }
                if (err === "new_password_dont_match_complexity") {
                    form.markInvalid({
                        "passphrase": TeamPass.Locales.gettext("SETTINGS.NEW_RSA_PASSPHRASE_COMPLEXITY_ERROR")
                    });
                }
            }
        }
    },

    /**
     * validates the given passwords. check if they are equal an meet the requirements
     *
     * @param newPassword
     * @param repeatedNewPassword
     *
     * @return void
     */
    checkPassword: function(newPassword, repeatedNewPassword) {
        if(!newPassword.match(this.rsaPassPhraseComplexity)) {
            throw ('new_password_dont_match_complexity');
        }

        if (newPassword !== repeatedNewPassword) {
            throw ('new_password_mismatch');
        }
    }
});

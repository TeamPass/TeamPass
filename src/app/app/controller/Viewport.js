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

Ext.define('TeamPass.controller.Viewport', {
    extend: 'TeamPass.controller.Abstract',
    views: 'Viewport',

    refs: [
        {
            selector: 'viewport',
            ref: 'Viewport'
        },{
            selector: 'loginform',
            ref: 'LoginForm'
        }, {
            selector: 'menupanel',
            ref: 'MenuPanel'
        }
    ],

    stores: ['GroupTree', 'Language', 'ElementGrids'],

    init: function() {
        this.control({
            'button[itemId="loginbtn"]': {
                click: this.onLoginSubmit
            },
            'loginform > textfield[name=userName]' : {
                specialkey: function(field, e) {
                    if(e.getKey() == e.ENTER) {
                        this.onUserNameTextfieldEnter();
                    }
                }
            },
            'loginform > textfield[name=password]' : {
                specialkey: function(field, e) {
                    if(e.getKey() == e.ENTER) {
                        this.onLoginSubmit();
                    }
                }
            },
            'loginform' : {
                activate: this.onActivateLoginForm
            }
        });

        this.application.on({
            appStart: this.onAppStart,
            appShutdown: this.onAppShutdown,
            scope: this
        });
    },

    /**
     * invoked by controller after launch.
     * method decide if application or login screen should be displayed
     *
     * @return void
     */
    onLaunch: function() {
        Ext.ux.ActivityMonitor.maxInactive = (1000 * 60 * 30);
        Ext.ux.ActivityMonitor.init();

        Ext.ux.ActivityMonitor.start();

        var loadingScreen = Ext.fly('appLoading');
        if (loadingScreen) {
            loadingScreen.destroy();
        }

        var p = this.getViewport();
        var user = this.getSessionDataValue("userId");

        p.add(
            Ext.create('TeamPass.view.HeaderPanel'),
            Ext.create('TeamPass.view.MenuPanel'),
            Ext.create('TeamPass.view.MainPanel')
        );

        if (!user) {
            Ext.create('TeamPass.view.LoginForm').show();
        } else {
            // trigger a global event to start the application
            this.application.fireEvent("appStart");
        }
    },

    /**
     * sets the focus to the user textfield
     *
     * @return void
     */
    onUserNameTextfieldEnter: function() {
        this.getLoginForm().down("textfield[name=pass]").focus();
    },

    /**
     * sets the focus to the user textfield
     *
     * @return void
     */
    onActivateLoginForm: function() {
        me = this;
        setTimeout( function() {
            //me.getLoginForm().down("textfield[name=user]").focus();
        }, 100);
    },

    /**
     * triggered by click event
     *
     * simple credential validation on client site (all required fields are filled).
     * sends an ext.direct call to validate credentials
     * reloads browser window on success, else mark form fields als invalid
     *
     * @return void
     */
    onLoginSubmit: function() {
        var form = this.getLoginForm().getForm();
        if (form.isValid()) {

            this.getLoginForm().setLoading(true);

            person = {
                person: form.getValues()
            };

            Ext.Ajax.request({
                url: '/api/v1/auth/login',
                jsonData: person,
                scope:this,
                success: function(response) {
                    response = Ext.JSON.decode(response.responseText);
                    this.getLoginForm().setLoading(false);

                    this.persistSessionData(response.result);

                    formValues = form.getValues();
                    if (formValues.language == "default") {
                        if (response.result.presetLanguage !== TeamPass.Locales.getCurrentLanguage()) {
                            // set new current locale und reload app
                            TeamPass.Locales.setLanguage(response.result.presetLanguage);
                            window.location.reload();
                        } else {

                            this.getLoginForm().close();
                            // trigger a global event to start the application
                            this.application.fireEvent("appStart");
                        }
                    } else {
                        if (formValues.language !== TeamPass.Locales.getCurrentLanguage()) {
                            // set new current locale and reload app
                            TeamPass.Locales.setLanguage(formValues.language);
                            window.location.reload();
                        } else {
                            this.getLoginForm().close();
                            // start the login check
                            // trigger a global event to start the application
                            this.application.fireEvent("appStart");
                        }
                    }
                },
                failure: function(response) {
                    response = Ext.JSON.decode(response.responseText);

                    this.getLoginForm().setLoading(false);
                    form.markInvalid(response.message);
                }
            });
        }
    },

    /**
     * triggered by 'appStart' event
     *
     * start the task runner to check the session of this instance
     *
     * @return void
     */
    onAppStart: function() {
        var task = Ext.TaskManager.start({
            run: this.checkIdentifier,
            scope: this,
            interval: 10000
        });
    },

    /**
     * triggered by 'appShutdown' event
     *
     * deletes all session data an resets trees and grids
     *
     * @return void
     */
    onAppShutdown: function() {
        // clear the session data
        // the session cookie still exists but is useless in this browser tab, because of the missing session data
        this.destroySessionData();

        //reload window
        window.location.reload();

        /*
        // unload the tree store manually
        // disable autosync feature to prevent deleting all records on server side
        this.getGroupTreeStore().suspendAutoSync();

        // clear all records in tree store
        this.getGroupTreeStore().getRootNode().removeAll();

        // resume autosync feature
        this.getGroupTreeStore().resumeAutoSync();

        // clear element grid
        this.getElementGridsStore().removeAll();

        // clear all elements in viewport
        this.getViewport().removeAll();

        // trigger upstart
        // this.onLaunch();

        */
    }
});

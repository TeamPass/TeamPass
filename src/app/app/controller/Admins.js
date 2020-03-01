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

Ext.define('TeamPass.controller.Admins', {
    extend: 'TeamPass.controller.Abstract',

    requires:[
        'TeamPass.view.AdminPanel',
        'TeamPass.view.AdminTreePanel',
        'TeamPass.view.Admin.Group.Container',
        'TeamPass.view.Admin.User.Container',
        'TeamPass.view.Admin.User.Grid',
        'TeamPass.view.Admin.Permissions.Container',
        'TeamPass.view.Admin.Settings.Container',
        'TeamPass.view.Admin.Settings.Grid',
        'TeamPass.view.Admin.Directory.Container',
        'TeamPass.view.Admin.Directory.Grid',
        'TeamPass.view.Admin.Directory.Implementation.Container'
    ],

    refs: [
        {
            selector: 'menupanel',
            ref: 'MenuPanel'
        }, {
            selector: 'mainpanel',
            ref: 'MainPanel'
        }, {
            selector: 'adminpanel',
            ref: 'AdminPanel'
        }, {
            selector: 'centerpanel',
            ref: 'CenterPanel'
        }
    ],

    models: [
        'AdminTree'
    ],

    stores: [
        'AdminTree'
    ],

    init: function() {

        this.control({
            'admintreepanel' : {
                activate : this.onAdminTreePanelTabVisible,
                itemclick: this.onAdminTreeElementClick
            }
        });

        this.application.on({
            appstart: this.onAppStart,
            scope:this
        });

        this.listen({
            store: {
                '#AdminTree': {
                    beforeload: this.loadAdminTreeStore,
                    beforesync: this.syncAdminTreeStore
                }
            }
        });
    },

    onEditDirectory: function(a, menu, record) {
        this.createTab("widget.admindirectoryimplementationcontainer", {implementationClass: menu.implementationClass});
    },

    onAppStart:function() {
        isAdmin = this.getSessionDataValue('isAdmin');
        if (isAdmin === true) {
            var adminGrid = Ext.create("widget.admintreepanel");
            this.getAdminTreeStore().load();
            this.getMenuPanel().add(adminGrid);
            this.getMenuPanel().setActiveTab(0);

            var adminPanel = Ext.create("widget.adminpanel");
            this.getMainPanel().add(adminPanel);
            this.getMainPanel().setActiveTab(0);
        }
    },

    loadAdminTreeStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminTreeStore: function() {
        this.setCSRFToken(this.getAdminTreeStore());
    },

    onAdminTreeElementClick: function(view, record) {
        alias = record.get("alias");

        this.createTab(alias);
    },

    onAdminTreePanelTabVisible: function() {
        this.getCenterPanel().setVisible(false);
        this.getAdminPanel().setVisible(true);
    },

    createTab: function(alias, params) {
        var panel = Ext.create(alias, params);
        this.getAdminPanel().removeAll();
        this.getAdminPanel().add(panel);
    }
});

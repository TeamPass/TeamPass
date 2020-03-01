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

Ext.define('TeamPass.controller.Admin.Settings', {
    extend: 'TeamPass.controller.Abstract',

    requires:[
        'TeamPass.view.Admin.Settings.Grid'
    ],

    refs: [{
        selector: 'adminsettingsgrid',
        ref: 'AdminSettingsGrid'
    }],

    models: [
        'Admin.SettingGrid'
    ],

    stores: [
        'Admin.SettingGrid'
    ],

    init: function() {

        this.control({
            'adminsettingsgrid' : {
                afterrender : this.onAdminSettingsGridAfterRender
            },
            'adminsettingsgrid [itemId=saveBtn]' : {
                click : this.onSettingSaveClick
            }
        });

        this.listen({
            store: {
                '#Admin.SettingGrid': {
                    beforeload: this.loadAdminSettingGridStore,
                    beforesync: this.syncAdminSettingGridStore
                }
            }
        });
    },

    loadAdminSettingGridStore: function(store) {
        this.setCSRFToken(store);
    },

    syncAdminSettingGridStore: function() {
        this.setCSRFToken(this.getAdminSettingGridStore());
    },

    onAdminSettingsGridAfterRender: function() {
        this.getAdminSettingGridStore().load();
    },

    onSettingSaveClick: function () {
        this.getAdminSettingGridStore().sync();
    }
});

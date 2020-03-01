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

Ext.define('TeamPass.view.ChangePasswordPanel', {
    extend: 'Ext.form.Panel',
    alias: 'widget.changepasswordpanel',

    initComponent: function() {
        this.callParent();
    },

    defaults: {
        width:400,
        labelWidth: 200
    },
    align: 'center',
    trackResetOnLoad: true,
    items   : [{
            fieldLabel: TeamPass.Locales.gettext("SETTINGS.CURRENT_PASSWORD_TEXT"),
            xtype: 'textfield',
            name: 'password',
            allowBlank: false,
            inputType: 'password',
            msgTarget: 'under',
            margin: "30 0 10 40"
        }, {
            fieldLabel: TeamPass.Locales.gettext("SETTINGS.NEW_PASSWORD_TEXT"),
            name: 'newPassword',
            xtype: 'textfield',
            allowBlank: false,
            inputType: 'password',
            msgTarget: 'side',
            margin: "10 0 10 40"
        }, {
            fieldLabel: TeamPass.Locales.gettext("SETTINGS.REPEAT_NEW_PASSWORD_TEXT"),
            name: 'repeatedNewPassword',
            xtype: 'textfield',
            allowBlank: false,
            inputType: 'password',
            msgTarget: 'side',
            margin: "10 0 10 40"
        },{
            xtype: 'button',
            text: TeamPass.Locales.gettext("SETTINGS.SET_PASSWORD_BTN"),
            name: 'submit',
            itemId: 'submitBtn',
            formBind: true,
            align:'center',
            margin: "30 0 10 200",
            width:150
    }]
});


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

Ext.define('TeamPass.view.ChangeRSAKeyPanel', {
    extend: 'Ext.form.Panel',
    alias: 'widget.changersakeypanel',

    initComponent: function() {
        this.callParent();
    },
    defaults: {
        width:400,
        labelWidth: 200
    },
    align: 'center',
    trackResetOnLoad: true,
    items: [{
        fieldLabel: TeamPass.Locales.gettext("SETTINGS.CURRENT_RSA_PASSPHRASE_TEXT"),
        xtype: 'textfield',
        name: 'currentPassphrase',
        allowBlank: false,
        inputType: 'password',
        msgTarget: 'side',
        margin: "30 0 10 40"
    },{
        fieldLabel: TeamPass.Locales.gettext("SETTINGS.NEW_RSA_PASS_PHRASE_TEXT"),
        name: 'passphrase',
        xtype: 'textfield',
        allowBlank: false,
        inputType: 'password',
        msgTarget: 'side',
        margin: "10 0 10 40"
    }, {
        fieldLabel: TeamPass.Locales.gettext("SETTINGS.REPEAT_NEW_RSA_PASS_PHRASE_TEXT"),
        name: 'passphrase2',
        xtype: 'textfield',
        allowBlank: false,
        inputType: 'password',
        msgTarget: 'side',
        margin: "10 0 10 40"
    }, {
        xtype: 'button',
        text: TeamPass.Locales.gettext("SETTINGS.SUBMIT_NEW_RSA_PASS_PHRASE_TEXT"),
        name: 'submit',
        formBind: true,
        align:'center',
        margin: "30 0 10 200",
        width:'auto'
    }]
});


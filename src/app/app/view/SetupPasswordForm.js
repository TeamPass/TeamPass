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

Ext.define('TeamPass.view.SetupPasswordForm', {
    extend: 'Ext.form.Panel',
    alias: 'widget.setuppasswordform',

    border:false,
    margin: '10 10 10 10',
    bodyPadding: 10,
    defaults: {
        anchor: '100%'
    },
    items: [{
            fieldLabel: TeamPass.Locales.gettext("SETUP.PASSWORD"),
            xtype: 'textfield',
            inputType: 'password',
            name: 'pass',
            id: 'pass',
            allowBlank:false
        }, {
            fieldLabel: TeamPass.Locales.gettext("SETUP.REPEAT_PASSWORD"),
            xtype: 'textfield',
            inputType: 'password',
            name: 'pass-cfrm',
            vtype: 'password',
            allowBlank:false,
            initialPassField: 'pass'
        }, {
            xtype: 'button',
            text: TeamPass.Locales.gettext("SETUP.PROCEED"),
            width:"150",
            formBind: true,
            margin: "20 0 0 0"
    }],
    initComponent: function() {
        Ext.apply(Ext.form.field.VTypes, {
            password: function(val, field) {
                if (field.initialPassField) {
                    var pwd = field.up('form').down('#' + field.initialPassField);
                    return (val == pwd.getValue());
                }
                return true;
            },
            passwordText: TeamPass.Locales.gettext("SETUP.PASSWORD_MISMATCH")
        });
        this.callParent();
    }
});

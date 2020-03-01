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

Ext.define('TeamPass.view.LoginForm', {
    extend: 'Ext.form.Panel',
    alias: 'widget.loginform',
    initComponent: function(){
        this.callParent(arguments);
    },
    frame:true,
    floating:true,
    modal: true,
    type:'vbox',
    align:'center',
    pack:'center',
    title: TeamPass.Locales.gettext("LOGIN.HEADLINE"),
    bodyStyle:'padding:5px 5px 0px',
    width: 350,
    fieldDefaults: {
        msgTarget: 'side',
        labelWidth: 100
    },
    defaultType: 'textfield',
    defaults: {
        anchor: '90%'
    },
    items: [{
        fieldLabel: TeamPass.Locales.gettext("LOGIN.USERNAME"),
        name: 'userName',
        allowBlank: false,
        msgTarget: 'under'
    },{
        fieldLabel: TeamPass.Locales.gettext("LOGIN.PASSWORD"),
        name: 'password',
        allowBlank: false,
        inputType: 'password',
        msgTarget: 'under'
    }, {
        xtype: 'combo',
        fieldLabel: TeamPass.Locales.gettext("LOGIN.LANGUAGE"),
        hiddenName: 'lang',
        name: 'language',
        store: 'Language',
        valueField: 'value',
        displayField: 'text',
        triggerAction: 'all',
        typeAhead: true,
        queryMode: 'local',
        value: "default",
        editable: true
    }],
    buttons: [{
        text: TeamPass.Locales.gettext("LOGIN.LOGINBTN"),
        itemId : 'loginbtn',
        iconCls: 'x-fa fa-sign-in',
        formBind: false, //only enabled once the form is valid
        disabled: false
    },{
        text: TeamPass.Locales.gettext("LOGIN.RESETBTN"),
        iconCls: 'x-fa fa-undo',
        handler: function() {
            this.up('form').getForm().reset();
        }
    }]
});

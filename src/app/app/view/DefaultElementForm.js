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

Ext.define('TeamPass.view.DefaultElementForm', {
    extend: 'Ext.form.Panel',
    alias: 'widget.defaultelementform',
    border:false,
    margin: '10 10 10 10',
    bodyPadding: 10,
    trackResetOnLoad: false,
    defaults: {
        width:400
    },
    items: [{
            fieldLabel: TeamPass.Locales.gettext("ELEMENT.DEFAULT_FORM_TITLE"),
            xtype: 'textfield',
            name: 'title',
            allowBlank: false,
            msgTarget: 'side'
        },{
        xtype: 'fieldcontainer',
        layout: 'hbox',
        border:false,
        width:600,
        items:[{
                fieldLabel: TeamPass.Locales.gettext("ELEMENT.DEFAULT_FORM_URL"),
                name: 'url',
                xtype: 'textfield',
                allowBlank: true,
                msgTarget: 'side',
                width:400
            }, {
                xtype: 'button',
                itemId: 'openurlbtn',
                text: TeamPass.Locales.gettext("ELEMENT.DEFAULT_FORM_OPEN_URL_BTN"),
                margin: '0 0 0 5'
            }]
        }, {
            fieldLabel: TeamPass.Locales.gettext("ELEMENT.DEFAULT_FORM_USERNAME"),
            name: 'username',
            xtype: 'textfield',
            allowBlank: true,
            msgTarget: 'side'
        }, {
            xtype: 'fieldcontainer',
            layout: 'hbox',
            border:false,
            width:600,
            items:[{
                    fieldLabel: TeamPass.Locales.gettext("ELEMENT.DEFAULT_FORM_PASSWORD"),
                    name: 'password',
                    xtype: 'textfield',
                    inputType:'password',
                    allowBlank: true,
                    msgTarget: 'side',
                    width:400
                }, {
                    xtype: 'button',
                    itemId: 'hidebtn',
                    text: TeamPass.Locales.gettext("ELEMENT.DEFAULT_FORM_UNHIDE_BTN"),
                    margin: '0 0 0 5',
                    enableToggle: true
                }, {
                    xtype: 'button',
                    itemId: 'copybtn',
                    disabled: true,
                    text: TeamPass.Locales.gettext("ELEMENT.DEFAULT_FORM_COPY_BTN"),
                    margin: '0 0 0 5'
            }]
        }, {
            xtype: 'textarea',
            fieldLabel: TeamPass.Locales.gettext("ELEMENT.DEFAULT_FORM_COMMENT"),
            labelAlign: 'top',
            width:600,
            height: 130,
            name: 'comment',
            grow: true,
            allowBlank: true,
            maxLength: 255,
            enforceMaxLength: true,
            msgTarget: 'side'
        }, {
            xtype: 'button',
            text: TeamPass.Locales.gettext("ELEMENT.DEFAULT_FORM_SAVE_BTN"),
            name: 'submit',
            formBind: true,
            align:'center',
            width:150
    }],

    initComponent: function() {
        this.callParent();
    },

    /**
     * toggle the hide button and change the dom type for the password field
     *
     * @param button
     *
     * @return void
     */
    toggleHideBtn: function(button) {

        var form = this;

        if(form.down('textfield[name=password]').inputEl.dom.type == 'text'){
            form.down('textfield[name=password]').inputEl.dom.type = 'password';
            button.setText(TeamPass.Locales.gettext("ELEMENT.DEFAULT_FORM_UNHIDE_BTN"));
        }
        else{
            form.down('textfield[name=password]').inputEl.dom.type = 'text';
            button.setText(TeamPass.Locales.gettext("ELEMENT.DEFAULT_FORM_HIDE_BTN"));
        }
    },

    disableFormFields: function() {
        var form = this;

        form.down("button[name=submit]").setDisabled(true);
        form.down("textfield[name=password]").setDisabled(true);
        form.down("textfield[name=url]").setDisabled(true);
        form.down("textfield[name=username]").setDisabled(true);
        form.down("textfield[name=title]").setDisabled(true);
        form.down("textarea[name=comment]").setDisabled(true);
    }

});

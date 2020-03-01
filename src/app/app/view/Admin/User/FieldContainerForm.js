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

Ext.define('TeamPass.view.Admin.User.FieldContainerForm', {
    extend: 'Ext.form.FieldContainer',
    alias: 'widget.adminuserfieldcontainerform',

    layout: 'vbox',
    flex: 1,
    border:false,
    items:[{
        fieldLabel: TeamPass.Locales.gettext("ADMIN.USER_DETAIL_FORM_USERNAME"),
        xtype: 'textfield',
        name: 'userName',
        labelWidth: 115,
        padding: "30 0 0 30",
        width: "95%",
        maxwidth: 400,
        allowBlank: false
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.USER_DETAIL_FORM_FULLNAME"),
        xtype: 'textfield',
        padding: "0 0 0 30",
        labelWidth: 115,
        width: "95%",
        maxwidth: 400,
        name: 'fullName'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.USER_DETAIL_FORM_PASSWORD"),
        name: 'newPassword',
        padding: "0 0 0 30",
        xtype: 'textfield',
        inputType:'password',
        width: "95%",
        labelWidth: 115,
        maxwidth: 400,
        allowBlank: true,
        msgTarget: 'top'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.USER_DETAIL_FORM_EMAIL"),
        xtype: 'textfield',
        padding: "0 0 0 30",
        name: 'emailAddress',
        labelWidth: 115,
        width: "95%",
        maxwidth: 400,
        allowBlank: false
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.USER_DETAIL_FORM_ENABLED"),
        xtype: 'checkboxfield',
        padding: "0 0 0 30",
        labelWidth: 115,
        name: 'enabled'
    }],

    initComponent: function() {
        this.callParent();
    }
});

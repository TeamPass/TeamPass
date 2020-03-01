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

Ext.define('TeamPass.view.Admin.Directory.Implementation.Form.Ldap', {
    extend: 'Ext.form.Panel',
    alias: 'widget.admindirectoryimplementationformldap',

    defaults: {
        width:"95%",
        labelWidth: 200,
        margin: '15 30 15 30'
    },
    scrollable: true,
    trackResetOnLoad: true,
    items   : [{
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_TYPE_LABEL"),
        xtype: 'displayfield',
        name: 'implementationClass'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_NAME_LABEL"),
        name: 'name',
        xtype: 'textfield',
        allowBlank: false,
        msgTarget: 'side'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_HOSTNAME_LABEL"),
        name: 'hostname',
        xtype: 'textfield',
        allowBlank: false,
        msgTarget: 'side'
    } ,{
      xtype: 'radiogroup',
      fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_SECURITY_LABEL"),
      items: [
          {boxLabel: 'none', name: 'security', inputValue: "none"},
          {boxLabel: 'SSL', name: 'security', inputValue: "ssl"},
          {boxLabel: 'TLS', name: 'security', inputValue: "tls"}
      ],
      listeners: {
          change: function(scope, newValue) {

              var port = scope.up().down("textfield[name=port]");

              if (port.isDirty()) {
                  return;
              }

              if (newValue.security == "ssl") {
                  port.setValue(TeamPass.Util.Settings.getLdapDefaultSSLPort());
                  port.clearDirty();
              } else {
                  port.setValue(TeamPass.Util.Settings.getLdapDefaultPort());
                  port.clearDirty();
              }
          }
      }
    }, {
      fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_PORT_LABEL"),
      name: 'port',
      xtype: 'formfieldnumber',
      decimalPrecision: 0,
      minValue: 1,
      maxValue: 65536,
      allowBlank: false,
      msgTarget: 'side'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_ANONYMOUS_BIND_LABEL"),
        name: 'anonymous',
        xtype: 'checkbox',
        listeners: {
            change: function(scope, newValue) {
                if (newValue) {
                    scope.up().down("textfield[name=ldapAdminDn]").disable();
                    scope.up().down("textfield[name=ldapAdminPassword]").disable();
                } else {
                    scope.up().down("textfield[name=ldapAdminDn]").enable();
                    scope.up().down("textfield[name=ldapAdminPassword]").enable();
                }
            }
        }
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_USERNAME_LABEL"),
        name: 'ldapAdminDn',
        xtype: 'textfield',
        allowBlank: true,
        msgTarget: 'side'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_PASSWORD_LABEL"),
        name: 'ldapAdminPassword',
        xtype: 'textfield',
        inputType:'password',
        allowBlank: true,
        msgTarget: 'side'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_BASEDN_LABEL"),
        name: 'ldapBasedn',
        xtype: 'textfield',
        allowBlank: false,
        msgTarget: 'side'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_SYNCINTERVAL_LABEL"),
        name: 'ldapSynchroniseIntervalInMin',
        xtype: 'textfield',
        allowBlank: true,
        msgTarget: 'side'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_READ_TIMEOUT_LABEL"),
        name: 'ldapReadTimeoutInSec',
        xtype: 'textfield',
        allowBlank: true,
        msgTarget: 'side'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_OBJECTCLASS_LABEL"),
        name: 'ldapUserObjectclass',
        xtype: 'textfield',
        allowBlank: true,
        msgTarget: 'side'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_FILTER_LABEL"),
        name: 'ldapUserFilter',
        xtype: 'textfield',
        allowBlank: true,
        msgTarget: 'side'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_USER_ATTR_LABEL"),
        name: 'ldapUserUsername',
        xtype: 'textfield',
        allowBlank: true,
        msgTarget: 'side'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_DISPLAYNAME_ATTR_LABEL"),
        name: 'ldapUserDisplayname',
        xtype: 'textfield',
        allowBlank: true,
        msgTarget: 'side'
    }, {
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_EMAIL_ATTR_LABEL"),
        name: 'ldapUserEmail',
        xtype: 'textfield',
        allowBlank: true,
        msgTarget: 'side'
    }, {
        buttonAlign: 'center',
        buttons: [{
            xtype: 'button',
            text: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_SUBMIT_BTN_TEXT"),
            name: 'submit',
            disabled: true,
            itemId: 'submitBtn',
            formBind: true,
            width: 200
        }]
    }]
});

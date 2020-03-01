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

Ext.define('TeamPass.view.SettingsWindow', {
    extend: 'Ext.window.Window',
    alias: 'widget.settingswindow',

    requires:[
        'TeamPass.view.ChangePasswordPanel',
        'TeamPass.view.ChangeLanguagePanel',
        'TeamPass.view.ChangeRSAKeyPanel',
        'TeamPass.view.Settings.MiscPanel'
    ],
    modal: true,
    width: 600,
    height:480,
    border:false,
    title: TeamPass.Locales.gettext("SETTINGS.WINDOW_TITLE"),
    closable: true,
    align: 'fit',
    layout: 'accordion',
    items: [{
        xtype:'changepasswordpanel',
        title: TeamPass.Locales.gettext("SETTINGS.CHANGE_PASSWORD_FORM_TITLE"),
        border:false,
        disabled: true
    }, {
        xtype:'changelanguagepanel',
        title: TeamPass.Locales.gettext("SETTINGS.CHANGE_LANGUAGE_FORM_TITLE"),
        border:false
    },{
        xtype:'changersakeypanel',
        title: TeamPass.Locales.gettext("SETTINGS.GENERATE_NEW_RSA_KEY_FORM_TITLE"),
        border:false
    },{
        xtype:'settingsmiscpanel',
        title: TeamPass.Locales.gettext("SETTINGS.MISC_PANEL_TITLE"),
        border:false
    }],
    initComponent: function() {
        this.callParent();
    }
});

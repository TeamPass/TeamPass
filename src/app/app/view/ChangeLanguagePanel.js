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

Ext.define('TeamPass.view.ChangeLanguagePanel', {
    extend: 'Ext.form.Panel',
    alias: 'widget.changelanguagepanel',

    initComponent: function() {
        this.callParent();
    },

    defaults: {
        width:400,
        labelWidth: 200
    },
    align: 'center',
    trackResetOnLoad: true,
    items : [
        {
            fieldLabel: "Current default language",
            xtype: 'displayfield',
            name: 'currentDefaultLanguage',
            margin: "30 0 10 40"
        },
        {
            xtype: 'combo',
            fieldLabel: TeamPass.Locales.gettext("SETTINGS.SET_LANGUAGE_TEXT"),
            hiddenName: 'lang',
            name: "language",
            store: 'AllLanguages',
            valueField: 'value',
            displayField: 'text',
            triggerAction: 'all',
            queryMode: 'local',
            value: "en",
            typeAhead: false,
            editable: false,
            margin: "10 0 10 40"
        },
        {
            xtype: 'button',
            text: TeamPass.Locales.gettext("SETTINGS.SET_LANGUAGE_BTN"),
            name: 'submit',
            itemId: 'submitBtn',
            formBind: true,
            align:'center',
            margin: "30 0 10 230",
            width:'auto'
        }
    ],
    updateCurrentLangCode: function(langCode) {
        this.down('displayfield').setValue(langCode);
        this.down('combo').setValue(langCode);
    }
});


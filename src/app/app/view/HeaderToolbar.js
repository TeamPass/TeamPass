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

Ext.define('TeamPass.view.HeaderToolbar', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.headertoolbar',
    initComponent: function(){
        this.callParent(arguments);
    },
    border:true,
    items:[{
        xtype:'tbtext',
        name: 'userText',
        itemId: 'welcometext',
        text: ''
    },{
        xtype: 'tbfill'
    },{
        xtype: "button",
        itemId: 'encryptionBtn',
        cls : 'encryptionBtnNormal',
        iconCls: 'x-fa fa-key',
        text:  TeamPass.Locales.gettext("HEADER.ENCRYPTION_BUTTON"),
        margins: {
            left:15,
            right: 15
        },
        hidden: true
    },{
        xtype: 'button',
        iconCls: 'x-fa fa-align-justify',
        itemId: 'settingsBtn',
        text:  TeamPass.Locales.gettext("HEADER.SETTINGSBTN")
    },{
        xtype: 'button',
        iconCls: 'x-fa fa-sign-out',
        text:  TeamPass.Locales.gettext("HEADER.LOGOUTBTN"),
        itemId: 'logoutbtn',
        margins: {
            left:15,
            right: 15
        }
    }]
});

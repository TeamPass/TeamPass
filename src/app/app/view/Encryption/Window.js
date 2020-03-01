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

Ext.define('TeamPass.view.Encryption.Window', {
    extend: 'Ext.window.Window',
    alias: 'widget.encryption.window',

    requires:[
        'TeamPass.view.Encryption.WrapperPanel'
    ],
    modal: true,
    width: 600,
    height: 450,
    layout: "fit",
    border: false,
    title: TeamPass.Locales.gettext("ENCRYPTION.WINDOW_TITLE"),
    closable: true,
    dockedItems: [{
        xtype: 'toolbar',
        dock: 'top',
        items: [{
            xtype:'tbtext',
            itemId: 'statustoolbar'
        },{
            xtype: 'tbfill'
        }, {
            xtype: 'button',
            iconCls: 'x-fa fa-key',
            itemId: 'encryptallbtn',
            text: TeamPass.Locales.gettext("ENCRYPTION.ENCRYPT_ALL_BTN")
        }]
    }],

    items:[{
        xtype: 'encryption.grid'
    }],

    initComponent: function() {
        this.callParent();
    }
});

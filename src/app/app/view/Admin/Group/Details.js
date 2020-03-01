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

Ext.define('TeamPass.view.Admin.Group.Details', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.admingroupdetails',

    requires:[
        'TeamPass.view.Admin.Group.Detail.Form',
        'TeamPass.view.Admin.Group.Detail.Panel'
    ],
    layout: {
        type: 'vbox',
        pack: 'start',
        align: 'stretch'
    },
    border: true,
    split: true,
    autoScroll:true,
    trackResetOnLoad: true,
    items:[{
        xtype: 'admingroupdetailform',
        flex: 1
    }, {
        xtype: 'admingroupdetailpanel',
        flex: 5
    }],
    dockedItems: [{
        xtype: 'toolbar',
        border:false,
        dock: 'top',
        items: [{
            xtype: 'button',
            itemId: 'saveBtn',
            iconCls: 'x-fa fa-floppy-o',
            text:  TeamPass.Locales.gettext("ADMIN.GROUP_GRID_SAVE_BTN")
        }]
    }],
    initComponent: function() {
        this.callParent();
    }
});

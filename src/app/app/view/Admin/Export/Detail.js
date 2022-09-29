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

Ext.define('TeamPass.view.Admin.Export.Detail', {
    extend: 'Ext.form.Panel',
    alias: 'widget.adminexportdetail',

    region: 'north',
    border:true,
    defaults: {
        width: "97%",
        height: "150",
        margin: '10'
    },
    items: [{
        xtype: 'textarea',
        fieldLabel: TeamPass.Locales.gettext("ADMIN.EXPORT_DETAIL_STATUS_TEXTAREA_LABEL"),
        itemId: "exportGroupStatusArea",
        labelAlign: 'top',
        readOnly: true,
        name: 'status',
        grow: true,
        allowBlank: true
    }],
    dockedItems: [{
        xtype: 'toolbar',
        dock: 'top',
        items: [{
            xtype:'tbtext',
            name: 'exportGroupNameLabel',
            itemId: 'exportGroupNameLabel',
            text: 'selected: '
        }, {
            xtype:'tbtext',
            name: 'exportGroupName',
            itemId: 'exportGroupName',
            text: ''
        }, {
            xtype: 'hidden',
            name: 'exportGroupId',
            itemId: 'exportGroupId',
            text: ''
        }, {
            xtype: 'tbfill'
        },{
            text: TeamPass.Locales.gettext("ADMIN.EXPORT_DETAIL_TOOLBAR_BTN"),
            xtype: 'button',
            itemId: 'exportBtn',
            disabled: true
        }]
    }]
});

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

Ext.define('TeamPass.view.Admin.Directory.Implementation.Preview.Status', {
    extend: 'Ext.form.Panel',
    alias: 'widget.admindirectoryimplementationpreviewstatus',

    region: 'north',
    border:true,
    defaults: {
        width: "97%",
        height: "150",
        margin: '10'
    },
    items: [{
        xtype: 'textarea',
        fieldLabel: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_PREVIEW_STATUS_TEXTAREA_LABEL"),
        labelAlign: 'top',
        readOnly: true,
        name: 'status',
        grow: false,
        allowBlank: true
    }],
    dockedItems: [{
        xtype: 'toolbar',
        dock: 'top',
        items: [{
            text: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_PREVIEW_STATUS_CONN_TEST_BTN_TEXT"),
            xtype: 'button',
            itemId: 'connectionTestBtn'
        }]
    }]
});

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

Ext.define('TeamPass.view.Admin.Group.Detail.Form', {
    extend: 'Ext.form.Panel',
    alias: 'widget.admingroupdetailform',

    layout: {
        type:'hbox'
    },
    border:false,
    trackResetOnLoad: true,
    items:[{
        fieldLabel:  TeamPass.Locales.gettext("ADMIN.GROUP_DETAIL_NAME_TEXT"),
        xtype: 'textfield',
        name: 'groupName',
        padding: "30 0 0 30",
        allowBlank: false
    },{
        fieldLabel:  TeamPass.Locales.gettext("ADMIN.GROUP_DETAIL_ISADMIN_TEXT"),
        xtype: 'checkboxfield',
        padding: "30 0 0 50",
        name: 'isAdmin'
    }],
    initComponent: function() {
        this.callParent();
    }
});

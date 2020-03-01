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

Ext.define('TeamPass.view.Admin.User.Detail', {
    extend: 'Ext.form.Panel',
    alias: 'widget.adminuserdetail',

    requires:[
        'TeamPass.view.Admin.User.AvailableGroupsGrid',
        'TeamPass.view.Admin.User.CurrentGroupsGrid',
        'TeamPass.view.Admin.User.ReadOnlyFieldContainerForm',
        'TeamPass.view.Admin.User.FieldContainerForm'
    ],
    border: true,
    region:'south',
    split: true,
    autoScroll:true,
    trackResetOnLoad: true,
    items   : [{
            xtype: 'fieldcontainer',
            layout: 'hbox',
            items:[{
                xtype: 'adminuserfieldcontainerform',
                flex: 1
            }, {
                xtype: 'adminuseravailablegroupsgrid',
                flex: 1
            }, {
                xtype: 'adminusercurrentgroupsgrid',
                flex: 1
            }]
    }],
    initComponent: function() {
        this.callParent();
    }
});

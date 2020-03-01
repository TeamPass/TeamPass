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

Ext.define('TeamPass.view.Admin.Permissions.Container', {
    extend: 'Ext.container.Container',
    alias: 'widget.adminpermissionscontainer',

    requires:[
        'TeamPass.view.Admin.Permissions.TreeGrid',
        'TeamPass.view.Admin.Permissions.DetailGrid',
        'TeamPass.view.Admin.Permissions.AvailableGroupsGrid'
    ],
    initComponent: function(){
        Ext.apply(this, {
            region: 'center',
            autoScroll:false,
            layout: {
                type: 'hbox',
                align : 'stretch',
                pack  : 'start'
            },
            items: [{
                xtype: 'adminpermissionstreegrid',
                padding: '0 10 0 10',
                flex:2
            }, {
                xtype: 'adminpermissionsdetailgrid',
                padding: '0 10 0 10',
                flex: 4
            }, {
                xtype: 'adminpermissionsavailablegroupsgrid',
                padding: '0 10 0 10',
                flex: 1
            }]
        });
        this.callParent(arguments);
    }
});

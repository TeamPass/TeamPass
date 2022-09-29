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

Ext.define('TeamPass.view.Admin.Export.Container', {
    extend: 'Ext.container.Container',
    alias: 'widget.adminexportcontainer',

    requires:[
        'TeamPass.view.Admin.Export.TreeGrid',
        'TeamPass.view.Admin.Export.Detail'
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
                xtype: 'adminexporttreegrid',
                padding: '0 10 0 10',
                flex: 1
            },{
                xtype: 'adminexportdetail',
                padding: '0 10 0 10',
                flex: 1
            }]
        });
        this.callParent(arguments);
    }
});
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

Ext.define('TeamPass.view.Admin.Group.Detail.Panel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.admingroupdetailpanel',

    requires:[
        'TeamPass.view.Admin.Group.AvailableUsersGrid',
        'TeamPass.view.Admin.Group.CurrentUsersGrid'
    ],
    layout: {
        type: 'hbox',
        pack: 'start',
        align: 'stretch'
    },
    border: false,
    trackResetOnLoad: true,
    items:[{
        xtype: 'admingroupavailableusersgrid',
        flex: 1
    }, {
        xtype: 'admingroupcurrentusersgrid',
        flex: 1
    }],
    initComponent: function() {
        this.callParent();
    }
});

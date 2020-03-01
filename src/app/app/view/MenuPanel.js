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

Ext.define('TeamPass.view.MenuPanel', {
    extend: 'Ext.tab.Panel',
    alias: 'widget.menupanel',
    requires:[
        'TeamPass.view.GroupTreePanel',
        'TeamPass.view.AdminTreePanel'
    ],
    stateful: true,
    stateId: 'menupanel',
    region: 'west',
    width: 250,
    margins: '0 0 10 10',
    minWidth: 150,
    maxWidth: 500,
    split: true,
    layout:'border',
    border: false,
    tabPosition: 'bottom',
    items: [{
            xtype: 'grouptreepanel',
            title: "TeamPass"
    }]
});

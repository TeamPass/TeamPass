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

Ext.define('TeamPass.view.ElementPanel', {
    extend: 'Ext.Panel',
    alias: 'widget.elementpanel',
    requires:[
        'TeamPass.view.ElementForm'
    ],

    margin:'0 10 10 10',
    region:'south',
    items: [{
            xtype: 'elementform'
        }],
    initComponent: function() {

        this.callParent();
    }
});


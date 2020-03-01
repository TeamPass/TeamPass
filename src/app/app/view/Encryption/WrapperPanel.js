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

Ext.define('TeamPass.view.Encryption.WrapperPanel', {
    extend: 'Ext.container.Container',
    alias: 'widget.encryption.wrapperpanel',

    requires:[
        'TeamPass.view.Encryption.StatusPanel',
        'TeamPass.view.Encryption.Grid'
    ],
    region: 'center',
    layout: 'border',
    autoScroll:false,
    items:[{
        xtype:'encryption.grid'
    }],
    initComponent: function() {
        this.callParent();
    }
});


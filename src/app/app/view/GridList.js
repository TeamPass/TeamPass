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

Ext.define('TeamPass.view.GridList', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.gridlist',

    initComponent: function(){
        Ext.apply(this, {
            border: true,
            useArrows: true,
            region:'center',
            cls: 'no-leaf-icons',
            store: 'ElementGrids',
            stateful: true,
            stateId: 'gridlist',
            columns: [{
                text:  TeamPass.Locales.gettext("ELEMENTGRID.TITLE"),
                sortable: true,
                dataIndex: 'title',
                flex:1
            }, {
                text:  TeamPass.Locales.gettext("ELEMENTGRID.URL"),
                sortable: true,
                id: 'dummyurl',
                dataIndex: 'dummyurl',
                flex:1
            }, {
                text:  TeamPass.Locales.gettext("ELEMENTGRID.USERNAME"),
                sortable: true,
                id: 'dummyusername',
                dataIndex: 'dummyusername',
                flex:1
            }, {
                text:  TeamPass.Locales.gettext("ELEMENTGRID.PASSWORD"),
                sortable: true,
                id: 'dummypassword',
                dataIndex: 'dummypassword',
                flex:1
            }, {
                text:  TeamPass.Locales.gettext("ELEMENTGRID.COMMENT"),
                dataIndex: 'comment',
                flex:3
            }]
        });
        this.callParent(arguments);
    }
});


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

Ext.define('TeamPass.view.Directory.SyncActionColumn', {
    extend: 'Ext.grid.column.Column',
    alias: 'widget.admindirectorysyncactioncolumn',

    processEvent: function(type, view, cell, recordIndex, cellIndex, e, record, row) {
        if (record.get("type") == "internal") {
            return this.callParent(arguments);
        }
        else {
            return TeamPass.view.Column.superclass.superclass.processEvent.apply(this, arguments);
        }
    },

    renderer : function(value, meta, record) {
        if (record.isLeaf()) {
            return this.callParent(arguments);
        }
        return '';
    }
});

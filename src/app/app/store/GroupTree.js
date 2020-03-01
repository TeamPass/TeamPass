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

Ext.define('TeamPass.store.GroupTree', {
    extend: 'TeamPass.store.AbstractTreeStore',

    requires: 'TeamPass.model.GroupTree',
    model: 'TeamPass.model.GroupTree',

    autoLoad:false,
    autoSync: true,

    root: {
        expanded: true,
        loaded:true
    },
    proxy: {
        type: 'rest',
        url: '/api/v1/grouptree/',
        reader: {
            type: 'json',
            rootProperty: 'children'
        },
        writer: {
            type: 'json',
            transform: {
                fn: function(data, request) {
                    if (!Array.isArray(data)) {
                        data = [data];
                    }
                    return {treeNodeCollection: data};
                },
                scope: this
            },
            allowSingle: true
        }
    }
});

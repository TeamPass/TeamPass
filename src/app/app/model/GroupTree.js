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

Ext.define('TeamPass.model.GroupTree', {
    extend: 'Ext.data.TreeModel',

    idProperty: 'id',
    fields: [{
        name: 'id'
    }, {
        name: 'parentId',
        type: 'string',
        defaultValue: null
    }, {
        name: 'isRoot',
        type: 'boolean',
        defaultValue: false
    }, {
        name: 'text',
        type:'string'
    }, {
        name:'recordid',
        type:'int'
    }, {
        name: 'index',
        type: 'int'
    }, {
        name: 'leaf',
        type: 'boolean'
    }, {
        name: 'expanded',
        defaultValue: true
    }, {
        name: 'children'
    }, {
        name: 'pRead',
        type: 'bool',
        defaultValue: false
    }, {
        name: 'pCreate',
        type: 'bool',
        defaultValue: false
    }, {
        name: 'pUpdate',
        type: 'bool',
        defaultValue: false
    }, {
        name: 'pDelete',
        type: 'bool',
        defaultValue: false
    }]
});

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

Ext.define('TeamPass.model.Admin.Permission', {
    extend: 'Ext.data.Model',

    idProperty: 'id',
    identifier: 'sequential',
    fields: [{
        name: 'id',
        type: 'int'
    },{
        name: 'gteId',
        type: 'int'
    }, {
        name: 'userGroupId',
        type: 'int'
    }, {
        name: 'groupName',
        type: 'string',
        persist : false
    }, {
        name: 'inherited',
        type: 'bool',
        defaultValue: false,
        persist: false
    }, {
        name: 'pRead',
        type: 'bool',
        defaultValue:false
    }, {
        name: 'pCreate',
        type: 'bool',
        defaultValue:false
    }, {
        name: 'pUpdate',
        type: 'bool',
        defaultValue:false
    }, {
        name: 'pDelete',
        type: 'bool',
        defaultValue: false
    }]
});

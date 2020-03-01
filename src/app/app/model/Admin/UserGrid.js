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

Ext.define('TeamPass.model.Admin.UserGrid', {
    extend: 'Ext.data.Model',

    idProperty: 'userId',
    fields: [{
        name: 'userId',
        type: 'int'
    }, {
        name: 'userName',
        type: 'string'
    }, {
        name: 'fullName',
        type: 'string'
    }, {
        name: 'newPassword',
        type: 'string'
    }, {
        name: 'emailAddress',
        type: 'string'
    }, {
        name: 'setupCompleted',
        type: 'boolean',
        persist : false
    }, {
        name: 'directoryType',
        type: 'string',
        persist : false
    }, {
        name: 'directoryName',
        type: 'string',
        persist : false
    }, {
        name: 'groups',
        type: 'string',
        persist : false
    }, {
        name: 'enabled',
        type: 'boolean'
    }, {
        name: 'admin',
        type: 'boolean',
        persist : false
    }, {
        name: 'deleted',
        type: 'boolean',
        persist : false
    }]
});

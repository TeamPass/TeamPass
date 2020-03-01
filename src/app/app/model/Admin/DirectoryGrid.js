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

Ext.define('TeamPass.model.Admin.DirectoryGrid', {
    extend: 'Ext.data.Model',

    idProperty: 'directoryId',
    fields: [{
        name: 'directoryId',
        type: 'int'
    }, {
        name: 'positionIndex',
        type: 'int'
    }, {
        name: 'directoryName',
        type: 'string'
    }, {
        name: 'type',
        type: 'string'
    }, {
        name: 'configuration',
        type: 'string'
    }, {
        name: 'adapter',
        type: 'string'
    }],
    proxy: {
        type: 'rest',
        url: '/api/v1/admin/directory',
        reader: {
            type: 'json'
        },
        writer: {
            type: 'json'
        }
    }
});

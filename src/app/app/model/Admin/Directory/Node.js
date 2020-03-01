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

Ext.define('TeamPass.model.Admin.Directory.Node', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id'},
        {name: 'directoryId', type: 'int'},
        {name: 'implementationClass', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'hostname', type: 'string'},
        {name: 'security', type: 'string'},
        {name: 'port', type: 'string'},
        {name: 'anonymous', type: 'boolean'},
        {name: 'ldapAdminDn', type: 'string'},
        {name: 'ldapAdminPassword', type: 'string'},
        {name: 'ldapBasedn', type: 'string'},
        {name: 'ldapSynchroniseIntervalInMin', type: 'int'},
        {name: 'ldapReadTimeoutInSec', type: 'int'},
        {name: 'ldapUserObjectclass', type: 'string'},
        {name: 'ldapUserFilter', type: 'string'},
        {name: 'ldapUserUsername', type: 'string'},
        {name: 'ldapUserDisplayname', type: 'string'},
        {name: 'ldapUserEmail', type: 'string'}
    ]
});

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

Ext.define('TeamPass.model.ElementGrid', {
    extend: 'TeamPass.model.Abstract',

    idProperty: 'elementId',
    fields: [{
        name: 'elementId',
        type: 'string',
        defaultValue: null
    }, {
        name: 'text',
        type: 'string',
        persist: false
    }, {
        name: 'title',
        type: 'string'
    }, {
        name: 'groupId',
        type: 'int'
    }, {
        name: 'comment',
        type: 'string'
    }, {
        name: 'template',
        type: 'string'
    }, {
        name: 'dummyusername',
        type: 'string',
        defaultValue: "********",
        persist: false
    }, {
        name: 'dummypassword',
        type: 'string',
        defaultValue: "********",
        persist: false
    }, {
        name: 'dummyurl',
        type: 'string',
        defaultValue: "********",
        persist: false
    }, {
        name: 'isEncrypted',
        type: 'bool'
    }, {
        name: 'rsaEncAesKey',
        type: 'string'
    }, {
        name: 'local',
        type: 'bool',
        persist: false
    }]
});

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

Ext.define('TeamPass.view.ElementForm', {
    extend: 'Ext.Panel',
    alias: 'widget.elementform',

    border:false,
    margin: '10 10 10 10',
    bodyPadding: 10,
    defaults: {
        anchor: '100%'
    },
    items: [{
        fieldLabel: 'Titel',
        xtype: 'textfield',
        name: 'name',
        allowBlank: false,
        msgTarget: 'side'
    }, {
        fieldLabel: 'URL',
        name: 'url',
        xtype: 'textfield',
        allowBlank: true,
        msgTarget: 'side'
    }, {
        fieldLabel: 'Benutzername',
        name: 'username',
        xtype: 'textfield',
        allowBlank: true,
        msgTarget: 'side'
    }, {
        fieldLabel: 'Passwort',
        name: 'pw1',
        xtype: 'textfield',
        inputType:'password',
        allowBlank: false,
        msgTarget: 'side'
    }, {
        fieldLabel: 'Wdh.',
        name: 'pw2',
        xtype: 'textfield',
        inputType:'password',
        allowBlank: false,
        msgTarget: 'side'
    }, {
        fieldLabel: 'Beschreibung',
        labelAlign: 'top',
        width:265,
        name: 'description',
        xtype: 'textarea',
        allowBlank: true
    }],
    buttons: [{
        xtype: 'button',
        text: 'Abbrechen'
    },{
        xtype: 'button',
        text: 'Speichern'
    }],

    initComponent: function() {
        this.callParent();
    }
});


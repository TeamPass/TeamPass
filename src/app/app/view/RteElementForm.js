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

Ext.define('TeamPass.view.RteElementForm', {
    extend: 'Ext.form.Panel',
    alias: 'widget.rteelementform',
    border:false,
    margin: '10 10 10 10',
    bodyPadding: 10,
    trackResetOnLoad: true,
    defaults: {
        width:400
    },
    items: [
        {
            fieldLabel: TeamPass.Locales.gettext("ELEMENT.RTE_FORM_TITLE"),
            labelAlign: "left",
            xtype: 'textfield',
            name: 'title',
            allowBlank: false,
            msgTarget: 'side'
        }, {
            name: 'rtecontent',
            xtype: 'htmleditor',
            width: '90%',
            height: 300,
            resizable: false,
            enableColors: false,
            enableAlignments: false,
            allowBlank: false
        }, {
            xtype: 'button',
            text: TeamPass.Locales.gettext("ELEMENT.RTE_FORM_SUBMIT_BTN"),
            name: 'submit',
            formBind: true,
            align:'center',
            width:150
        }
    ],
    initComponent: function() {
        this.callParent();
    }
});

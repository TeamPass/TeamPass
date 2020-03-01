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

Ext.define('TeamPass.view.Settings.MiscPanel', {
    extend: 'Ext.form.Panel',
    alias: 'widget.settingsmiscpanel',

    initComponent: function() {
        this.callParent();
    },
    defaults: {
        width:400,
        labelWidth: 200
    },
    align: 'center',
    trackResetOnLoad: true,
    items: [{
        xtype: 'button',
        text: TeamPass.Locales.gettext("SETTINGS.SUBMIT_CLEAR_LOCAL_CACHE"),
        align:'center',
        margin: "30 0 10 200",
        width: 'auto'
    },
    {
        fieldLabel: TeamPass.Locales.gettext("SETTINGS.USE_PINKTHEME_FIELDLABEL"),
        name: 'usePinkTheme',
        xtype: 'checkbox',
        width: 400,
        labelWidth: 200,
        margin: "10 0 10 100"
    },
    {
        fieldLabel: TeamPass.Locales.gettext("SETTINGS.TREE_SORT_ORDER_FIELDLABEL"),
        name: 'treeAlphabeticallyOrder',
        xtype: 'checkbox',
        width: 400,
        labelWidth: 200,
        margin: "10 0 10 100"
    }]
});

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

Ext.define('TeamPass.view.SetupPasswordWindow', {
    extend: 'Ext.window.Window',
    alias: 'widget.setuppasswordwindow',
    requires:[
        'TeamPass.view.SetupPasswordForm'
    ],
    config: {
        keyPair: false
    },
    initComponent: function() {
        Ext.apply(this, {
            title: TeamPass.Locales.gettext("SETUP.SETUP_WINDOW_TITLE"),
            height: 225,
            width: 400,
            modal: true,
            border:false,
            closable:false,
            layout: 'fit',
            items:[{xtype:'setuppasswordform'}]
        });
        this.callParent();
    }
});

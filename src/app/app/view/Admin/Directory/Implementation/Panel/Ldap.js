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

Ext.define('TeamPass.view.Admin.Directory.Implementation.Panel.Ldap', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.admindirectoryimplementationpanelldap',

    requires:[
        'TeamPass.view.Admin.Directory.Implementation.Form.Ldap',
        'TeamPass.view.Admin.Directory.Implementation.Previews'
    ],

    config: {
        implementationClass: undefined,
        directoryId: undefined
    },

    title: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_LDAP_PANEL_TITLE"),
    shrinkWrap: false,
    width: "100%",
    height: "100%",
    region: 'center',
    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    items: [{
        xtype: 'admindirectoryimplementationformldap',
        flex:1
    }, {
        xtype: 'admindirectoryimplementationpreviews',
        flex: 1
    }]
});

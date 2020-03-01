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

Ext.define('TeamPass.view.Admin.Directory.Implementation.Container', {
    extend: 'Ext.container.Container',
    alias: 'widget.admindirectoryimplementationcontainer',

    requires:[
        'TeamPass.view.Admin.Directory.Implementation.Panel.Ldap'
    ],

    config: {
        implementationClass: undefined,
        directoryId: undefined
    },

    region: 'center',
    autoScroll:false,

    loadForm: function() {
        var panel = undefined;
        if (this.getImplementationClass() == "ActiveDirectoryImplementation") {
            panel = Ext.create('widget.admindirectoryimplementationpanelldap',
                {
                    implementationClass: this.getImplementationClass(),
                    directoryId: this.getDirectoryId()
                }
            );
        }

        if (this.getImplementationClass() == "OpenLdapImplementation") {
            panel = Ext.create('widget.admindirectoryimplementationpanelldap',
                {
                    implementationClass: this.getImplementationClass(),
                    directoryId: this.getDirectoryId()
                }
            );
        }

        if (panel) {
            this.add(panel);
        }
    }
});

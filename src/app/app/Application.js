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

Ext.define('TeamPass.Application', {
    name: 'TeamPass',
    extend: 'Ext.app.Application',
    appFolder: TeamPass.Util.Settings.base_path,

    require: [
        "Ext.state.SessionStorageProvider",
        'TeamPass.Locales',
        'TeamPass.Util.Settings'
    ],

    controllers: [
        'Viewport',
        'Groups',
        'Elements',
        'Management',
        'Admins',
        'Admin.Permissions',
        'Admin.Users',
        'Admin.Settings',
        'Admin.UserGroups',
        'Admin.Directories',
        'Admin.Directory.Management',
        'Encryption'
    ],
    models: [
        'ElementGrid',
        'GroupTree',
        'Admin.Permission',
        'Admin.GroupTree',
        'Admin.UserGrid',
        'Admin.SettingGrid',
        'Admin.GroupGrid',
        'Admin.DirectoryGrid',
        'Admin.GroupAvailableUsersGrid',
        'Admin.GroupCurrentUsersGrid',
        'TemplateSelector',
        'Encryption.Grid',
        'Admin.Directory.Preview',
        'Admin.Directory.ExternalDirectories'
    ],
    stores: [
        'ElementGrids',
        'GroupTree',
        'Admin.Permissions',
        'Admin.GroupTree',
        'Admin.UserGrid',
        'Admin.SettingGrid',
        'Admin.GroupGrid',
        'Admin.DirectoryGrid',
        'Admin.GroupAvailableUsersGrid',
        'Admin.GroupCurrentUsersGrid',
        'TemplateSelector',
        'Encryption.Grid',
        'Admin.Directory.Preview',
        'Admin.Directory.ExternalDirectories'
    ],
    autoCreateViewport: false,
    launch: function(){
        // destroy the appLoading SplashScreen

        //Ext.state.Manager.setProvider(Ext.create('Ext.state.SessionStorageProvider'));
        Ext.state.Manager.setProvider(Ext.create('Ext.state.LocalStorageProvider'));
        //Ext.state.Manager.set('state', {value: false});
        Ext.create('TeamPass.view.Viewport');
    }
});

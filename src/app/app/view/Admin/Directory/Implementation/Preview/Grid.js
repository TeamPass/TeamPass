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

Ext.define('TeamPass.view.Admin.Directory.Implementation.Preview.Grid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.admindirectoryimplementationpreviewgrid',

    store: 'Admin.Directory.Preview',
    region: 'south',
    border:true,
    columns: [{
        text: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_PREVIEW_GRID_FULLNAME_TEXT"),
        sortable: true,
        menuDisabled: true,
        dataIndex: 'fullName',
        flex:1
    }, {
        text: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_PREVIEW_GRID_USERNAME_TEXT"),
        sortable: true,
        menuDisabled: true,
        dataIndex: 'userName',
        flex:1
    }, {
        text: TeamPass.Locales.gettext("ADMIN.DIRECTORY_IMPLEMENTATION_PREVIEW_GRID_EMAIL_TEXT"),
        sortable: true,
        menuDisabled: true,
        dataIndex: 'emailAddress',
        flex:1
    }]
});

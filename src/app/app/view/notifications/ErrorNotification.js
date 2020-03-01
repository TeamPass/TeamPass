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

Ext.define('TeamPass.view.notifications.ErrorNotification', {
    extend: 'Ext.ux.window.Notification',
    alias:'widget.ErrorNotification',

    title:  TeamPass.Locales.gettext("NOTIFICATIONS.ERROR_NOTIFICATION_TITLE"),
    position: 'tr',
    minWidth:250,
    manager: 'instructions',
    cls: 'ux-notification-light',
    iconCls: 'x-fa fa-exclamation-triangle ux-notification-red',
    html: 'action failed',
    autoCloseDelay: 4000,
    slideBackDuration: 500,
    slideInAnimation: 'bounceOut',
    slideBackAnimation: 'easeIn'
});

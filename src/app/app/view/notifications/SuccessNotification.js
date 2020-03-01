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

Ext.define('TeamPass.view.notifications.SuccessNotification', {
    extend: 'Ext.ux.window.Notification',
    alias:'widget.SuccessNotification',

    title:  TeamPass.Locales.gettext("NOTIFICATIONS.SUCCESS_NOTIFICATION_TITLE"),
    position: 'tr',
    minWidth:150,
    manager: 'instructions',
    cls: 'ux-notification-light',
    iconCls: 'x-fa fa-check ux-notification-green',
    html: 'action successful',
    autoCloseDelay: 4000,
    slideBackDuration: 500,
    slideInAnimation: 'bounceOut',
    slideBackAnimation: 'easeIn'
});

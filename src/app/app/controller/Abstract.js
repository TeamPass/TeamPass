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

Ext.define('TeamPass.controller.Abstract', {
    extend: 'Ext.app.Controller',

    config: {

        /**
         * the session name
         */
        sessionId: 'Neos_Flow_Session',

        /**
         * the default rsa key size. value have to changed on server side as well
         */
        defaultKeySize: 2048
    },

    mixins: {
        aesDecrypt: 'TeamPass.mixin.Encryption',
        aesEncrypt: 'TeamPass.mixin.Encryption',
        rsaDecrypt: 'TeamPass.mixin.Encryption',
        getPrivateKey: 'TeamPass.mixin.Encryption',
        getAesPrivateKey: 'TeamPass.mixin.Encryption',
        getSessionAesKey: 'TeamPass.mixin.Encryption',
        setCSRFToken: 'TeamPass.mixin.CSRFGuard',
        getCSRFTokenHeaderObject: 'TeamPass.mixin.CSRFGuard',
        destroySessionData: 'TeamPass.mixin.Session',
        getSessionDataValue: 'TeamPass.mixin.Session',
        persistSessionData: 'TeamPass.mixin.Session',
        setSessionDataValue: 'TeamPass.mixin.Session',
        setLocalStorageValue: 'TeamPass.mixin.LocalStorage',
        getLocalStorageValue:'TeamPass.mixin.LocalStorage',
        destroyCookies: 'TeamPass.mixin.Session',
        checkIdentifier: 'TeamPass.mixin.Session'
    }
});

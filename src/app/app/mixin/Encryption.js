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

Ext.define('TeamPass.mixin.Encryption', {
    /**
     * aes decrypts given encrypted value
     *
     * @param {string} value the encrypted value
     *
     * @returns {string} decrypted result
     */
    aesDecrypt: function (value) {
        var sessionAesKey = this.getSessionAesKey();
        return GibberishAES.dec(value, sessionAesKey);
    },

    /**
     * transforms and aes encrypts given value
     *
     * @param content {*} content the content to get encrypted
     * @param key {string} the aes key to encrypt with
     *
     * @returns {string} encrypted result
     */
    aesEncrypt: function (content, key) {
        jsonResult = JSON.stringify(content);
        if (key) {
            aesKey = key;
        } else {
            aesKey = this.getSessionAesKey();
        }
        return GibberishAES.enc(jsonResult, aesKey);
    },

    /**
     * decrypts given value with the rsa private-key
     *
     * @param {*} value the content to get decrypted
     *
     * @returns {string} decrypted result
     */
    rsaDecrypt: function (value) {

        var privateKey = this.getPrivateKey();
        var crypt = new JSEncrypt({default_key_size: this.defaultKeySize});
        crypt.setPrivateKey(privateKey);

        return crypt.decrypt(value);
    },

    /**
     * Returns the rsa private key
     *
     * @returns {*}
     */
    getPrivateKey: function() {
        return this.getSessionDataValue('privateKey');
    },

    /**
     * Returns the aes encrypted rsa private key
     *
     * @returns {*}
     */
    getAesPrivateKey: function() {
        return this.getSessionDataValue('aesPrivateKey');
    },

    /**
     * Returns the aes session key
     *
     * @returns {*}
     */
    getSessionAesKey: function() {
        return this.getSessionDataValue('sessionAesKey');
    }
});

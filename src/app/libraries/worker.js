if( 'function' === typeof importScripts) {
    var window = self;
    importScripts('jsencrypt.js');

    onmessage = function (message) {
        var keyGenerator = new JSEncrypt({default_key_size: 2048});
        var publicKey = keyGenerator.getPublicKeyB64();
        var privateKey = keyGenerator.getPrivateKeyB64();
        var result = new Array();
        result['privateKey'] = privateKey;
        result['publicKey'] = publicKey;
        postMessage(result);
    }
}
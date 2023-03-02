var config = {
    map: {
        '*': {
            prCookieRestriction: 'Plumrocket_CookieConsent/js/model/restriction',
            prCookieBodyScripts: 'Plumrocket_CookieConsent/js/model/body-scripts',
            prJsCookie: 'Plumrocket_CookieConsent/js/lib/js.cookie',
        }
    },
    config: {
        mixins: {
            'jquery/jquery.cookie': {
                "Plumrocket_CookieConsent/js/jquery-cookies-mixin": true
            },
            'mage/cookies': {
                "Plumrocket_CookieConsent/js/mage-cookies-mixin": true
            },
        }
    }
};

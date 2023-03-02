/**
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

/**
 * Finds and executes scripts in a newly added element's body.
 * Needed since innerHTML does not run scripts.
 */
define(['prCookieRestriction', 'domReady!'], function (prCookieRestriction) {
    'use strict';

    function BodyScripts()
    {
        this.execute = function (element, idPrefix) {
            var scripts = this.collectScriptsFromChildren(element);
            this.executeScripts(scripts, 0, idPrefix);
        };

        this.executeScripts = function (scripts, i, idPrefix) {
            var self = this;
            var script = scripts[i];
            if (script.parentNode) {
                script.parentNode.removeChild(script);
            }
            this.evalScript(scripts[i], idPrefix + '_' + i, function () {
                if (i < scripts.length-1) {
                    self.executeScripts(scripts, ++i, idPrefix);
                }
            });
        }

        this.nodeName = function (elem, name) {
            return elem.nodeName && elem.nodeName.toUpperCase() === name.toUpperCase();
        };

        this.evalScript = function (elem, id, callback) {
            var data = (elem.text || elem.textContent || elem.innerHTML || ''),
                head = document.getElementsByTagName('head')[0] ||
                    document.documentElement;

            var script = document.createElement('script');
            script.type = "text/javascript";
            if (id !== '') {
                script.setAttribute('id', id);
            }

            if (elem.src !== '') {
                script.src = elem.src;
                head.appendChild(script);
                // Then bind the event to the callback function.
                // There are several events for cross browser compatibility.
                script.onreadystatechange = callback;
                script.onload = callback;
            } else {
                try {
                    // doesn't work on ie...
                    script.appendChild(document.createTextNode(data));
                } catch (e) {
                    // IE has funky script nodes
                    script.text = data;
                }
                head.appendChild(script);
                callback();
            }
        };

        this.collectScriptsFromChildren = function (node) {
            var scripts = [],
                children_nodes = node.childNodes,
                child,
                i;

            if (children_nodes === undefined) {
                return;
            }

            for (i = 0; i<children_nodes.length; i++) {
                child = children_nodes[i];
                if (this.nodeName(child, 'script') &&
                    (!child.type || child.type.toLowerCase() === 'text/javascript')
                ) {
                    scripts.push(child);
                } else {
                    var newScripts = this.collectScriptsFromChildren(child);
                    for (var j = 0; j < newScripts.length; j++) {
                        scripts.push(newScripts[j]);
                    }
                }
            }

            return scripts;
        };

        /**
         * Active <script type="pr_cookie_category/..."> scripts.
         *
         * Allowed types are:
         *  - pr_cookie_category/all
         *  - pr_cookie_category/{{category_name}}
         *
         * @param {HTMLScriptElement} notActiveScript
         */
        this.activatePrCookieCategoryScript = function (notActiveScript) {
            var cookieCategory = notActiveScript.type.replace('pr_cookie_category/', '');
            var isScriptAllowed = ('all' === cookieCategory)
                ? prCookieRestriction.isAllCategoriesAllowed()
                : prCookieRestriction.isAllowedCategory(cookieCategory);
            if (isScriptAllowed) {
                var script = document.createElement('script');
                script.type = 'text/javascript';
                if (notActiveScript.getAttribute('id')) {
                    script.setAttribute('id', notActiveScript.getAttribute('id'));
                }
                if (notActiveScript.src) {
                    script.src = notActiveScript.src;
                } else {
                    script.text = notActiveScript.text;
                }
                notActiveScript.replaceWith(script);
            }
        };
    }

    return new BodyScripts();
});

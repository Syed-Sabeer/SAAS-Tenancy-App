export function matchPath(pattern, pathname) {
    var patternParts = pattern.replace(/^\/+|\/+$/g, '').split('/');
    var pathParts = pathname.replace(/^\/+|\/+$/g, '').split('/');

    if (patternParts.length !== pathParts.length) {
        return null;
    }

    var params = {};

    for (var i = 0; i < patternParts.length; i++) {
        if (patternParts[i].charAt(0) === ':') {
            params[patternParts[i].slice(1)] = pathParts[i];
            continue;
        }

        if (patternParts[i] !== pathParts[i]) {
            return null;
        }
    }

    return params;
}

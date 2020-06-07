import { COOKIE_CONFIGURATION_UPDATE } from 'src/plugin/cookie/cookie-configuration.plugin';
import CookieStorageHelper from 'src/helper/storage/cookie-storage.helper';

const PREVIEW_THEME_COOKIE = 'preview-theme-id';

export default class PreviewThemeUtil{
    constructor() {
        this._init();
    }

    _init() {
        this._includePreviewTheme();
        this._registerEvents();
    }

    _registerEvents () {
        document.$emitter.subscribe(COOKIE_CONFIGURATION_UPDATE, this._includePreviewTheme);
    }

    _includePreviewTheme() {
        if (!CookieStorageHelper.isSupported()) {
            return;
        }

        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const previewThemeId = urlParams.get(PREVIEW_THEME_COOKIE);
        if (!previewThemeId) {
            return;
        }

        if (previewThemeId == this._getCookie(PREVIEW_THEME_COOKIE)) {
            return;
        }

        CookieStorageHelper.setItem(PREVIEW_THEME_COOKIE, previewThemeId, 30);
        location.reload();
    }

    _getCookie(cname) {
        var name = cname + '=';
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return '';
    }
}

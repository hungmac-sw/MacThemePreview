import './modules/sw-theme-manager/component/sw-theme-modal'

import MacThemePreviewService from "./service/mac-theme-preview.api.service";

import './modules/sw-theme-manager/snippet/de-DE'
import './modules/sw-theme-manager/snippet/en-GB'

Shopware.Application.addServiceProvider('macThemePreviewService', () => {
    const initContainer = Shopware.Application.getContainer('init');
    return new MacThemePreviewService(initContainer.httpClient, Shopware.Service('loginService'));
});

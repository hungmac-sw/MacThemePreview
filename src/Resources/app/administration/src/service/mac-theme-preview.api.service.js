import ApiService
    from 'src/core/service/api.service';

class MacThemePreviewService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'mac-theme-preview') {
        super(httpClient, loginService, apiEndpoint);
        this.name = 'MacThemePreviewService';
    }

    themeCompile(salesChannelId, themeId) {
        const apiRoute = `/_action/${this.getApiBasePath()}/compile`;

        return this.httpClient.post(
            apiRoute, {
                sales_channel_id: salesChannelId,
                theme_id: themeId,
            },
            { headers: this.getBasicHeaders() }
        ).then((response) => {
            return ApiService.handleResponse(response);
        });
    }

}

export default MacThemePreviewService;

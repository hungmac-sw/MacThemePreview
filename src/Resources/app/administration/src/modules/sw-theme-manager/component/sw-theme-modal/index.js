import template from './sw-theme-modal.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.override('sw-theme-modal', {
    template,

    inject: [
        'macThemePreviewService'
    ],

    data() {
        return {
            selected: null,
            themes: [],
            isLoadingPreview: false
        };
    },

    computed: {
        salesChannelRepository() {
            return this.repositoryFactory.create('sales_channel');
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.loadEntityData();
        },

        loadEntityData() {
            if (!this.$route.params.id) {
                return;
            }

            this.loadSalesChannel();
        },

        loadSalesChannel() {
            this.isLoadingPreview = true;
            const criteria = new Criteria();
            criteria.addAssociation('domains');

            this.salesChannelRepository
                .get(this.$route.params.id, Shopware.Context.api, criteria)
                .then((entity) => {
                    this.salesChannel = entity;
                    this.isLoadingPreview = false;
                });
        },

        selectPreview() {
            const theme = this.themes.find((theme) => theme.id === this.selected);
            if (!!theme && !!this.salesChannel.domains) {
                this.isLoadingPreview = true;

                this.macThemePreviewService.themeCompile(this.salesChannel.id, theme.id).then(() => {
                    this.isLoadingPreview = false;
                    const previewUrl = this.salesChannel.domains.first().url + '/?preview-theme-id=' + this.selected;
                    window.open(previewUrl);
                });
            }
        }
    }
});

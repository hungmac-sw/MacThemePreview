import template from './sw-theme-modal.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const domUtils = Shopware.Utils.dom;

Component.override('sw-theme-modal', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    inject: [
        'macThemePreviewService'
    ],

    data() {
        return {
            selected: null,
            themes: [],
            isLoadingPreview: false,
            isLoadingCopy: false
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
            if (this.checkConditions()) {
                this.isLoadingPreview = true;
                this.macThemePreviewService.themeCompile(this.salesChannel.id, this.selected).then(() => {
                    this.isLoadingPreview = false;
                    window.open(this.renderPreviewUrl());
                });
            }
        },

        copyToClipboard() {
            if (this.checkConditions()) {
                this.isLoadingCopy = true;
                this.macThemePreviewService.themeCompile(this.salesChannel.id, this.selected).then(() => {
                    this.isLoadingCopy = false;
                    domUtils.copyToClipboard(this.renderPreviewUrl());
                    this.createNotificationInfo({
                        title: this.$tc('global.default.info'),
                        message: this.$tc('mac-theme-preview.notification.notificationCopyLinkSuccess')
                    });
                });
            }
        },

        renderPreviewUrl() {
            return this.salesChannel.domains.first().url + '/?preview-theme-id=' + this.selected;
        },

        checkConditions() {
            const theme = this.themes.find((theme) => theme.id === this.selected);
            if (!theme) {
                this.createNotificationError({
                    title: this.$tc('global.default.error'),
                    message: this.$tc('global.notification.unspecifiedSaveErrorMessage')
                });
                return false;
            }

            if (!this.salesChannel.domains.first()) {
                this.createNotificationError({
                    title: this.$tc('global.default.error'),
                    message: this.$tc('mac-theme-preview.notification.notificationEmptyDomain')
                });
                return false;
            }

            return true;
        }
    }
});

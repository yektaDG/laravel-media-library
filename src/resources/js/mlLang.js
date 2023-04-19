class Multilingual {
    constructor(locale, langPath) {
        this.lang = locale || navigator.language || 'en';
        this.translations = {};
        this.loadTranslations(locale, langPath).then(() => this.createGetters());
    }

    async loadTranslations(locale, langPath) {
        try {
            const file = `${langPath}/${locale}.json`;
            const response = await fetch(file);
            this.translations = await response.json();
        } catch (error) {
            console.error('Failed to load translations', error);
        }
    }

    setLocale(locale) {
        this.lang = locale;
    }

    /**
     * creates getter for each key of json file
     */
    createGetters() {
        const keys = Object.keys(this.translations);
        for (const key of keys) {
            Object.defineProperty(this, key, {
                get: function () {
                    return this.translations[key];
                }
            });
        }
    }
}

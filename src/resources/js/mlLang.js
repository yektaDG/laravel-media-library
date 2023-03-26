class Multilingual {
    constructor(locale, langPath) {
        this.lang = locale || navigator.language || 'en';
        this.translations = {};
        this.loadTranslations(locale, langPath);
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

    get notifyDelete() {
        return this.translations.notifyDelete;
    }

    get notifyAdd() {
        return this.translations.notifyAdd;
    }

    get notifyAddToTinymce() {
        return this.translations.notifyAddToTinymce;
    }

    get notifyAddToFolder() {
        return this.translations.notifyAddToFolder;
    }

    get notifyChangeAlt() {
        return this.translations.notifyChangeAlt;
    }

    get notifyRemovedFolder() {
        return this.translations.notifyRemovedFolder;
    }
}

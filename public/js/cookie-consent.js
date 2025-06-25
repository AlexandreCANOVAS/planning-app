import { run } from 'vanilla-cookieconsent';
import 'vanilla-cookieconsent/dist/cookieconsent.css';

console.log('DEBUG: cookie-consent.js SCRIPT CHARGÉ');

// Attendre que le document soit complètement chargé et que la fenêtre soit disponible
window.addEventListener('load', function() {
    try {
        console.log('DEBUG: Window loaded, initializing cookie consent');
        initCookieConsent();
    } catch (error) {
        console.error('Error initializing cookie consent:', error);
    }
});

function initCookieConsent() {
    try {
    console.log('DEBUG: DOMContentLoaded, LANCEMENT DU BANDEAU');
    run({
    gui_options: {
        consent_modal: {
            layout: "cloud",
            position: "bottom right",
            transition: "slide",
        },
        settings_modal: {
            layout: "box",
            position: "left",
            transition: "slide"
        }
    },

    categories: {
        necessary: {
            readOnly: true
        },
        analytics: {}
    },

    language: {
        default: "fr",
        translations: {
            fr: {
                consent_modal: {
                    title: "Nous utilisons des cookies",
                    description: "Ce site utilise des cookies pour améliorer votre expérience. En cliquant sur ‘Accepter’, vous consentez à l'utilisation de tous les cookies.",
                    primary_btn: {
                        text: "Accepter",
                        role: "accept_all"
                    },
                    secondary_btn: {
                        text: "Personnaliser",
                        role: "settings"
                    }
                },
                settings_modal: {
                    title: "Préférences des cookies",
                    save_settings_btn: "Enregistrer mes préférences",
                    accept_all_btn: "Accepter tout",
                    reject_all_btn: "Refuser tout",
                    close_btn_label: "Fermer",
                    blocks: [
                        {
                            title: "Utilisation des cookies",
                            description: "Nous utilisons des cookies pour assurer les fonctionnalités de base du site et pour améliorer votre expérience en ligne. Vous pouvez choisir pour chaque catégorie de refuser les cookies quand vous le souhaitez. Pour plus de détails, veuillez lire notre <a href=\"/politique-de-confidentialite\">politique de confidentialité</a>."
                        },
                        {
                            title: "Cookies strictement nécessaires",
                            description: "Ces cookies sont essentiels au bon fonctionnement du site. Sans ces cookies, le site ne fonctionnerait pas correctement.",
                            toggle: {
                                value: "necessary",
                                enabled: true,
                                readOnly: true
                            }
                        },
                        {
                            title: "Cookies de performance et d'analyse",
                            description: "Ces cookies nous permettent de recueillir des informations sur la façon dont vous utilisez le site, afin que nous puissions mesurer et améliorer les performances de notre site.",
                            toggle: {
                                value: "analytics",
                                enabled: false,
                                readOnly: false
                            }
                        },
                        {
                            title: "Plus d'informations",
                            description: "Pour toute question relative à notre politique en matière de cookies et à vos choix, veuillez <a href=\"/contact\">nous contacter</a>."
                        }
                    ]
                }
            }
        }
    }
    });
    } catch (error) {
        console.error('Error in cookie consent configuration:', error);
    }
}

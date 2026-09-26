# VivaHost — WordPress Child Theme (Standalone Canvas)

Tema WordPress professionale sviluppato su misura per **VivaHost**, partner di gestione affitti brevi e Superhost Airbnb a Salvador, Bahia.

## Caratteristiche

- **Architettura Canvas Standalone:** Landing page ottimizzata ad altissima conversione, zero dipendenze jQuery, Vanilla JS e CSS Tokens moderni.
- **Integrazione Form & WhatsApp:** Generazione automatica del link WhatsApp precompilato con i dati del lead (`admin-ajax.php`), invio email SMTP con sanitizzazione e dual honeypot antispam.
- **Crittografia Credenziali:** Cifratura simmetrica AES-256-CBC con chiave e IV univoci derivati dal salt di WordPress per le credenziali SMTP.
- **Customizer Live Preview:** Oltre 15 sezioni personalizzabili in tempo reale dall'amministrazione WordPress (*Aspetto → Personalizza*).
- **SEO & Core Web Vitals:** Schema.org JSON-LD (`WebSite`, `RealEstateAgent`, `FAQPage`, `BlogPosting`), Open Graph completo, Twitter Cards e meta tag geografici per Salvador (BA).
- **Aggiornamenti Automatici da GitHub:** Supporto nativo per aggiornamenti 1-click direttamente dalla bacheca di WordPress (*Aparência → Temas*).

## Installazione

1. Scarica l'ultima release `.zip` da [Releases](https://github.com/spano/vivahost-tema/releases).
2. Nel pannello di amministrazione WordPress, vai su **Aparência → Temas → Adicionar novo → Carregar tema**.
3. Seleziona il file `vivahost-tema.zip` e clicca su **Instalar agora**.
4. Attiva il tema.

## Aggiornamenti Automatici

Nel pannello **Configurações → VivaHost Email**, inserisci il percorso del tuo repository GitHub nel campo:
`Repositório GitHub (usuário/repositório)` (es. `tuo-utente/vivahost-tema`).

Ogni volta che pubblichi una nuova release o tag su GitHub (es. `v3.8.4`), WordPress mostrerà automaticamente la notifica di aggiornamento nella bacheca.

## Licenza

GPL v3 or later.

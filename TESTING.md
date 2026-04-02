# Piano di Testing Manuale — Colors S.r.l.

> Eseguire in ordine. Spuntare ogni casella man mano. Usare dati reali (prodotti, ordini, utenti).
>
> **Setup consigliato prima di iniziare:**
> - Crea 2 account utente: uno normale, uno admin
> - Inserisci almeno 3 prodotti (uno esaurito, uno con varianti, uno senza)
> - Inserisci almeno 1 servizio
> - Crea almeno 2 categorie (una con sottocategorie)
> - Assicurati che Stripe/PayPal siano in modalità test

---

## 1. NAVIGAZIONE PUBBLICA

### 1.1 Home page (`/`)
- [ ] La pagina carica senza errori
- [ ] Hero: immagine di sfondo visibile, testo e CTA leggibili
- [ ] Sezione categorie: le immagini delle categorie si caricano, l'hover funziona
- [ ] Click su una categoria → porta a `/prodotti?categoria=...`
- [ ] Carosello "Prodotti in evidenza": scorre con frecce prev/next e torna in loop
- [ ] Sezione servizi: icone e testi visibili
- [ ] Click sul servizio → porta a `/servizi`
- [ ] Sezione FAQ: le domande si espandono/chiudono
- [ ] CTA "Scopri tutti i prodotti" → porta a `/prodotti`
- [ ] La pagina è responsive: controlla su mobile (Chrome DevTools, 375px)

### 1.2 Navbar
- [ ] Logo cliccabile → porta a `/`
- [ ] Dropdown "Prodotti" al hover: mostra le categorie radice
- [ ] Click su una categoria nel dropdown → filtra i prodotti
- [ ] Link "Prodotti" → porta a `/prodotti`
- [ ] Link "Servizi" → porta a `/servizi`
- [ ] Dropdown carrello al hover: mostra "Il carrello è vuoto" se vuoto
- [ ] Dropdown account (non loggato): mostra Login / Registrati
- [ ] Dropdown account (loggato): mostra nome utente, link area personale, logout
- [ ] Mobile: il menu hamburger apre/chiude il menu
- [ ] Mobile: tutti i link del menu mobile funzionano

### 1.3 Footer
- [ ] Tutti i link del footer funzionano (non ci sono `#` morti)
- [ ] Email di contatto cliccabile
- [ ] La pagina è completa senza overflow orizzontale

---

## 2. CATALOGO PRODOTTI

### 2.1 Pagina prodotti (`/prodotti`)
- [ ] Carica e mostra tutti i prodotti attivi con `manage_stock = true`
- [ ] Immagini prodotto visibili (o placeholder se assente)
- [ ] Badge "Esaurito" visibile sui prodotti senza stock
- [ ] Paginazione: naviga tra le pagine, i parametri filtro si mantengono
- [ ] **Filtro categoria:** clicca su una categoria root → i prodotti si filtrano
- [ ] **Subcategorie:** selezionando una categoria con figli appare la riga di pill sottocategorie
- [ ] **Filtro brand:** seleziona un brand dal select → i prodotti si filtrano
- [ ] **Ricerca:** digita nel campo ricerca → filtra per nome/SKU/descrizione
- [ ] **Combinazione filtri:** categoria + brand + ricerca insieme funzionano
- [ ] **Reset filtri:** rimuovere i filtri ripristina tutti i prodotti
- [ ] Pill categoria attiva è evidenziata diversamente dalle altre
- [ ] La pagina è responsive

### 2.2 Pagina prodotto singolo (`/prodotti/{slug}`)
- [ ] Carica il prodotto corretto dalla URL
- [ ] Prodotto inesistente → 404
- [ ] Titolo, brand, prezzo visibili
- [ ] Prezzo barrato e badge sconto visibili se `compare_at_price` è impostato
- [ ] **Galleria:** immagine principale visibile
- [ ] **Thumbnails:** click su thumbnail cambia l'immagine principale
- [ ] **Varianti:** i pulsanti variante sono visibili se il prodotto ha varianti
- [ ] Variante esaurita: pulsante disabilitato con testo "(esaurito)"
- [ ] Click su variante → si seleziona (bordo primario)
- [ ] **Selettore quantità:** `-` non scende sotto 1, `+` non supera 99
- [ ] **Prodotto esaurito:** pulsante "Esaurito" disabilitato, overlay visibile
- [ ] **Prodotto disponibile:** pulsante "Aggiungi al carrello" attivo
- [ ] Aggiunta al carrello senza selezionare variante (se varianti presenti) → errore validazione o aggiunta senza variante
- [ ] Aggiunta al carrello con variante selezionata → prodotto nel carrello
- [ ] **Wishlist:** click sull'icona cuore (non loggato) → redirect login
- [ ] **Wishlist:** click sull'icona cuore (loggato) → cuore diventa rosso/pieno
- [ ] **Wishlist:** secondo click → rimuove dalla wishlist, cuore torna grigio
- [ ] Descrizione breve visibile sotto il form
- [ ] Descrizione completa visibile nella sezione "Descrizione"
- [ ] Breadcrumb funziona correttamente
- [ ] **Sezione recensioni:** media stelle e contatore visibili (anche se 0)
- [ ] **Prodotti correlati:** carosello "Potrebbe interessarti" visibile se esistono correlati
- [ ] Carosello correlati: frecce prev/next funzionano

---

## 3. SERVIZI

### 3.1 Pagina servizi (`/servizi`)
- [ ] Carica e mostra tutti i servizi (prodotti con `is_service = true`)
- [ ] I prodotti fisici (anche quelli con varianti) **non** appaiono tra i servizi
- [ ] Layout testo + immagine alternato corretto
- [ ] Immagini dei servizi visibili
- [ ] Click su un servizio → porta alla pagina dettaglio

### 3.2 Dettaglio servizio (`/servizi/{slug}`)
- [ ] Carica correttamente il servizio
- [ ] Servizio inesistente → 404
- [ ] Contenuto e immagini visibili

---

## 4. AUTENTICAZIONE

### 4.1 Registrazione (`/register`)
- [ ] Form visibile per utenti non loggati
- [ ] Utente già loggato → redirect a home
- [ ] **Validazione:** invia form vuoto → errori su tutti i campi obbligatori
- [ ] **Validazione:** email già in uso → errore specifico
- [ ] **Validazione:** password troppo corta → errore
- [ ] **Validazione:** conferma password non coincide → errore
- [ ] **Registrazione valida:** compila tutti i campi → registrazione riuscita
- [ ] Dopo registrazione: redirect alla home (o pagina verifica email)
- [ ] Email di benvenuto ricevuta nella inbox

### 4.2 Verifica email
- [ ] Dopo registrazione appare il banner "Verifica la tua email"
- [ ] Il banner mostra un pulsante per reinviare l'email
- [ ] Click sul link nell'email → email verificata, redirect corretto
- [ ] Link scaduto (dopo 60 min) → errore appropriato
- [ ] Reinvio email verifica funziona (throttle: max 6 volte in 1 minuto)
- [ ] Utente non verificato che tenta il checkout → redirect a `/email/verifica`

### 4.3 Login (`/login`)
- [ ] Form visibile per utenti non loggati
- [ ] **Validazione:** credenziali errate → errore "Credenziali non valide"
- [ ] **Login valido:** redirect alla pagina precedente o home
- [ ] "Remember me" mantiene la sessione alla chiusura del browser

### 4.4 Logout
- [ ] Click logout → sessione terminata
- [ ] Dopo logout → redirect home
- [ ] Accesso a pagine protette dopo logout → redirect login

### 4.5 Reset password (`/password/reset`)
- [ ] Form "Hai dimenticato la password?" visibile
- [ ] **Email inesistente:** invia → stesso messaggio di successo (no user enumeration)
- [ ] **Email esistente:** invia → email di reset ricevuta
- [ ] Link nell'email di reset funziona → apre form nuova password
- [ ] **Nuova password:** validazione (min 8 caratteri, conferma)
- [ ] Password aggiornata con successo → redirect login con messaggio
- [ ] Tentativo di riutilizzare lo stesso link → errore "token scaduto/non valido"

---

## 5. CARRELLO

### 5.1 Aggiunta prodotti
- [ ] Aggiunta dalla pagina prodotto (senza variante)
- [ ] Aggiunta dalla pagina prodotto (con variante selezionata)
- [ ] Aggiunta dalla card prodotto (bottone rapido)
- [ ] Badge carrello in navbar si aggiorna dopo ogni aggiunta
- [ ] Aggiunta prodotto già presente → incrementa la quantità
- [ ] Aggiunta prodotto esaurito → non possibile (pulsante disabilitato)

### 5.2 Pagina carrello (`/carrello`)
- [ ] Carrello vuoto → messaggio "Il carrello è vuoto" con CTA
- [ ] Prodotti presenti: nome, variante, immagine, prezzo, quantità visibili
- [ ] **Modifica quantità:** cambia la quantità → totale si aggiorna
- [ ] Quantità a 0 o negativa → non permesso o rimozione
- [ ] **Rimozione prodotto:** click sul tasto elimina → prodotto rimosso
- [ ] Subtotale, spese di spedizione, totale calcolati correttamente
- [ ] CTA "Procedi al checkout" → se loggato e verificato va al checkout
- [ ] CTA "Procedi al checkout" → se non loggato va al login

### 5.3 Dropdown preview carrello (navbar)
- [ ] Hover sull'icona carrello → dropdown si apre
- [ ] Mostra lista prodotti con immagine, nome, variante, quantità × prezzo
- [ ] Mostra totale in fondo
- [ ] Carrello vuoto → "Il carrello è vuoto"
- [ ] Click su un prodotto nel dropdown → porta alla pagina prodotto
- [ ] Mouse fuori dal dropdown → si chiude (con delay)

---

## 6. CHECKOUT

### 6.1 Accesso e prerequisiti
- [ ] Accesso senza login → redirect `/login`
- [ ] Accesso senza verifica email → redirect `/email/verifica`
- [ ] Carrello vuoto → redirect al carrello con messaggio
- [ ] Accesso corretto → form checkout visibile

### 6.2 Form dati di spedizione
- [ ] **Indirizzi salvati:** se l'utente ha indirizzi salvati, appaiono per la selezione
- [ ] Click su un indirizzo salvato → pre-compila i campi
- [ ] **Validazione:** invia form vuoto → errori su tutti i campi obbligatori
- [ ] **Validazione:** CAP non numerico → errore
- [ ] **Validazione:** email formato errato → errore

### 6.3 Codice sconto
- [ ] Campo sconto visibile
- [ ] **Codice inesistente** → errore "Codice non valido"
- [ ] **Codice scaduto** → errore appropriato
- [ ] **Codice valido** → tag verde con codice, importo sconto e nuovo totale
- [ ] Rimozione sconto (×) → totale torna al valore originale
- [ ] Sconto percentuale calcolato correttamente
- [ ] Sconto fisso calcolato correttamente
- [ ] Sconto con `min_order_amount`: sotto la soglia → errore appropriato
- [ ] Codice monouso già usato dallo stesso utente → errore

### 6.4 Selezione metodo di pagamento
- [ ] Opzione Stripe visibile con badge delle carte (Visa, Mastercard, ecc.)
- [ ] Opzione PayPal visibile con logo
- [ ] Click su un'opzione → si seleziona (bordo primario evidenziato)
- [ ] Solo una opzione selezionabile alla volta

### 6.5 Pagamento Stripe
- [ ] Seleziona Stripe → invia form → redirect a Stripe Checkout
- [ ] Pagina Stripe mostra i prodotti e il totale corretto
- [ ] **Pagamento riuscito** (usa carta test `4242 4242 4242 4242`):
  - [ ] Redirect a `/checkout/stripe/successo`
  - [ ] Redirect finale a `/checkout/successo`
  - [ ] Ordine salvato in DB con `payment_status = paid`
  - [ ] Carrello svuotato
  - [ ] Email conferma ordine ricevuta
- [ ] **Pagamento annullato** (click "Torna al negozio" su Stripe):
  - [ ] Redirect al checkout
  - [ ] Ordine NON salvato (o rimane `pending`)
  - [ ] Carrello ancora presente
- [ ] **Carta rifiutata** (usa carta test `4000 0000 0000 0002`):
  - [ ] Stripe mostra errore
  - [ ] L'utente può riprovare

### 6.6 Pagamento PayPal
- [ ] Seleziona PayPal → invia form → redirect a PayPal
- [ ] **Pagamento riuscito** (usa account sandbox):
  - [ ] Redirect a `/checkout/paypal/successo`
  - [ ] Redirect finale a `/checkout/successo`
  - [ ] Ordine salvato con `payment_status = paid`
  - [ ] Carrello svuotato
  - [ ] Email conferma ordine ricevuta
- [ ] **Pagamento annullato** su PayPal:
  - [ ] Redirect al checkout
  - [ ] Carrello ancora presente

### 6.7 Pagina successo (`/checkout/successo`)
- [ ] Mostra numero ordine
- [ ] Mostra riepilogo acquisto
- [ ] CTA "Vai ai tuoi ordini" funziona
- [ ] Accesso diretto a `/checkout/successo` senza aver appena pagato → redirect home

---

## 7. AREA UTENTE

### 7.1 Dashboard (`/account`)
- [ ] Benvenuto con nome utente
- [ ] Riepilogo ordini recenti
- [ ] Link rapidi a: Ordini, Indirizzi, Wishlist, Profilo

### 7.2 Ordini (`/account/ordini`)
- [ ] Lista ordini con numero, data, totale, stato
- [ ] Badge stato colorato correttamente (verde=consegnato, blu=spedito, ecc.)
- [ ] Ordini consegnati: link "Recensisci su Google" visibile
- [ ] Click su un ordine → dettaglio ordine

### 7.3 Dettaglio ordine (`/account/ordini/{id}`)
- [ ] Mostra tutti i prodotti dell'ordine con quantità e prezzi
- [ ] Mostra indirizzo di spedizione
- [ ] Mostra stato, metodo di pagamento, totale
- [ ] Mostra sconto applicato se presente
- [ ] Accesso a ordine di un altro utente → 403 o 404
- [ ] **Link prodotto:** click sul nome di un prodotto fisico → porta alla pagina prodotto
- [ ] Prodotto eliminato dal catalogo o servizio → nome non cliccabile (testo semplice)
- [ ] **Banner recensioni (ordine consegnato):** se ci sono prodotti non ancora recensiti → banner visibile con bottoni "Recensisci X"
- [ ] Banner **non** visibile se tutti i prodotti dell'ordine sono già stati recensiti
- [ ] Banner **non** visibile su ordini non ancora consegnati
- [ ] Click su "Recensisci X" → porta alla sezione recensioni della pagina prodotto

### 7.4 Indirizzi (`/account/indirizzi`)
- [ ] Lista indirizzi salvati
- [ ] **Aggiunta indirizzo:** compila form → indirizzo appare nella lista
- [ ] **Validazione:** form vuoto → errori
- [ ] **Eliminazione:** click elimina → conferma → indirizzo rimosso
- [ ] L'indirizzo salvato appare nella selezione al checkout

### 7.5 Wishlist (`/account/wishlist`)
- [ ] Lista prodotti in wishlist
- [ ] Wishlist vuota → messaggio appropriato
- [ ] Click su un prodotto → porta alla pagina prodotto
- [ ] Rimozione dalla wishlist funziona

### 7.6 Profilo (`/account/profilo`)
- [ ] Mostra dati attuali (nome, cognome, email, telefono)
- [ ] **Modifica dati:** cambia nome → dati aggiornati
- [ ] **Cambio password:** vecchia password errata → errore
- [ ] **Cambio password:** nuova password valida → aggiornata
- [ ] **Eliminazione account:**
  - [ ] Click "Elimina account" → form di conferma appare
  - [ ] Conferma → account eliminato, sessione terminata
  - [ ] Email con link di riattivazione ricevuta
  - [ ] Click link riattivazione (entro 7 giorni) → account riattivato

---

## 8. RECENSIONI

### 8.1 Visibilità del form
- [ ] Utente **non loggato**: nessun form visibile nella pagina prodotto
- [ ] Utente loggato **senza ordine consegnato** per quel prodotto: nessun form
- [ ] Utente loggato **con ordine consegnato** per quel prodotto: form visibile
- [ ] **Banner sconto:** "Lascia una recensione e ricevi il 15% di sconto" visibile se l'utente non ha mai ricevuto il reward
- [ ] **Banner neutro:** "La tua opinione è importante per noi" visibile se l'utente ha già ricevuto lo sconto in precedenza (nessuna menzione al 15%)
- [ ] Utente che ha **già recensito** quel prodotto: messaggio "Hai già lasciato una recensione"

### 8.2 Invio recensione
- [ ] **Invio senza stelle** → errore validazione
- [ ] **Invio con sole stelle** (titolo e commento vuoti) → recensione inviata
- [ ] **Invio completo** (stelle + titolo + commento) → recensione inviata
- [ ] Dopo invio: messaggio di conferma "Grazie! La tua recensione è in attesa di approvazione"
- [ ] Form scompare dopo l'invio (già recensito = true)
- [ ] Tentativo secondo invio per stesso prodotto → bloccato
- [ ] **Badge "Acquisto verificato"** appare nella recensione se l'utente ha un ordine consegnato con quel prodotto

### 8.3 Approvazione e incentivo
- [ ] Admin approva recensione dal pannello Filament
- [ ] Recensione appare nella pagina prodotto
- [ ] **Prima approvazione:** email sconto 15% ricevuta con codice `REC-XXXXXXXX`
- [ ] **Seconda recensione approvata** (stesso utente, prodotto diverso): nessuna email sconto
- [ ] Codice sconto ricevuto funziona al checkout (15% di sconto)
- [ ] Codice sconto non riutilizzabile (secondo tentativo → errore)

### 8.4 Visualizzazione recensioni
- [ ] Solo recensioni approvate visibili pubblicamente
- [ ] Stelle riflettono il rating corretto
- [ ] Media stelle e contatore corretti
- [ ] Nome utente (solo `first_name`) visibile
- [ ] Data visibile

---

## 9. EMAIL

Per ogni email verifica: layout corretto, logo in header, colori, testo italiano, link funzionanti, no HTML rotto.

- [ ] **Email verifica account** — arriva dopo registrazione, link funziona
- [ ] **Email benvenuto** — arriva dopo registrazione
- [ ] **Email reset password** — arriva dal form dimenticato password, link funziona, link scade
- [ ] **Email conferma ordine** — arriva dopo pagamento, mostra prodotti e totale
- [ ] **Email spedizione** — arriva quando admin cambia status a "spedito"
  - [ ] Se tracking number impostato: è visibile nell'email
  - [ ] CTA "Lascia una recensione su Google" visibile in fondo
- [ ] **Email sconto recensione** — arriva dopo prima approvazione, codice visibile e corretto
- [ ] **Email eliminazione account** — arriva dopo cancellazione, link riattivazione funziona

---

## 10. PANNELLO ADMIN (Filament)

Accedi con account admin su `/admin`.

### 10.1 Accesso
- [ ] Login admin funziona
- [ ] Utente normale → accesso negato
- [ ] Dashboard mostra statistiche (ordini, prodotti, ecc.)

### 10.2 Prodotti
- [ ] Lista prodotti con ricerca e filtri
- [ ] **Creazione prodotto:** compila tutti i campi → prodotto salvato
- [ ] **Immagini:** upload immagine principale e galleria
- [ ] **Varianti:** aggiunta varianti con stock
- [ ] **Modifica prodotto:** cambia prezzo/stock → aggiornato sul frontend
- [ ] **Disattivazione:** prodotto disattivato non appare in lista pubblica
- [ ] **Stock = 0:** prodotto appare come esaurito

### 10.3 Categorie e Brand
- [ ] Creazione categoria (root e con padre)
- [ ] Creazione brand
- [ ] Categoria con slug duplicato → errore

### 10.4 Ordini
- [ ] Lista ordini con filtri per stato
- [ ] **Cambio stato → "shipped":** email spedizione inviata automaticamente
- [ ] **Cambio stato → "delivered":** ordine marcato come consegnato
- [ ] **Cambio stato → "cancelled":** stock ripristinato automaticamente
- [ ] Visualizzazione dettaglio ordine con prodotti

### 10.5 Recensioni
- [ ] Lista recensioni in attesa di approvazione
- [ ] **Approva recensione** → appare sul frontend, email sconto inviata
- [ ] **Rifiuta/elimina recensione** → non appare sul frontend

### 10.6 Sconti
- [ ] Creazione codice sconto (percentuale e fisso)
- [ ] Impostazione data scadenza, limite utilizzi
- [ ] Visualizzazione utilizzi attuali

---

## 11. SEO E PERFORMANCE

- [ ] Ogni pagina ha `<title>` e `<meta description>` corretti
- [ ] Immagini: verificare che non ci siano immagini rotte (aprire DevTools > Console)
- [ ] Nessun errore JavaScript in console su tutte le pagine principali
- [ ] Nessun errore 500 o 404 inatteso in console di rete

---

## 12. RESPONSIVE / MOBILE

Testa su almeno due viewport: **375px (iPhone SE)** e **768px (tablet)**.

- [ ] Home: testo leggibile, immagini scalate, carosello funziona
- [ ] Lista prodotti: griglia si adatta (2 colonne su mobile)
- [ ] Pagina prodotto: galleria, form aggiunta carrello, info prodotto
- [ ] Navbar: hamburger menu funziona
- [ ] Carrello: lista prodotti leggibile, quantità modificabile
- [ ] Checkout: form compilabile, selezione metodo pagamento
- [ ] Account: sezioni navigabili
- [ ] Footer: testo non troncato

---

## 13. EDGE CASE E CASI LIMITE

- [ ] URL manuale `/prodotti/slug-inesistente` → 404
- [ ] URL manuale `/account/ordini/99999` → 404 o 403
- [ ] Accesso `/checkout` con carrello vuoto → redirect
- [ ] Accesso `/admin` da utente normale → 403
- [ ] Prodotto rimosso dal catalogo mentre è nel carrello → gestione graceful
- [ ] Doppio click sul pulsante "Invia recensione" → non crea due recensioni
- [ ] Codice sconto con `usage_limit = 1` usato due volte → secondo utilizzo bloccato
- [ ] Reset password con token già usato → errore "token non valido"
- [ ] Verifica email con hash alterato → errore 403

---

## CHECKLIST FINALE PRE-LANCIO

- [ ] Chiavi Stripe/PayPal **live** (non test) inserite nel `.env` di produzione
- [ ] `APP_ENV=production` e `APP_DEBUG=false` in produzione
- [ ] Email SMTP reale configurata e testata
- [ ] `GOOGLE_PLACE_ID` impostato correttamente
- [ ] Certificato SSL attivo
- [ ] Backup database configurato
- [ ] Tutti i test sopra superati con ✓

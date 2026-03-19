# Colors S.r.l. — Pannello Admin E-commerce

Pannello di amministrazione per la gestione dell'e-commerce **Colors S.r.l.**, azienda specializzata in prodotti per la verniciatura e la pittura.

---

## Stack Tecnologico

| Componente | Versione |
|---|---|
| PHP | 8.4 |
| Laravel | 12 |
| Filament | 5 |
| MySQL | 8.0 |
| Node.js | 20 |
| Nginx | 1.25 |

---

## Requisiti

- Docker & Docker Compose
- Git

---

## Installazione e Avvio

```bash
# 1. Clona il repository
git clone <repo-url> progetto_colors
cd progetto_colors

# 2. Copia il file di configurazione
cp .env.example .env

# 3. Avvia i container Docker
docker compose up -d

# 4. Installa le dipendenze PHP
docker compose exec app composer install

# 5. Genera la chiave applicazione
docker compose exec app php artisan key:generate

# 6. Esegui le migration e i seeder
docker compose exec app php artisan migrate --seed

# 7. Installa le dipendenze frontend e compila
docker compose exec vite npm install
docker compose exec vite npm run build
```

L'applicazione sarà disponibile su **http://localhost:8000/admin**

### Credenziali admin di default (dal seeder)

| Campo | Valore |
|---|---|
| Email | admin@colorsrl.it |
| Password | password |

---

## Architettura Docker

```
nginx   → localhost:8000  (web server)
app     → php-fpm (applicazione Laravel)
mysql   → localhost:3307  (database)
queue   → php artisan queue:work (job asincroni)
vite    → localhost:5173  (asset frontend in sviluppo)
```

---

## Struttura del Pannello Admin

### Dashboard
La homepage del pannello mostra:
- **Statistiche** — Fatturato totale, fatturato mensile con confronto mese precedente, prodotti in esaurimento, prodotti esauriti, valutazione media clienti, ordini da gestire
- **Grafico Andamento Fatturato** — Visualizzazione per settimana / mese / trimestre / anno degli ordini pagati
- **Ultimi Ordini** — Tabella con gli ultimi 10 ordini ricevuti con link diretto alla gestione
- **Prodotti Più Venduti** — Top 10 prodotti per volume di vendite
- **Alert Stock Basso** — Prodotti che hanno superato la soglia di scorte minime
- **Ultime Recensioni** — Recensioni recenti con possibilità di approvarle direttamente dalla dashboard

---

## Guida alla Gestione Admin

### Catalogo

#### Prodotti
- **Lista prodotti** — filtri per categoria, brand, stato attivo, gestione stock; tab rapidi "Scorte Basse" e "Esauriti"
- **Crea/Modifica prodotto** — scheda completa con: dati base, prezzi (costo, prezzo, IVA), gestione stock (quantità, soglia minima), varianti, immagini, categorie, brand, SEO (slug auto-generato)
- **Varianti** — ogni variante ha stock proprio, SKU e prezzo differenziato
- **Clic su riga** → modale con tutte le informazioni del prodotto

#### Categorie & Brand
- Creabili sia dalla sezione dedicata sia direttamente durante la creazione/modifica di un prodotto (tramite modal inline)
- Lo slug è generato automaticamente dal nome

---

### Ordini

#### Gestione Ordini
- **Lista ordini** — tab per stato (Tutti, In Attesa, In Elaborazione, Spediti, Consegnati, Annullati); filtri per stato pagamento, data, fonte
- **Clic su riga** → modale di visualizzazione riepilogativa
- **Pulsante "Gestisci"** → pagina completa di gestione con:
  - Dettaglio cliente e indirizzi di spedizione/fatturazione
  - Lista articoli ordinati con prezzi unitari e totali
  - Riepilogo importi (subtotale, sconto, spedizione, IVA, totale)
  - Azioni: **Segna come Spedito**, **Segna come Consegnato**, **Rifiuta Ordine**, **Scarica Fattura PDF**

> **Nota:** Non è possibile segnare un ordine come spedito se il pagamento non è ancora stato ricevuto. Il sistema blocca l'azione con una notifica di errore.

#### Flusso di lavoro ordine
```
pending → processing → shipped → delivered
                    ↘ cancelled  (solo da pending o processing)
```

---

### Clienti

- **Lista clienti** — ricerca per nome/email, filtri per stato account e iscrizione newsletter, badge con numero ordini
- **Clic su riga** → modale con dati personali, impostazioni account, statistiche (ordini totali, data registrazione, stato verifica email)
- **Azioni per riga** — Modifica, Attiva/Disattiva account (con modale di conferma), Elimina
- **Selezione multipla** — Attiva/Disattiva/Elimina in blocco

> **Nota importante:** Un cliente che ha effettuato ordini **non può essere eliminato**. Usa "Disattiva" per bloccare l'accesso al suo account mantenendo intatto lo storico ordini. Un account disattivato può essere riattivato in qualsiasi momento.

---

### Fornitori & Ordini Fornitori

#### Fornitori
- CRUD completo con dati aziendali (ragione sociale, P.IVA, contatti, indirizzo, referente)
- **Attiva/Disattiva** fornitore senza perdere lo storico degli ordini
- **Eliminazione bloccata** se il fornitore ha ordini ancora attivi (in stato bozza, inviato o confermato)

#### Ordini Fornitori
- Creazione ordine con selezione fornitore, righe articolo (prodotto o variante, quantità ordinata/ricevuta, prezzo unitario), spese di spedizione e **aliquota IVA configurabile per ordine** (default 22%)
- **Cambio stato manuale** direttamente dall'ActionGroup nella lista:

```
bozza → inviato → confermato → ricevuto_parzialmente → ricevuto
                                                     ↘ annullato
```

- **"Segna come Ricevuto"** aggiorna automaticamente lo stock. Se la quantità ricevuta (`quantity_received`) è inferiore a quella ordinata, viene usata la quantità effettivamente ricevuta per gestire le **ricezioni parziali**
- **Eliminazione** permessa solo su ordini in stato Bozza

---

### Magazzino

#### Inventario
- Panoramica completa dello stock di tutti i prodotti (con e senza varianti)
- Filtri rapidi per stock basso, esauriti, solo prodotti con gestione stock abilitata
- Export Excel dell'inventario aggiornato
- **Clic su riga** → modale con il dettaglio completo del prodotto

#### Movimenti Stock
- **Lista movimenti** — storico completo di tutti i carichi/scarichi con filtri per tipo di movimento, prodotto e intervallo di date
- **Crea movimento manuale** — carico (+) o scarico (-) manuale con motivo
- **Annulla movimento** — lo stock viene ripristinato automaticamente; il movimento originale rimane visibile come **"Annullato"** per garantire la tracciabilità, e viene creato un movimento inverso di tracciamento
- **Selezione multipla** — possibilità di annullare più movimenti insieme

**Tipi di movimento:**

| Tipo | Descrizione | Quando si genera |
|---|---|---|
| `manual_load` | Carico Manuale | Creato manualmente dall'admin |
| `manual_unload` | Scarico Manuale | Creato manualmente dall'admin |
| `order_fulfilled` | Ordine Evaso | Automatico al "Segna come Spedito" |
| `supplier_order_received` | Carico Fornitore | Automatico al "Segna come Ricevuto" |
| `return` | Reso/Annullamento | Automatico all'annullamento ordine |

---

### Fatture & Documenti di Trasporto

#### Fatture
- Creazione manuale o collegamento a un ordine esistente
- Tipi: **Fattura standard** (FAT-YYYY-NNNNN), **Nota di credito** (NC-YYYY-NNNNN)
- Gestione stati: `Bozza` → `Inviata` → `Pagata` / `Scaduta` / `Annullata`
- Il **badge di navigazione** mostra il numero di fatture scadute attive (escluse bozze e annullate)
- **Azioni bulk** — Scarica PDF multipli in ZIP, Segna come Pagate
- **Clic su riga** → modale con tutti i dettagli e le righe fattura

#### Documenti di Trasporto (DDT)
- Gestione DDT collegati agli ordini di spedizione
- **Azioni bulk** — Scarica PDF multipli in ZIP

---

### Sconti & Promozioni

- **Tipi di sconto:** Percentuale (%), Importo Fisso (€), Spedizione Gratuita
- **Configurazione:** codice promozionale (salvato sempre in maiuscolo automaticamente), date di inizio/fine validità, importo minimo ordine, limite numero utilizzi
- Gli sconti di tipo **Spedizione Gratuita** azzerano automaticamente il costo di spedizione; il campo "Valore" non è applicabile per questo tipo
- Clic su riga → modale con tutti i dettagli

---

### Recensioni

- Lista recensioni con filtri per stato approvazione e acquisto verificato
- **Approvazione** direttamente dalla lista o dalla modale di dettaglio
- L'approvazione di una recensione aggiorna automaticamente il rating medio del prodotto
- Distinzione tra **acquisto verificato** (l'utente ha effettivamente acquistato quel prodotto) e recensione non verificata

---

## Comandi Utili

```bash
# Avviare i container
docker compose up -d

# Fermare i container
docker compose down

# Accedere al container PHP
docker compose exec app bash

# Eseguire le migration
docker compose exec app php artisan migrate

# Eseguire i seeder (dati di esempio)
docker compose exec app php artisan db:seed

# Ripulire il DB e riseminare da zero
docker compose exec app php artisan migrate:fresh --seed

# Visualizzare i log in tempo reale
docker compose logs -f app

# Svuotare la cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan view:clear
```

---

## Note Tecniche

- Il fuso orario è configurato su **Europe/Rome (CET/CEST)**
- I codici sconto sono sempre normalizzati in **maiuscolo** automaticamente al salvataggio
- L'IVA sugli ordini fornitori è **configurabile per singolo ordine** (default 22%)
- La gestione stock su prodotti con varianti opera a livello di singola variante; i prodotti senza varianti gestiscono lo stock a livello prodotto
- I movimenti stock annullati non vengono eliminati ma marcati con `cancelled_at` per garantire l'audit trail contabile

---

## Roadmap (Funzionalità Future)

- [ ] Creazione automatica account cliente al primo ordine guest
- [ ] Workflow resi formalizzato con restock automatico
- [ ] Notifiche automatiche admin (stock basso, fatture scadute, nuovi ordini)
- [ ] RBAC multi-admin con permessi granulari per ruolo
- [ ] Autenticazione a due fattori (2FA)
- [ ] Import bulk prodotti/ordini da CSV/Excel
- [ ] Integrazione gateway di pagamento (Stripe / PayPal)
- [ ] Sistema punti fedeltà (LoyaltyCard)

# Sito Colors S.r.l.

Gestionale e-commerce per Colors S.r.l., sviluppato con **Laravel 12** e **Filament 5** (pannello admin), containerizzato con Docker.

---

## Stack tecnologico

| Layer | Tecnologia |
|---|---|
| Backend | PHP 8.4 + Laravel 12 |
| Admin panel | Filament 5 |
| Frontend (dev) | Vite + Node 20 |
| Database | MySQL 8.0 |
| Web server | Nginx 1.25 |
| Containerizzazione | Docker + Docker Compose |
| PDF | barryvdh/laravel-dompdf |
| Export Excel | maatwebsite/excel |
| Grafici trend | flowframe/laravel-trend |
| Test | PestPHP 4 |

---

## Avvio con Docker

### Prima installazione (o rebuild completo)

```bash
docker compose up -d --build
```

Questo comando:
- Builda l'immagine PHP (Dockerfile)
- Avvia tutti i container: `app`, `nginx`, `mysql`, `queue`, `vite`
- MySQL diventa healthy prima che l'app parta (healthcheck configurato)

Dopo il build, esegui le migration e seeders dentro il container app:

```bash
docker compose exec app php artisan migrate --seed
```

Crea il primo utente admin:

```bash
docker compose exec app php artisan make:filament-user
```

### Avvio normale (container già buildati)

```bash
docker compose up -d
```

### Fermare i container

```bash
docker compose down
```

Per rimuovere anche i volumi (attenzione: cancella il DB):

```bash
docker compose down -v
```

### URL locali

| Servizio | URL |
|---|---|
| App / Admin | http://localhost:8000 |
| Admin Filament | http://localhost:8000/admin |
| Vite HMR (dev) | http://localhost:5173 |
| MySQL (host) | localhost:3307 |

> La porta MySQL sull'host è **3307** (non 3306) perché 3306 è già occupata da un'installazione MySQL nativa.

### Comandi utili dentro il container

```bash
# Entrare nel container app
docker compose exec app bash

# Lanciare artisan
docker compose exec app php artisan <comando>

# Vedere i log dell'app
docker compose logs -f app

# Vedere i log di tutti i servizi
docker compose logs -f
```

---

## Struttura del database

### Tabelle principali

| Tabella | Descrizione |
|---|---|
| `users` | Utenti (clienti + admin) |
| `categories` | Categorie prodotti (alberatura con parent_id) |
| `products` | Prodotti |
| `product_variants` | Varianti prodotto (colore, taglia, ecc.) |
| `product_images` | Immagini prodotti |
| `brands` | Marchi/brand |
| `discounts` | Sconti e coupon |
| `orders` | Ordini clienti |
| `order_items` | Righe ordine |
| `addresses` | Indirizzi di spedizione/fatturazione |
| `carts` | Carrelli |
| `cart_items` | Prodotti nel carrello |
| `wishlists` | Liste desideri |
| `stock_logs` | Log movimenti stock |
| `warehouse_movements` | Movimenti di magazzino (carico/scarico/reso) |
| `reviews` | Recensioni prodotti |
| `suppliers` | Fornitori |
| `supplier_orders` | Ordini ai fornitori |
| `quotes` | Preventivi |
| `invoices` | Fatture |
| `transport_documents` | DDT (documenti di trasporto) |
| `calendar_events` | Calendario eventi/scadenze |
| `loyalty_cards` | Carte fedeltà clienti |
| `loyalty_discounts` | Sconti programma fedeltà |
| `promotions` | Promozioni |

---

## Pannello Admin (Filament)

Accessibile su `/admin`.

### Resources disponibili

| Resource | Descrizione |
|---|---|
| **Products** | Gestione prodotti con varianti, immagini e recensioni (relation managers) |
| **Categories** | Categorie e sottocategorie |
| **Brands** | Marchi |
| **Orders** | Ordini clienti con export Excel |
| **Customers** | Anagrafica clienti |
| **Suppliers** | Fornitori |
| **SupplierOrders** | Ordini ai fornitori |
| **Discounts** | Sconti e coupon |
| **Promotions** | Promozioni |
| **Quotes** | Preventivi |
| **Invoices** | Fatture (con fatture ricorrenti) |
| **TransportDocuments** | DDT |
| **LoyaltyCards** | Carte fedeltà |
| **LoyaltyDiscounts** | Sconti fedeltà |
| **CalendarEvents** | Calendario eventi |
| **Reviews** | Recensioni prodotti |

### Pagine custom

| Pagina | Descrizione |
|---|---|
| **Dashboard** | Dashboard principale con widget KPI |
| **Panoramica** | Panoramica generale |
| **DashboardMagazzino** | Dashboard dedicata al magazzino |
| **Inventario** | Gestione inventario |
| **CaricoMerce** | Registrazione carichi merce |
| **ScaricoMerce** | Registrazione scarichi merce |
| **ResoMerce** | Gestione resi merce |
| **StoricoMovimenti** | Storico movimenti di magazzino |
| **AnalyticsAvanzate** | Analisi avanzate e statistiche |
| **ReportGenerale** | Report generale vendite |
| **ReportResi** | Report resi |
| **ReportFedelta** | Report programma fedeltà |

### Widget dashboard

- `StatsOverview` — KPI principali (ordini, fatturato, ecc.)
- `RevenueChart` — Grafico ricavi
- `LatestOrders` — Ultimi ordini
- `TopProductsChart` — Prodotti più venduti
- `SalesByBrandChart` — Vendite per brand
- `SalesByCategoryChart` — Vendite per categoria
- `SalesForecastChart` — Previsioni vendite
- `LowStockAlert` — Alert scorte basse
- `PopularProducts` — Prodotti popolari
- `RecentReviews` — Recensioni recenti
- `AbcAnalysisWidget` — Analisi ABC prodotti
- `PerformanceComparisonWidget` — Confronto performance
- `RecentMovementsWidget` — Movimenti recenti magazzino
- `ReorderSuggestionsWidget` — Suggerimenti riordino
- `UpcomingEventsWidget` — Prossimi eventi calendario

---

## Sviluppo locale (senza Docker)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
composer run dev   # avvia server, queue worker e Vite in parallelo
```

---

## Test

```bash
composer test
# oppure
php artisan test
```

---

## To do / sezioni da completare

- [ ] Design frontend (font, colori primario/secondario/accento da definire)
- [ ] Homepage pubblica
- [ ] Catalogo prodotti pubblico (con categorie e sottocategorie)
- [ ] Pagina Chi siamo
- [ ] Supporto/Contatti
- [ ] Area utente (registrazione, login, storico ordini)
- [ ] Carrello e checkout

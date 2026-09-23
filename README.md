# JobPulse — Remote Job Market Analytics

A Laravel dashboard that tracks the live remote job market: postings-per-day trends,
which companies are hiring the most, and which skills are trending week over week.

**No demo data.** Every number comes from the [Remotive](https://remotive.com/api-documentation)
public job API, a free, key-free feed of real, currently-open remote postings. A scheduled
Artisan command pulls fresh listings every 6 hours and an analytics service turns them into
the charts on the dashboard.

This folder contains only the *custom* application code — models, controllers, services,
migrations, views, the Vite/Tailwind setup, and tests. It's meant to be dropped into a fresh
Laravel install, not run standalone.

## What it actually does

- `php artisan jobs:fetch` — pulls jobs from Remotive across several categories and
  upserts them by Remotive's own id, so re-running never creates duplicates.
- A scheduled job (`routes/console.php`) runs that fetch automatically every 6 hours —
  Remotive asks integrators not to poll more than ~4x/day, so this respects that.
- `JobAnalyticsService` computes: total postings tracked, new postings this week, companies
  hiring, jobs-by-category, postings-per-day for the last 30 days, and trending skills
  (this week's tag mentions vs. last week's, as a % change) — all cached for 15 minutes.
- A public dashboard (`/`) with a live ticker of the newest postings, stat blocks, a
  postings-over-time line chart, a category donut, a top-companies bar chart, and a
  trending-skills table with up/down percentages.
- A searchable, filterable job board (`/jobs`) and a detail page per posting.
- A JSON API (`/api/stats`) exposing the same numbers, plus a token-gated `/api/stats/refresh`
  so you (or a recruiter poking around) can trigger a live refresh from the browser.
- Unit + feature tests for the analytics logic and the routes.

## Local setup

```bash
composer create-project laravel/laravel jobpulse-analytics
cd jobpulse-analytics

# copy every file from this project into the fresh install, overwriting
# routes/web.php, routes/console.php, config/services.php, .env.example

composer require guzzlehttp/guzzle
npm install

cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # if you're using sqlite, as .env.example suggests

php artisan migrate
php artisan jobs:fetch           # populates real data immediately — do this before your first look

npm run build   # or `npm run dev` while working on the UI
php artisan serve
```

Open `http://127.0.0.1:8000` — the dashboard should already show live numbers, not zeros,
because you ran `jobs:fetch` before starting the server.

## Where to deploy it so recruiters can actually open it

You need three things for this project specifically: a public URL, a database that
persists between deploys, and something to run `php artisan jobs:fetch` on a schedule
(not just serve pages). That last one rules out purely static or serverless-function hosts.

**Recommended: Railway** (railway.app)
- Free tier is enough for a portfolio project with this traffic.
- One-click provisions a Postgres database alongside your app — swap `DB_CONNECTION` to
  `pgsql` and it works without code changes.
- Has a built-in Cron plugin so `php artisan jobs:fetch` runs on schedule without you
  needing a persistent worker process.
- Deploys straight from a GitHub repo, gives you a `*.up.railway.app` URL immediately,
  and lets you attach a custom domain for free — worth doing before sharing it with recruiters.

**Solid alternative: Render** (render.com)
- Same shape as Railway: a free web service + free Postgres + a separate free Cron Job
  resource for the fetch command. Slightly more manual setup (you configure the build
  and start commands yourself), but just as capable and just as free.

**If you want it to look more "production":** DigitalOcean App Platform (~$5/mo) or
Laravel Forge on a small Hetzner/Vultr box. Neither is necessary for a portfolio piece,
but either signals you know how real Laravel apps get deployed, not just how they get built —
worth mentioning in an interview even if you don't pay for it.

**Don't use:** Vercel or Netlify. They're built for static sites and JS serverless
functions, not long-running PHP apps with a database and a cron scheduler — you'd fight
the platform the whole way.

### On whichever host you pick
1. Set every variable from `.env.example` in the platform's environment settings
   (generate `APP_KEY` locally with `php artisan key:generate --show` and paste it in).
2. Run `php artisan migrate --force` as a release/build step.
3. Schedule `php artisan jobs:fetch` to run every 6 hours (Railway/Render's Cron plugin,
   or the platform's scheduler equivalent) — or point their scheduler at
   `php artisan schedule:run` every minute if the platform expects that pattern instead.
4. Run `php artisan jobs:fetch` once manually right after the first deploy, so the
   dashboard isn't empty when a recruiter's first visit lands between scheduled runs.

## Why this is a stronger portfolio piece than a CRUD demo

- It integrates a real third-party API and handles its constraints (rate limits, upserts,
  stale data) rather than reading from a seeded database.
- The analytics service does actual computation — grouping, time-bucketing, week-over-week
  comparison — not just `Model::all()` dumped into a table.
- It has tests that check the analytics logic itself, not just that a page returns 200.
- It ships a JSON API alongside the HTML views, which is worth pointing out in an interview
  even if nobody asks.

# RedFlag MVP

RedFlag is a safety-first community tool for sex workers to check phone numbers, SMS senders, and WhatsApp contacts before they waste time or accept unsafe interactions.

This branch turns the old `phone_crawler` repository into a modern RedFlag MVP monorepo:

- `apps/api` — FastAPI + SQLite backend for number lookup, anonymous reports, risk calculation, moderation state, and demo seed data.
- `apps/mobile` — Expo React Native app prototype for manual number checks, incoming-call style warnings, after-call report prompts, and privacy-first UX.
- `docs` — product, privacy, moderation, and Android-native integration notes.

> The MVP is intentionally centralized and simple. It is designed for fast private beta testing before adding heavier privacy architecture, native Android call screening modules, and production moderation tooling.

## Core MVP features

- Normalize and check international phone numbers.
- Return clear risk levels: `none`, `low`, `warning`, `high`, `danger`.
- Show category totals for: no-show, unclean, rude, violent.
- Submit anonymous safety reports with optional factual comments.
- Keep user identity out of reports; beta auth uses a shared API key only.
- Include moderation status so future admin tooling can hide abusive or low-quality reports.
- Provide seed data for local testing.
- Mobile UX includes manual check, quick report, simulated incoming warning, privacy controls, and after-call prompt flow.

## Quick start

```bash
# clone your branch locally
git clone https://github.com/sushiomsky/phone_crawler.git redflag
cd redflag
git checkout redflag-mvp-app

# start backend
cd apps/api
python3 -m venv .venv
. .venv/bin/activate
pip install -e '.[dev]'
redflag-api seed
redflag-api serve --host 127.0.0.1 --port 8080
```

Open the API docs at:

```text
http://127.0.0.1:8080/docs
```

Run the mobile prototype:

```bash
cd apps/mobile
npm install
npm run start
```

For Android emulator talking to a host API, use:

```bash
EXPO_PUBLIC_REDFLAG_API_URL=http://10.0.2.2:8080 npm run android
```

For a real phone on the same LAN, use your computer IP:

```bash
EXPO_PUBLIC_REDFLAG_API_URL=http://192.168.x.x:8080 npm run start
```

## API key

Local default development key:

```text
redflag-dev-key
```

Set another key before running production-like tests:

```bash
export REDFLAG_API_KEY='change-me'
```

## Important Android reality check

Manual check and reporting work in the Expo prototype. Real incoming-call/SMS/WhatsApp detection requires Android native integrations:

- `CallScreeningService` or default-phone role for reliable incoming call access.
- `BroadcastReceiver` with SMS permissions for SMS sender detection.
- `NotificationListenerService` for WhatsApp notifications where user explicitly grants notification access.
- Foreground service or high-priority notifications for warning overlays.

The app architecture already separates the product flow from those native hooks so the next PR can add native Android code without rewriting the UX.

## Safety and legal principles

RedFlag stores community safety experiences, not verified accusations. UI and API wording should stay factual and careful:

- “reported as”
- “community safety note”
- “risk signal”
- “not independently verified”
- “pattern reported by multiple users”

No public profiles, no likes, no leaderboards, no social feed mechanics.

## Repository layout

```text
apps/
  api/       FastAPI backend
  mobile/    Expo React Native mobile prototype
docs/
  android-native-roadmap.md
  moderation-and-trust.md
  privacy-threat-model.md
```

## Development status

This is a private-beta MVP foundation. Production work still needed:

- production authentication and invite management
- admin moderation UI
- native Android call/SMS/notification hooks
- encrypted at-rest storage strategy
- abuse prevention and stronger audit logging
- legal review for wording and data retention

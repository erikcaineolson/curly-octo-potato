# CalcTek Calculator

An API-driven calculator with history ("ticker tape"), built with **Laravel 12 + Inertia.js + Vue 3 + MySQL**, fully Dockerized.

Admittedly I went overboard, but as I was asked to mock how I would tackle an E2E component. During our conversation the comment was made we don't really do much frontend testing, which is fine, but as the request asked for coverage, and he mentioned we maintain 99.9% coverage on the backend, I figured it made sense in this example.

I didn't have a local environment...so I built one. My general approach when building software is to manage local development via Docker, as I did here.

Finally, I made a design decision on the Expression calculator to colorize the parenthetical pairs (like some versions of vi/vim do, or the "Rainbow Brackets" plugin for JetBrains' PHP Storm IDE). Looking at monochrome text makes tracking multiple parentheses difficult; this should improve usability.

## Quick Start

```bash
# 1. Clone & enter
git clone <repo-url> && cd curly-octo-potato

# 2. Copy environment file
cp .env.example .env

# 3. Start everything (dependencies, key generation, migrations, and seeding run automatically)
docker compose up -d --build

# 4. Open the app
open http://localhost:8000
```

The Vite dev server runs on the `node` container at port **5173** for hot module replacement during development. If you visit http://localhost:5173, you'll see the info reflected on that. I didn't change it because it wasn't pertinent here, and local development doesn't require the restrictions of a prod environment. On production, we could [preferably] not run any dev servers, but if we REALLY wanted to keep the hot module replacement functionality, we could guard it behind a firewall. I can't imagine ever having a reason to keep it on prod.

## Features

- **Standard Calculator** — click-or-type number pad with +, -, x, /
- **Expression Mode** — type complex math expressions like `sqrt((((9*9)/12)+(13-4))*2)^2`
- **Ticker Tape** — scrollable history of all calculations
- **Delete** — remove individual entries or clear all history (soft deletes)
- **Keyboard Support** — use number keys, operators, Enter, Escape, Backspace
- **REST API** — all operations available via JSON endpoints

## API Endpoints

| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `/api/calculations` | List history (paginated, newest first) |
| `POST` | `/api/calculations` | Create a calculation |
| `DELETE` | `/api/calculations/{id}` | Soft-delete one calculation |
| `DELETE` | `/api/calculations` | Soft-delete all calculations |

### POST /api/calculations

**Simple mode:**
```json
{
  "operand_a": 9,
  "operand_b": 3,
  "operator": "add"
}
```

Operators: `add`, `subtract`, `multiply`, `divide`

**Expression mode:**
```json
{
  "expression": "sqrt((((9*9)/12)+(13-4))*2)^2"
}
```

Supports: `+`, `-`, `*`, `/`, `^`, `sqrt()`, parentheses, unary minus, decimals.

## Expression Parser

The expression parser uses a recursive descent approach to safely evaluate mathematical expressions. It tokenizes the input and walks it within strict parsing rules to avoid arbitrary code execution. It supports:

- Operator precedence: `2+3*4` = `14`
- Parentheses: `(2+3)*4` = `20`
- Right-associative exponents: `2^3^2` = `512`
- Square root: `sqrt(9)` = `3`
- Unary minus: `-5+3` = `-2`

## Running Tests

```bash
docker compose exec app php artisan test
```

Or locally (uses SQLite in-memory):

```bash
php artisan test
```

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12 (PHP 8.4) |
| Frontend | Vue 3 + Inertia.js |
| Styling | Tailwind CSS v4 |
| Database | MySQL 8 |
| Build | Vite |
| Infrastructure | Docker Compose |

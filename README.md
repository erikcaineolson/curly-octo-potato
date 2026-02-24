# CalcTek Calculator

An API-driven calculator with history ("ticker tape"), built with **Laravel 12 + Inertia.js + Vue 3 + MySQL**, fully Dockerized.

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

The Vite dev server runs on the `node` container at port **5173** for hot module replacement during development.

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

The expression parser is a hand-written **recursive descent parser** that safely evaluates mathematical expressions. It tokenizes and walks the input using a formal grammar — no arbitrary code execution. It supports:

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
| Backend | Laravel 12 (PHP 8.3) |
| Frontend | Vue 3 + Inertia.js |
| Styling | Tailwind CSS v4 |
| Database | MySQL 8 |
| Build | Vite |
| Infrastructure | Docker Compose |

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Api/CalculationController.php   # JSON API
│   └── CalculatorController.php        # Inertia page
├── Http/Requests/StoreCalculationRequest.php
├── Http/Resources/CalculationResource.php
├── Models/Calculation.php
└── Services/ExpressionParser.php       # Recursive descent parser

resources/js/
├── Pages/Calculator.vue                # Main page
├── Components/
│   ├── CalculatorDisplay.vue
│   ├── CalculatorKeypad.vue
│   └── TickerTape.vue
├── composables/useCalculations.js
└── services/api.js

tests/Feature/
├── CalculationApiTest.php              # 14 API tests
└── ExpressionParserTest.php            # 17 parser tests
```

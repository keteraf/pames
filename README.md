# Pames

**Pames** is a website that offers a **new logic and math game every day**. Come back each day to find a fresh puzzle, sharpen your reasoning, and challenge yourself with a different brain teaser than the day before.

This project was built as a **portfolio piece** to showcase modern full-stack web development with Laravel and Livewire.

## What it does

- 🧩 **Daily puzzles** — a different logic or math game is available each day.
- 🔁 **Fresh every day** — the game rotates automatically, so there's always something new to play.
- 🧠 **Logic & math focused** — puzzles are designed to exercise reasoning, pattern recognition, and numerical thinking.
- 👤 **User accounts** — sign up, log in, and (optionally) track your progress.

## Tech stack

Pames is a modern Laravel application built with:

- **[Laravel 13](https://laravel.com)** — application framework
- **[Livewire 4](https://livewire.laravel.com)** — dynamic, reactive UI without leaving PHP
- **[Flux UI](https://fluxui.dev)** — Livewire component library
- **[Laravel Fortify](https://laravel.com/docs/fortify)** — authentication (login, registration, 2FA, passkeys)
- **[Tailwind CSS 4](https://tailwindcss.com)** — styling
- **[Vite](https://vitejs.dev)** — asset bundling
- **[Pest 4](https://pestphp.com)** — testing
- **PHP 8.3+**

## Getting started

### Requirements

- PHP 8.3 or higher
- Composer
- Node.js & npm

### Installation

```bash
# Install PHP dependencies
composer install

# Install front-end dependencies
npm install

# Set up your environment
cp .env.example .env
php artisan key:generate

# Run database migrations (and seeders)
php artisan migrate --seed
```

### Running locally

```bash
# Start the full dev environment (server, queue, and Vite)
composer run dev
```

Or run the pieces individually:

```bash
php artisan serve   # start the web server
npm run dev         # start Vite for hot-reloading assets
```

Then open the app in your browser at the address shown in the terminal (typically `http://localhost:8000`).

### Building for production

```bash
npm run build
```

## Testing

Pames uses [Pest](https://pestphp.com) for testing:

```bash
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
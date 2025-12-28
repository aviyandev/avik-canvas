# avik/canvas

Canvas is the view and template layer of the Avik framework.

It is intentionally **simple**, **PHP-first**, and **predictable**.

Canvas is **not Blade**, **not a new language**, and **not a logic engine**.
It is a very thin declarative layer that compiles to plain PHP and renders HTML.

---

## Philosophy

Canvas follows these strict principles:

- PHP remains PHP
- HTML remains readable
- No magic runtime parsing
- No heavy DSL or expression language
- Compile once, render many times
- Syntax is intentionally limited
- Future power grows internally, not via new syntax

> Canvas is designed to be stable for years without redesign.

---

## What Canvas Is

- A view renderer
- A PHP execution wrapper
- A minimal declarative syntax for common patterns
- A compiled, cached template system

---

## What Canvas Is NOT

- Blade
- Twig
- A logic engine
- A controller system
- A reactive system
- A component framework (yet)

Canvas does **not** try to replace PHP.
It only removes small pain points.

---

## Supported Syntax (Final & Closed)

⚠️ The syntax below is **FINAL and IMMUTABLE**.
No new directives will be added in future versions.

### Output

```text
{{ variable }}        escaped output
{!! variable !!}      raw output
###Loop
@each items as item
    ...
@endEach


Meaning:

foreach ($items as $item)

###Conditional Display (truthy only)
@show variable
    ...
@endshow


Meaning:

if (!empty($variable))


###There is intentionally:

no else

no elseif

###This keeps templates simple and readable.

Layouts
@layout app

@content
    ...
@endcontent


app refers to app.avik.php

###Layout controls structure

Child view only defines content

Components
@component card


Loads components/card.avik.php

Static include

No props in v1

No lifecycle

No logic

###Example View
@layout app

@content
    <h1>{{ title }}</h1>

    @each users as user
        <p>{{ user.name }}</p>
    @endEach

    @show isAdmin
        <strong>Admin Panel</strong>
    @endshow

    @component footer
@endcontent

Rendering a View
$factory = new Factory(
    viewsPath: __DIR__ . '/views',
    cachePath: __DIR__ . '/storage/canvas'
);

$view = $factory->make('home', [
    'title' => 'Welcome',
    'users' => $users,
    'isAdmin' => true,
]);

echo $view->render();

Compilation & Caching

Templates are compiled to plain PHP

Compiled files are cached by file hash

No runtime parsing

No performance penalty after first render
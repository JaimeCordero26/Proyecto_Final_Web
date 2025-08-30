<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <style>
        :root{--bg:#FDFDFC;--bg-dark:#0a0a0a;--text:#1b1b18;--text-dark:#EDEDEC;--card:#fff;--card-dark:#161615;--primary:#1b1b18}
        *{box-sizing:border-box;margin:0}
        body{font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif;min-height:100svh;display:flex;flex-direction:column;gap:1rem;padding:1.5rem;background:var(--bg);color:var(--text)}
        @media (prefers-color-scheme:dark){body{background:var(--bg-dark);color:var(--text-dark)}}

        header{width:100%;max-width:56rem;margin:0 auto}
        nav{display:flex;gap:.75rem;justify-content:flex-end;flex-wrap:wrap}

        /* Botnes */
        .btn{display:inline-block;text-decoration:none;border-radius:.5rem;border:1px solid #e3e3e0;padding:.75rem 1.25rem;font-weight:600;font-size:0.95rem}
        .btn:hover{border-color:#bdbdbd}
        .btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
        .btn-primary:hover{opacity:.9}
        .btn-ghost{background:transparent;color:inherit}

        .wrap{display:flex;justify-content:center}
        .main{width:100%;max-width:56rem;background:var(--card);border:1px solid #eaeaea;border-radius:.75rem;overflow:hidden}
        @media (prefers-color-scheme:dark){.main{background:var(--card-dark);border-color:#3E3E3A}}

        /* Layout en columnaa */
        .panel{padding:1.5rem 1.5rem 0}
        .panel h1{font-size:1.35rem;font-weight:700;margin-bottom:.5rem}
        .panel p{opacity:.8}

        /* Banner abajo*/
        .banner{padding:1rem 1.5rem 1.5rem}
        .banner img{display:block;width:100%;height:auto;border-radius:.5rem}
        .spacer{height:3.5rem}
    </style>
</head>
<body>
<header>
    @if (Route::has('login'))
        <nav>
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">Ir al Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Iniciar sesión</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-ghost">Registrarse</a>
                @endif
            @endauth
        </nav>
    @endif
</header>

<div class="wrap">
    <main class="main">
        <!-- Mensaje arriba -->
        <section class="panel">
            <h1>Bienvenido</h1>
            <p>Inicia sesión para gestionar tu cuenta. Aquí puedes poner un texto corto de bienvenida o marketing.</p>
        </section>

        <!-- Banner abaj -->
        <section class="banner" aria-label="Banner">
            <img src="{{ asset('images/banner.jpeg') }}" alt="Banner">
        </section>
    </main>
</div>

@if (Route::has('login'))
    <div class="spacer"></div>
@endif
</body>
</html>

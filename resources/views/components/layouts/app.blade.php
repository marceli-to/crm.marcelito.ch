<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>marceli.to</title>
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#ff0000">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..600&display=swap" rel="stylesheet">
@vite('resources/css/app.css')
@fluxStyles
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
  <flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
      <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

      <x-icons.brand class="w-40 h-auto my-3 ml-2" />

      <flux:navlist variant="outline" class="space-y-2">
        <flux:navlist.item icon="home" href="#">Home</flux:navlist.item>
        <flux:navlist.item icon="presentation-chart-line" href="/">Dashboard</flux:navlist.item>
        <flux:navlist.item icon="clock" href="/timer">Timer</flux:navlist.item>
        <flux:navlist.item icon="building-storefront" href="/companies">Companies</flux:navlist.item>
        <flux:navlist.item icon="wrench-screwdriver" href="/projects">Projects</flux:navlist.item>
        <flux:navlist.item icon="banknotes" href="/invoices">Invoices</flux:navlist.item>
        <flux:navlist.item icon="ticket" href="/expenses">Expenses</flux:navlist.item>


        {{-- <flux:navlist.group expandable heading="Favorites" class="hidden lg:grid">
            <flux:navlist.item href="#">Marketing site</flux:navlist.item>
            <flux:navlist.item href="#">Android app</flux:navlist.item>
            <flux:navlist.item href="#">Brand guidelines</flux:navlist.item>
        </flux:navlist.group> --}}
      </flux:navlist>

      <flux:spacer />

      <flux:navlist variant="outline">
          <flux:navlist.item icon="cog-6-tooth" href="#">Settings</flux:navlist.item>
          <flux:navlist.item icon="information-circle" href="#">Help</flux:navlist.item>
      </flux:navlist>

      <flux:dropdown position="top" align="left" class="max-lg:hidden">
          <flux:profile avatar="https://fluxui.dev/img/demo/user.png" name="Marcel Stadelmann" />
          <flux:menu>
            <flux:menu.item icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
          </flux:menu>
      </flux:dropdown>
  </flux:sidebar>

  <flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
    <flux:spacer />
    <flux:profile avatar="https://fluxui.dev/img/demo/user.png" />
  </flux:header>

  <flux:main>
    {{ $slot }}
  </flux:main>
  
  <flux:toast position="bottom right" />
  @fluxScripts
</body>
</html>

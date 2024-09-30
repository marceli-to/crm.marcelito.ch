<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Laravel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..600&display=swap" rel="stylesheet">
@vite('resources/css/app.css')
@fluxStyles
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
  <flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
      <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

      <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="px-2 dark:hidden" />
      <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." class="px-2 hidden dark:flex" />

      <flux:input as="button" variant="filled" placeholder="Search..." icon="magnifying-glass" />

      <flux:navlist variant="outline">
        <flux:navlist.item icon="home" href="#" current>Home</flux:navlist.item>
        <flux:navlist.item icon="inbox" badge="12" href="#">Inbox</flux:navlist.item>
        <flux:navlist.item icon="document-text" href="#">Documents</flux:navlist.item>
        <flux:navlist.item icon="calendar" href="#">Calendar</flux:navlist.item>

        <flux:navlist.group expandable heading="Favorites" class="hidden lg:grid">
            <flux:navlist.item href="#">Marketing site</flux:navlist.item>
            <flux:navlist.item href="#">Android app</flux:navlist.item>
            <flux:navlist.item href="#">Brand guidelines</flux:navlist.item>
        </flux:navlist.group>
      </flux:navlist>

      <flux:spacer />

      <flux:navlist variant="outline">
          <flux:navlist.item icon="cog-6-tooth" href="#">Settings</flux:navlist.item>
          <flux:navlist.item icon="information-circle" href="#">Help</flux:navlist.item>
      </flux:navlist>

      <flux:dropdown position="top" align="left" class="max-lg:hidden">
          <flux:profile avatar="https://fluxui.dev/img/demo/user.png" name="Olivia Martin" />

          <flux:menu>
              <flux:menu.radio.group>
                  <flux:menu.radio checked>Olivia Martin</flux:menu.radio>
                  <flux:menu.radio>Truly Delta</flux:menu.radio>
              </flux:menu.radio.group>

              <flux:menu.separator />

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
      <flux:heading size="xl" level="1">Good afternoon, Olivia</flux:heading>

      <flux:subheading size="lg" class="mb-6">Here's what's new today</flux:subheading>

      <flux:separator variant="subtle" />
  </flux:main>
  @fluxScripts
</body>
</html>

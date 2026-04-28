<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/js/ui/inertia.app.ts')
    @inertiaHead
  </head>
  <body>
    <div class="isolate">
      @inertia
    </div>
    @if(show_dev_tools())
    <div class="isolate">
      <div id="devtools-mount"></div>
    </div>
    @endif
  </body>
</html>
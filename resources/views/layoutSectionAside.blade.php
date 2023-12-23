@extends('app')
@section('content')
<section class="col-span-5 col-start-2 row-start-2 max-h-[800px]">
    @yield('sectionContent')
</section>
<aside class="col-span-1 col-start-1 row-start-2 max-h-[800px]">
    @yield('asideContent')
    @vite('resources/js/clientScripts/sidebar.ts')
</aside>
<footer class="col-span-5 col-start-2 row-start-3">
    Delevoped by Kjetil Baksaas
</footer>
@endsection

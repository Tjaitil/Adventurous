@extends('app')
@section('content')
    <section class="col-span-5 col-start-2 row-start-2 max-h-[800px] min-h-[600px] py-2 px-2">
        @yield('sectionContent')
    </section>
    <aside class="col-span-1 col-start-1 row-start-2 max-h-[800px] z-20 relative">
        @yield('asideContent')
        @vite('resources/js/clientScripts/sidebar.ts')
    </aside>
    <footer class="col-span-5 col-start-2 row-start-3">
        Delevoped by Kjetil Baksaas
    </footer>
@endsection

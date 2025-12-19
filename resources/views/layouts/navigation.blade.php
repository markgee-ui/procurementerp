<ul class="space-y-2 px-2">
    <li>
        <a href="#" class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700">
            {{-- Dashboard Icon (Home) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.155-.439 1.595 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125h9.75a1.125 1.125 0 0 0 1.125-1.125V11.25M12 9v3.75m-3.75 0h7.5" />
            </svg>
            Dashboard
        </a>
    </li>

    {{-- PROCUREMENT MAIN SECTION --}}
    <li>
        <a href="{{ route('procurement.order.index') }}" 
           class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 
           {{ request()->routeIs('procurement.*') ? 'bg-gray-700 text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.5 2.365 3.332c.18.252.327.545.419.85.122.403.047.818-.284 1.139l-1.637 1.638m.345-4.5h-2.11M8.706 12l2.365 3.332c.18.252.327.545.419.85.122.403.047.818-.284 1.139l-1.637 1.638M4.092 14.739l2.11 2.11m4.5-5.25h1.5m-3 0h1.5m-1.5 0h1.5m-1.5 0h1.5m-1.5 0h1.5m-1.5 0h1.5" />
            </svg>
            Procurement
        </a>
    </li>
    <li>
        <a href="{{ route('procurement.service-order.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('procurement.service-order.*') ? 'font-bold text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.341-.088.661-.237.958-.445.424-.297.697-.733.788-1.222A2.498 2.498 0 0019.123 5.4M16 19l-1.5-1.5M11.33 13.84l-1.5-1.5m5.877-5.877l1.5-1.5" />
            </svg>
            Service Orders
        </a>
    </li>
    <li>
        <a href="{{ route('procurement.requisition.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('procurement.requisition.*') ? 'font-bold text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            PRs Ready for PO
        </a>
    </li>
    <li>
        <a href="{{ route('procurement.supplier.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('procurement.supplier.index') ? 'font-bold text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M4.5 21H19.5M4.5 3h15M4.5 3v.75m15-.75v.75M4.5 6v-.75m15 .75v-.75m-9 3h.008v.008H12V9Zm3.75-3.75h.008v.008h-.008V5.25ZM9.75 6.75h.008v.008H9.75V6.75ZM6 6.75h.008v.008H6V6.75Zm9-3.75h.008v.008h-.008V3Zm-9 3h.008v.008H6V6Zm9 0h.008v.008h-.008V6Zm-3 0h.008v.008H9V6Z" />
            </svg>
            Suppliers List
        </a>
    </li>
    <li> 
        <a href="{{ route('procurement.product.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('procurement.product.index') ? 'font-bold text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.125.75.75 0 0 1-.724.75h-.75a.75.75 0 0 1-.527-1.285l1.458-1.549A9.152 9.152 0 0 1 12 7.728c4.38 0 8.242 1.986 10.244 4.965m-8.913 2.5a.75.75 0 0 0-.546-.356C9.1 13.91 8.583 13.75 8.05 13.75c-.533 0-1.05.16-1.504.469-.453.309-.774.724-.91 1.258-.135.534-.058 1.099.208 1.631.266.532.695.961 1.227 1.227.532.266 1.097.343 1.631.208.534-.136.949-.457 1.258-.91.309-.454.469-.971.469-1.504 0-.533-.16-1.05-.469-1.504Z" />
            </svg>
            Products List
        </a>
    </li>
    {{-- Procurement Reports --}}
    @if(Auth::user()->hasRole('procurement'))
    <li>
        <a href="{{ route('procurement.reports.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 {{ request()->routeIs('procurement.reports.*') ? 'font-bold text-white' : '' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Procurement Reports
        </a>
    </li>
    @endif
    
    {{-- QUANTITY SURVEYOR / PLANNING SECTION --}}
    <li class="mt-4">
        <a href="{{ route('qs.index') }}" 
           class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 
           {{ request()->routeIs('qs.*') ? 'bg-gray-700 text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Planning (QS)
        </a>
    </li>
    <li>
        <a href="{{ route('qs.boq.create') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('qs.boq.create') ? 'font-bold text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create BOQ
        </a>
    </li>
    <li>
        <a href="{{ route('qs.boq.index') }}" 
             class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
             {{ request()->routeIs('qs.boq.index') ? 'font-bold text-white' : '' }}">
            {{-- List Icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h1.5M8.25 10.5h1.5M8.25 14.25h1.5m4.5 4.5h3m-12.75-6.75h7.5m-7.5 3h7.5m7.5-3h.75m-7.5-6h.75m7.5-3h.75m-9 12h.75m-7.5 3h7.5M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
            </svg>
            Bill of Quantities List
        </a>
    </li>
    <li>
        <a href="{{ route('qs.requisitions.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('qs.requisitions.*') ? 'font-bold text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3 7.5-7.5M19.5 7.5v-2.25a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 5.25v13.5A2.25 2.25 0 0 0 6.75 21h8.25" />
            </svg>
            PR Approvals (QS)
        </a>
    </li>
    
    {{-- OFFICE PROJECT MANAGER SECTION (OPM) --}}
    <li class="mt-4">
        <a href="{{ route('opm.index') }}" 
           class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 
           {{ request()->routeIs('opm.*') ? 'bg-gray-700 text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25a1.5 1.5 0 0 0-1.5-1.5H5.25A1.5 1.5 0 0 1 3.75 10.5v-.75m0 0a1.5 1.5 0 0 1 1.5-1.5h1.5a1.5 1.5 0 0 1 1.5 1.5m-4.5 0V7.5m0 0h12v.75m0 0h1.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H9.75M9 3v2.25M9 6v.75M12 9v12" />
            </svg>
            Office Management (OPM)
        </a>
    </li>
    <li>
        <a href="{{ route('opm.requisitions.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('opm.requisitions.*') ? 'font-bold text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 6.824a5.9 5.9 0 0 1 1.34-1.34c.16-.16.295-.333.393-.521.433-.827.84-1.768.84-2.61a.75.75 0 0 0-.75-.75H4.5a.75.75 0 0 0-.75.75c0 .842.407 1.783.84 2.61.098.188.233.361.393.521a5.9 5.9 0 0 1 1.34 1.34M12 17.25H4.5a.75.75 0 0 1-.75-.75V8.25c0-.414.336-.75.75-.75h7.5m5.625 9.75c-1.233 0-2.434-.148-3.56-1.144a.75.75 0 0 0-1.196.442 7.5 7.5 0 0 0 11.25 0 .75.75 0 0 0-1.196-.442c-1.126.996-2.327 1.144-3.56 1.144Z" />
            </svg>
            PR Approvals (OPM)
        </a>
    </li>
    {{-- OPM Reports --}}
    @if(Auth::user()->hasRole('offpm'))
    <li>
        <a href="{{ route('opm.reports.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 {{ request()->routeIs('opm.reports.*') ? 'font-bold text-white' : '' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
            Office Admin Reports
        </a>
    </li>
    @endif
    
    {{-- PROJECT MANAGEMENT SECTION (PM) --}}
    <li class="mt-4">
        <a href="{{ route('pm.index') }}" 
           class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 
           {{ request()->routeIs('pm.index') || request()->routeIs('pm.requisitions.*') ? 'bg-gray-700 text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75v-2.25m3 3h-3m4.5-5.25v7.5m1.5 1.5h-.75m-14.25-5.25h15" />
            </svg>
            Site Management (PM)
        </a>
    </li>
    <li>
        <a href="{{ route('pm.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('pm.requisitions.create') ? 'font-bold text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create Requisition
        </a>
    </li>
    <li>
        <a href="{{ route('pm.requisitions.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('pm.requisitions.index') ? 'font-bold text-white' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h14.25m-14.25 4.5h14.25M3 18h14.25m3-13.5v7.5a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75V4.5" />
            </svg>
            My Requisitions
        </a>
    </li>
    {{-- PM Reports --}}
    @if(Auth::user()->hasRole('pm'))
    <li>
        <a href="{{ route('pm.reports.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 {{ request()->routeIs('pm.reports.*') ? 'font-bold text-white' : '' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            Site Activity Reports
        </a>
    </li>
    @endif
</ul>
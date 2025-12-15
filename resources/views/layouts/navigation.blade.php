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
        {{-- Main Procurement Link (Check for any route starting with 'procurement.') --}}
        <a href="{{ route('procurement.order.index') }}" 
           class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 
           {{ request()->routeIs('procurement.*') ? 'bg-gray-700 text-white' : '' }}">
            {{-- Procurement Icon (Shopping Bag/Cart) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.5 2.365 3.332c.18.252.327.545.419.85.122.403.047.818-.284 1.139l-1.637 1.638m.345-4.5h-2.11M8.706 12l2.365 3.332c.18.252.327.545.419.85.122.403.047.818-.284 1.139l-1.637 1.638M4.092 14.739l2.11 2.11m4.5-5.25h1.5m-3 0h1.5m-1.5 0h1.5m-1.5 0h1.5m-1.5 0h1.5m-1.5 0h1.5" />
            </svg>
            Procurement
        </a>
    </li>
    <li>
        <a href="{{ route('procurement.requisition.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('procurement.requisition.*') ? 'font-bold text-white' : '' }}">
            {{-- Checkmark/Ready Icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            PRs Ready for PO
        </a>
    </li>

    {{-- PROCUREMENT SUB-LINKS --}}
    <li>
        <a href="{{ route('procurement.supplier.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('procurement.supplier.index') ? 'font-bold text-white' : '' }}">
            {{-- Suppliers Icon (Reduced size for sub-link) --}}
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
            {{-- Products Icon (Reduced size for sub-link) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.125.75.75 0 0 1-.724.75h-.75a.75.75 0 0 1-.527-1.285l1.458-1.549A9.152 9.152 0 0 1 12 7.728c4.38 0 8.242 1.986 10.244 4.965m-8.913 2.5a.75.75 0 0 0-.546-.356C9.1 13.91 8.583 13.75 8.05 13.75c-.533 0-1.05.16-1.504.469-.453.309-.774.724-.91 1.258-.135.534-.058 1.099.208 1.631.266.532.695.961 1.227 1.227.532.266 1.097.343 1.631.208.534-.136.949-.457 1.258-.91.309-.454.469-.971.469-1.504 0-.533-.16-1.05-.469-1.504Z" />
            </svg>
            Products List
        </a>
    </li>
    <li>
        <a href="{{ route('procurement.order.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('procurement.order.*') ? 'font-bold text-white' : '' }}">
            {{-- Purchase Orders Icon (Reduced size for sub-link) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.023.832l.98 6.495m.879 1.125a2.121 2.121 0 0 1 0 3.375l-.948 1.422c-.637.957-.29 2.167.625 2.502a4.4 4.4 0 0 0 1.625.295h12.553a1.5 1.5 0 0 0 1.43-1.059l2.7-7.221a.75.75 0 0 0-.083-.795L21.75 8.25m-18 4.717V17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm16.5 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
            </svg>
            Purchase Orders
        </a>
    </li>
    
    
    {{-- QUANTITY SURVEYOR / PLANNING SECTION --}}
    <li>
        <a href="{{ route('qs.index') }}" 
           class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 
           {{ request()->routeIs('qs.*') ? 'bg-gray-700 text-white' : '' }}">
            {{-- Quality & Standards Icon (Checkmark/Scales) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Planning (QS)
        </a>
    </li>

    {{-- QS SUB-LINKS (BOQ MANAGEMENT) --}}
    <li>
        <a href="{{ route('qs.boq.create') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('qs.boq.create') ? 'font-bold text-white' : '' }}">
            {{-- Add Icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create Bill of Quantities
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
    
    {{-- QS SUB-LINKS (PR APPROVAL) --}}
    <li>
        <a href="{{ route('qs.requisitions.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('qs.requisitions.*') ? 'font-bold text-white' : '' }}">
            {{-- Approval Icon (Clipboard with Check) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3 7.5-7.5M19.5 7.5v-2.25a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 5.25v13.5A2.25 2.25 0 0 0 6.75 21h8.25" />
            </svg>
            PRs Awaiting QS Approval
        </a>
    </li>
    
    {{-- OFFICE PROJECT MANAGER SECTION (OPM) --}}
    <li>
        <a href="{{ route('opm.index') }}" 
           class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 
           {{ request()->routeIs('opm.*') ? 'bg-gray-700 text-white' : '' }}">
            {{-- Office PM Icon (Building/Office) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25a1.5 1.5 0 0 0-1.5-1.5H5.25A1.5 1.5 0 0 1 3.75 10.5v-.75m0 0a1.5 1.5 0 0 1 1.5-1.5h1.5a1.5 1.5 0 0 1 1.5 1.5m-4.5 0V7.5m0 0h12v.75m0 0h1.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H9.75M9 3v2.25M9 6v.75M12 9v12" />
            </svg>
            Office Management (OPM)
        </a>
    </li>

    {{-- OPM SUB-LINKS (PR APPROVAL) --}}
    <li>
        <a href="{{ route('opm.requisitions.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('opm.requisitions.*') ? 'font-bold text-white' : '' }}">
            {{-- Approval Icon (Clipboard with Check) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 6.824a5.9 5.9 0 0 1 1.34-1.34c.16-.16.295-.333.393-.521.433-.827.84-1.768.84-2.61a.75.75 0 0 0-.75-.75H4.5a.75.75 0 0 0-.75.75c0 .842.407 1.783.84 2.61.098.188.233.361.393.521a5.9 5.9 0 0 1 1.34 1.34M12 17.25H4.5a.75.75 0 0 1-.75-.75V8.25c0-.414.336-.75.75-.75h7.5m5.625 9.75c-1.233 0-2.434-.148-3.56-1.144a.75.75 0 0 0-1.196.442 7.5 7.5 0 0 0 11.25 0 .75.75 0 0 0-1.196-.442c-1.126.996-2.327 1.144-3.56 1.144Z" />
            </svg>
            PRs Awaiting OPM Approval
        </a>
    </li>
    
    {{-- PROJECT MANAGEMENT SECTION (PM) --}}
    <li>
        {{-- Main PM Link (Project List) --}}
        <a href="{{ route('pm.index') }}" 
           class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 
           {{ request()->routeIs('pm.index') || request()->routeIs('pm.requisitions.*') ? 'bg-gray-700 text-white' : '' }}">
            {{-- Project Icon (Hardhat/Construction) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75v-2.25m3 3h-3m4.5-5.25v7.5m1.5 1.5h-.75m-14.25-5.25h15" />
            </svg>
            Project Site Management (PM)
        </a>
    </li>
    
    {{-- PM SUB-LINKS --}}
    <li>
        {{-- Create PR Link --}}
        <a href="{{ route('pm.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('pm.requisitions.create') ? 'font-bold text-white' : '' }}">
            {{-- Create Icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create Purchase Requisition
        </a>
    </li>

    <li>
        {{-- View PRs Link --}}
        <a href="{{ route('pm.requisitions.index') }}" 
           class="sidebar-link flex items-center pl-8 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-700 
           {{ request()->routeIs('pm.requisitions.index') ? 'font-bold text-white' : '' }}">
            {{-- List Icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h14.25m-14.25 4.5h14.25M3 18h14.25m3-13.5v7.5a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75V4.5" />
            </svg>
            Submitted Requisitions
        </a>
    </li>

    <li>
        <a href="#" class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700">
            {{-- Reports Icon (Chart/Document) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3-3v2.25m-3-3v2.25M3.75 9h16.5M3.75 12H12m-9 3h7.5m-7.5 3h15M18 6v3" />
            </svg>
            Reports
        </a>
    </li>
</ul>
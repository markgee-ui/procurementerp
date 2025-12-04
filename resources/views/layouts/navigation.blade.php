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
    <li>
        <a href="{{ route('procurement.supplier.index') }}" class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 {{ request()->is('suppliers') ? 'active' : '' }}">
            {{-- Suppliers Icon (Users/Office Building) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M4.5 21H19.5M4.5 3h15M4.5 3v.75m15-.75v.75M4.5 6v-.75m15 .75v-.75m-9 3h.008v.008H12V9Zm3.75-3.75h.008v.008h-.008V5.25ZM9.75 6.75h.008v.008H9.75V6.75ZM6 6.75h.008v.008H6V6.75Zm9-3.75h.008v.008h-.008V3Zm-9 3h.008v.008H6V6Zm9 0h.008v.008h-.008V6Zm-3 0h.008v.008H9V6Z" />
            </svg>
            Suppliers
        </a>
    </li>
    <li> 
        <a href="{{ route('procurement.product.index') }}" class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 {{ request()->is('products') ? 'active' : '' }}">
            {{-- Products Icon (Tag/Box) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.125.75.75 0 0 1-.724.75h-.75a.75.75 0 0 1-.527-1.285l1.458-1.549A9.152 9.152 0 0 1 12 7.728c4.38 0 8.242 1.986 10.244 4.965m-8.913 2.5a.75.75 0 0 0-.546-.356C9.1 13.91 8.583 13.75 8.05 13.75c-.533 0-1.05.16-1.504.469-.453.309-.774.724-.91 1.258-.135.534-.058 1.099.208 1.631.266.532.695.961 1.227 1.227.532.266 1.097.343 1.631.208.534-.136.949-.457 1.258-.91.309-.454.469-.971.469-1.504 0-.533-.16-1.05-.469-1.504Z" />
            </svg>
            Products
        </a>
    </li>
    <!-- <li>
        {{-- Use request()->is() to check the current route and apply 'active' --}}
        <a href="{{ route('procurement.create') }}" 
           class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 {{ request()->is('procurement/create') ? 'active' : '' }}">
            {{-- Add New Entry Icon (Plus/Pencil) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add New Entry
        </a>
    </li> -->
    <li>
        {{-- Check if the current route name starts with 'procurement.order.' for active state --}}
        <a href="{{ route('procurement.order.index') }}" 
           class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 
           {{ request()->routeIs('procurement.order.*') ? 'bg-gray-700 text-white' : '' }}">
            {{-- Purchase Orders Icon (Shopping Cart) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.023.832l.98 6.495m.879 1.125a2.121 2.121 0 0 1 0 3.375l-.948 1.422c-.637.957-.29 2.167.625 2.502a4.4 4.4 0 0 0 1.625.295h12.553a1.5 1.5 0 0 0 1.43-1.059l2.7-7.221a.75.75 0 0 0-.083-.795L21.75 8.25m-18 4.717V17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm16.5 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
            </svg>
            Purchase Orders
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
    <li>
        <a href="{{ route('qs.index') }}" 
           class="sidebar-link flex items-center p-3 rounded-lg hover:bg-gray-700 
           {{ request()->routeIs('qs.*') ? 'bg-gray-700 text-white' : '' }}">
            {{-- Quality & Standards Icon (Checkmark/Scales) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Planning
        </a>
    </li>
    
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
</ul>
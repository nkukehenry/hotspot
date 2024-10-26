@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4">Manage Vouchers</h1>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.vouchers') }}" class="mb-4">
            <div class="flex space-x-4">
                <div class="w-1/3">
                    <label for="location_id" class="block text-gray-700 font-semibold mb-1">Location</label>
                    <select name="location_id" id="location_id"
                        class="block w-full mt-1 p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Locations</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-1/3">
                    <label for="package_id" class="block text-gray-700 font-semibold mb-1">Package</label>
                    <select name="package_id" id="package_id"
                        class="block w-full mt-1 p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Packages</option>
                        @foreach ($packages as $package)
                            <option value="{{ $package->id }}"
                                {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                {{ $package->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end w-1/3">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-md">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Vouchers Table -->
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4">Voucher Code</th>
                    <th class="py-2 px-4">Location</th>
                    <th class="py-2 px-4">Package</th>
                    <th class="py-2 px-4">Price</th>
                    <th class="py-2 px-4">Status</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vouchers as $voucher)
                    <tr class="text-gray-700 border-b">
                        <td class="py-2 px-4">{{ $voucher->code }}</td>
                        <td class="py-2 px-4">{{ $voucher->location_name }}</td>
                        <td class="py-2 px-4">{{ $voucher->package_name }}</td>
                        <td class="py-2 px-4">{{ $voucher->cost }}</td>
                        <td class="py-2 px-4">
                            <span
                                class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $voucher->is_used ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $voucher->is_used ? 'Used' : 'Available' }}
                            </span>
                        </td>
                        <td class="py-2 px-4">
                            <!-- Add actions like edit or delete if needed -->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $vouchers->links() }}
        </div>
    </div>
@endsection

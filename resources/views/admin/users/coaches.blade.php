@extends('layouts.dashboard')

@section('title', 'Coach Approvals')
@section('dashboard-type', 'Admin')
@section('user-role', 'Administrator')
@section('user-name', Auth::user()->name)
@section('status-message', 'Admin Access')
@section('dashboard-home-link', route('admin.dashboard'))

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : '' }}">
        <i class="fa-solid fa-gauge-high sidebar-icon"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'sidebar-active' : '' }}">
        <i class="fa-solid fa-users sidebar-icon"></i><span>User Management</span>
    </a>
    <a href="{{ route('admin.coaches') }}" class="sidebar-link {{ request()->routeIs('admin.coaches*') ? 'sidebar-active' : '' }}">
        <i class="fa-solid fa-user-tie sidebar-icon"></i>
        <span>Coach Approvals</span>
        @if($pendingCoachesCount > 0)
            <span class="ml-auto bg-red-500 text-white text-xs font-medium px-2 py-1 rounded-full">{{ $pendingCoachesCount }}</span>
        @endif
    </a>
    <a href="{{ route('admin.courses') }}" class="sidebar-link {{ request()->routeIs('admin.courses*') ? 'sidebar-active' : '' }}">
        <i class="fa-solid fa-graduation-cap sidebar-icon"></i><span>Course Management</span>
    </a>
    <a href="{{ route('admin.reservations') }}" class="sidebar-link {{ request()->routeIs('admin.reservations*') ? 'sidebar-active' : '' }}">
        <i class="fa-solid fa-calendar-check sidebar-icon"></i><span>Reservations</span>
    </a>
@endsection

@section('page-title', 'Coach Approvals')

@section('content')
    {{-- Pending --}}
    <div class="content-card mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            Pending Approval
            @if($pendingCoachesCount > 0)
                <span class="ml-2 bg-red-100 text-red-700 text-sm font-medium px-2.5 py-0.5 rounded-full">{{ $pendingCoachesCount }}</span>
            @endif
        </h2>

        @if($pendingCoaches->isEmpty())
            <div class="bg-gray-50 rounded-lg p-6 text-center text-gray-500">
                <i class="fa-solid fa-circle-check text-green-400 text-3xl mb-2"></i>
                <p>No pending coach approvals.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Coach</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registered</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingCoaches as $coach)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-yellow-400 flex items-center justify-center text-white font-bold">
                                            {{ substr($coach->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3 text-sm font-medium text-gray-900">{{ $coach->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $coach->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $coach->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <form action="{{ route('admin.approve-coach', $coach->id) }}" method="POST">
                                            @csrf
                                            <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-sm">
                                                <i class="fa-solid fa-check mr-1"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reject-coach', $coach->id) }}" method="POST">
                                            @csrf
                                            <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm">
                                                <i class="fa-solid fa-times mr-1"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Approved --}}
    <div class="content-card">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Approved Coaches</h2>
        @if($approvedCoaches->isEmpty())
            <p class="text-gray-500 text-sm">No approved coaches yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Coach</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Courses</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($approvedCoaches as $coach)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                            {{ substr($coach->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3 text-sm font-medium text-gray-900">{{ $coach->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $coach->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $coach->courses()->count() }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $coach->updated_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

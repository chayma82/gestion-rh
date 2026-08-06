@extends('layouts.layout')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Notifications
            </h1>
            <p class="mt-1 text-gray-500 text-sm">
                Historique complet de vos notifications.
            </p>
        </div>

        @if($notifications->where('lue', false)->count() > 0)
            <form action="{{ route('notifications.marquerToutesLues') }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                    <i class="fa-solid fa-check-double"></i>
                    Tout marquer comme lu
                </button>
            </form>
        @endif

    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden divide-y divide-gray-100">

        @forelse($notifications as $n)

            <form action="{{ route('notifications.marquerLue', $n->id) }}" method="POST">
                @csrf
                @method('PUT')

                <button type="submit"
                    class="w-full text-left flex items-start gap-4 px-6 py-5 hover:bg-gray-50 transition {{ !$n->lue ? 'bg-orange-50/40' : '' }}">

                    <div class="w-10 h-10 rounded-full {{ $n->couleur }} flex items-center justify-center shrink-0">
                        <i class="fa-solid {{ $n->icon }}"></i>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                            {{ $n->titre }}
                            @if(!$n->lue)
                                <span class="w-2 h-2 rounded-full bg-[#E2721B] shrink-0"></span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-500 mt-1">{{ $n->message }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $n->date_reception?->format('d/m/Y à H:i') }}</p>
                    </div>

                </button>
            </form>

        @empty

            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-regular fa-bell text-xl text-gray-300"></i>
                </div>
                <p class="text-sm font-medium text-gray-400">Aucune notification pour le moment.</p>
            </div>

        @endforelse

    </div>

    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif

</div>

@endsection

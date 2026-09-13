<div x-data="{ showPassword: false }" class="w-full max-w-md bg-white/90 backdrop-blur-md rounded-3xl p-8 border border-[#E3EEE8] shadow-2xl space-y-6">
    <!-- Brand Header -->
    <div class="text-center space-y-3">
        <img src="{{ asset('favicon.svg') }}" alt="Raja POS" class="w-14 h-14 mx-auto rounded-2xl shadow-md shrink-0">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">RAJA AKSESORIS</h1>
            <p class="text-xs text-[#718379] font-bold mt-1 uppercase tracking-wider">Retail Management System</p>
        </div>
    </div>

    <!-- Login Form -->
    <form wire:submit.prevent="login" class="space-y-4 text-xs font-semibold">
        <div>
            <label class="block text-[#232E28] font-bold mb-1.5">Username</label>
            <input
                type="text"
                wire:model="username"
                autocomplete="username"
                placeholder="Masukkan username kasir/admin..."
                class="w-full p-3.5 border border-slate-200 rounded-2xl bg-[#F3F6F4] text-xs font-bold text-[#232E28] focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] focus:bg-white transition"
                required
                autofocus
            />
            @error('username')
                <span class="text-rose-600 font-bold text-[11px] mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-[#232E28] font-bold mb-1.5">Password</label>
            <div class="relative w-full flex items-center">
                <input
                    :type="showPassword ? 'text' : 'password'"
                    wire:model="password"
                    autocomplete="current-password"
                    placeholder="Masukkan password..."
                    class="w-full p-3.5 pr-12 border border-slate-200 rounded-2xl bg-[#F3F6F4] text-xs font-bold text-[#232E28] focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] focus:bg-white transition"
                    required
                />
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    tabindex="-1"
                    class="absolute right-3 text-slate-400 hover:text-[#3F7A5D] transition p-1.5 cursor-pointer focus:outline-none flex items-center justify-center rounded-lg z-10"
                    :title="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                >
                    <!-- Eye Open Icon -->
                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Eye Closed Icon -->
                    <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a9.957 9.957 0 013.682-.913c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18" />
                    </svg>
                </button>
            </div>
            @error('password')
                <span class="text-rose-600 font-bold text-[11px] mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model="remember" class="w-4 h-4 text-[#3F7A5D] rounded border-slate-300 focus:ring-[#3F7A5D]">
                <span class="text-[#718379] font-medium">Ingat Saya</span>
            </label>
        </div>

        @if ($errors->has('login_error'))
            <div class="p-3.5 bg-rose-50/90 border border-rose-200 rounded-2xl text-rose-700 text-xs font-bold flex items-center gap-2.5 shadow-2xs transition-all">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="leading-tight">{{ $errors->first('login_error') }}</span>
            </div>
        @endif

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-75 cursor-not-allowed"
            class="w-full py-4 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs uppercase tracking-wider transition active:scale-95 shadow-md mt-2 cursor-pointer flex items-center justify-center gap-2"
        >
            <svg wire:loading class="animate-spin w-4 h-4 text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove>MASUK KE SISTEM POS</span>
            <span wire:loading>MEMPROSES LOGIN...</span>
        </button>
    </form>
</div>

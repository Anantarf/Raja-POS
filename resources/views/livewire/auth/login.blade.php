<div class="w-full max-w-md bg-white rounded-3xl p-8 border border-[#E3EEE8] shadow-2xl space-y-6">
    <!-- Brand Header & Mascot -->
    <div class="text-center space-y-3">
        <div class="w-20 h-20 mx-auto rounded-3xl bg-white border border-[#E3EEE8] p-1 flex items-center justify-center shadow-md overflow-hidden">
            <img src="/images/dashboard_welcome_character_3d.jpg" alt="Raja Aksesoris Mascot" class="w-full h-full object-cover rounded-2xl">
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">RAJA AKSESORIS</h1>
            <p class="text-xs text-[#718379] font-medium mt-1">Sistem Kasir & Ritel Aksesoris Terintegrasi</p>
        </div>
    </div>

    <!-- Login Form -->
    <form wire:submit.prevent="login" class="space-y-4 text-xs font-semibold">
        <div>
            <label class="block text-[#232E28] font-bold mb-1.5">Username *</label>
            <input
                type="text"
                wire:model="username"
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
            <label class="block text-[#232E28] font-bold mb-1.5">Password *</label>
            <input
                type="password"
                wire:model="password"
                placeholder="Masukkan password..."
                class="w-full p-3.5 border border-slate-200 rounded-2xl bg-[#F3F6F4] text-xs font-bold text-[#232E28] focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] focus:bg-white transition"
                required
            />
            @error('password')
                <span class="text-rose-600 font-bold text-[11px] mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model="remember" class="w-4 h-4 text-[#3F7A5D] rounded border-slate-300 focus:ring-[#3F7A5D]">
                <span class="text-[#718379] font-medium">Ingat Saya</span>
            </label>
            <span class="text-indigo-600 bg-indigo-50 border border-indigo-200/60 px-2 py-0.5 rounded-md font-mono text-[10px] font-bold">Raja Aksesoris 2.0</span>
        </div>

        <button
            type="submit"
            class="w-full py-4 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs uppercase tracking-wider transition active:scale-95 shadow-md mt-2"
        >
            MASUK KE SISTEM POS
        </button>
    </form>

    <!-- Quick Info -->
    <div class="pt-2 border-t border-slate-100 text-center text-[11px] text-[#718379] font-medium">
        Default Superadmin Login: <span class="font-mono font-bold text-[#232E28] bg-slate-100 px-1.5 py-0.5 rounded">superadmin</span> / <span class="font-mono font-bold text-[#232E28] bg-slate-100 px-1.5 py-0.5 rounded">password</span>
    </div>
</div>

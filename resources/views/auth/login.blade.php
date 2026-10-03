<x-layouts.guest title="Login">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        {{-- dekorasi halus --}}
        <div class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-brand-500/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -right-24 h-[28rem] w-[28rem] rounded-full bg-brand-400/10 blur-3xl"></div>

        <div class="absolute right-4 top-4 sm:right-6 sm:top-6"><x-theme-toggle /></div>

        <div class="relative w-full max-w-[420px]">
            <div class="mb-8 flex flex-col items-center text-center">
                <x-logo size="lg" />
                <p class="mt-4 max-w-xs text-[13.5px] text-muted">Sistem monitoring brief konten Dthree — dari upload hingga pengerjaan Tim Kreatif.</p>
            </div>

            <div class="card p-6 shadow-pop sm:p-8">
                <h1 class="text-xl font-bold">Masuk ke akun Anda</h1>
                <p class="mt-1 text-[13px] text-muted">Gunakan email dan password yang terdaftar.</p>

                <form method="POST" action="{{ route('login.attempt') }}" class="mt-6 space-y-4" x-data="{ show: false }">
                    @csrf
                    <div>
                        <label for="email" class="label">Email</label>
                        <div class="search-wrap">
                            <x-icon name="mail" />
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                placeholder="nama@dthree.test" @class(['input', 'input-error' => $errors->has('email')])>
                        </div>
                        <x-input-error for="email" />
                    </div>

                    <div>
                        <label for="password" class="label">Password</label>
                        <div class="search-wrap">
                            <x-icon name="lock" />
                            <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" required autocomplete="current-password"
                                placeholder="••••••••" @class(['input pr-11', 'input-error' => $errors->has('password')])>
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-ink" :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                                <x-icon name="eye" class="h-4 w-4" x-show="!show" /><x-icon name="eye-off" class="h-4 w-4" x-show="show" x-cloak />
                            </button>
                        </div>
                        <x-input-error for="password" />
                    </div>

                    <label class="flex cursor-pointer items-center gap-2 text-[13px] text-muted">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-line text-brand-600 focus:ring-brand-500/30"> Ingat saya
                    </label>

                    <button type="submit" class="btn btn-primary h-11 w-full">Login</button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-muted">© {{ date('Y') }} Dthree · BRIEFLY</p>
        </div>
    </div>
</x-layouts.guest>

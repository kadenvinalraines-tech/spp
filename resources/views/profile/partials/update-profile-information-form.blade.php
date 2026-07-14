<section>
    <p class="text-muted small mb-4">Perbarui informasi profil dan alamat email akun Anda.</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="small text-muted">
                        Email Anda belum diverifikasi.
                        <button form="send-verification" class="btn btn-link btn-sm p-0 text-primary">Kirim ulang email verifikasi.</button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="small text-success fw-semibold">Link verifikasi baru telah dikirim ke email Anda.</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="signature" class="form-label">Tanda Tangan Kasir (opsional)</label>
            @if($user->signature)
                <div class="mb-2">
                    <img src="{{ Storage::url($user->signature) }}" alt="Signature" class="border rounded" style="max-height: 80px;">
                </div>
            @endif
            <input type="file" id="signature" name="signature" class="form-control @error('signature') is-invalid @enderror" accept="image/*">
            @error('signature')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Simpan Profil
            </button>
            @if (session('status') === 'profile-updated')
                <span class="text-success small fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> Tersimpan!</span>
            @endif
        </div>
    </form>
</section>

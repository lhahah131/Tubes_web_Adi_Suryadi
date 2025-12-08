<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard Guru</title>
</head>

<body>
    <h1>Dashboard Guru</h1>
    <p>Selamat datang, Guru. Di sini Anda dapat melihat ringkasan dan navigasi cepat.</p>
    <ul>
        <li><a href="{{ route('guru.absensi') }}">Kelola Absensi</a></li>
        <li><a href="{{ route('guru.laporan') }}">Lihat Laporan</a></li>
        <li><a href="{{ route('dashboard') }}">Ke Dashboard Umum</a></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </li>
    </ul>
</body>

</html>
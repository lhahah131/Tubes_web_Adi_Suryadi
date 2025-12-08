<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard Siswa</title>
</head>

<body>
    <h1>Dashboard Siswa</h1>
    <p>Selamat datang, Siswa. Di sini Anda dapat melihat riwayat dan melakukan absensi.</p>
    <ul>
        <li><a href="{{ route('siswa.absensi') }}">Absensi (QR)</a></li>
        <li><a href="{{ route('siswa.riwayat') }}">Riwayat Absensi</a></li>
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
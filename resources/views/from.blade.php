<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>halaman login</title>
</head>

<body>

    {{-- bisa menggunakan ini  --}}
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif


    <form action="/form" method="POST">
        @csrf
        {{-- atau bisa juga menggunakan ini --}}
        <label>username: @error('username') {{ $message }} @enderror  <input type="text" name="username" value="{{ old("username") }}"></label><br>
        <label>password:  @error('password') {{ $message }} @enderror <input type="text" name="password" value="{{ old("password") }}"></label><br>
        <input type="submit" value="login">

    </form>
</body>

</html>

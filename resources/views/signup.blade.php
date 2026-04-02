<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>
    <div style="max-width: 400px; margin: 60px auto; padding: 20px;">
        @if (session('success'))
            <div style="background: #dff2df; color: #1f6b1f; padding: 10px; margin-bottom: 15px; border: 1px solid #b9dfb9;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="background: #ffe2e2; color: #a12626; padding: 10px; margin-bottom: 15px; border: 1px solid #f1b7b7;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background: #ffe2e2; color: #a12626; padding: 10px; margin-bottom: 15px; border: 1px solid #f1b7b7;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <h1 style="margin-bottom: 20px;">Sign Up</h1>

        <form method="POST" action="/signup">
            @csrf

            <div style="margin-bottom: 15px;">
                <label for="name" style="display: block; margin-bottom: 6px;">Name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    placeholder="Enter your name"
                    style="width: 100%; padding: 10px; box-sizing: border-box;"
                >
            </div>

            <div style="margin-bottom: 15px;">
                <label for="email" style="display: block; margin-bottom: 6px;">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    style="width: 100%; padding: 10px; box-sizing: border-box;"
                >
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password" style="display: block; margin-bottom: 6px;">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    style="width: 100%; padding: 10px; box-sizing: border-box;"
                >
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password_confirmation" style="display: block; margin-bottom: 6px;">Confirm Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirm your password"
                    style="width: 100%; padding: 10px; box-sizing: border-box;"
                >
            </div>

            <button type="submit" style="padding: 10px 20px;">
                Sign Up
            </button>
        </form>

        <p style="margin-top: 15px;">
            Already have an account?
            <a href="/login">Login</a>
        </p>
    </div>
</body>
</html>

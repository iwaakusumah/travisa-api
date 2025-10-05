<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>API Documentation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 2rem auto;
            max-width: 900px;
            background: #f4f6f8;
            color: #333;
            line-height: 1.6;
        }

        h1,
        h2,
        h3 {
            color: #2c3e50;
        }

        code {
            background: #eaeaea;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: Consolas, monospace;
            font-size: 0.9rem;
        }

        pre {
            background: #272822;
            color: #f8f8f2;
            padding: 1rem;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            margin-bottom: 1.5rem;
        }

        section {
            margin-bottom: 3rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #ddd;
        }

        .endpoint {
            font-weight: bold;
            font-size: 1.1rem;
            color: #2980b9;
        }

        .method {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            margin-right: 8px;
            font-size: 0.9rem;
        }

        .method.post {
            background: #d35400;
        }

        .method.put {
            background: #2980b9;
        }

        .method.delete {
            background: #c0392b;
        }
    </style>
</head>

<body>
    <h1>API Documentation</h1>
    <p>Selamat datang di dokumentasi API TRAVISA. Berikut ini adalah endpoint yang tersedia beserta contoh request dan
        response.</p>

    <section>
        <h2>Authentication</h2>

        <article>
            <span class="method post">POST</span>
            <span class="endpoint">/api/login</span>
            <p><strong>Deskripsi:</strong> Login menggunakan email dan password, mengembalikan token autentikasi.</p>
            <p><strong>Payload JSON:</strong></p>
            <pre>
{
    "email": "john@example.com",
    "password": "secret"
}
        </pre>
            <p><strong>Response sukses:</strong></p>
            <pre>
{
    "token": "dummy-api-token-1234567890",
    "type": "Bearer"
}
        </pre>
            <p><strong>Response gagal:</strong></p>
            <pre>
Status: 401 Unauthorized
{
    "message": "Invalid credentials"
}
        </pre>
        </article>
    </section>


    <section>
        <h2>User Management</h2>

        <article>
            <span class="method get">GET</span>
            <span class="endpoint">/api/users</span>
            <p><strong>Deskripsi:</strong> Mengambil daftar semua pengguna.</p>
            <pre>
GET /api/users
Response:
[
    {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    {
        "id": 2,
        "name": "Jane Doe",
        "email": "jane@example.com"
    }
]
            </pre>
        </article>

        <article>
            <span class="method get">GET</span>
            <span class="endpoint">/api/users/{id}</span>
            <p><strong>Deskripsi:</strong> Mengambil data pengguna berdasarkan ID.</p>
            <pre>
GET /api/users/1
Response:
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
}
            </pre>
        </article>

        <article>
            <span class="method post">POST</span>
            <span class="endpoint">/api/users</span>
            <p><strong>Deskripsi:</strong> Membuat pengguna baru.</p>
            <p><strong>Payload JSON:</strong></p>
            <pre>
{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "secret"
}
            </pre>
            <p><strong>Response:</strong></p>
            <pre>
Status: 201 Created
{
    "message": "User created successfully",
    "user": {
        "name": "Jane Doe",
        "email": "jane@example.com",
        "password": "secret"
    }
}
            </pre>
        </article>

        <article>
            <span class="method put">PUT</span>
            <span class="endpoint">/api/users/{id}</span>
            <p><strong>Deskripsi:</strong> Memperbarui data pengguna.</p>
            <p><strong>Payload JSON:</strong></p>
            <pre>
{
    "name": "Jane Smith",
    "email": "jane.smith@example.com"
}
            </pre>
            <p><strong>Response:</strong></p>
            <pre>
{
    "message": "User 1 updated successfully",
    "user": {
        "name": "Jane Smith",
        "email": "jane.smith@example.com"
    }
}
            </pre>
        </article>

        <article>
            <span class="method delete">DELETE</span>
            <span class="endpoint">/api/users/{id}</span>
            <p><strong>Deskripsi:</strong> Menghapus pengguna berdasarkan ID.</p>
            <pre>
{
    "message": "User 1 deleted successfully"
}
            </pre>
        </article>
    </section>

    <section>
        <h2>Catatan</h2>
        <ul>
            <li>Semua endpoint API diakses dengan prefix <code>/api</code>.</li>
            <li>Gunakan header <code>Accept: application/json</code> untuk menerima response dalam format JSON.</li>
            <li>Autentikasi menggunakan token (Bearer Token) dari endpoint login belum diimplementasikan di contoh ini,
                tapi direkomendasikan untuk API nyata.</li>
            <li>Untuk endpoint yang butuh proteksi, gunakan middleware autentikasi sesuai kebutuhan.</li>
        </ul>
    </section>

</body>

</html>
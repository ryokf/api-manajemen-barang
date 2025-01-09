
# Dokumentasi API untuk ProductController dan AuthController

Dokumentasi ini mencakup API untuk dua controller utama, yaitu ProductController dan AuthController.

ProductController mengelola operasi terkait produk seperti menampilkan daftar produk, menambah, mengubah, dan menghapus produk.

AuthController mengelola operasi terkait otentikasi pengguna seperti registrasi, login, dan logout.

## 1. AuthController

### POST /register
**Deskripsi:**
Endpoint ini digunakan untuk mendaftarkan pengguna baru dan mengembalikan token akses.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "johndoe@example.com",
  "password": "password123"
}
```

**Response (201):**
```json
{
  "token": "1|qwertyuiop1234567890"
}
```

**Swagger Annotation:**
```php
@OA\Post(
    path="/register",
    tags={"Authentication"},
    summary="Registrasi pengguna baru",
    description="Endpoint ini digunakan untuk mendaftarkan pengguna baru dan mengembalikan token akses.",
    @OA\RequestBody(
        required=true,
        @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
    ),
    @OA\Response(
        response=201,
        description="Registrasi berhasil",
        @OA\JsonContent(ref="#/components/schemas/RegisterResponse")
    ),
    @OA\Response(response=400, description="Registrasi gagal"),
    @OA\Response(response=500, description="Kesalahan server")
)
```

### POST /login
**Deskripsi:**
Endpoint ini digunakan untuk login dan mendapatkan token akses.

**Request Body:**
```json
{
  "email": "johndoe@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "token": "1|qwertyuiop1234567890"
}
```

**Swagger Annotation:**
```php
@OA\Post(
    path="/login",
    tags={"Authentication"},
    summary="Login pengguna",
    description="Endpoint ini digunakan untuk login dan mendapatkan token akses.",
    @OA\RequestBody(
        required=true,
        @OA\JsonContent(ref="#/components/schemas/LoginRequest")
    ),
    @OA\Response(
        response=200,
        description="Login berhasil",
        @OA\JsonContent(ref="#/components/schemas/LoginResponse")
    ),
    @OA\Response(response=400, description="Login gagal"),
    @OA\Response(response=500, description="Kesalahan server")
)
```

### POST /logout
**Deskripsi:**
Endpoint ini digunakan untuk logout dan menghapus token akses saat ini.

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

**Swagger Annotation:**
```php
@OA\Post(
    path="/logout",
    tags={"Authentication"},
    summary="Logout pengguna",
    description="Endpoint ini digunakan untuk logout dan menghapus token akses saat ini.",
    security={
        {"sanctum": {}}
    },
    @OA\Response(
        response=200,
        description="Logout berhasil",
        @OA\JsonContent(ref="#/components/schemas/LogoutResponse")
    ),
    @OA\Response(response=400, description="Logout gagal"),
    @OA\Response(response=500, description="Kesalahan server")
)
```

## 2. ProductController

### GET /products
**Deskripsi:**
Endpoint ini digunakan untuk mendapatkan daftar produk, dengan dukungan fitur pencarian, pemfilteran harga, dan paginasi.

**Query Params:**
- search: Kata kunci untuk mencari nama produk
- price_min: Harga minimum untuk filter
- price_max: Harga maksimum untuk filter
- page: Nomor halaman untuk pagination

**Response (200):**
```json
{
  "data": [...],
  "links": {...},
  "meta": {...}
}
```

**Swagger Annotation:**
```php
@OA\Get(
    path="/products",
    tags={"Product"},
    summary="Daftar produk",
    description="Endpoint ini digunakan untuk mendapatkan daftar produk dengan pencarian dan pemfilteran harga.",
    @OA\Parameter(
        name="search",
        in="query",
        description="Kata kunci pencarian untuk nama produk",
        required=false,
        @OA\Schema(type="string")
    ),
    @OA\Parameter(
        name="price_min",
        in="query",
        description="Harga minimum produk",
        required=false,
        @OA\Schema(type="number", format="float")
    ),
    @OA\Parameter(
        name="price_max",
        in="query",
        description="Harga maksimum produk",
        required=false,
        @OA\Schema(type="number", format="float")
    ),
    @OA\Response(
        response=200,
        description="Daftar produk berhasil ditemukan",
        @OA\JsonContent(ref="#/components/schemas/ProductListResponse")
    ),
    @OA\Response(response=500, description="Kesalahan server")
)
```

### POST /products
**Deskripsi:**
Endpoint ini digunakan untuk menambah produk baru.

**Request Body:**
```json
{
  "name": "Produk Baru",
  "description": "Deskripsi produk baru",
  "quantity": 10,
  "price": 100.50
}
```

**Response (201):**
```json
{
  "id": 1,
  "name": "Produk Baru",
  "description": "Deskripsi produk baru",
  "quantity": 10,
  "price": 100.50
}
```

**Swagger Annotation:**
```php
@OA\Post(
    path="/products",
    tags={"Product"},
    summary="Tambah produk baru",
    description="Endpoint ini digunakan untuk menambah produk baru.",
    @OA\RequestBody(
        required=true,
        @OA\JsonContent(ref="#/components/schemas/ProductRequest")
    ),
    @OA\Response(
        response=201,
        description="Produk berhasil ditambahkan",
        @OA\JsonContent(ref="#/components/schemas/ProductResponse")
    ),
    @OA\Response(response=400, description="Permintaan tidak valid"),
    @OA\Response(response=500, description="Kesalahan server")
)
```

### GET /products/{id}
**Deskripsi:**
Endpoint ini digunakan untuk mendapatkan informasi detail produk berdasarkan ID.

**Response (200):**
```json
{
  "id": 1,
  "name": "Produk Baru",
  "description": "Deskripsi produk baru",
  "quantity": 10,
  "price": 100.50
}
```

**Swagger Annotation:**
```php
@OA\Get(
    path="/products/{id}",
    tags={"Product"},
    summary="Detail produk",
    description="Endpoint ini digunakan untuk mendapatkan detail produk berdasarkan ID.",
    @OA\Parameter(
        name="id",
        in="path",
        description="ID produk",
        required=true,
        @OA\Schema(type="integer")
    ),
    @OA\Response(
        response=200,
        description="Detail produk berhasil ditemukan",
        @OA\JsonContent(ref="#/components/schemas/ProductResponse")
    ),
    @OA\Response(response=404, description="Produk tidak ditemukan"),
    @OA\Response(response=500, description="Kesalahan server")
)
```


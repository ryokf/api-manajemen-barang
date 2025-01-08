<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      title="Manajemen Barang API",
 *      version="1.0.0",
 *      description="Dokumentasi API untuk manajemen barang menggunakan L5 Swagger.",
 *      @OA\Contact(
 *          email="support@example.com"
 *      )
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8000/api",
 *      description="Development Server"
 * )
 *
 * @OA\Tag(
 *     name="Products",
 *     description="API untuk mengelola data produk"
 * )
 *
 * @OA\Schema(
 *     schema="Product",
 *     title="Schema Product",
 *     description="Schema produk",
 *     required={"name", "quantity", "price"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Keyboard Mechanical"),
 *     @OA\Property(property="description", type="string", example="Keyboard dengan lampu RGB."),
 *     @OA\Property(property="quantity", type="integer", example=10),
 *     @OA\Property(property="price", type="number", format="float", example=500000.00),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-07T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-07T12:00:00Z")
 * )
 */

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *      path="/products",
     *      tags={"Products"},
     *      summary="Tampilkan semua produk",
     *      description="Mengambil daftar semua produk dengan fitur filter, pagination, dan pencarian.",
     *      @OA\Parameter(
     *          name="search",
     *          description="Cari produk berdasarkan nama",
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="price_min",
     *          description="Harga minimum",
     *          in="query",
     *          @OA\Schema(type="number")
     *      ),
     *      @OA\Parameter(
     *          name="price_max",
     *          description="Harga maksimum",
     *          in="query",
     *          @OA\Schema(type="number")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Daftar produk berhasil diambil",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *      ),
     *      @OA\Response(response=500, description="Terjadi kesalahan server")
     * )
     */
    public function index(Request $request)
    {
        try {
            // Filtering
            $query = Product::query();

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->has('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }

            if ($request->has('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }

            // Pagination
            $products = $query->paginate(10);

            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/products",
     *      tags={"Products"},
     *      summary="Tambah produk baru",
     *      description="Menyimpan data produk baru ke database.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "quantity", "price"},
     *              @OA\Property(property="name", type="string", example="Keyboard Mechanical"),
     *              @OA\Property(property="description", type="string", example="Keyboard dengan lampu RGB."),
     *              @OA\Property(property="quantity", type="integer", example=10),
     *              @OA\Property(property="price", type="number", format="float", example=500000.00)
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Produk berhasil ditambahkan",
     *          @OA\JsonContent(ref="#/components/schemas/Product")
     *      ),
     *      @OA\Response(response=400, description="Data tidak valid"),
     *      @OA\Response(response=500, description="Terjadi kesalahan server")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:products',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
            ]);

            $product = Product::create($validated);
            return response()->json($product, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/products/{id}",
     *      tags={"Products"},
     *      summary="Tampilkan produk berdasarkan ID",
     *      description="Mengambil detail produk berdasarkan ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID Produk",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Detail produk berhasil diambil",
     *          @OA\JsonContent(ref="#/components/schemas/Product")
     *      ),
     *      @OA\Response(response=404, description="Produk tidak ditemukan"),
     *      @OA\Response(response=500, description="Terjadi kesalahan server")
     * )
     */
    public function show(Product $product)
    {
        try {
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *      path="/products/{id}",
     *      tags={"Products"},
     *      summary="Perbarui data produk",
     *      description="Mengupdate data produk berdasarkan ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID Produk",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Keyboard Wireless"),
     *              @OA\Property(property="description", type="string", example="Keyboard dengan koneksi Bluetooth."),
     *              @OA\Property(property="quantity", type="integer", example=15),
     *              @OA\Property(property="price", type="number", format="float", example=450000.00)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Produk berhasil diperbarui",
     *          @OA\JsonContent(ref="#/components/schemas/Product")
     *      ),
     *      @OA\Response(response=404, description="Produk tidak ditemukan"),
     *      @OA\Response(response=500, description="Terjadi kesalahan server")
     * )
     */
    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'sometimes|integer|min:0',
                'price' => 'sometimes|numeric|min:0',
            ]);

            $product->update($validated);
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/products/{id}",
     *      tags={"Products"},
     *      summary="Hapus produk",
     *      description="Menghapus produk berdasarkan ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID Produk",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Produk berhasil dihapus",
     *          @OA\JsonContent(ref="#/components/schemas/Product")
     *      ),
     *      @OA\Response(response=404, description="Produk tidak ditemukan"),
     *      @OA\Response(response=500, description="Terjadi kesalahan server")
     * )
     */
    public function destroy(Product $product)
    {
        try {
            $deletedProduct = $product;
            $product->delete();
            return response()->json($deletedProduct);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;

class ProductController extends Controller
{
    public function index()
    {
        try {
            // Lấy tất cả sản phẩm với thông tin liên quan
            $products = Product::with(['categories:id,name', 'colors:id,name_color', 'sizes:id,size'])
                ->where('is_active', 1)
                ->whereHas('categories', function ($query) {
                    $query->where('is_active', 1);
                })
                ->get();



            // Lấy tất cả màu sắc và kích thước từ bảng colors và sizes
            $allColors = Color::all(); // Tất cả các màu sắc
            $allSizes = Size::all();   // Tất cả các kích thước

            // Chuyển đổi dữ liệu sản phẩm
            $products = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'avatar_url' => $product->avatar ? asset('storage/ProductAvatars/' . basename($product->avatar)) : null,
                    'categories' => $product->categories,
                    'price' => $product->price,
                    'avatar' => $product->avatar,
                    'quantity' => $product->quantity,
                    'sell_quantity' => $product->sell_quantity,
                    'view' => $product->view,
                    'colors' => $product->colors, // Màu sắc liên quan đến sản phẩm
                    'sizes' => $product->sizes,   // Kích thước liên quan đến sản phẩm
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            });

            $response = [
                'products' => $products,   // Danh sách sản phẩm
                'all_colors' => $allColors, // Tất cả các màu sắc
                'all_sizes' => $allSizes,   // Tất cả các kích thước
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Không thể lấy danh sách sản phẩm. ' . $e->getMessage()], 500);
        }
    }


    public function show(Product $product)
    {
        try {
            if (!$product) {
                return response()->json(['message' => 'Sản phẩm không tồn tại.'], 404);
            }

            // Load các mối quan hệ
            $product->load([
                'categories:id,name',
                'colors:id,name_color',
                'sizes:id,size',
                'galleries',
                'reviews' // Load reviews
            ]);

            // Tính toán rating trung bình
            $averageRating = $product->reviews->avg('rating');

            // Định dạng dữ liệu galleries
            $product->galleries = $product->galleries->map(function ($gallery) {
                return [
                    'id' => $gallery->id,
                    'product_id' => $gallery->product_id,
                    'image_path' => $gallery->image_path,
                    'image_url' => asset('storage/ProductAvatars/' . basename($gallery->image_path)),
                    'created_at' => $gallery->created_at,
                    'updated_at' => $gallery->updated_at,
                ];
            });

            // Định dạng dữ liệu reviews
            $product->reviews = $product->reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user->fullname ?? $review->user->email, // Hiển thị fullname hoặc email
                    'user_avatar' => $review->user->avatar ? asset('storage/UserAvatar/' . basename($review->user->avatar)) : null, // Lấy avatar với đường dẫn đầy đủ
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'image_url' => $review->image_path ? asset('storage/reviews/' . basename($review->image_path)) : null,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at,
                ];
            });

            $response = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'avatar' => $product->avatar ? asset('storage/ProductAvatars/' . basename($product->avatar)) : null, // Cập nhật đường dẫn avatar ở đây
                'categories' => $product->categories,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'sell_quantity' => $product->sell_quantity,
                'view' => $product->view,
                'colors' => $product->colors,
                'sizes' => $product->sizes,
                'galleries' => $product->galleries,
                'reviews' => $product->reviews, // Bao gồm reviews
                'average_rating' => round($averageRating, 2), // Hiển thị rating trung bình làm tròn 2 chữ số
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Không thể lấy thông tin sản phẩm: ' . $e->getMessage()], 500);
        }
    }



    public function getProductsByCategory($categoryId)
    {
        try {
            // Lấy danh mục theo ID và tất cả sản phẩm kèm theo màu sắc và kích thước
            $category = Category::with([
                'products' => function ($query) {
                    $query->where('is_active', 1) // Điều kiện kiểm tra sản phẩm có is_active = 1
                        ->with(['colors:id,name_color', 'sizes:id,size', 'galleries']); // Tiếp tục nạp các quan hệ khác
                }
            ])->findOrFail($categoryId);
            

            // Lấy tất cả màu sắc và kích thước từ bảng colors và sizes
            $allColors = Color::all(); // Lấy tất cả các bản ghi từ bảng colors
            $allSizes = Size::all();   // Lấy tất cả các bản ghi từ bảng sizes

            // Chuyển đổi dữ liệu sản phẩm
            $products = $category->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'avatar_url' => $product->avatar ? asset('storage/ProductAvatars/' . basename($product->avatar)) : null,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    'sell_quantity' => $product->sell_quantity,
                    'view' => $product->view,
                    'colors' => $product->colors, // Màu sắc liên quan đến sản phẩm
                    'sizes' => $product->sizes,   // Kích thước liên quan đến sản phẩm
                    'galleries' => $product->galleries->map(function ($gallery) {
                        return [
                            'id' => $gallery->id,
                            'image_url' => asset('storage/ProductAvatars/' . basename($gallery->image_path)),
                        ];
                    }),
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            });

            $response = [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                ],
                'products' => $products,
                'all_colors' => $allColors, // Tất cả các màu sắc
                'all_sizes' => $allSizes,   // Tất cả các kích thước
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Không thể lấy sản phẩm: ' . $e->getMessage()], 500);
        }
    }
}

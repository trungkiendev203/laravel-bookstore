<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Hiển thị trang giỏ hàng
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        
        // Tính tổng tiền sản phẩm trong giỏ hàng
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Tính phí vận chuyển theo quy định:
        // Đơn từ 300k trở lên -> miễn phí vận chuyển. Dưới 300k -> phí vận chuyển 30k.
        $shippingFee = $subtotal >= 300000 ? 0 : 30000;
        $total = $subtotal + $shippingFee;

        return view('cart.index', compact('cart', 'subtotal', 'shippingFee', 'total'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $bookId = $request->input('book_id');
        $quantity = $request->input('quantity');

        $book = Book::findOrFail($bookId);

        // Kiểm tra xem truyện còn hoạt động không
        if (!$book->is_active) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Truyện này hiện không còn kinh doanh.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Truyện này hiện không còn kinh doanh.');
        }

        // Lấy giỏ hàng hiện tại
        $cart = session()->get('cart', []);

        // Kiểm tra tồn kho trước khi thêm
        $currentInCart = isset($cart[$bookId]) ? $cart[$bookId]['quantity'] : 0;
        $requestedQuantity = $currentInCart + $quantity;

        if ($book->stock < $requestedQuantity) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Không thể thêm số lượng đã chọn. Kho chỉ còn {$book->stock} cuốn và bạn đã có {$currentInCart} cuốn trong giỏ."
                ], 400);
            }
            return redirect()->back()->with('error', "Không thể thêm số lượng đã chọn. Kho chỉ còn {$book->stock} cuốn và bạn đã có {$currentInCart} cuốn trong giỏ.");
        }

        // Nếu sản phẩm đã có trong giỏ hàng, cập nhật số lượng
        if (isset($cart[$bookId])) {
            $cart[$bookId]['quantity'] = $requestedQuantity;
        } else {
            // Thêm mới sản phẩm vào giỏ hàng
            // Lưu giá hoạt động hiện tại (nếu có sale_price thì dùng sale_price)
            $price = $book->active_price;

            $cart[$bookId] = [
                'id' => $book->id,
                'title' => $book->title,
                'slug' => $book->slug,
                'cover_image' => $book->cover_image_url,
                'price' => $price,
                'quantity' => $quantity,
                'stock' => $book->stock,
            ];
        }

        session()->put('cart', $cart);

        // Nếu request là AJAX thì trả JSON (cho frontend JS), ngược lại redirect
        $cartCount = array_sum(array_column($cart, 'quantity'));
        if ($request->ajax() || $request->wantsJson()) {
            $subtotal = 0;
            foreach ($cart as $ci) {
                $subtotal += $ci['price'] * $ci['quantity'];
            }
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm truyện vào giỏ hàng thành công.',
                'cart_count' => $cartCount,
                'subtotal' => number_format($subtotal) . 'đ',
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm truyện vào giỏ hàng thành công.');
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $bookId = $request->input('book_id');
        $quantity = $request->input('quantity');

        $book = Book::findOrFail($bookId);
        $cart = session()->get('cart', []);

        if (isset($cart[$bookId])) {
            // Kiểm tra kho hàng
            if ($book->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Không đủ hàng trong kho. Hiện tại kho chỉ còn {$book->stock} cuốn."
                ], 400);
            }

            $cart[$bookId]['quantity'] = $quantity;
            session()->put('cart', $cart);

            // Tính toán lại tổng tiền để trả về AJAX
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $shippingFee = $subtotal >= 300000 ? 0 : 30000;
            $total = $subtotal + $shippingFee;

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật số lượng thành công.',
                'cart_count' => array_sum(array_column($cart, 'quantity')),
                'item_subtotal' => number_format($cart[$bookId]['price'] * $quantity) . 'đ',
                'subtotal' => number_format($subtotal) . 'đ',
                'shipping_fee' => number_format($shippingFee) . 'đ',
                'total' => number_format($total) . 'đ',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.'], 404);
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove(Request $request)
    {
        $request->validate([
            'book_id' => 'required|integer',
        ]);

        $bookId = $request->input('book_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$bookId])) {
            unset($cart[$bookId]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        }

        return redirect()->back()->with('error', 'Sản phẩm không có trong giỏ hàng.');
    }
}

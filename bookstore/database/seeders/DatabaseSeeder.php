<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tạo tài khoản mẫu
        User::create([
            'name' => 'Admin Shop',
            'email' => 'admin@shop.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0987654321',
            'address' => 'Hà Nội, Việt Nam',
        ]);

        User::create([
            'name' => 'Customer Shop',
            'email' => 'user@shop.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '0123456789',
            'address' => 'Hồ Chí Minh, Việt Nam',
        ]);

        // 2. Tạo 6 thể loại
        $categoriesData = [
            ['name' => 'Manga', 'slug' => 'manga'],
            ['name' => 'Tiểu thuyết', 'slug' => 'tieu-thuyet'],
            ['name' => 'Trinh thám', 'slug' => 'trinh-tham'],
            ['name' => 'Thiếu nhi', 'slug' => 'thieu-nhi'],
            ['name' => 'Ngôn tình', 'slug' => 'ngon-tinh'],
            ['name' => 'Kỹ năng', 'slug' => 'ky-nang'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[] = Category::create($c);
        }

        // 3. Tạo 20 truyện mẫu
        $booksData = [
            // Manga
            [
                'title' => 'One Piece - Tập 100',
                'author' => 'Eiichiro Oda',
                'price' => 30000,
                'sale_price' => 27000,
                'stock' => 50,
                'description' => 'Tập đặc biệt đánh dấu cột mốc 100 tập của hành trình tìm kiếm kho báu One Piece.',
                'category_index' => 0, // Manga
            ],
            [
                'title' => 'Doraemon - Tập 1',
                'author' => 'Fujiko F. Fujio',
                'price' => 25000,
                'sale_price' => null,
                'stock' => 100,
                'description' => 'Chú mèo máy Doraemon đến từ tương lai để giúp đỡ cậu bé Nobita hậu đậu.',
                'category_index' => 0,
            ],
            [
                'title' => 'Conan - Tập 101',
                'author' => 'Gosho Aoyama',
                'price' => 30000,
                'sale_price' => null,
                'stock' => 0, // Hết hàng để test
                'description' => 'Thám tử lừng danh Conan tiếp tục hành trình phá các vụ án hóc búa.',
                'category_index' => 0,
            ],
            [
                'title' => 'Naruto - Tập 72',
                'author' => 'Masashi Kishimoto',
                'price' => 28000,
                'sale_price' => 25000,
                'stock' => 30,
                'description' => 'Tập cuối cùng của bộ truyện huyền thoại về thế giới Ninja Naruto.',
                'category_index' => 0,
            ],
            // Tiểu thuyết
            [
                'title' => 'Nhà Giả Kim',
                'author' => 'Paulo Coelho',
                'price' => 89000,
                'sale_price' => 79000,
                'stock' => 45,
                'description' => 'Cuốn sách bán chạy chỉ sau Kinh Thánh, kể về hành trình theo đuổi ước mơ của cậu bé Santiago.',
                'category_index' => 1, // Tiểu thuyết
            ],
            [
                'title' => 'Số Đỏ',
                'author' => 'Vũ Trọng Phụng',
                'price' => 65000,
                'sale_price' => null,
                'stock' => 20,
                'description' => 'Tác phẩm trào phúng kinh điển của văn học Việt Nam về xã hội tư sản thành thị xưa.',
                'category_index' => 1,
            ],
            [
                'title' => 'Bố Già (The Godfather)',
                'author' => 'Mario Puzo',
                'price' => 125000,
                'sale_price' => 110000,
                'stock' => 15,
                'description' => 'Cuốn tiểu thuyết hình sự kinh điển về thế giới ngầm Mafia Ý tại Mỹ.',
                'category_index' => 1,
            ],
            // Trinh thám
            [
                'title' => 'Sự Im Lặng Của Bầy Cừu',
                'author' => 'Thomas Harris',
                'price' => 115000,
                'sale_price' => null,
                'stock' => 25,
                'description' => 'Cuộc đấu trí nghẹt thở giữa nữ nhân viên FBI Clarice Starling và bác sĩ Hannibal Lecter.',
                'category_index' => 2, // Trinh thám
            ],
            [
                'title' => 'Phía Sau Nghi Can X',
                'author' => 'Keigo Higashino',
                'price' => 109000,
                'sale_price' => 99000,
                'stock' => 40,
                'description' => 'Tác phẩm trinh thám xuất sắc của Keigo về tình yêu mù quáng và sự hy sinh vĩ đại.',
                'category_index' => 2,
            ],
            [
                'title' => 'Án Mạng Trên Chuyến Tàu Tốc Hành Phương Đông',
                'author' => 'Agatha Christie',
                'price' => 95000,
                'sale_price' => null,
                'stock' => 30,
                'description' => 'Vụ án kinh điển của thám tử Hercule Poirot trên chuyến tàu bị kẹt tuyết.',
                'category_index' => 2,
            ],
            // Thiếu nhi
            [
                'title' => 'Dế Mèn Phiêu Lưu Ký',
                'author' => 'Tô Hoài',
                'price' => 45000,
                'sale_price' => 38000,
                'stock' => 60,
                'description' => 'Hành trình phiêu lưu tự lập đầy bài học ý nghĩa của chú Dế Mèn.',
                'category_index' => 3, // Thiếu nhi
            ],
            [
                'title' => 'Kính Vạn Hoa',
                'author' => 'Nguyễn Nhật Ánh',
                'price' => 150000,
                'sale_price' => null,
                'stock' => 10,
                'description' => 'Tuyển tập những câu chuyện học trò vui nhộn và cảm động của bộ ba Quý ròm, Tiểu Long, Hạnh.',
                'category_index' => 3,
            ],
            [
                'title' => 'Hoàng Tử Bé',
                'author' => 'Antoine de Saint-Exupéry',
                'price' => 55000,
                'sale_price' => 49000,
                'stock' => 35,
                'description' => 'Câu chuyện triết lý nhẹ nhàng sâu sắc về tình bạn, tình yêu và cuộc sống.',
                'category_index' => 3,
            ],
            // Ngôn tình
            [
                'title' => 'Từng Có Một Người Yêu Tôi Như Sinh Mệnh',
                'author' => 'Thư Nghi',
                'price' => 99000,
                'sale_price' => null,
                'stock' => 15,
                'description' => 'Câu chuyện tình yêu đầy đau thương và cảm động giữa Tôn Gia Ngộ và Triệu Mai.',
                'category_index' => 4, // Ngôn tình
            ],
            [
                'title' => 'Bên Nhau Trọn Đời',
                'author' => 'Cố Mạn',
                'price' => 88000,
                'sale_price' => 75000,
                'stock' => 20,
                'description' => 'Chuyện tình yêu bền chặt, vượt qua sóng gió thời gian của Hà Dĩ Thâm và Triệu Mặc Sênh.',
                'category_index' => 4,
            ],
            // Kỹ năng
            [
                'title' => 'Đắc Nhân Tâm',
                'author' => 'Dale Carnegie',
                'price' => 86000,
                'sale_price' => 76000,
                'stock' => 80,
                'description' => 'Cuốn sách nghệ thuật ứng xử nổi tiếng nhất mọi thời đại giúp thay đổi cuộc đời hàng triệu người.',
                'category_index' => 5, // Kỹ năng
            ],
            [
                'title' => 'Đời Thay Đổi Khi Chúng Ta Thay Đổi',
                'author' => 'Andrew Matthews',
                'price' => 78000,
                'sale_price' => null,
                'stock' => 50,
                'description' => 'Phương pháp tư duy tích cực để có cuộc sống hạnh phúc và thành công hơn.',
                'category_index' => 5,
            ],
            [
                'title' => 'Tư Duy Nhanh Và Chậm',
                'author' => 'Daniel Kahneman',
                'price' => 189000,
                'sale_price' => 160000,
                'stock' => 15,
                'description' => 'Cuốn sách kiệt tác phân tích hai hệ thống tư duy quyết định hành vi con người.',
                'category_index' => 5,
            ],
            [
                'title' => '7 Thói Quen Để Thành Đạt',
                'author' => 'Stephen R. Covey',
                'price' => 145000,
                'sale_price' => null,
                'stock' => 25,
                'description' => 'Cung cấp những nguyên lý cơ bản để nâng cao hiệu quả làm việc cá nhân và tổ chức.',
                'category_index' => 5,
            ],
            [
                'title' => 'Khéo Ăn Nói Sẽ Có Được Thiên Hạ',
                'author' => 'Trác Nhã',
                'price' => 95000,
                'sale_price' => 85000,
                'stock' => 0, // Hết hàng tiếp để test
                'description' => 'Cẩm nang giao tiếp hữu ích cho mọi đối tượng trong cuộc sống hàng ngày.',
                'category_index' => 5,
            ],
        ];

        foreach ($booksData as $b) {
            $cat = $categories[$b['category_index']];
            Book::create([
                'category_id' => $cat->id,
                'title' => $b['title'],
                'author' => $b['author'],
                'slug' => Str::slug($b['title']) . '-' . rand(100, 999),
                'price' => $b['price'],
                'sale_price' => $b['sale_price'],
                'stock' => $b['stock'],
                'description' => $b['description'],
                'cover_image' => null,
                'is_active' => true,
                'sold_count' => rand(0, 30),
            ]);
        }

        // 4. Tạo vài đơn hàng mẫu để hiển thị admin dashboard
        $user = User::where('role', 'customer')->first();
        $booksForOrders = Book::where('stock', '>', 5)->limit(3)->get();

        if ($user && $booksForOrders->count() >= 2) {
            // Đơn 1: COD đã giao thành công
            $order1 = Order::create([
                'order_code' => 'ORD-' . date('Ymd') . '-1001',
                'user_id' => $user->id,
                'customer_name' => $user->name,
                'customer_phone' => $user->phone,
                'customer_address' => $user->address,
                'note' => 'Giao giờ hành chính',
                'shipping_fee' => 0, // Sẽ miễn phí nếu >= 300k
                'total_amount' => 0, // Tính sau
                'payment_method' => 'cod',
                'payment_status' => 'paid',
                'status' => 'completed',
            ]);

            $subtotal1 = 0;
            foreach ($booksForOrders as $index => $book) {
                if ($index > 1) break;
                $qty = 2;
                $price = $book->active_price;
                $sub = $price * $qty;
                $subtotal1 += $sub;

                OrderItem::create([
                    'order_id' => $order1->id,
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'price' => $price,
                    'quantity' => $qty,
                    'subtotal' => $sub,
                ]);

                // Khấu trừ tồn kho của đơn mẫu
                $book->stock = max(0, $book->stock - $qty);
                $book->sold_count += $qty;
                $book->save();
            }
            $ship1 = $subtotal1 >= 300000 ? 0 : 30000;
            $order1->update([
                'shipping_fee' => $ship1,
                'total_amount' => $subtotal1 + $ship1,
            ]);

            // Đơn 2: VNPay đang xử lý
            $book2 = $booksForOrders[2] ?? $booksForOrders[0];
            $order2 = Order::create([
                'order_code' => 'ORD-' . date('Ymd') . '-1002',
                'user_id' => null, // Khách vãng lai
                'customer_name' => 'Nguyễn Văn A',
                'customer_phone' => '0909090909',
                'customer_address' => '123 Đường Láng, Hà Nội',
                'note' => null,
                'shipping_fee' => 30000,
                'total_amount' => $book2->active_price + 30000,
                'payment_method' => 'vnpay',
                'payment_status' => 'pending',
                'status' => 'processing',
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'book_id' => $book2->id,
                'book_title' => $book2->title,
                'price' => $book2->active_price,
                'quantity' => 1,
                'subtotal' => $book2->active_price,
            ]);

            // Khấu trừ tồn kho của đơn mẫu
            $book2->stock = max(0, $book2->stock - 1);
            $book2->sold_count += 1;
            $book2->save();
        }
    }
}

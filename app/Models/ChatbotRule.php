<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ChatbotRule
 * * Đại diện cho bảng 'chatbot_rules' trong cơ sở dữ liệu,
 * nơi lưu trữ các luật (keyword và answer) cho chatbot dựa trên luật.
 *
 * Các trường quan trọng:
 * - id: Khóa chính (Primary Key)
 * - keyword: Từ khóa để so khớp trong tin nhắn người dùng (ví dụ: "xin chào")
 * - answer: Câu trả lời tĩnh tương ứng (ví dụ: "Chào bạn, chào mừng đến với PeakVN")
 */
class ChatbotRule extends Model
{
    use HasFactory;

    // Tên bảng tương ứng trong cơ sở dữ liệu
    protected $table = 'chatbot_rules';

    // Khóa chính của bảng (mặc định là 'id', nếu khác thì cần khai báo)
    protected $primaryKey = 'id';

    // Cho phép gán hàng loạt (Mass Assignment) cho các trường này
    protected $fillable = [
        'keyword',
        'answer',
        'is_active', // Nếu bạn có trường này để bật/tắt luật
    ];

    // Bảng này không sử dụng các trường timestamps (created_at và updated_at)
    // Dựa trên lược đồ DB của bạn, tôi thấy bảng này không có 2 cột đó.
    public $timestamps = false;
}
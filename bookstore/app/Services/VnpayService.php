<?php

namespace App\Services;

class VnpayService
{
    protected string $tmnCode;
    protected string $hashSecret;
    protected string $vnpUrl;
    protected string $returnUrl;

    public function __construct()
    {
        // Lấy thông tin cấu hình từ config/services.php
        $this->tmnCode = config('services.vnpay.tmn_code', 'xxx');
        $this->hashSecret = config('services.vnpay.hash_secret', 'xxx');
        $this->vnpUrl = config('services.vnpay.vnp_url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $this->returnUrl = config('services.vnpay.return_url', 'http://127.0.0.1:8000/vnpay/return');
    }

    /**
     * Tạo đường dẫn thanh toán VNPay
     * 
     * @param string $orderCode Mã đơn hàng
     * @param float $amount Tổng số tiền thanh toán (VND)
     * @param string $orderInfo Mô tả thanh toán
     * @return string
     */
    public function createPaymentUrl(string $orderCode, float $amount, string $orderInfo = 'Thanh toan don hang'): string
    {
        $vnp_TxnRef = $orderCode;
        // VNPay yêu cầu số tiền nhân với 100 (ví dụ 10,000 VND thành 1,000,000)
        $vnp_Amount = $amount * 100;

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->tmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip() ?? '127.0.0.1',
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => $orderInfo,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $this->returnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        // Sắp xếp các tham số theo bảng chữ cái A-Z trước khi hash
        ksort($inputData);

        $query = "";
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnpSecureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        
        return $this->vnpUrl . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;
    }

    /**
     * Kiểm tra tính hợp lệ của chữ ký VNPay trả về
     * 
     * @param array $vnpData Dữ liệu từ $_GET hoặc $_POST trả về từ VNPay
     * @return bool
     */
    public function validateSignature(array $vnpData): bool
    {
        $vnpSecureHash = $vnpData['vnp_SecureHash'] ?? '';
        
        // Loại bỏ trường secure hash khỏi dữ liệu tính toán chữ ký
        unset($vnpData['vnp_SecureHash']);
        unset($vnpData['vnp_SecureHashType']);

        ksort($vnpData);
        
        $hashData = "";
        $i = 0;
        foreach ($vnpData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        return hash_equals($secureHash, $vnpSecureHash);
    }
}

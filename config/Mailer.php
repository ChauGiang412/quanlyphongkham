
<?php

class Mailer
{
    private array $config;
    private $socket = null;

    private int $timeout = 10;

    public function __construct()
    {
        $configFile = __DIR__ . '/Mail.php';

        if (!file_exists($configFile)) {
            throw new RuntimeException('Không tìm thấy file config/Mail.php.');
        }

        $this->config = require $configFile;

        if (!extension_loaded('openssl')) {
            throw new RuntimeException('PHP chưa bật extension OpenSSL.');
        }

        foreach (['host', 'port', 'username', 'password', 'from_email', 'from_name'] as $key) {
            if (empty($this->config[$key])) {
                throw new RuntimeException("Thiếu cấu hình Gmail SMTP: {$key}");
            }
        }
    }

    /**
     * Đọc phản hồi SMTP và kiểm tra timeout.
     */
    private function readResponse(): string
    {
        if (!is_resource($this->socket)) {
            throw new RuntimeException('Kết nối SMTP không còn tồn tại.');
        }

        $response = '';

        while (true) {
            $line = fgets($this->socket, 515);

            if ($line === false) {
                $meta = stream_get_meta_data($this->socket);

                if (!empty($meta['timed_out'])) {
                    throw new RuntimeException('Gmail SMTP phản hồi quá thời gian chờ.');
                }

                throw new RuntimeException('Không đọc được phản hồi từ Gmail SMTP.');
            }

            $response .= $line;

            // Dòng cuối của phản hồi SMTP có dấu cách ở vị trí thứ 4.
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        return $response;
    }

    /**
     * Gửi lệnh SMTP và kiểm tra mã phản hồi.
     */
    private function command(string $command, array $expected): string
    {
        $written = fwrite($this->socket, $command . "\r\n");

        if ($written === false) {
            throw new RuntimeException('Không gửi được lệnh tới Gmail SMTP.');
        }

        $response = $this->readResponse();
        $code = (int) substr($response, 0, 3);

        if (!in_array($code, $expected, true)) {
            throw new RuntimeException('Gmail SMTP từ chối lệnh: ' . trim($response));
        }

        return $response;
    }

    /**
     * Gửi email qua Gmail SMTP.
     */
    public function send(string $to, string $subject, string $html): bool
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Địa chỉ email người nhận không hợp lệ.');
        }

        $host = $this->config['host'];
        $port = (int) $this->config['port'];

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'peer_name' => $host
            ]
        ]);

        $this->socket = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$this->socket) {
            throw new RuntimeException(
                "Không kết nối được Gmail SMTP: {$errstr} ({$errno})"
            );
        }

        stream_set_timeout($this->socket, $this->timeout);

        try {
            // 1. Đọc lời chào ban đầu của Gmail.
            $greeting = $this->readResponse();

            if ((int) substr($greeting, 0, 3) !== 220) {
                throw new RuntimeException(
                    'Gmail không chấp nhận kết nối: ' . trim($greeting)
                );
            }

            // 2. Khởi tạo SMTP.
            $this->command('EHLO localhost', [250]);

            // 3. Bật mã hóa TLS.
            $this->command('STARTTLS', [220]);

            $crypto = @stream_socket_enable_crypto(
                $this->socket,
                true,
                STREAM_CRYPTO_METHOD_TLS_CLIENT
            );

            if ($crypto !== true) {
                throw new RuntimeException(
                    'Không thể thiết lập kết nối TLS với Gmail.'
                );
            }

            // 4. Gửi lại EHLO sau khi bật TLS.
            $this->command('EHLO localhost', [250]);

            // 5. Đăng nhập Gmail.
            $this->command('AUTH LOGIN', [334]);

            $this->command(
                base64_encode($this->config['username']),
                [334]
            );

            $this->command(
                base64_encode($this->config['password']),
                [235]
            );

            // 6. Khai báo người gửi và người nhận.
            $this->command(
                'MAIL FROM:<' . $this->config['from_email'] . '>',
                [250]
            );

            $this->command(
                'RCPT TO:<' . $to . '>',
                [250, 251]
            );

            // 7. Bắt đầu gửi nội dung email.
            $this->command('DATA', [354]);

            $fromName = '=?UTF-8?B?' .
                base64_encode($this->config['from_name']) .
                '?=';

            $encodedSubject = '=?UTF-8?B?' .
                base64_encode($subject) .
                '?=';

            $message = '';
            $message .= "From: {$fromName} <{$this->config['from_email']}>\r\n";
            $message .= "To: <{$to}>\r\n";
            $message .= "Subject: {$encodedSubject}\r\n";
            $message .= "MIME-Version: 1.0\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: 8bit\r\n";
            $message .= "\r\n";
            $message .= $html;
            $message .= "\r\n.\r\n";

            $written = fwrite($this->socket, $message);

            if ($written === false) {
                throw new RuntimeException('Không gửi được nội dung email.');
            }

            $response = $this->readResponse();

            if ((int) substr($response, 0, 3) !== 250) {
                throw new RuntimeException(
                    'Gmail không chấp nhận email: ' . trim($response)
                );
            }

            $this->command('QUIT', [221]);

            return true;

        } finally {
            if (is_resource($this->socket)) {
                fclose($this->socket);
            }

            $this->socket = null;
        }
    }
}

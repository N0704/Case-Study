# Quick Start Guide

## Yêu cầu
*   **XAMPP** (PHP, MySQL)
*   **Node.js** (để chạy Tailwind CSS)

## Cài đặt

1.  **Clone dự án** vào thư mục `htdocs` của XAMPP.
2.  **Cài đặt thư viện Node.js**:
    ```bash
    npm install
    ```
3.  **Cài đặt Database**:
    *   Mở phpMyAdmin: `http://localhost/phpmyadmin`
    *   Tạo database tên: `GTPT`
    *   Import file: `database.sql`

## Các lệnh chạy

### 1. Chạy Tailwind CSS (Development)
Mở terminal tại thư mục dự án và chạy lệnh sau để tự động compile CSS khi sửa code:
```bash
npx @tailwindcss/cli -i ./assets/css/input.css -o ./assets/css/output.css --watch
```

### 2. Chạy Web
*   Bật **Apache** và **MySQL** trong XAMPP.
*   Truy cập: `http://localhost/case_study` (hoặc tên thư mục của bạn).

## Tài khoản Demo
*   **Admin**: `admin` / `123456`
*   **User**: `user2` / `123456`

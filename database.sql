CREATE DATABASE GTPT;
USE GTPT;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100),
    email VARCHAR(100),
    avatar VARCHAR(255),
    role INT DEFAULT 0, -- 0:user | 1:admin
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- USERS
INSERT INTO users (id, username, password, fullname) VALUES
(1, 'admin', '123456', 'Admin'),
(2, 'user2', '123456', 'Người đăng tin');

CREATE TABLE districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
);

-- DISTRICTS
INSERT INTO districts (id, name, latitude, longitude) VALUES
(1, 'Gần Đại Học Vinh', 18.67500000, 105.68800000),
(2, 'Trường Vinh', 18.68000000, 105.68500000),
(3, 'Thành Vinh', 18.67000000, 105.68200000),
(4, 'Vinh Hưng', 18.67800000, 105.69500000),
(5, 'Vinh Phú', 18.67200000, 105.68900000),
(6, 'Vinh Lộc', 18.66500000, 105.68300000),
(7, 'Cửa Lò');

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

-- CATEGORIES
INSERT INTO categories (id, name) VALUES
(1, 'Phòng trọ'),
(2, 'Căn hộ mini');

CREATE TABLE motels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    price INT,
    area INT,
    address VARCHAR(255),

    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),

    utilities VARCHAR(255),
    phone VARCHAR(20),
    image VARCHAR(255),

    count_view INT DEFAULT 0,
    approve INT DEFAULT 0, -- 0:chờ | 1:hiển thị | 2:ẩn

    user_id INT,
    category_id INT,
    district_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (district_id) REFERENCES districts(id)
);

CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO motels
(id, user_id, category_id, title, description, price, area, address,
 latitude, longitude, image, utilities, phone,
 approve, count_view, district_id, created_at)
VALUES
(1, 2, 1,
 'Cho thuê phòng trọ khép kín 18m² (1tr5/tháng)',
 'Phòng khép kín sạch sẽ, có cửa sổ hướng Đông đón nắng sáng, phù hợp 1-2 người. Gần Đại học Vinh.',
 1500000, 18,
 'Ngõ 55 Nguyễn Viết Xuân, Phường Bến Thủy, TP. Vinh',
 18.67500000, 105.68800000,
 'https://cdn.chotot.com/RMPsVeJq_Fn_f4dXmhGdQwk0l_8ogo0_8fLg8DecHzc/preset:listing/plain/402f7eae72a0a92a7b183ddd5346449f-2956556915138796200.jpg',
 'Wifi, gửi xe miễn phí',
 '0987654321',
 1, 121, 1, '2025-12-01 12:41:22'),

(2, 2, 1,
 'Phòng trọ đầy đủ nội thất, gần Quảng trường',
 'Phòng trọ mới xây, đầy đủ nội thất, gần Quảng trường Hồ Chí Minh.',
 3000000, 25,
 'Đường An Dương Vương, Phường Trường Thi, TP. Vinh',
 18.68000000, 105.68500000,
 'https://lighthouse.chotot.com/_next/image?url=https%3A%2F%2Fcdn.chotot.com%2FzQpw_-eXFQ2BPghCSzRvgUJ-rnnrIs0bTs479SwZ_sU%2Fpreset%3Alisting%2Fplain%2Fcc654ed3a3f34198856ed761aa5f986b-2957112062612107429.jpg',
 'Điều hòa, nóng lạnh',
 '0978123456',
 1, 85, 2, '2025-12-01 12:41:22'),

(3, 2, 1,
 'Phòng trọ mới xây, có gác, an ninh tốt',
 'Phòng mới xây, có gác lửng, an ninh tốt, gần chợ Đại học.',
 2200000, 18,
 'Đường Phượng Hoàng, Phường Trung Đô, TP. Vinh',
 18.67000000, 105.68200000,
 'https://lighthouse.chotot.com/_next/image?url=https%3A%2F%2Fcdn.chotot.com%2FyuL-ER2nByUiN0H_iHvVozTMvhDmPOfdzGVOajtgjrw%2Fpreset%3Alisting%2Fplain%2Ff31e02c7740cada9cc118ee98b19c8b9-2957287660017018865.jpg',
 'Camera, wifi',
 '0966666666',
 1, 45, 3, '2025-12-01 12:41:22'),

(4, 2, 1,
 'Phòng trọ giá rẻ, gần ĐH Kỹ Thuật Vinh',
 'Phòng trọ giá rẻ cho sinh viên, gần ĐH Kỹ Thuật Vinh.',
 1200000, 16,
 'Đường Nguyễn Phong Sắc, Phường Hưng Dũng, TP. Vinh',
 18.67800000, 105.69500000,
 'https://lighthouse.chotot.com/_next/image?url=https%3A%2F%2Fcdn.chotot.com%2FU_l1XrMuISabJtCwLtUPvtUJE_ISi_PnlahHzdmgY4M%2Fpreset%3Alisting%2Fplain%2F63c56849da9b91ca77960b4b848b33f9-2956851695223068854.jpg',
 'Wifi',
 '0967777777',
 1, 211, 4, '2025-12-01 12:41:22');


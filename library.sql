-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 23, 2026 lúc 02:14 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `library`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_phieu_muon`
--

CREATE TABLE `chi_tiet_phieu_muon` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phieu_muon_id` bigint(20) UNSIGNED NOT NULL,
  `sach_id` bigint(20) UNSIGNED NOT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 1,
  `tinh_trang_khi_muon` varchar(255) DEFAULT NULL,
  `tinh_trang_khi_tra` varchar(255) DEFAULT NULL,
  `tien_phat` decimal(10,2) DEFAULT 0.00,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_phieu_muon`
--

INSERT INTO `chi_tiet_phieu_muon` (`id`, `phieu_muon_id`, `sach_id`, `so_luong`, `tinh_trang_khi_muon`, `tinh_trang_khi_tra`, `tien_phat`, `ghi_chu`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Sách còn tốt', 'Sách còn tốt', 0.00, NULL, '2026-10-16 07:27:41', '2026-12-24 07:27:41'),
(2, 2, 5, 1, 'Sách còn tốt', 'Sách còn tốt', 100000.00, 'trả muộn, hỏng bìa', '2026-10-16 07:27:41', '2026-12-24 03:01:15'),
(3, 3, 8, 1, 'Sách còn tốt', NULL, 0.00, NULL, '2026-10-16 07:27:41', '2026-12-30 07:27:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_danh_muc` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id`, `ten_danh_muc`, `mo_ta`, `created_at`, `updated_at`) VALUES
(1, 'Công nghệ thông tin ', 'Tài liệu về khoa học máy tính ', '2025-03-14 07:27:41', '2025-03-14 07:17:26'),
(2, 'Kinh tế & quản trị kinh doanh ', 'Tài liệu về kinh tế vi mô, vĩ mô, quản trị doanh nghiệp', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(3, 'Khoa học cơ bản ', 'Toán cao cấp, Vật lý đại cương, Hóa học, ...', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(4, 'Khoa học sức khỏe \r\n', 'Tài liệu Y đa khoa, Dược học, Điều dưỡng,...', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(5, 'Ngoại ngữ', 'Tài liệu học tiếng Anh (VSTEP, TOEIC, IELTS), tiếng Nhật, tiếng Hàn, ...', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(6, 'Kỹ thuật ô tô & cơ điện tử ', 'Tài liệu về động cơ đốt trong, hệ thống điều khiển cơ điện tử.', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(7, 'Triết học & Lý luận chính trị ', 'Tài liệu Triết học Mác - Lênin, Tư tưởng Hồ Chí Minh.', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(8, 'Kỹ năng mềm ', 'Kỹ năng giao tiếp, kỹ năng thuyết trình, phương pháp học tập.', '2025-03-14 07:27:41', '2025-03-14 07:27:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doc_gia`
--

CREATE TABLE `doc_gia` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `dia_chi` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `ngay_dang_ky` date NOT NULL,
  `ngay_het_han` date DEFAULT NULL,
  `trang_thai` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `doc_gia`
--

INSERT INTO `doc_gia` (`id`, `ho_ten`, `ngay_sinh`, `dia_chi`, `email`, `so_dien_thoai`, `ngay_dang_ky`, `ngay_het_han`, `trang_thai`, `created_at`, `updated_at`) VALUES
(1, 'Lê Hoàng Phúc', '1990-01-15', 'Lào Cai ', 'Phucdz@gmail.com', '0333333333', '2026-10-16', '2026-01-01', 1, '2025-03-14 07:27:41', '2025-03-14 07:07:06'),
(2, 'Nguyễn Văn Thành Hưng ', '1995-05-20', 'Hà Nội ', 'Hung123@gmail.com', '0123456789', '2026-10-16', '2026-01-05', 1, '2025-03-14 07:27:41', '2025-03-14 07:07:48'),
(3, 'Nguyễn Mạnh Đạt ', '1988-12-10', 'Hà Nội ', 'manhdat@gmail.com', '0999999999', '2026-10-16', '2026-01-10', 1, '2025-03-14 07:27:41', '2025-03-14 07:08:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nha_xuat_ban`
--

CREATE TABLE `nha_xuat_ban` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_nxb` varchar(255) NOT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nha_xuat_ban`
--

INSERT INTO `nha_xuat_ban` (`id`, `ten_nxb`, `dia_chi`, `email`, `so_dien_thoai`, `created_at`, `updated_at`) VALUES
(1, 'NXB Khoa học và Kỹ thuật', 'Hà Nội', 'contact@nxbkhkt.vn', '0686868686', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(2, 'NXB Đại học Quốc gia Hà Nội', 'Hà Nội', 'nxb@vnu.edu.vn', '02437547706', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(3, 'NXB Giáo dục', 'Hà Nội', 'contact@nxbgd.vn', '0243822108', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(4, 'NXB Bách Khoa Hà Nội', 'Hà Nội', 'nxbbk@hust.edu.vn', '0243822732', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(5, 'NXB Kinh tế Quốc dân', 'Hà Nội ', 'nxb@neu.edu.vn', '02436280280', '2025-03-14 07:27:41', '2025-03-14 07:27:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 3, 'postman-test', '9115f38ac4ae0a59bf9565bdcb105ebd296ca581a29578c2dd62cca2fc96e0c3', '[\"*\"]', NULL, NULL, '2026-09-25 11:28:22', '2026-09-25 11:28:22'),
(2, 'App\\Models\\User', 3, 'postman-test', '1e45e54f40a668f63cbc672bb605ce63ca73cbb35c3baae1d2d501a1db25c5aa', '[\"*\"]', NULL, NULL, '2026-09-25 11:34:40', '2026-09-25 11:34:40'),
(3, 'App\\Models\\User', 1, 'postman-test', '65be76dd1dc28281a840ef72012e5a6e8968b55bd17f90da6ea007f4f1f68957', '[\"*\"]', NULL, NULL, '2026-09-25 11:44:04', '2026-09-25 11:44:04'),
(4, 'App\\Models\\User', 2, 'postman-test', 'f6848bc924ff28eeadfbf799eefb78c05d00d1e3846e82e924861ac96b4f07f9', '[\"*\"]', NULL, NULL, '2026-09-25 11:44:57', '2026-09-25 11:44:57'),
(5, 'App\\Models\\User', 1, 'postman-test', 'd103a4fcb2f1c85df35dc1ae9d90e020b323d5a30d3c1ef74ba6ed1a7500a482', '[\"*\"]', NULL, NULL, '2026-09-29 16:08:29', '2026-09-29 16:08:29'),
(6, 'App\\Models\\User', 2, 'postman-test', 'e3246d5f5b03882f150eed5eca617826bd3df8ac90a619d57f665b35cb1257a5', '[\"*\"]', NULL, NULL, '2026-09-29 16:15:47', '2026-09-29 16:15:47'),
(7, 'App\\Models\\User', 3, 'postman-test', 'd22f1ffad35a925787abfe2a421495297568a9487949e2c474a98c5b9edbf12f', '[\"*\"]', NULL, NULL, '2026-09-29 16:45:39', '2026-09-29 16:45:39'),
(8, 'App\\Models\\User', 3, 'postman-test', '2bdb34a380f39ac8a35538469085aae7d62d9a1830ba446536eeffef70267da7', '[\"*\"]', NULL, NULL, '2026-09-29 16:46:46', '2026-09-29 16:46:46'),
(9, 'App\\Models\\User', 3, 'postman-test', 'a3076ae1ff09713635905bfbbe65a5eea6981cec07076376fcac53ad99dfa9e9', '[\"*\"]', NULL, NULL, '2026-09-29 16:46:57', '2026-09-29 16:46:57'),
(10, 'App\\Models\\User', 3, 'postman-test', '0a881b2aaf19e461b0da82897f7bff122f6f4ed905a690625d07d8c0b2d5bfd3', '[\"*\"]', NULL, NULL, '2026-09-29 16:47:00', '2026-09-29 16:47:00'),
(11, 'App\\Models\\User', 3, 'postman-test', '5039e53d1a46cdc070f0e95eba2378aba198fbb54ce05270dcd6ac602b3b2509', '[\"*\"]', '2026-09-29 16:51:40', NULL, '2026-09-29 16:47:18', '2026-09-29 16:51:40'),
(12, 'App\\Models\\User', 1, 'postman-test', '08e6aa5809ff5117f53ea1b7a45cd947ec22d78bc9439821d934c7ca16c52a23', '[\"*\"]', '2026-09-29 16:56:20', NULL, '2026-09-29 16:54:51', '2026-09-29 16:56:20'),
(13, 'App\\Models\\User', 3, 'postman-test', '5090cc13dbeeff6b6e2187246dc2bc751c10909221576445326c742aeb51a8e9', '[\"*\"]', '2026-09-29 21:24:29', NULL, '2026-09-29 21:22:38', '2026-09-29 21:24:29'),
(14, 'App\\Models\\User', 1, 'postman-test', '21d3caaa8ae8b3cec8887028b05769e64e2c92be1bce1ab376da3a91e571e053', '[\"*\"]', '2026-09-29 21:27:45', NULL, '2026-09-29 21:25:24', '2026-09-29 21:27:45'),
(15, 'App\\Models\\User', 1, 'postman-test', '658bfcb87f5acd8f539b24eb38c1a9bee8fb83a8b1efaf05cc3c1f4e4fc233cc', '[\"*\"]', NULL, NULL, '2026-10-06 17:42:03', '2026-10-06 17:42:03'),
(16, 'App\\Models\\User', 1, 'postman-test', '0247036f1a1010d721d78dddc8bf61aa266821265154935d5dd8e96c0c966667', '[\"*\"]', '2026-10-06 18:17:33', NULL, '2026-10-06 18:09:52', '2026-10-06 18:17:33'),
(17, 'App\\Models\\User', 1, 'postman-test', '0c459473e0778b1e899f1568ce47c4139021740946cf115e6d030c5017ce5d81', '[\"*\"]', '2026-10-06 21:02:16', NULL, '2026-10-06 20:25:45', '2026-10-06 21:02:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieu_muon`
--

CREATE TABLE `phieu_muon` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `doc_gia_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `ngay_muon` date NOT NULL,
  `ngay_hen_tra` date NOT NULL,
  `ngay_tra` date DEFAULT NULL,
  `trang_thai` enum('đang mượn','đã trả','quá hạn') NOT NULL DEFAULT 'đang mượn',
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phieu_muon`
--

INSERT INTO `phieu_muon` (`id`, `doc_gia_id`, `user_id`, `ngay_muon`, `ngay_hen_tra`, `ngay_tra`, `trang_thai`, `ghi_chu`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2025-01-10', '2025-02-24', '2025-02-24', 'đã trả', 'Sách trả đúng hạn', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(2, 2, 2, '2025-01-15', '2025-03-29', '2026-01-06', 'đã trả', NULL, '2025-03-14 07:27:41', '2026-01-06 03:01:15'),
(3, 3, 2, '2025-01-20', '2026-02-03', NULL, 'đang mượn', NULL, '2025-03-14 07:27:41', '2025-03-14 07:11:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_vai_tro` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `ten_vai_tro`, `mo_ta`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Quản trị viên hệ thống', '2025-03-14 07:27:40', '2025-03-14 07:27:40'),
(2, 'librarian', 'Thủ thư', '2025-03-14 07:27:40', '2025-03-14 07:27:40'),
(3, 'user', 'Người dùng thông thường', '2025-03-14 07:27:40', '2025-03-14 07:27:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sach`
--

CREATE TABLE `sach` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `danh_muc_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tac_gia_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nha_xuat_ban_id` bigint(20) UNSIGNED DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `so_trang` int(11) DEFAULT NULL,
  `nam_xuat_ban` int(11) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 0,
  `so_luong_con_lai` int(11) NOT NULL DEFAULT 0,
  `gia` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sach`
--

INSERT INTO `sach` (`id`, `tieu_de`, `danh_muc_id`, `tac_gia_id`, `nha_xuat_ban_id`, `isbn`, `so_trang`, `nam_xuat_ban`, `mo_ta`, `hinh_anh`, `so_luong`, `so_luong_con_lai`, `gia`, `created_at`, `updated_at`) VALUES
(1, 'Phát triển Web ứng dụng với Laravel và Vue.js ', 1, 1, 1, '9786041018167', 378, 2010, 'Hướng dẫn toàn tập xây dựng hệ thống quản lý thư viện số, học liệu mở bằng PHP Laravel, MySQL và XAMPP.', 'sach/1767691634_unnamed.jpg', 10, 10, 85000.00, '2025-03-14 07:27:41', '2026-01-06 02:27:14'),
(2, 'Giáo trình toán cao cấp - đại số tuyến tính ', 3, 2, 2, '9786040215840', 150, 1941, 'Giáo trình cung cấp kiến thức nền tảng về ma trận, hệ phương trình tuyến tính và không gian vector dùng chung cho khối ngành kỹ thuật.', 'sach/1767691883_ebook-de-men-phieu-luu-ky-prc-pdf-epub.jpg', 100, 32, 65000.00, '2025-03-14 07:27:41', '2026-01-06 02:31:23'),
(3, 'Nghệ thuật kiểm thử phần mềm tự động ', 1, 3, 4, '9786045876541', 120, 1943, 'Tài liệu chi tiết về API Testing với Postman, Thunder Client và Unit Test với PHPUnit, JUnit.', 'sach/1767691892_z4529778288710-9a538b8bcac451561.jpg', 100, 53, 50000.00, '2025-03-14 07:27:41', '2026-01-06 02:31:32'),
(4, 'Nguyên lý kinh tế vĩ mô ', 2, 4, 5, '9786041092083', 224, 1988, 'Giáo trình phân tích các yếu tố tổng lượng của nền kinh tế như lạm phát, thất nghiệp và tăng trưởng GDP.', 'sach/1767691902_ebook-nha-gia-kim-prc-pdf-epub.jpg', 8, 8, 78000.00, '2025-03-14 07:27:41', '2026-01-06 02:31:42'),
(5, 'Quản trị khởi nghiệp và đổi mới sáng tạo ', 2, 5, 5, '9786045876732', 420, 1987, 'Tài liệu hướng dẫn xây dựng mô hình kinh doanh, gọi vốn và phát triển sản phẩm cho Startup.', 'sach/1767691963_ebook-rung-na-uy-prc-pdf-epub.jpg', 100, 7, 120000.00, '2025-03-14 07:27:41', '2026-01-06 03:01:15'),
(6, 'Đắc nhân tâm ', 8, 6, 3, '9786041052000', 320, 1997, 'Cuốn sách kinh điển về nghệ thuật thu phục lòng người, kỹ năng giao tiếp và thấu hiểu tâm lý trong cuộc sống và công việc.', 'sach/1767692109_39088362.jpg', 9, 9, 150000.00, '2025-03-14 07:27:41', '2026-01-06 02:35:09'),
(7, 'Cấu trúc dữ liệu và giải thuật với C++ ', 1, 7, 4, '9786045876541', 180, 2005, 'Giáo trình chuẩn về Vector, Set và tối ưu thuật toán giải quyết bài toán thi đấu lập trình.', 'sach/1767692118_1704452281553-Bia-canh-dong-bat-tan_8de38.jpg', 100, 50, 75000.00, '2025-03-14 07:27:41', '2026-01-06 02:35:18'),
(8, 'Giáo trình triết học Mác - Lênin ', 7, 8, 2, '9786041018167', 200, 1994, 'Giáo trình dùng cho bậc đại học hệ không chuyên lý luận chính trị, cung cấp kiến thức nền tảng về chủ nghĩa duy vật biện chứng và duy vật lịch sử.', 'sach/1767692198_z7403281842378_6abf20d1e6805b51011af49f82db77d2.jpg', 12, 12, 25000.00, '2025-03-14 07:27:41', '2026-01-06 02:36:38'),
(9, 'Giải thích ngữ pháp tiếng anh ', 5, 9, 5, '9786045876541', 320, 1936, 'Cuốn sách ôn tập ngữ pháp tiếng Anh toàn diện, phù hợp cho học sinh, sinh viên và người luyện thi TOEIC, IELTS, VSTEP.', 'sach/1767692213_ebook-dac-nhan-tam-prc-pdf-epub.jpg', 15, 15, 90000.00, '2025-03-14 07:27:41', '2026-01-06 02:36:53'),
(10, 'Làm chủ HTML, CSS, JavaScript ', 1, 1, 1, '9786041018167', 300, 1990, 'Sách tham khảo kỹ năng thiết kế Frontend cơ bản cho sinh viên thực hiện đồ án môn học.', 'sach/1767692222_38539172566_a1baf89e4a_o.jpg', 100, 70, 82000.00, '2025-03-14 07:27:41', '2026-01-06 02:37:02'),
(11, 'Digital Marketing - Từ chiến lược đến thực thi ', 2, 5, 5, '9786045876541', 505, 2002, 'Cẩm nang tối ưu hóa công cụ tìm kiếm (SEO), quảng cáo mạng xã hội và phân tích dữ liệu khách hàng.', 'sach/1767692293_kafka-ben-bo-bien-bia.jpg', 100, 85, 135000.00, '2025-03-14 07:27:41', '2026-01-06 02:38:13'),
(12, 'Quản trị cơ sở dữ liệu và bảo mật với MySQL ', 1, 1, 1, '9786041018167', 208, 2008, 'Phân tích thiết kế Database, đánh chỉ mục toàn văn (Full-Text Index) và giám sát dữ liệu mạng.', 'sach/1767692335_ebook-Cho-toi-mot-ve-di-tuoi-tho-prc-pdf-epub.jpg', 100, 40, 75000.00, '2025-03-14 07:27:41', '2026-01-06 02:38:55'),
(13, 'Cẩm nang thực hành Git và quản lý mã nguồn ', 1, 3, 4, '9786045876541', 100, 1941, 'Kỹ năng quản lý version control, phân nhánh và làm việc nhóm hiệu quả trong dự án công nghệ.', 'sach/1767692378_vn-11134207-7ra0g-m6nr4tgwi1k706.jpg', 100, 60, 45000.00, '2025-03-14 07:27:41', '2026-01-06 02:39:38'),
(14, 'Tư duy nhanh và chậm ', 8, 6, 3, '9786041052000', 352, 1998, 'Sách phân tích hai hệ thống tư duy của con người, giúp rèn luyện kỹ năng ra quyết định, giải quyết vấn đề và tránh các lỗi nhận thức.', 'sach/1767692410_34460806195_0721b9178a_z.jpg', 100, 75, 160000.00, '2025-03-14 07:27:41', '2026-01-06 02:40:10'),
(15, 'Giáo trình tư tưởng Hồ Chí Minh ', 7, 8, 2, '9786041018167', 192, 2020, 'Giáo trình nghiên cứu hệ thống quan điểm toàn diện và sâu sắc về những vấn đề cơ bản của cách mạng Việt Nam.', 'sach/1767692474_image1920-2cd66406-611a-4dc1-a19.jpg', 10, 10, 25000.00, '2025-03-14 07:27:41', '2026-01-06 02:41:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tac_gia`
--

CREATE TABLE `tac_gia` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_tac_gia` varchar(255) NOT NULL,
  `tieu_su` text DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tac_gia`
--

INSERT INTO `tac_gia` (`id`, `ten_tac_gia`, `tieu_su`, `ngay_sinh`, `created_at`, `updated_at`) VALUES
(1, 'Robert C. Martin', 'Kỹ sư phần mềm huyền thoại, còn được gọi là Uncle Bob, tác giả của Clean Code.', '1955-05-07', '2025-03-14 07:27:41', '2025-03-14 07:10:16'),
(2, 'Nguyễn Đình Trí', 'Giáo sư, Tiến sĩ Toán học, Chủ biên cuốn Toán cao cấp rất quen thuộc với sinh viên kỹ thuật.', '1920-09-27', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(3, 'Phạm Văn Ất', 'Tiến sĩ, tác giả nhiều giáo trình CNTT kinh điển tại Việt Nam.', '1917-10-29', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(4, 'N. Gregory Mankiw', 'Giáo sư kinh tế học tại Đại học Harvard, tác giả giáo trình Nguyên lý Kinh tế.', '1947-08-24', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(5, 'Eric Ries', 'Doanh nhân, tác giả cuốn Khởi nghiệp tinh gọn (The Lean Startup).', '1949-01-12', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(6, 'Dale Carnegie', 'Tác giả cuốn sách Đắc nhân tâm nổi tiếng thế giới.', '1965-07-31', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(7, 'Phạm Huy Hoàng', 'Blogger \"Tôi Đi Code Dạo\", chia sẻ kinh nghiệm về ngành IT.', '1976-01-01', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(8, 'Bộ Giáo dục và Đào tạo', 'Cơ quan chủ quản biên soạn các giáo trình đại cương bắt buộc.', '1963-06-21', '2025-03-14 07:27:41', '2025-03-14 07:27:41'),
(9, 'Mai Lan Hương', 'Tác giả sách ngữ pháp và bài tập tiếng Anh kinh điển tại Việt Nam.', '1888-11-24', '2025-03-14 07:27:41', '2025-03-14 07:27:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT 3,
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `dia_chi` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role_id`, `so_dien_thoai`, `dia_chi`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, '$2y$12$s.AObj4vyHW8ms6o5WdmBOU79xzuQoeeisLqFeRmDbX0/OXWr06hi', 1, NULL, NULL, '3AVapbMrkXutARnAWU0BcnOncR68ndTatsGoKefKeEwnuaB12RXoap1MEr5r', '2026-10-01 07:27:41', '2026-10-08 21:19:30'),
(2, 'Thủ Thư', 'thuthu@gmail.com', NULL, '$2y$12$x7UrANUlNuBmNuk2K3ZJHedyW.6UALLTApMnVPPHNdwIDvLlb/Bji', 2, NULL, NULL, 'VaYoR7l0OnB6XNZzlcAOFwkrmCzP0gESo8OxHAO0WaKZLrxIGnrI5NFnRGxN', '2025-10-01 07:27:41', '2026-09-29 16:14:01'),
(3, 'Người Dùng', 'user@gmail.com', NULL, '$2y$12$.MYQkIjENg2dalgrNQNs.etrNvesCGL5n0oE7cgbV9.YI9BsbdiWS', 3, NULL, NULL, 'bIJhwNnBvISrG242XxRJvbe4P2jLAkGwMWjIceEvKVdM6DR9tszK0Xdvyunz', '2026-10-01 07:27:41', '2026-09-29 16:44:12');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chi_tiet_phieu_muon`
--
ALTER TABLE `chi_tiet_phieu_muon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chi_tiet_phieu_muon_phieu_muon_id_foreign` (`phieu_muon_id`),
  ADD KEY `chi_tiet_phieu_muon_sach_id_foreign` (`sach_id`);

--
-- Chỉ mục cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `doc_gia`
--
ALTER TABLE `doc_gia`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nha_xuat_ban`
--
ALTER TABLE `nha_xuat_ban`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `phieu_muon`
--
ALTER TABLE `phieu_muon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phieu_muon_doc_gia_id_foreign` (`doc_gia_id`),
  ADD KEY `phieu_muon_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sach_danh_muc_id_foreign` (`danh_muc_id`),
  ADD KEY `sach_tac_gia_id_foreign` (`tac_gia_id`),
  ADD KEY `sach_nha_xuat_ban_id_foreign` (`nha_xuat_ban_id`);

--
-- Chỉ mục cho bảng `tac_gia`
--
ALTER TABLE `tac_gia`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chi_tiet_phieu_muon`
--
ALTER TABLE `chi_tiet_phieu_muon`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `doc_gia`
--
ALTER TABLE `doc_gia`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nha_xuat_ban`
--
ALTER TABLE `nha_xuat_ban`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `phieu_muon`
--
ALTER TABLE `phieu_muon`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `sach`
--
ALTER TABLE `sach`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `tac_gia`
--
ALTER TABLE `tac_gia`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chi_tiet_phieu_muon`
--
ALTER TABLE `chi_tiet_phieu_muon`
  ADD CONSTRAINT `chi_tiet_phieu_muon_phieu_muon_id_foreign` FOREIGN KEY (`phieu_muon_id`) REFERENCES `phieu_muon` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_phieu_muon_sach_id_foreign` FOREIGN KEY (`sach_id`) REFERENCES `sach` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `phieu_muon`
--
ALTER TABLE `phieu_muon`
  ADD CONSTRAINT `phieu_muon_doc_gia_id_foreign` FOREIGN KEY (`doc_gia_id`) REFERENCES `doc_gia` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phieu_muon_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `sach`
--
ALTER TABLE `sach`
  ADD CONSTRAINT `sach_danh_muc_id_foreign` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sach_nha_xuat_ban_id_foreign` FOREIGN KEY (`nha_xuat_ban_id`) REFERENCES `nha_xuat_ban` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sach_tac_gia_id_foreign` FOREIGN KEY (`tac_gia_id`) REFERENCES `tac_gia` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

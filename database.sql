-- Database: `db_perpustakaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `category_id` int DEFAULT NULL,
  `stock` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `category_id`, `stock`, `created_at`) VALUES
(1, 'PHP Object Oriented', 'John Doe', 1, 0, '2025-11-29 08:10:36'),
(2, 'PHP Object Oriented', 'John Doe', 1, 0, '2025-11-29 08:24:41'),
(3, 'PHP Object Oriented', 'John Doe', 1, 0, '2025-11-29 08:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Teknologi'),
(2, 'Novel'),
(3, 'Sejarah');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int NOT NULL,
  `member_name` varchar(100) DEFAULT NULL,
  `book_title` varchar(100) DEFAULT NULL,
  `loan_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('borrowed','returned') DEFAULT 'borrowed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `member_name`, `book_title`, `loan_date`, `status`) VALUES
(1, 'Ani Siswa', 'PHP Object Oriented', '2025-11-29 15:10:36', 'borrowed'),
(2, 'Ani Siswa', 'PHP Object Oriented', '2025-11-29 15:10:36', 'borrowed'),
(3, 'Ani Siswa', 'PHP Object Oriented', '2025-11-29 15:24:41', 'borrowed'),
(4, 'Ani Siswa', 'PHP Object Oriented', '2025-11-29 15:24:41', 'borrowed'),
(5, 'Ani Siswa', 'PHP Object Oriented', '2025-11-29 15:33:40', 'borrowed'),
(6, 'Ani Siswa', 'PHP Object Oriented', '2025-11-29 15:33:40', 'borrowed');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

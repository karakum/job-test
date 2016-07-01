-- phpMyAdmin SQL Dump
-- version 4.5.0.2
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 01 2016 г., 20:18
-- Версия сервера: 5.6.27-log
-- Версия PHP: 5.6.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `yii2_basic_tests`
--

-- --------------------------------------------------------

--
-- Структура таблицы `document_invoice`
--

CREATE TABLE `document_invoice` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payer_id` int(11) NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` decimal(10,2) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `document_transfer`
--

CREATE TABLE `document_transfer` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` decimal(10,2) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `flows`
--

CREATE TABLE `flows` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `operation_id` int(11) NOT NULL,
  `begin` decimal(10,2) NOT NULL,
  `debit` decimal(10,2) NOT NULL,
  `credit` decimal(10,2) NOT NULL,
  `end` decimal(10,2) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1467379082),
('m160630_115952_create_sessions', 1467379085),
('m160630_120050_create_users', 1467379085),
('m160630_121740_create_operation', 1467379085),
('m160630_122830_create_flows', 1467379085),
('m160630_125200_create_document_transfer', 1467379086),
('m160630_130432_create_document_invoice', 1467379086);

-- --------------------------------------------------------

--
-- Структура таблицы `operation`
--

CREATE TABLE `operation` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `document_type` smallint(6) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--

CREATE TABLE `sessions` (
  `id` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expire` int(11) NOT NULL,
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authKey` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `document_invoice`
--
ALTER TABLE `document_invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_datetime` (`user_id`,`datetime`),
  ADD KEY `idx_payer_datetime` (`payer_id`,`datetime`);

--
-- Индексы таблицы `document_transfer`
--
ALTER TABLE `document_transfer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_datetime` (`user_id`,`datetime`),
  ADD KEY `idx_recipient_datetime` (`recipient_id`,`datetime`);

--
-- Индексы таблицы `flows`
--
ALTER TABLE `flows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_datetime` (`user_id`,`datetime`),
  ADD KEY `fk-flows-operation_id` (`operation_id`);

--
-- Индексы таблицы `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `operation`
--
ALTER TABLE `operation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_datetime` (`user_id`,`datetime`);

--
-- Индексы таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_expire` (`expire`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `document_invoice`
--
ALTER TABLE `document_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `document_transfer`
--
ALTER TABLE `document_transfer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `flows`
--
ALTER TABLE `flows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `operation`
--
ALTER TABLE `operation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `document_invoice`
--
ALTER TABLE `document_invoice`
  ADD CONSTRAINT `fk-document_invoice-payer_id` FOREIGN KEY (`payer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk-document_invoice-user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `document_transfer`
--
ALTER TABLE `document_transfer`
  ADD CONSTRAINT `fk-document_transfer-recipient_id` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk-document_transfer-user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `flows`
--
ALTER TABLE `flows`
  ADD CONSTRAINT `fk-flows-operation_id` FOREIGN KEY (`operation_id`) REFERENCES `operation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk-flows-user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `operation`
--
ALTER TABLE `operation`
  ADD CONSTRAINT `fk-operation-user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

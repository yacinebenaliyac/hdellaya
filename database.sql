-- H-DELLAYA — base de données
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS hdellaya
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hdellaya;

-- Catégories
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(60) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  emoji VARCHAR(10) DEFAULT '🌸',
  position INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO categories (slug, name, emoji, position) VALUES
('henne',     'Henné mariée',      '🕯️', 1),
('bouquets',  'Bouquets en cire',  '🌸', 2),
('parfumees', 'Bougies parfumées', '✨', 3),
('coffrets',  'Coffrets cadeaux',  '🎁', 4);

-- Produits
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NULL,
  name VARCHAR(180) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  image VARCHAR(255) DEFAULT '',
  featured TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO products (id, category_id, name, description, price) VALUES
(1, 1, 'Bougie de henné classique', 'Décor perles et ruban de tulle, pour la nuit du henné.', 2500),
(2, 1, 'Bougie de henné prestige',  'Dentelle fine, couronne de fleurs et finitions dorées.', 4500),
(3, 2, 'Bouquet de roses en cire',  'Un bouquet de roses sculptées à la main, sur socle en bois.', 3000),
(4, 2, 'Petites fleurs faveurs',    'Lot de 10 fleurs en cire parfumée, pour vos invités.', 1800),
(5, 3, 'Bougie en pot de verre',    'Cire de soja parfumée, en fleur de lotus ou vanille douce.', 2200),
(6, 4, 'Coffret bougie & fleurs',   'Bougie parfumée et fleurs en cire, assorties dans un coffret.', 3500);

-- Commandes
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  address VARCHAR(255) DEFAULT '',
  items TEXT,
  notes TEXT,
  total DECIMAL(10,2) DEFAULT 0,
  status ENUM('nouveau','en_cours','livre','annule') DEFAULT 'nouveau',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Paramètres
CREATE TABLE IF NOT EXISTS settings (
  `key` VARCHAR(60) PRIMARY KEY,
  `value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO settings (`key`, `value`) VALUES
('brand',        'H-DELLAYA'),
('phone',        '0696352703'),
('phone_intl',   '+213696352703'),
('instagram',    'https://www.instagram.com/h_dellaya'),
('tiktok',       'https://www.tiktok.com/@h_dellaya'),
('location',     'Tlemcen, Algérie'),
('hero_kicker',  'Bougies artisanales · Tlemcen, Algérie'),
('hero_title',   'La lumière commence par un *geste*, et se prolonge dans chaque instant.'),
('hero_lead',    'H-DELLAYA façonne à la main des bougies de henné, des bouquets en cire et des créations parfumées, pensées pour accompagner vos plus belles occasions.'),
('ig_posts',     '44'),
('ig_followers', '940'),
('ig_following', '2 246'),
('announce',     'Livraison à Tlemcen et environs · Fait main · Sur commande');

SET FOREIGN_KEY_CHECKS = 1;
-- Migration für den Webshop
-- Datum: 2026-08-17
--
-- Diese Datei nur EINMAL manuell in phpMyAdmin auf der Datenbank
-- "pbbfa24akr_giftboxes" ausführen. Sie verändert die bestehende
-- Struktur NICHT, sondern ergänzt nur zwei Dinge, die für die
-- Anforderungen (Bild-Upload, Admin-Bereich) nötig sind:
--
--   1. Neue, optionale Spalte "image_path" in der Tabelle "products"
--      für den Pfad des hochgeladenen Produktbilds.
--   2. Ein neuer Eintrag "admin" in der Tabelle "roles", damit es
--      überhaupt eine Admin-Rolle geben kann (bisher existiert nur "user").
--
-- Bestehende Daten bleiben unverändert erhalten.

ALTER TABLE `products`
    ADD COLUMN `image_path` VARCHAR(255) NULL DEFAULT NULL AFTER `stock`;

INSERT INTO `roles` (`role_name`) VALUES ('admin');

-- Optional: den vorhandenen Testbenutzer (user_id 2) zum Admin machen,
-- damit man sich sofort im Admin-Bereich anmelden kann.
-- role_id 2 entspricht hier dem neu angelegten "admin" (bei einer frischen
-- Datenbank ist roles.role_id für "admin" = 2, da "user" bereits 1 ist).
-- Vorher unbedingt in phpMyAdmin prüfen: SELECT * FROM roles;
--
-- UPDATE `users` SET `role_id` = 2 WHERE `user_id` = 2;

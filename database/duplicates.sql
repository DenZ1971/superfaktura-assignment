DROP TABLE IF EXISTS `duplicates`;

CREATE TABLE `duplicates` (
    `id` int NOT NULL AUTO_INCREMENT,
    `value` int NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_duplicates_value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `duplicates` (`id`, `value`) VALUES
     (1, 1),
     (2, 2),
     (3, 3),
     (4, 2),
     (5, 4),
     (6, 4),
     (7, 5),
     (8, 6),
     (9, 6),
     (10, 2);

SELECT d.*
FROM `duplicates` AS d
JOIN (
    SELECT `value`
    FROM `duplicates`
    GROUP BY `value`
    HAVING COUNT(*) > 1
) AS duplicated_values
    ON d.`value` = duplicated_values.`value`
ORDER BY d.`id`;
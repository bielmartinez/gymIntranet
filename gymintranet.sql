-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-05-2025 a las 22:06:19
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gymintranet`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `classes`
--

CREATE TABLE `classes` (
  `classe_id` int(11) NOT NULL,
  `tipus_classe_id` int(11) DEFAULT NULL,
  `monitor_id` int(11) DEFAULT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `duracio` int(11) NOT NULL,
  `capacitat_maxima` int(11) NOT NULL,
  `capacitat_actual` int(11) DEFAULT 0,
  `sala` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `classes`
--

INSERT INTO `classes` (`classe_id`, `tipus_classe_id`, `monitor_id`, `data`, `hora`, `duracio`, `capacitat_maxima`, `capacitat_actual`, `sala`) VALUES
(10, 3, 7, '2025-05-14', '20:00:00', 30, 15, 0, '1'),
(11, 4, 7, '2025-05-15', '10:30:00', 45, 10, 0, '2'),
(12, 1, 9, '2025-05-16', '15:15:00', 45, 10, 1, '4');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinataris_notificacions`
--

CREATE TABLE `destinataris_notificacions` (
  `notificacio_id` int(11) NOT NULL,
  `usuari_id` int(11) NOT NULL,
  `llegit_el` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `destinataris_notificacions`
--

INSERT INTO `destinataris_notificacions` (`notificacio_id`, `usuari_id`, `llegit_el`) VALUES
(5, 2, '2025-05-03 16:36:06'),
(5, 6, '2025-05-04 19:11:27'),
(5, 7, '2025-05-05 20:17:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `exercicis`
--

CREATE TABLE `exercicis` (
  `exercici_id` int(11) NOT NULL,
  `rutina_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `descripcio` text DEFAULT NULL,
  `series` int(11) DEFAULT 3,
  `repeticions` int(11) DEFAULT 10,
  `descans` int(11) DEFAULT 60,
  `ordre` int(11) DEFAULT 0,
  `info_adicional` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `exercicis`
--

INSERT INTO `exercicis` (`exercici_id`, `rutina_id`, `nom`, `descripcio`, `series`, `repeticions`, `descans`, `ordre`, `info_adicional`) VALUES
(1, 6, 'ejercicio falso', 'no hacer nada', 4, 12, 60, 1, NULL),
(2, 6, 'Wide-grip barbell curl', 'Stand up with your torso upright while holding a barbell at the wide outer handle. The palm of your hands should be facing forward. The elbows should be close to the torso. This will be your starting position. While holding the upper arms stationary, curl the weights forward while contracting the biceps as you breathe out. Tip: Only the forearms should move. Continue the movement until your biceps are fully contracted and the bar is at shoulder level. Hold the contracted position for a second and squeeze the biceps hard. Slowly begin to bring the bar back to starting position as your breathe in. Repeat for the recommended amount of repetitions.  Variations:  You can also perform this movement using an E-Z bar or E-Z attachment hooked to a low pulley. This variation seems to really provide a good contraction at the top of the movement. You may also use the closer grip for variety purposes.', 3, 12, 60, 1, '{\"muscle\":\"No especificado\",\"equipment\":\"barbell\",\"difficulty\":\"\"}'),
(3, 6, 'Dumbbell Bench Press', 'Lie down on a flat bench with a dumbbell in each hand resting on top of your thighs. The palms of your hands will be facing each other. Then, using your thighs to help raise the dumbbells up, lift the dumbbells one at a time so that you can hold them in front of you at shoulder width. Once at shoulder width, rotate your wrists forward so that the palms of your hands are facing away from you. The dumbbells should be just to the sides of your chest, with your upper arm and forearm creating a 90 degree angle. Be sure to maintain full control of the dumbbells at all times. This will be your starting position. Then, as you breathe out, use your chest to push the dumbbells up. Lock your arms at the top of the lift and squeeze your chest, hold for a second and then begin coming down slowly. Tip: Ideally, lowering the weight should take about twice as long as raising it. Repeat the movement for the prescribed amount of repetitions of your training program.  Caution: When you are done, do not drop the dumbbells next to you as this is dangerous to your rotator cuff in your shoulders and others working out around you. Just lift your legs from the floor bending at the knees, twist your wrists so that the palms of your hands are facing each other and place the dumbbells on top of your thighs. When both dumbbells are touching your thighs simultaneously push your upper torso up (while pressing the dumbbells on your thighs) and also perform a slight kick forward with your legs (keeping the dumbbells on top of the thighs). By doing this combined movement, momentum will help you get back to a sitting position with both dumbbells still on top of your thighs. At this moment you can place the dumbbells on the floor. Variations: Another variation of this exercise is to perform it with the palms of the hands facing each other. Also, you can perform the exercise with the palms facing each other and then twisting the wrist as you lift the dumbbells so that at the top of the movement the palms are facing away from the body. I personally do not use this variation very often as it seems to be hard on my shoulders.', 3, 12, 60, 1, NULL),
(4, 6, 'Barbell Curl', 'Stand up with your torso upright while holding a barbell at a shoulder-width grip. The palm of your hands should be facing forward and the elbows should be close to the torso. This will be your starting position. While holding the upper arms stationary, curl the weights forward while contracting the biceps as you breathe out. Tip: Only the forearms should move. Continue the movement until your biceps are fully contracted and the bar is at shoulder level. Hold the contracted position for a second and squeeze the biceps hard. Slowly begin to bring the bar back to starting position as your breathe in. Repeat for the recommended amount of repetitions.  Variations:  You can also perform this movement using a straight bar attachment hooked to a low pulley. This variation seems to really provide a good contraction at the top of the movement. You may also use the closer grip for variety purposes.', 3, 12, 60, 1, '{\"muscle\":\"No especificado\",\"equipment\":\"barbell\",\"difficulty\":\"\"}'),
(5, 6, 'Rocky Pull-Ups/Pulldowns', 'Grab the pull-up bar with the palms facing forward using a wide grip. As you have both arms extended in front of you holding the bar at the chosen grip width, bring your torso back around 30 degrees or so while creating a curvature on your lower back and sticking your chest out. This is your starting position. Pull your torso up until the bar touches your upper chest by drawing the shoulders and the upper arms down and back. Exhale as you perform this portion of the movement. Tip: Concentrate on squeezing the back muscles once you reach the full contracted position. The upper torso should remain stationary as it moves through space and only the arms should move. The forearms should do no other work other than hold the bar. After a second on the contracted position, start to inhale and slowly lower your torso back to the starting position when your arms are fully extended and the lats are fully stretched. Now repeat the same movements as described above except this time your torso will remain straight as you go up and the bar will touch the back of the neck instead of the upper chest. Tip: Use the head to lean forward slightly as it will help you properly execute this portion of the exercise. Once you have lowered yourself back down to the starting position, repeat the exercise for the prescribed amount of repetitions in your program.  Caution: The behind the neck variation can be hard on the rotator cuff due to the hyperextension created by bringing the bar behind the neck so this exercise is not recommended for people with shoulder problems. Variations:  If you are new at this exercise and do not have the strength to perform it, use a chin assist machine if available. These machines use weight to help you push your bodyweight. Otherwise, a spotter holding your legs can help. You can also use a pull-down machine.', 2, 12, 60, 1, NULL),
(6, 6, 'Leverage Shrug', 'Load the pins to an appropriate weight. Position yourself directly between the handles. Grasp the top handles with a comfortable grip, and then lower your hips as you take a breath. Look forward with your head and keep your chest up. Drive through the floor with your heels, extending your hips and knees as you rise to a standing position. Keep your arms straight throughout the movement, finishing with your shoulders back. This will be your starting position. Raise the weight by shrugging the shoulders towards your ears, moving straight up and down. Pause at the top of the motion, and then return the weight to the starting position.', 3, 12, 60, 2, NULL),
(8, 8, 'prensa hack', 'hack', 4, 8, 60, 1, NULL),
(9, 8, 'extensiones de quad', 'quad', 4, 14, 60, 5, NULL),
(10, 8, 'sentadilla', 'fafsd', 4, 10, 60, 7, NULL),
(11, 8, 'femoral', 'fa', 3, 12, 60, 7, NULL),
(12, 8, 'ultimo', 'fdsa', 3, 12, 60, 6, NULL),
(13, 8, 'yyyy', '', 3, 12, 60, 4, NULL),
(14, 8, 'gggg', '', 3, 12, 60, 2, NULL),
(15, 8, 'Single-arm palm-in dumbbell shoulder press', 'Start by having a dumbbell in one hand with your arm fully extended to the side using a neutral grip. Use your other arm to hold on to an incline bench to keep your balance. Your feet should be shoulder width apart from each other. Now slowly lift the dumbbell up until you create a 90 degree angle with your arm. Note: Your forearm should be perpendicular to the floor. Continue to maintain a neutral grip throughout the entire exercise. Slowly lift the dumbbell up until your arm is fully extended. This the starting position. While inhaling lower the weight down until your arm is at a 90 degree angle again. Feel the contraction for a second and then lift the weight back up towards the starting position while exhaling. Remember to hold on to the incline bench and keep your feet positioned to keep balance during the exercise. Repeat for the recommended amount of repetitions. Switch arms and repeat the exercise.  Variation: This exercise can be performed with dumbbells in each arm as the dumbbells will help to keep you balanced. This is another great way to add variety to your routines and keep them interesting.', 3, 12, 60, 3, 'dsa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacions`
--

CREATE TABLE `notificacions` (
  `notificacio_id` int(11) NOT NULL,
  `titol` varchar(100) NOT NULL,
  `missatge` text NOT NULL,
  `creat_el` timestamp NOT NULL DEFAULT current_timestamp(),
  `classe_id` int(11) DEFAULT NULL,
  `emisor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificacions`
--

INSERT INTO `notificacions` (`notificacio_id`, `titol`, `missatge`, `creat_el`, `classe_id`, `emisor_id`) VALUES
(5, 'ALERTA', 'PROVA', '2025-05-01 09:54:07', NULL, NULL),
(14, 'NOTIFICACION', 'Se notifica a los usuarios', '2025-05-09 19:49:32', NULL, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserves`
--

CREATE TABLE `reserves` (
  `reserva_id` int(11) NOT NULL,
  `classe_id` int(11) DEFAULT NULL,
  `usuari_id` int(11) DEFAULT NULL,
  `data_reserva` timestamp NOT NULL DEFAULT current_timestamp(),
  `assistencia` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reserves`
--

INSERT INTO `reserves` (`reserva_id`, `classe_id`, `usuari_id`, `data_reserva`, `assistencia`) VALUES
(12, 12, 6, '2025-05-09 19:51:54', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutines`
--

CREATE TABLE `rutines` (
  `rutina_id` int(11) NOT NULL,
  `usuari_id` int(11) DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `descripcio` text DEFAULT NULL,
  `creat_el` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rutines`
--

INSERT INTO `rutines` (`rutina_id`, `usuari_id`, `nom`, `descripcio`, `creat_el`) VALUES
(6, 6, 'porva3', 'fsa', '2025-05-05 15:49:48'),
(8, 6, 'rutina dani', 'rutina pierna', '2025-05-07 14:19:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguiment_fisic`
--

CREATE TABLE `seguiment_fisic` (
  `seguiment_id` int(11) NOT NULL,
  `usuari_id` int(11) DEFAULT NULL,
  `pes` decimal(5,2) DEFAULT NULL,
  `alcada` decimal(5,2) DEFAULT NULL,
  `imc` decimal(4,2) DEFAULT NULL,
  `data_mesura` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `seguiment_fisic`
--

INSERT INTO `seguiment_fisic` (`seguiment_id`, `usuari_id`, `pes`, `alcada`, `imc`, `data_mesura`) VALUES
(5, 6, 75.00, 178.00, 23.67, '2025-05-08 07:36:18'),
(6, 6, 72.00, 178.00, 22.72, '2025-05-08 07:36:28'),
(7, 6, 67.00, 178.00, 21.15, '2025-05-08 07:40:25'),
(9, 6, 85.00, 178.00, 26.83, '2025-05-08 18:05:32'),
(10, 6, 58.00, 178.00, 18.31, '2025-05-08 19:29:23'),
(11, 6, 77.00, 178.00, 24.30, '2025-05-09 07:48:26'),
(12, 6, 73.00, 178.00, 23.04, '2025-05-09 16:07:34'),
(13, 11, 62.00, 160.00, 24.22, '2025-05-09 18:59:26'),
(14, 11, 55.00, 160.00, 21.48, '2025-05-09 18:59:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipus_classes`
--

CREATE TABLE `tipus_classes` (
  `tipus_classe_id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `descripcio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipus_classes`
--

INSERT INTO `tipus_classes` (`tipus_classe_id`, `nom`, `descripcio`) VALUES
(1, 'Ioga', 'Classes de ioga per a tots els nivells'),
(2, 'Pilates', 'Exercicis de pilates i core'),
(3, 'Funcional', 'Entrenament funcional d alta intensitat'),
(4, 'Spinning', ''),
(5, 'Zumba', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuaris`
--

CREATE TABLE `usuaris` (
  `usuari_id` int(11) NOT NULL,
  `contrasenya` varchar(255) NOT NULL,
  `correu` varchar(100) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `cognoms` varchar(50) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `actiu` tinyint(1) DEFAULT 1,
  `creat_el` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultim_acces` timestamp NULL DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL COMMENT 'Teléfono del usuario',
  `token_recuperacio` varchar(255) DEFAULT NULL,
  `token_expiracio` datetime DEFAULT NULL,
  `token_creat` datetime DEFAULT NULL,
  `data_naixement` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuaris`
--

INSERT INTO `usuaris` (`usuari_id`, `contrasenya`, `correu`, `nom`, `cognoms`, `role`, `actiu`, `creat_el`, `ultim_acces`, `phone`, `token_recuperacio`, `token_expiracio`, `token_creat`, `data_naixement`) VALUES
(2, '$2y$10$JGDnxBC.tySNv7.ksVqjGOInpdNGzVl/8GCBURhll.YWhI8T1D.YG', 'admin@admin.com', 'admin', '', 'admin', 1, '2025-04-28 14:52:42', NULL, NULL, NULL, NULL, NULL, NULL),
(3, '$2y$10$NKTTzGuHabVGtwu/G3rwJOxWEt8Bt9hIcRBv7FaCahkk0VHWmSp7W', 'b.martinez@sapalomera.cat', 'admin', '', 'admin', 1, '2025-04-28 14:55:10', NULL, NULL, NULL, NULL, NULL, NULL),
(5, '$2y$10$2h7ykeg/SalyCQnoj26Laef9GgerwdW4q8kL7v3iv/0pKnoPaH4Qi', 'bielmailerphp.com@gmail.com', 'Prova', 'provez', 'admin', 1, '2025-04-29 17:01:52', NULL, NULL, NULL, NULL, NULL, NULL),
(6, '$2y$10$dszNzuPMHeeytPw/MeUE4ufiAzUnirWuD1mpTczd8/u5W/Z6YxwwS', 'user@user.com', 'User', 'user', 'user', 1, '2025-05-01 10:09:33', NULL, '12345678', NULL, NULL, NULL, '2024-03-13'),
(7, '$2y$10$jbCEiwYBUIswaElJTPz7TuwAzZ0us0vVZYRSW1bIn0fK9vEAMMdjy', 'profe@profe.com', 'Profe', 'profez', 'staff', 1, '2025-05-01 17:08:46', NULL, '12345678', NULL, NULL, NULL, '2023-05-06'),
(9, '$2y$10$eMIu2j4geoKXcm/49/sC1eIFhWYYvMZKp.fjiYg4wqDAd6tv1zua2', 'profe2@profe2.com', 'profe2', '', 'staff', 1, '2025-05-08 21:34:37', NULL, '', NULL, NULL, NULL, NULL),
(10, '$2y$10$ehMBkhSVfn4gnTRVgGqkt.5zzYSm6ecedCalRbgZckEpJytGQBPZi', 'bielmartinezcaceres16@gmail.com', 'biel', 'yo', 'user', 1, '2025-05-08 22:07:33', NULL, '', NULL, NULL, NULL, NULL),
(11, '$2y$10$scfXFLuQm9.CTOqHtPjVGek8eiHV7DmKKw/EHmXx6mFzIXKktLv2G', 'carli@carli.com', 'carli', '', 'user', 0, '2025-05-09 18:57:13', NULL, '12345678', NULL, NULL, NULL, '2025-05-02');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`classe_id`),
  ADD KEY `tipus_classe_id` (`tipus_classe_id`),
  ADD KEY `classes_ibfk_2` (`monitor_id`);

--
-- Indices de la tabla `destinataris_notificacions`
--
ALTER TABLE `destinataris_notificacions`
  ADD PRIMARY KEY (`notificacio_id`,`usuari_id`),
  ADD KEY `usuari_id` (`usuari_id`);

--
-- Indices de la tabla `exercicis`
--
ALTER TABLE `exercicis`
  ADD PRIMARY KEY (`exercici_id`),
  ADD KEY `rutina_id` (`rutina_id`);

--
-- Indices de la tabla `notificacions`
--
ALTER TABLE `notificacions`
  ADD PRIMARY KEY (`notificacio_id`),
  ADD KEY `idx_notificacions_creades` (`creat_el`),
  ADD KEY `fk_notificacions_classes` (`classe_id`),
  ADD KEY `fk_notificacions_emisor` (`emisor_id`);

--
-- Indices de la tabla `reserves`
--
ALTER TABLE `reserves`
  ADD PRIMARY KEY (`reserva_id`),
  ADD UNIQUE KEY `classe_id` (`classe_id`,`usuari_id`),
  ADD KEY `idx_reserves_usuari` (`usuari_id`),
  ADD KEY `idx_reserves_classe` (`classe_id`);

--
-- Indices de la tabla `rutines`
--
ALTER TABLE `rutines`
  ADD PRIMARY KEY (`rutina_id`),
  ADD KEY `usuari_id` (`usuari_id`);

--
-- Indices de la tabla `seguiment_fisic`
--
ALTER TABLE `seguiment_fisic`
  ADD PRIMARY KEY (`seguiment_id`),
  ADD KEY `idx_seguiment_usuari` (`usuari_id`),
  ADD KEY `idx_seguiment_data` (`data_mesura`);

--
-- Indices de la tabla `tipus_classes`
--
ALTER TABLE `tipus_classes`
  ADD PRIMARY KEY (`tipus_classe_id`);

--
-- Indices de la tabla `usuaris`
--
ALTER TABLE `usuaris`
  ADD PRIMARY KEY (`usuari_id`),
  ADD UNIQUE KEY `correu` (`correu`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `classes`
--
ALTER TABLE `classes`
  MODIFY `classe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `exercicis`
--
ALTER TABLE `exercicis`
  MODIFY `exercici_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `notificacions`
--
ALTER TABLE `notificacions`
  MODIFY `notificacio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `reserves`
--
ALTER TABLE `reserves`
  MODIFY `reserva_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `rutines`
--
ALTER TABLE `rutines`
  MODIFY `rutina_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `seguiment_fisic`
--
ALTER TABLE `seguiment_fisic`
  MODIFY `seguiment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `tipus_classes`
--
ALTER TABLE `tipus_classes`
  MODIFY `tipus_classe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuaris`
--
ALTER TABLE `usuaris`
  MODIFY `usuari_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`tipus_classe_id`) REFERENCES `tipus_classes` (`tipus_classe_id`),
  ADD CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`monitor_id`) REFERENCES `usuaris` (`usuari_id`);

--
-- Filtros para la tabla `destinataris_notificacions`
--
ALTER TABLE `destinataris_notificacions`
  ADD CONSTRAINT `destinataris_notificacions_ibfk_1` FOREIGN KEY (`notificacio_id`) REFERENCES `notificacions` (`notificacio_id`),
  ADD CONSTRAINT `destinataris_notificacions_ibfk_2` FOREIGN KEY (`usuari_id`) REFERENCES `usuaris` (`usuari_id`);

--
-- Filtros para la tabla `exercicis`
--
ALTER TABLE `exercicis`
  ADD CONSTRAINT `exercicis_ibfk_1` FOREIGN KEY (`rutina_id`) REFERENCES `rutines` (`rutina_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificacions`
--
ALTER TABLE `notificacions`
  ADD CONSTRAINT `fk_notificacions_classes` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`classe_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_notificacions_emisor` FOREIGN KEY (`emisor_id`) REFERENCES `usuaris` (`usuari_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `reserves`
--
ALTER TABLE `reserves`
  ADD CONSTRAINT `reserves_ibfk_1` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`classe_id`),
  ADD CONSTRAINT `reserves_ibfk_2` FOREIGN KEY (`usuari_id`) REFERENCES `usuaris` (`usuari_id`);

--
-- Filtros para la tabla `rutines`
--
ALTER TABLE `rutines`
  ADD CONSTRAINT `rutines_ibfk_1` FOREIGN KEY (`usuari_id`) REFERENCES `usuaris` (`usuari_id`);

--
-- Filtros para la tabla `seguiment_fisic`
--
ALTER TABLE `seguiment_fisic`
  ADD CONSTRAINT `seguiment_fisic_ibfk_1` FOREIGN KEY (`usuari_id`) REFERENCES `usuaris` (`usuari_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

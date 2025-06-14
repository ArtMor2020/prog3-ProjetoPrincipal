drop database if exists forum_database;
create database forum_database;

INSERT INTO user (
    name,
    email,
    password,
    about,
    is_private,
    is_banned,
    is_deleted
) VALUES
('Alice Johnson', 'alice@example.com', '$2y$10$examplehash1', 'I love hiking and coding.', FALSE, FALSE, FALSE),
('Bob Smith', 'bob@example.com', '$2y$10$examplehash2', 'Backend developer and coffee enthusiast.', FALSE, FALSE, FALSE),
('Carol White', 'carol@example.com', '$2y$10$examplehash3', 'UX/UI designer who enjoys painting.', FALSE, FALSE, FALSE),
('David Brown', 'david@example.com', '$2y$10$examplehash4', 'Full-stack dev and open-source contributor.', FALSE, FALSE, FALSE),
('Eva Green', 'eva@example.com', '$2y$10$examplehash5', 'Cybersecurity geek and gamer.', FALSE, FALSE, FALSE),
('Frank Harris', 'frank@example.com', '$2y$10$examplehash6', 'PHP developer. Code, sleep, repeat.', FALSE, FALSE, FALSE),
('Grace Lee', 'grace@example.com', '$2y$10$examplehash7', 'Front-end dev. Lover of clean design.', FALSE, FALSE, FALSE),
('Henry King', 'henry@example.com', '$2y$10$examplehash8', 'DevOps and cloud engineer.', FALSE, FALSE, FALSE),
('Ivy Adams', 'ivy@example.com', '$2y$10$examplehash9', 'Machine learning explorer.', FALSE, FALSE, FALSE),
('Jack Nelson', 'jack@example.com', '$2y$10$examplehash10', 'Tech writer and blogger.', FALSE, FALSE, FALSE);


INSERT INTO friendship (
    id_user1,
    id_user2,
    status,
    requested_at,
    friends_since
) VALUES
(1, 2, 'friend_request', '2025-06-07 10:00:00', NULL),
(2, 3, 'friends', '2025-05-30 12:00:00', '2025-05-31 14:00:00'),
(4, 5, 'friend_request', '2025-06-09 08:00:00', NULL),
(6, 7, 'friends', '2025-05-20 09:30:00', '2025-05-22 10:15:00'),
(1, 3, 'friends', '2025-05-10 16:45:00', '2025-05-12 18:00:00'),
(8, 9, 'friend_request', '2025-06-08 19:00:00', NULL),
(10, 1, 'friends', '2025-04-10 08:20:00', '2025-04-11 09:00:00'),
(3, 6, 'friends', '2025-06-01 11:11:00', '2025-06-02 13:30:00'),
(5, 10, 'friend_request', '2025-06-06 17:55:00', NULL),
(2, 8, 'friends', '2025-05-25 20:20:00', '2025-05-26 21:21:00');

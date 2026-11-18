-- Touiteur.app : base de donnees (MariaDB / MySQL)
-- A importer dans une base VIDE nommee "touiteur" (phpMyAdmin > Importer)

CREATE TABLE utilisateur (
    id INT NOT NULL AUTO_INCREMENT,
    pseudo VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE (pseudo)
);

-- texte : 1000 caracteres car les textes sont "nettoyes" avant d'etre enregistres
-- (une apostrophe devient &#39;) ; la limite de 235 caracteres est verifiee en PHP.
CREATE TABLE touite (
    id INT NOT NULL AUTO_INCREMENT,
    texte VARCHAR(1000) NOT NULL,
    date_pub DATETIME NOT NULL,
    image VARCHAR(100) NULL,
    id_utilisateur INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id)
);

CREATE TABLE tag (
    id INT NOT NULL AUTO_INCREMENT,
    libelle VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE (libelle)
);

CREATE TABLE touite_tag (
    id_touite INT NOT NULL,
    id_tag INT NOT NULL,
    PRIMARY KEY (id_touite, id_tag),
    FOREIGN KEY (id_touite) REFERENCES touite(id),
    FOREIGN KEY (id_tag) REFERENCES tag(id)
);

CREATE TABLE suit_utilisateur (
    id_suiveur INT NOT NULL,
    id_suivi INT NOT NULL,
    PRIMARY KEY (id_suiveur, id_suivi),
    FOREIGN KEY (id_suiveur) REFERENCES utilisateur(id),
    FOREIGN KEY (id_suivi) REFERENCES utilisateur(id)
);

CREATE TABLE suit_tag (
    id_utilisateur INT NOT NULL,
    id_tag INT NOT NULL,
    PRIMARY KEY (id_utilisateur, id_tag),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id),
    FOREIGN KEY (id_tag) REFERENCES tag(id)
);

-- valeur : 1 = like, -1 = dislike ; une seule note par utilisateur et par touite
CREATE TABLE note (
    id_utilisateur INT NOT NULL,
    id_touite INT NOT NULL,
    valeur INT NOT NULL,
    PRIMARY KEY (id_utilisateur, id_touite),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id),
    FOREIGN KEY (id_touite) REFERENCES touite(id)
);

-- Jeu d'essai
INSERT INTO utilisateur (pseudo) VALUES
('alice'), ('bob'), ('carol'), ('lucas'), ('emma'), ('hugo');

INSERT INTO tag (libelle) VALUES
('php'), ('web'), ('iut'), ('bonjour'), ('informatique'), ('dev');

INSERT INTO touite (texte, date_pub, image, id_utilisateur) VALUES
('Bonjour tout le monde #bonjour', '2026-11-01 09:00:00', NULL, 2),
('Je decouvre le PHP #php #informatique', '2026-11-01 10:30:00', NULL, 1),
('Premier cours de web a lIUT #web #iut', '2026-11-02 08:15:00', NULL, 3),
('Les namespaces ca devient clair #php #dev', '2026-11-02 11:00:00', NULL, 1),
('Nouveau projet en PHP #informatique #php', '2026-11-03 14:20:00', NULL, 4),
('Pause cafe a lIUT #iut', '2026-11-03 15:45:00', NULL, 5),
('Je teste lautoload de Composer #dev #php', '2026-11-04 09:10:00', NULL, 6),
('Le web cest fun #web', '2026-11-04 12:00:00', NULL, 2),
('Debut de la SAE Touiteur #iut #web #php', '2026-11-05 08:00:00', NULL, 3),
('Qui a compris les requetes preparees ? #php', '2026-11-05 17:30:00', NULL, 4),
('Bug trouve et corrige #dev', '2026-11-06 10:05:00', NULL, 5),
('Bon week-end a tous #bonjour', '2026-11-06 18:00:00', NULL, 1),
('Revisions pour lexamen de PHP #php #iut', '2026-11-07 20:00:00', NULL, 6),
('Le formulaire de publication marche #web #dev', '2026-11-08 16:00:00', NULL, 2);

INSERT INTO touite_tag (id_touite, id_tag) VALUES
(1,4),(2,1),(2,5),(3,2),(3,3),(4,1),(4,6),(5,5),(5,1),(6,3),(7,6),(7,1),
(8,2),(9,3),(9,2),(9,1),(10,1),(11,6),(12,4),(13,1),(13,3),(14,2),(14,6);

INSERT INTO suit_utilisateur (id_suiveur, id_suivi) VALUES
(1,2),(1,3),(2,1),(3,1),(4,1),(5,1),(6,1),(2,3),(4,3),(5,2);

INSERT INTO suit_tag (id_utilisateur, id_tag) VALUES
(1,2),(2,1),(3,6);

INSERT INTO note (id_utilisateur, id_touite, valeur) VALUES
(2,2,1),(3,2,1),(4,2,-1),(1,1,1),(5,4,1),(6,4,1),(1,8,1),(2,9,1);

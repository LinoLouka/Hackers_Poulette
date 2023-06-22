<?php
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = cleanInput($_POST["nom"]);
    $prenom = cleanInput($_POST["prenom"]);
    $email = cleanInput($_POST["email"]);
    $description = cleanInput($_POST["description"]);

    $errors = validateForm($nom, $prenom, $email, $_FILES["deposer"], $description);

    if (count($errors) === 0) {
        $host = 'localhost';
        $dbname = 'becode';
        $username = 'root';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erreur de connexion à la base de données : " . $e->getMessage();
        }

        $sql = "INSERT INTO formulaire (nom, prenom, email, description) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([$nom, $prenom, $email, $description]);
            echo "Données insérées avec succès dans la base de données.";
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion des données : " . $e->getMessage();
        }

        $pdo = null;
    }
}

function cleanInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateForm($nom, $prenom, $email, $file, $description)
{
    $errors = array();

    if (empty($nom) || strlen($nom) < 2 || strlen($nom) > 255) {
        $errors['nom'] = "Veuillez entrer un nom valide (au moins 2 caractères, max 255).";
    }

    if (empty($prenom) || strlen($prenom) < 2 || strlen($prenom) > 255) {
        $errors['prenom'] = "Veuillez entrer un prénom valide (au moins 2 caractères, max 255).";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
        $errors['email'] = "Veuillez entrer une adresse e-mail valide (max 255 caractères).";
    }

    if (!empty($file["name"])) {
        $allowedTypes = array("image/jpeg", "image/png", "image/gif");
        $maxFileSize = 2 * 1024 * 1024; // 2 Mo

        if (!in_array($file["type"], $allowedTypes)) {
            $errors['deposer'] = "Veuillez sélectionner un fichier image (jpg, png, gif).";
        }

        if ($file["size"] > $maxFileSize) {
            $errors['deposer'] = "La taille du fichier dépasse la limite autorisée (2 Mo).";
        }
    }

    if (empty($description) || strlen($description) < 2 || strlen($description) > 1000) {
        $errors['description'] = "Veuillez entrer une description valide (au moins 2 caractères, max 1000).";
    }

    return $errors;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hackers Poulette</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <h1>Contact</h1>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?t=<?php echo time(); ?>" enctype="multipart/form-data">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" required>
        <?php if (isset($errors['nom'])) : ?>
            <span class="error"><?php echo $errors['nom']; ?></span>
        <?php endif; ?>
        <br>

        <label for="prenom">Prénom:</label>
        <input type="text" id="prenom" name="prenom" required>
        <?php if (isset($errors['prenom'])) : ?>
            <span class="error"><?php echo $errors['prenom']; ?></span>
        <?php endif; ?>
        <br>

        <label for="email">Adresse e-mail:</label>
        <input type="email" id="email" name="email" required>
        <?php if (isset($errors['email'])) : ?>
            <span class="error"><?php echo $errors['email']; ?></span>
        <?php endif; ?>
        <br>

        <label for="deposer">Déposer un fichier:</label>
        <input type="file" id="deposer" name="deposer">
        <?php if (isset($errors['deposer'])) : ?>
            <span class="error"><?php echo $errors['deposer']; ?></span>
        <?php endif; ?>
        <br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="5" cols="40" required></textarea>

        <?php if (isset($errors['description'])) : ?>
            <span class="error"><?php echo $errors['description']; ?></span>
        <?php endif; ?>

        <br>

        <input type="submit" value="Envoyer">
    </form>
</body>

</html>